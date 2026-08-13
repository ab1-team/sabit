<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class BenchmarkPembayaranSpp extends Command
{
    protected $signature = 'audit:bench-spp {--db=sabit_demo} {--rows=50} {--iter=3}';
    protected $description = 'Benchmark pembayaran_spp — N+3 SPP query per siswa vs aggregate by anggota_kelas';

    public function handle(): int
    {
        $db = $this->option('db');
        $rows = (int) $this->option('rows');
        $iter = (int) $this->option('iter');

        Config::set('database.connections.bench', [
            'driver' => 'mysql',
            'host' => config('database.connections.central.host'),
            'port' => config('database.connections.central.port'),
            'database' => $db,
            'username' => config('database.connections.central.username'),
            'password' => config('database.connections.central.password'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'strict' => true,
        ]);
        DB::purge('bench');
        $c = DB::connection('bench');

        // Setup: pastikan ada data siswa & anggota_kelas
        $siswaCount = $c->table('siswa')->count();
        if ($siswaCount === 0) {
            $this->info("Inserting $rows siswa dummy + anggota_kelas + spp...");
            $this->seedDummy($c, $rows);
        }

        $this->info("Total siswa di DB: " . $c->table('siswa')->count());
        $this->info("Total anggota_kelas: " . $c->table('anggota_kelas')->count());
        $this->info("Total spp: " . $c->table('spp')->count());

        $tglAwal = '2026-01-01';
        $tglAkhir = '2026-12-31';

        // Get sample of anggota_kelas id
        $akIds = $c->table('anggota_kelas')->limit(50)->pluck('id')->all();

        $this->info('');
        $this->info('=== OLD: N+3 per siswa ===');

        $fnOld = function () use ($c, $akIds, $tglAwal, $tglAkhir) {
            foreach ($akIds as $akId) {
                $sppRows = $c->table('spp')
                    ->where('anggota_kelas', $akId)
                    ->whereBetween('tanggal', [$tglAwal, $tglAkhir])
                    ->orderBy('tanggal')
                    ->get();
                $c->table('spp')->where('anggota_kelas', $akId)->where('status', 'B')->whereBetween('tanggal', [$tglAwal, $tglAkhir])->sum('nominal');
                $c->table('spp')->where('anggota_kelas', $akId)->where('status', 'L')->whereBetween('tanggal', [$tglAwal, $tglAkhir])->sum('nominal');
            }
        };

        $times = [];
        for ($i = 0; $i < $iter; $i++) {
            $start = microtime(true);
            $fnOld();
            $times[] = (microtime(true) - $start) * 1000;
        }
        $this->line(sprintf("  OLD (per-siswa N+3): avg=%.2fms min=%.2fms max=%.2fms", array_sum($times)/count($times), min($times), max($times)));

        $this->info('');
        $this->info('=== NEW: aggregate by anggota_kelas ===');

        $fnNew = function () use ($c, $akIds, $tglAwal, $tglAkhir) {
            $sppAll = $c->table('spp')
                ->whereIn('anggota_kelas', $akIds)
                ->whereBetween('tanggal', [$tglAwal, $tglAkhir])
                ->get(['anggota_kelas', 'tanggal', 'nominal', 'status']);

            $sppAgg = $c->table('spp')
                ->whereIn('anggota_kelas', $akIds)
                ->whereBetween('tanggal', [$tglAwal, $tglAkhir])
                ->groupBy('anggota_kelas', 'status')
                ->selectRaw('anggota_kelas, status, SUM(nominal) as total')
                ->get();
        };

        $times = [];
        for ($i = 0; $i < $iter; $i++) {
            $start = microtime(true);
            $fnNew();
            $times[] = (microtime(true) - $start) * 1000;
        }
        $this->line(sprintf("  NEW (aggregate 2 query): avg=%.2fms min=%.2fms max=%.2fms", array_sum($times)/count($times), min($times), max($times)));

        return 0;
    }

    private function seedDummy($c, int $rows): void
    {
        $requiredDefaults = [
            'nik' => '0',
            'nama' => 'Bench',
            'jenis_kelamin' => 'L',
            'nisn' => '0',
            'nipd' => '0',
            'no_kk' => '0',
            'kode_kelas' => 'X-1',
            'status_siswa' => 'aktif',
            'tahun_akademik' => '2025/2026',
            'tanggal_lahir' => '2010-01-01',
            'tempat_lahir' => 'Bench',
            'agama' => 'Islam',
            'alamat' => '-',
            'rt' => '0',
            'rw' => '0',
            'dusun' => '-',
            'kelurahan' => '-',
            'kecamatan' => '-',
            'kode_pos' => '0',
            'jenis_tinggal' => 'orang_tua',
            'alat_transportasi' => '-',
            'hp' => '-',
            'email' => '-',
            'status_awal' => 'baru',
            'kode_jurusan' => '-',
            'ruang' => '-',
            'tingkat' => '10',
            'nama_ayah' => '-',
            'nama_ibu' => '-',
            'skhun' => '-',
            'penerima_kps' => '-',
            'no_kps' => '-',
            'foto' => 'default.png',
            'tgl_masuk' => '2025-07-01',
        ];

        $siswaBatch = [];
        for ($i = 1; $i <= $rows; $i++) {
            $row = $requiredDefaults;
            $row['nama'] = 'Siswa Bench ' . $i;
            $row['nisn'] = str_pad((string) $i, 10, '0', STR_PAD_LEFT);
            $row['nik'] = str_pad((string) $i, 16, '0', STR_PAD_LEFT);
            $row['nipd'] = str_pad((string) $i, 8, '0', STR_PAD_LEFT);
            $row['no_kk'] = str_pad((string) $i, 16, '0', STR_PAD_LEFT);
            $row['created_at'] = now();
            $row['updated_at'] = now();
            $siswaBatch[] = $row;
        }
        foreach (array_chunk($siswaBatch, 100) as $chunk) {
            $c->table('siswa')->insert($chunk);
        }
        $siswaIds = $c->table('siswa')->where('nama', 'like', 'Siswa Bench%')->pluck('id')->all();

        $akBatch = [];
        foreach ($siswaIds as $sid) {
            $akBatch[] = [
                'id_siswa' => $sid,
                'tahun_akademik' => '2025/2026',
                'kode_kelas' => 'X-1',
                'tingkat' => '10',
                'status' => 'aktif',
                'tgl_masuk' => '2025-07-01',
                'tgl_keluar' => '2026-06-30',
                'spp_nominal' => '250000',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        foreach (array_chunk($akBatch, 100) as $chunk) {
            $c->table('anggota_kelas')->insert($chunk);
        }
        $akIds = $c->table('anggota_kelas')->where('kode_kelas', 'X-1')->pluck('id')->all();

        $sppBatch = [];
        foreach ($akIds as $akId) {
            for ($m = 7; $m <= 12; $m++) {
                $sppBatch[] = [
                    'kode' => 'BENCH' . str_pad((string) $akId, 6, '0', STR_PAD_LEFT) . $m,
                    'anggota_kelas' => $akId,
                    'tanggal' => sprintf('2025-%02d-01', $m),
                    'nominal' => '250000',
                    'status' => $m % 3 == 0 ? 'L' : 'B',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            for ($m = 1; $m <= 6; $m++) {
                $sppBatch[] = [
                    'kode' => 'BENCH' . str_pad((string) $akId, 6, '0', STR_PAD_LEFT) . sprintf('%02d', $m + 12),
                    'anggota_kelas' => $akId,
                    'tanggal' => sprintf('2026-%02d-01', $m),
                    'nominal' => '250000',
                    'status' => $m % 2 == 0 ? 'L' : 'B',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        foreach (array_chunk($sppBatch, 1000) as $chunk) {
            $c->table('spp')->insert($chunk);
        }
    }
}
