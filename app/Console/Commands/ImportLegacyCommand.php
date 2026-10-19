<?php

namespace App\Console\Commands;

use App\Support\LegacyImporter;
use Illuminate\Console\Command;

class ImportLegacyCommand extends Command
{
    protected $signature = 'legacy:import
        {--sql=database/legacy/e_complain.sql : Path dump SQL, relatif ke root aplikasi}
        {--img= : Direktori gambar legacy (default: <root>/../legacy_native_backup/img)}
        {--dry-run : Parse dan laporkan tanpa menulis ke database}';

    protected $description = 'Impor dump SQL aplikasi native ke skema Laravel (idempoten, aman diulang)';

    public function handle(): int
    {
        $sql = $this->option('sql');
        $sqlPath = $this->resolvePath($sql);
        $imgDir = $this->option('img')
            ? $this->resolvePath($this->option('img'))
            : dirname(base_path()).DIRECTORY_SEPARATOR.'legacy_native_backup'.DIRECTORY_SEPARATOR.'img';

        if (! is_file($sqlPath)) {
            $this->error("File SQL tidak ditemukan: {$sqlPath}");

            return self::FAILURE;
        }

        $parsed = LegacyImporter::parseDump($sqlPath);
        $this->info('Baris di dump:');
        foreach ($parsed as $table => $rows) {
            $this->line("  {$table}: ".count($rows));
        }

        if ($this->option('dry-run')) {
            $this->info('Dry-run: tidak ada yang ditulis ke database.');

            return self::SUCCESS;
        }

        $result = (new LegacyImporter(is_dir($imgDir) ? $imgDir : null))->run($sqlPath);

        $this->info('Hasil impor:');
        foreach ($result['imported'] as $key => $n) {
            $this->line("  {$key}: {$n}");
        }
        $this->line('  files_copied: '.$result['files_copied']);
        $this->line('  files_missing: '.$result['files_missing']);

        foreach (array_slice($result['warnings'], 0, 20) as $w) {
            $this->warn($w);
        }
        if (count($result['warnings']) > 20) {
            $this->warn('... dan '.(count($result['warnings']) - 20).' peringatan lain.');
        }

        $this->info('Selesai. Password legacy (plaintext) sudah di-hash via cast User.');

        return self::SUCCESS;
    }

    private function resolvePath(string $path): string
    {
        if (preg_match('#^[A-Za-z]:[\\\\/]#', $path) || str_starts_with($path, '/')) {
            return $path;
        }

        return base_path($path);
    }
}
