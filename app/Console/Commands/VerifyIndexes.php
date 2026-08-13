<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class VerifyIndexes extends Command
{
    protected $signature = 'audit:verify-indexes {--db=sabit_demo}';
    protected $description = 'Verifikasi index sudah terpasang di DB tertentu';

    public function handle(): int
    {
        $db = $this->option('db');
        Config::set('database.connections.audit_v', [
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
        DB::purge('audit_v');
        $c = DB::connection('audit_v');

        $tables = ['transaksi','spp','anggota_kelas','siswa','menu','users','tahun_akademik','rekening','saldo','jenis_biaya','jurusan','kelas','ruangan','inventaris','master_arus_kas'];

        foreach ($tables as $t) {
            try {
                $rows = $c->select("SHOW INDEX FROM `{$t}`");
                $idxNames = collect($rows)->pluck('Key_name')->unique()->reject(fn($x) => $x === 'PRIMARY')->values()->all();
                $this->line(str_pad($t, 22)." indexes: " . count($idxNames) . " = " . implode(', ', $idxNames));
            } catch (\Throwable $e) {
                $this->line(str_pad($t, 22)." = [N/A]");
            }
        }

        return 0;
    }
}
