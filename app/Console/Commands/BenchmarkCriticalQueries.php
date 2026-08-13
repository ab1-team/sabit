<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class BenchmarkCriticalQueries extends Command
{
    protected $signature = 'audit:bench-critical {--db=sabit_demo} {--iter=3}';
    protected $description = 'Benchmark query yang sebelumnya N+1 / full table load';

    public function handle(): int
    {
        $db = $this->option('db');
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

        $this->info('Simulating buku_besar() — saldo awal tahun + kumulatif bulan lalu + bulan ini');
        $kode = '1.1.01';
        $tgl_awal_tahun = '2026-01-01';
        $tgl_akhir_sebelum = '2026-07-31';
        $tgl_awal_bulan = '2026-08-01';
        $tgl_akhir_bulan = '2026-08-31';

        $fnBukuBesar = function () use ($c, $kode, $tgl_awal_tahun, $tgl_akhir_sebelum, $tgl_awal_bulan, $tgl_akhir_bulan) {
            // 1. Saldo awal tahun
            $c->table('transaksi')
                ->whereNull('deleted_at')
                ->where('tanggal_transaksi', '<', $tgl_awal_tahun)
                ->where(function ($q) use ($kode) {
                    $q->where('rekening_debit', $kode)->orWhere('rekening_kredit', $kode);
                })
                ->selectRaw('
                    COALESCE(SUM(CASE WHEN rekening_debit = ? THEN jumlah ELSE 0 END),0) -
                    COALESCE(SUM(CASE WHEN rekening_kredit = ? THEN jumlah ELSE 0 END),0) AS net
                ', [$kode, $kode])
                ->value('net');

            // 2. Kumulatif s/d bulan lalu
            $c->table('transaksi')
                ->whereNull('deleted_at')
                ->whereBetween('tanggal_transaksi', [$tgl_awal_tahun, $tgl_akhir_sebelum])
                ->where(function ($q) use ($kode) {
                    $q->where('rekening_debit', $kode)->orWhere('rekening_kredit', $kode);
                })
                ->selectRaw('
                    COALESCE(SUM(CASE WHEN rekening_debit = ? THEN jumlah ELSE 0 END),0) AS debit,
                    COALESCE(SUM(CASE WHEN rekening_kredit = ? THEN jumlah ELSE 0 END),0) AS kredit
                ', [$kode, $kode])
                ->first();

            // 3. Transaksi bulan ini + totals
            $c->table('transaksi')
                ->whereNull('deleted_at')
                ->where(function ($q) use ($kode) {
                    $q->where('rekening_debit', $kode)->orWhere('rekening_kredit', $kode);
                })
                ->whereBetween('tanggal_transaksi', [$tgl_awal_bulan, $tgl_akhir_bulan])
                ->orderBy('tanggal_transaksi')
                ->select(['id', 'tanggal_transaksi', 'keterangan', 'rekening_debit', 'rekening_kredit', 'jumlah', 'user_id'])
                ->get();
        };

        $this->bench("buku_besar (3 query aggregate + list)", $fnBukuBesar, $iter);

        $this->info("");
        $this->info('Simulating neraca_saldo() — aggregate per rekening by rek_debit + rek_kredit');
        $fnNeraca = function () use ($c, $tgl_awal_bulan, $tgl_akhir_bulan) {
            $kodeList = $c->table('rekening')->whereNull('tgl_nonaktif')->pluck('kode_akun')->all();
            $rekenings = $c->table('rekening')->whereNull('tgl_nonaktif')->orderBy('kode_akun')->get(['kode_akun', 'nama_akun']);
            $debits = $c->table('transaksi')
                ->whereNull('deleted_at')
                ->whereBetween('tanggal_transaksi', [$tgl_awal_bulan, $tgl_akhir_bulan])
                ->whereIn('rekening_debit', $kodeList)
                ->groupBy('rekening_debit')
                ->selectRaw('rekening_debit as kode_akun, SUM(jumlah) as total')
                ->pluck('total', 'kode_akun');
            $kredits = $c->table('transaksi')
                ->whereNull('deleted_at')
                ->whereBetween('tanggal_transaksi', [$tgl_awal_bulan, $tgl_akhir_bulan])
                ->whereIn('rekening_kredit', $kodeList)
                ->groupBy('rekening_kredit')
                ->selectRaw('rekening_kredit as kode_akun, SUM(jumlah) as total')
                ->pluck('total', 'kode_akun');
            return $rekenings->map(function ($r) use ($debits, $kredits) {
                $r->total_debit  = (float) ($debits[$r->kode_akun] ?? 0);
                $r->total_kredit = (float) ($kredits[$r->kode_akun] ?? 0);
                return $r;
            });
        };
        $this->bench("neraca_saldo (2 aggregate + lookup)", $fnNeraca, $iter);

        $this->info("");
        $this->info('Simulating OLD N+1 neraca_saldo (load all + sum in PHP)');
        $fnNeracaOld = function () use ($c, $tgl_awal_bulan, $tgl_akhir_bulan) {
            $all = $c->table('rekening')
                ->whereNull('tgl_nonaktif')
                ->orderBy('kode_akun')
                ->get();

            $result = [];
            foreach ($all as $r) {
                $d = (float) $c->table('transaksi')
                    ->whereNull('deleted_at')
                    ->whereBetween('tanggal_transaksi', [$tgl_awal_bulan, $tgl_akhir_bulan])
                    ->where('rekening_debit', $r->kode_akun)
                    ->sum('jumlah');
                $k = (float) $c->table('transaksi')
                    ->whereNull('deleted_at')
                    ->whereBetween('tanggal_transaksi', [$tgl_awal_bulan, $tgl_akhir_bulan])
                    ->where('rekening_kredit', $r->kode_akun)
                    ->sum('jumlah');
                $result[] = (object) ['kode_akun' => $r->kode_akun, 'nama_akun' => $r->nama_akun, 'total_debit' => $d, 'total_kredit' => $k];
            }
            return $result;
        };
        $this->bench("neraca_saldo OLD (N+1: 2*146 query)", $fnNeracaOld, $iter);

        $this->info("");
        $this->info('Simulating OLD simpanSaldo (1 update per rekening, N=146)');
        $fnSimpanOld = function () use ($c, $tgl_awal_bulan, $tgl_akhir_bulan) {
            $rekenings = $c->table('rekening')->whereNull('tgl_nonaktif')->orderBy('kode_akun')->get();
            foreach ($rekenings as $rek) {
                $d = (float) $c->table('transaksi')
                    ->whereNull('deleted_at')
                    ->whereBetween('tanggal_transaksi', [$tgl_awal_bulan, $tgl_akhir_bulan])
                    ->where('rekening_debit', $rek->kode_akun)
                    ->sum('jumlah');
                $k = (float) $c->table('transaksi')
                    ->whereNull('deleted_at')
                    ->whereBetween('tanggal_transaksi', [$tgl_awal_bulan, $tgl_akhir_bulan])
                    ->where('rekening_kredit', $rek->kode_akun)
                    ->sum('jumlah');
            }
        };
        $this->bench("simpanSaldo OLD (2*146 query)", $fnSimpanOld, $iter);

        $this->info("");
        $this->info('Simulating NEW simpanSaldo (bulk aggregate + upsert)');
        $fnSimpanNew = function () use ($c, $tgl_awal_bulan, $tgl_akhir_bulan) {
            $kodeList = $c->table('rekening')->whereNull('tgl_nonaktif')->pluck('kode_akun')->all();
            $debits = $c->table('transaksi')
                ->whereNull('deleted_at')
                ->whereBetween('tanggal_transaksi', [$tgl_awal_bulan, $tgl_akhir_bulan])
                ->whereIn('rekening_debit', $kodeList)
                ->groupBy('rekening_debit')
                ->selectRaw('rekening_debit as kode_akun, SUM(jumlah) as total')
                ->pluck('total', 'kode_akun');

            $kredits = $c->table('transaksi')
                ->whereNull('deleted_at')
                ->whereBetween('tanggal_transaksi', [$tgl_awal_bulan, $tgl_akhir_bulan])
                ->whereIn('rekening_kredit', $kodeList)
                ->groupBy('rekening_kredit')
                ->selectRaw('rekening_kredit as kode_akun, SUM(jumlah) as total')
                ->pluck('total', 'kode_akun');
        };
        $this->bench("simpanSaldo NEW (2 query aggregate)", $fnSimpanNew, $iter);

        return 0;
    }

    private function bench(string $label, \Closure $fn, int $iter): void
    {
        $times = [];
        for ($i = 0; $i < $iter; $i++) {
            $start = microtime(true);
            $fn();
            $times[] = (microtime(true) - $start) * 1000;
        }
        $avg = array_sum($times) / count($times);
        $min = min($times);
        $max = max($times);
        $this->line(sprintf("  %s: avg=%.2fms min=%.2fms max=%.2fms (n=%d)", $label, $avg, $min, $max, $iter));
    }
}
