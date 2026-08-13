<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SeedBenchData extends Command
{
    protected $signature = 'audit:seed-bench {--db=sabit_demo} {--rows=10000}';
    protected $description = 'Seed data dummy untuk benchmark pada DB tertentu (HATI-HATI: akan insert banyak data)';

    public function handle(): int
    {
        $db = $this->option('db');
        $rows = (int) $this->option('rows');

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

        $exists = $c->table('transaksi')->count();
        if ($exists > 0) {
            $this->info("DB sudah punya $exists transaksi, skip seeding.");
            return 0;
        }

        $this->info("Seeding $rows transaksi dummy...");

        $rekeningList = $c->table('rekening')->pluck('kode_akun')->all();
        if (empty($rekeningList)) {
            $this->error("Tabel rekening kosong. Tambah rekening dulu.");
            return 1;
        }
        $kodeAkunDebit = array_values(array_filter($rekeningList, fn($k) => str_starts_with($k, '1.1.01')));
        if (empty($kodeAkunDebit)) $kodeAkunDebit = array_slice($rekeningList, 0, 5);

        $sppKodes = $c->table('spp')->pluck('kode')->all();
        if (empty($sppKodes)) {
            $this->info("Tidak ada spp di DB, membuat spp dummy 100 row...");
            $sppBatch = [];
            for ($i = 1; $i <= 100; $i++) {
                $sppBatch[] = [
                    'kode' => 'BENCH' . str_pad($i, 6, '0', STR_PAD_LEFT),
                    'anggota_kelas' => 1,
                    'tanggal' => '2026-01-01',
                    'nominal' => '100000',
                    'status' => 'B',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            $c->table('spp')->insert($sppBatch);
            $sppKodes = $c->table('spp')->pluck('kode')->all();
        }

        $batch = [];
        $tahun = date('Y');
        for ($i = 1; $i <= $rows; $i++) {
            $bulan = rand(1, 12);
            $tanggal = sprintf('%s-%02d-%02d', $tahun, $bulan, rand(1, 28));
            $batch[] = [
                'user_id' => 1,
                'tanggal_transaksi' => $tanggal,
                'rekening_debit' => $kodeAkunDebit[array_rand($kodeAkunDebit)],
                'rekening_kredit' => $rekeningList[array_rand($rekeningList)],
                'keterangan' => 'Bench #' . $i,
                'jumlah' => rand(1000, 1000000),
                'invoice' => 0,
                'kode_spp' => $sppKodes[array_rand($sppKodes)],
                'siswa_id' => 0,
                'idtp' => '0',
                'urutan' => '0',
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (count($batch) >= 1000) {
                $c->table('transaksi')->insert($batch);
                $batch = [];
                $this->line("  inserted $i");
            }
        }
        if (!empty($batch)) $c->table('transaksi')->insert($batch);

        $this->info("Done. {$rows} transaksi ditambah.");
        return 0;
    }
}
