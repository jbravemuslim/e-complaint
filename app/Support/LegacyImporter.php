<?php

namespace App\Support;

use App\Models\Complain;
use App\Models\Faq;
use App\Models\Jenis;
use App\Models\Response;
use App\Models\Tujuan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Impor dump SQL native (database/legacy/e_complain.sql) ke skema Laravel.
 *
 * Mapping: akun_admin/akun_user -> users, tjn_keluhan -> tujuans,
 * jns_keluhan -> jenis, faq -> faqs, complain -> complains
 * (no_antrian dipertahankan sebagai id), tindak_lanjut -> responses.
 *
 * Password legacy plaintext di-hash otomatis oleh cast 'hashed' di User.
 * Lampiran 'kosong'/'noImage.png'/'' dinormalisasi menjadi null.
 */
class LegacyImporter
{
    /** @var array<string, array<int, int>> old-id => new-id per entitas */
    private array $maps = [
        'tujuan' => [],
        'jenis' => [],
        'user' => [],
        'admin' => [],
    ];

    private array $warnings = [];

    public function __construct(private ?string $imgDir = null) {}

    /**
     * @return array{parsed: array<string,int>, imported: array<string,int>, files_copied: int, files_missing: int, warnings: list<string>}
     */
    public function run(string $sqlPath): array
    {
        $rows = self::parseDump($sqlPath);
        $parsed = array_map(count(...), $rows);

        $stats = [
            'tujuans' => 0, 'jenis' => 0, 'faqs' => 0,
            'users' => 0, 'complains' => 0, 'complains_skipped' => 0,
            'responses' => 0, 'responses_skipped' => 0,
        ];

        DB::transaction(function () use ($rows, &$stats): void {
            foreach ($rows['tjn_keluhan'] as $r) {
                [$id, $nama] = [(int) $r[0], trim((string) ($r[1] ?? ''))];
                if ($nama === '') {
                    continue;
                }
                $t = Tujuan::firstOrCreate(['nama_unit' => $nama]);
                $this->maps['tujuan'][$id] = $t->id;
                $stats['tujuans']++;
            }

            foreach ($rows['jns_keluhan'] as $r) {
                [$id, $nama] = [(int) $r[0], trim((string) ($r[1] ?? ''))];
                if ($nama === '') {
                    continue;
                }
                $j = Jenis::firstOrCreate(['nama_jenis' => $nama]);
                $this->maps['jenis'][$id] = $j->id;
                $stats['jenis']++;
            }

            foreach ($rows['faq'] as $r) {
                $pertanyaan = trim((string) ($r[1] ?? ''));
                $jawaban = (string) ($r[2] ?? '');
                if ($pertanyaan === '') {
                    continue;
                }
                Faq::firstOrCreate(
                    ['pertanyaan' => $pertanyaan, 'jawaban' => $jawaban]
                );
                $stats['faqs']++;
            }

            foreach ($rows['akun_admin'] as $r) {
                $this->importUser(
                    oldId: (int) $r[0],
                    name: (string) ($r[1] ?? ''),
                    username: (string) ($r[2] ?? ''),
                    password: (string) ($r[3] ?? ''),
                    role: in_array($r[4], ['admin', 'superadmin'], true) ? $r[4] : 'admin',
                    map: 'admin',
                    stats: $stats,
                );
            }

            foreach ($rows['akun_user'] as $r) {
                $this->importUser(
                    oldId: (int) $r[0],
                    name: (string) ($r[1] ?? ''),
                    username: (string) ($r[2] ?? ''),
                    password: (string) ($r[3] ?? ''),
                    role: 'user',
                    map: 'user',
                    stats: $stats,
                );
            }

            foreach ($rows['complain'] as $r) {
                $this->importComplain($r, $stats);
            }

            foreach ($rows['tindak_lanjut'] as $r) {
                $this->importResponse($r, $stats);
            }
        });

        ['copied' => $copied, 'missing' => $missing] = $this->copyLampiran($rows['complain']);

        return [
            'parsed' => $parsed,
            'imported' => $stats,
            'files_copied' => $copied,
            'files_missing' => $missing,
            'warnings' => $this->warnings,
        ];
    }

