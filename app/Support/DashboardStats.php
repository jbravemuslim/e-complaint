<?php

namespace App\Support;

use App\Models\Complain;
use App\Models\Jenis;
use App\Models\Response;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

/**
 * Agregator data untuk dashboard (admin, superadmin, pelanggan).
 * Semua query dipusatkan di sini agar view tidak menyentuh DB langsung.
 */
class DashboardStats
{
    /** Singkatan bulan Indonesia untuk label grafik. */
    private const BULAN = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];

    /** Rentang tren grafik, dalam hari. */
    private const TREND_HARI = 14;

    /**
     * Ringkasan untuk admin/superadmin.
     *
     * @return array{statusCounts: array<string, int>, mingguIni: int, selesaiMinggu: int, persenMinggu: int, persenTotal: int, ditanggapiSaya: int, trend: array{labels: list<string>, data: list<int>}, perJenis: array{labels: list<string>, proses: list<int>, selesai: list<int>}, topUsers: list<array{name: string, total: int, terakhir: string}>, recent: list<Complain>}
     */
    public static function staff(int $adminId): array
    {
        $statusCounts = Complain::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');
        $proses = (int) ($statusCounts['proses'] ?? 0);
        $selesai = (int) ($statusCounts['selesai'] ?? 0);
        $total = $proses + $selesai;

        $awalMinggu = CarbonImmutable::now()->startOfWeek()->toDateString();
        $mingguIni = (int) Complain::whereDate('tgl_keluhan', '>=', $awalMinggu)->count();
        $selesaiMinggu = (int) Complain::whereDate('tgl_keluhan', '>=', $awalMinggu)
            ->where('status', 'selesai')->count();

        return [
            'statusCounts' => ['proses' => $proses, 'selesai' => $selesai],
            'mingguIni' => $mingguIni,
            'selesaiMinggu' => $selesaiMinggu,
            'persenMinggu' => $mingguIni > 0 ? (int) round($selesaiMinggu / $mingguIni * 100) : 0,
            'persenTotal' => $total > 0 ? (int) round($selesai / $total * 100) : 0,
            'ditanggapiSaya' => (int) Response::where('admin_id', $adminId)->count(),
            'trend' => self::trend(),
            'perJenis' => self::perJenis(),
            'topUsers' => self::topUsers(),
            'recent' => self::recent(),
        ];
    }

    /**
     * Ringkasan personal untuk pelanggan.
     *
     * @return array{proses: int, selesai: int, total: int, trend: array{labels: list<string>, data: list<int>}, recent: list<Complain>}
     */
    public static function personal(int $userId): array
    {
        $statusCounts = Complain::where('user_id', $userId)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');
        $proses = (int) ($statusCounts['proses'] ?? 0);
        $selesai = (int) ($statusCounts['selesai'] ?? 0);

        return [
            'proses' => $proses,
            'selesai' => $selesai,
            'total' => $proses + $selesai,
            'trend' => self::trend($userId),
            'recent' => self::recent($userId, 5),
        ];
    }

    /**
     * Jumlah keluhan per hari pada rentang TREND_HARI terakhir (hari kosong diisi 0).
     *
     * @return array{labels: list<string>, data: list<int>}
     */
    private static function trend(?int $userId = null): array
    {
        $hari = CarbonImmutable::today();
        $awal = $hari->subDays(self::TREND_HARI - 1)->toDateString();

        $query = Complain::whereDate('tgl_keluhan', '>=', $awal)
            ->select('tgl_keluhan', DB::raw('count(*) as total'))
            ->groupBy('tgl_keluhan');
        if ($userId !== null) {
            $query->where('user_id', $userId);
        }
        $perTanggal = $query->pluck('total', 'tgl_keluhan');

        $labels = [];
        $data = [];
        for ($i = self::TREND_HARI - 1; $i >= 0; $i--) {
            $t = CarbonImmutable::today()->subDays($i);
            $labels[] = $t->day.' '.self::BULAN[$t->month];
            $data[] = (int) ($perTanggal[$t->toDateString()] ?? 0);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Keluhan per jenis keluhan, dipecah per status (dataset untuk bar bertumpuk).
     *
     * @return array{labels: list<string>, proses: list<int>, selesai: list<int>}
     */
    private static function perJenis(): array
    {
        $baris = Complain::query()
            ->select('jenis_id', 'status', DB::raw('count(*) as total'))
            ->groupBy('jenis_id', 'status')
            ->get();
        $nama = Jenis::orderBy('nama_jenis')->pluck('nama_jenis', 'id');

        $hasil = [];
        foreach ($nama as $id => $label) {
            $hasil[$id] = ['label' => $label, 'proses' => 0, 'selesai' => 0];
        }
        foreach ($baris as $b) {
            if (isset($hasil[$b->jenis_id])) {
                $hasil[$b->jenis_id][$b->status] = (int) $b->total;
            }
        }
        usort($hasil, fn (array $a, array $b): int => ($b['proses'] + $b['selesai']) <=> ($a['proses'] + $a['selesai']));

        return [
            'labels' => array_column($hasil, 'label'),
            'proses' => array_column($hasil, 'proses'),
            'selesai' => array_column($hasil, 'selesai'),
        ];
    }

    /**
     * Pelanggan teraktif berdasarkan jumlah keluhan.
     *
     * @return list<array{name: string, total: int, terakhir: string}>
     */
    private static function topUsers(int $limit = 5): array
    {
        return Complain::query()
            ->join('users', 'users.id', '=', 'complains.user_id')
            ->where('users.role', 'user')
            ->select('users.name', DB::raw('count(*) as total'), DB::raw('max(complains.tgl_keluhan) as terakhir'))
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->map(fn (object $r): array => ['name' => $r->name, 'total' => (int) $r->total, 'terakhir' => (string) $r->terakhir])
            ->all();
    }

    /**
     * Keluhan terbaru untuk tabel dashboard.
     *
     * @return list<Complain>
     */
    private static function recent(?int $userId = null, int $limit = 8): array
    {
        $query = Complain::query()->with(['user', 'jenis', 'tujuan', 'response.admin']);
        if ($userId !== null) {
            $query->where('user_id', $userId);
        }

        return $query->orderByDesc('id')->limit($limit)->get()->all();
    }
}
