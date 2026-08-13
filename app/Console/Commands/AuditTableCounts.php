<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AuditTableCounts extends Command
{
    protected $signature = 'audit:counts {--db=}';
    protected $description = 'Cek row count tabel utama untuk audit (langsung ke MySQL)';

    public function handle(): int
    {
        $db = $this->option('db') ?: config('database.connections.central.database');
        $tables = ['transaksi','spp','anggota_kelas','siswa','rekening','users','menu','tahun_akademik','jenis_biaya','jenis_pembayaran','akun_level1','akun_level2','akun_level3','saldo','profil','kelas','jurusan','ruangan','master_arus_kas','tanda_tangan','inventaris','anggota_kelas_arsip','transaksi_arsip'];

        $this->info("DB: $db");

        // Set koneksi dinamis
        config([
            'database.connections.audit_tmp' => [
                'driver' => 'mysql',
                'host' => config('database.connections.central.host'),
                'port' => config('database.connections.central.port'),
                'database' => $db,
                'username' => config('database.connections.central.username'),
                'password' => config('database.connections.central.password'),
                'unix_socket' => config('database.connections.central.unix_socket'),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
            ]
        ]);
        DB::purge('audit_tmp');
        $c = DB::connection('audit_tmp');

        foreach ($tables as $t) {
            try {
                $cnt = $c->table($t)->count();
                $this->line(str_pad($t, 22)." = $cnt");
            } catch (\Throwable $e) {
                $msg = $e->getMessage();
                $short = strlen($msg) > 60 ? substr($msg, 0, 60).'...' : $msg;
                $this->line(str_pad($t, 22)." = [N/A]");
            }
        }
        return 0;
    }
}