    private function importUser(int $oldId, string $name, string $username, string $password, string $role, string $map, array &$stats): void
    {
        $username = trim($username);
        if ($username === '') {
            $this->warnings[] = "Akun {$map}#{$oldId} dilewati: username kosong.";

            return;
        }

        $existing = User::where('username', $username)->first();
        if ($existing) {
            // Pertahankan password yang sudah ada (mis. user yang sudah register ulang).
            $existing->forceFill([
                'name' => $name !== '' ? $name : $existing->name,
                'role' => $role,
            ])->save();
            $this->maps[$map][$oldId] = $existing->id;

            return;
        }

        $user = new User;
        if (! User::where('id', $oldId)->exists()) {
            $user->id = $oldId; // pertahankan id agar relasi legacy tetap valid
        }
        // Cast 'hashed' di model mengubah plaintext legacy menjadi hash.
        $user->forceFill([
            'name' => $name !== '' ? $name : $username,
            'username' => $username,
            'password' => $password !== '' ? $password : $username,
            'role' => $role,
        ])->save();

        $this->maps[$map][$oldId] = $user->id;
        $stats['users']++;
    }

    private function importComplain(array $r, array &$stats): void
    {
        // (no_antrian, id_user, keluhan, lampiran, tgl_keluhan, tjn_kategori, jns_kategori, status)
        [$no, $idUser, $keluhan, $lampiran, $tgl, $tjn, $jns, $status] = array_pad($r, 8, null);

        $userId = $this->maps['user'][(int) $idUser] ?? null;
        if ($userId === null || ! User::where('id', $userId)->exists()) {
            $this->warnings[] = "Complain #{$no} dilewati: id_user {$idUser} tidak ditemukan.";
            $stats['complains_skipped']++;

            return;
        }

        $lampiran = self::normalizeLampiran($lampiran);
        $tgl = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $tgl) ? $tgl : now()->toDateString();

        $complain = Complain::find((int) $no) ?? new Complain;
        if (! $complain->exists) {
            // Direct assignment (bukan mass assignment) agar id eksplisit tidak dibuang $fillable.
            $complain->id = (int) $no;
        }
        $complain->forceFill([
            'user_id' => $userId,
            'keluhan' => (string) $keluhan,
            'lampiran' => $lampiran,
            'tgl_keluhan' => $tgl,
            'tujuan_id' => $this->maps['tujuan'][(int) $tjn] ?? null,
            'jenis_id' => $this->maps['jenis'][(int) $jns] ?? null,
            'status' => $status === 'selesai' ? 'selesai' : 'proses',
        ])->save();

        $stats['complains']++;
    }

    private function importResponse(array $r, array &$stats): void
    {
        // (id_lanjut, no_antrian, id_admin, tanggapan, tgl_tanggapan)
        [$id, $no, $idAdmin, $tanggapan, $tgl] = array_pad($r, 5, null);

        if (! Complain::where('id', (int) $no)->exists()) {
            $this->warnings[] = "Tindak lanjut #{$id} dilewati: no_antrian {$no} tidak ditemukan.";
            $stats['responses_skipped']++;

            return;
        }

        $adminId = $this->maps['admin'][(int) $idAdmin] ?? null;
        if ($adminId === null) {
            $this->warnings[] = "Tindak lanjut #{$id} dilewati: id_admin {$idAdmin} tidak ditemukan.";
            $stats['responses_skipped']++;

            return;
        }

        Response::updateOrCreate(
            ['complain_id' => (int) $no],
            [
                'admin_id' => $adminId,
                'tanggapan' => (string) $tanggapan,
                'tgl_tanggapan' => preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $tgl) ? $tgl : now()->toDateString(),
            ]
        );
        $stats['responses']++;
    }

    /** @param list<array> $complainRows */
    private function copyLampiran(array $complainRows): array
    {
        if ($this->imgDir === null || ! is_dir($this->imgDir)) {
            return ['copied' => 0, 'missing' => 0];
        }

        $copied = 0;
        $missing = 0;

        foreach ($complainRows as $r) {
            $file = self::normalizeLampiran($r[3] ?? null);
            if ($file === null) {
                continue;
            }
            if (Storage::disk('public')->exists('lampiran/'.$file)) {
                continue;
            }

            $src = rtrim($this->imgDir, '/\\').DIRECTORY_SEPARATOR.$file;
            if (is_file($src)) {
                Storage::disk('public')->put('lampiran/'.$file, file_get_contents($src));
                $copied++;
            } else {
                $missing++;
                $this->warnings[] = "Lampiran {$file} tidak ditemukan di {$this->imgDir}.";
            }
        }

        return ['copied' => $copied, 'missing' => $missing];
    }

    public static function normalizeLampiran(mixed $value): ?string
    {
        $v = trim((string) ($value ?? ''));
        if ($v === '' || in_array(strtolower($v), ['kosong', 'noimage.png', 'no_image.png'], true)) {
            return null;
        }

        return basename($v);
    }

    /**
     * Parse dump phpMyAdmin menjadi baris-baris per tabel.
     *
     * @return array<string, list<list<mixed>>>
     */
    public static function parseDump(string $sqlPath): array
    {
        if (! is_file($sqlPath)) {
            throw new \InvalidArgumentException("File SQL tidak ditemukan: {$sqlPath}");
        }

        $tables = ['akun_admin' => [], 'akun_user' => [], 'tjn_keluhan' => [], 'jns_keluhan' => [], 'faq' => [], 'complain' => [], 'tindak_lanjut' => []];
        $current = null;
        $buffer = '';

        foreach (file($sqlPath, FILE_IGNORE_NEW_LINES) as $line) {
            if (preg_match('/^INSERT INTO `(\w+)`/', trim($line), $m) && isset($tables[$m[1]])) {
                $current = $m[1];
                $pos = strpos($line, 'VALUES');
                $buffer = substr($line, $pos + 6);
            } elseif ($current !== null) {
                $buffer .= "\n".$line;
            } else {
                continue;
            }

            if (str_ends_with(rtrim($buffer), ';')) {
                foreach (self::splitTuples(rtrim(rtrim($buffer), ';')) as $tuple) {
                    $tables[$current][] = self::splitValues($tuple);
                }
                $current = null;
                $buffer = '';
            }
        }

        return $tables;
    }

    /** Pecah "(...),(...)" level-teratas dengan hormat pada string quoted. */
    private static function splitTuples(string $valuesPart): array
    {
        $tuples = [];
        $depth = 0;
        $inStr = false;
        $start = null;
        $len = strlen($valuesPart);

        for ($i = 0; $i < $len; $i++) {
            $ch = $valuesPart[$i];
            if ($inStr) {
                if ($ch === '\\') {
                    $i++;
                } elseif ($ch === "'") {
                    if (($valuesPart[$i + 1] ?? null) === "'") {
                        $i++;
                    } else {
                        $inStr = false;
                    }
                }

                continue;
            }
            if ($ch === "'") {
                $inStr = true;
            } elseif ($ch === '(') {
                if ($depth === 0) {
                    $start = $i + 1;
                }
                $depth++;
            } elseif ($ch === ')') {
                $depth--;
                if ($depth === 0 && $start !== null) {
                    $tuples[] = substr($valuesPart, $start, $i - $start);
                    $start = null;
                }
            }
        }

        return $tuples;
    }

    /** Pecah nilai tuple per koma dengan hormat pada string quoted. */
    private static function splitValues(string $tuple): array
    {
        $out = [];
        $cur = '';
        $inStr = false;
        $quoted = false;
        $len = strlen($tuple);

        $flush = function () use (&$out, &$cur, &$quoted): void {
            $token = trim($cur);
            if ($quoted) {
                $out[] = str_replace(["\\'", '""', "''"], ["'", '"', "'"], $token);
            } elseif (strcasecmp($token, 'null') === 0) {
                $out[] = null;
            } elseif (is_numeric($token)) {
                $out[] = $token + 0;
            } else {
                $out[] = $token;
            }
            $cur = '';
            $quoted = false;
        };

        for ($i = 0; $i < $len; $i++) {
            $ch = $tuple[$i];
            if ($inStr) {
                if ($ch === '\\' && $i + 1 < $len) {
                    $cur .= $ch.$tuple[$i + 1];
                    $i++;
                } elseif ($ch === "'") {
                    if (($tuple[$i + 1] ?? null) === "'") {
                        $cur .= "''";
                        $i++;
                    } else {
                        $inStr = false;
                    }
                } else {
                    $cur .= $ch;
                }

                continue;
            }
            if ($ch === "'") {
                $inStr = true;
                $quoted = true;
            } elseif ($ch === ',') {
                $flush();
            } else {
                $cur .= $ch;
            }
        }
        $flush();

        return $out;
    }
}
