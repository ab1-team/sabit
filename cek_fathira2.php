<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

DB::statement("USE `sabit_demo`");

echo "=== Transaksi Fathira di TA 2026/2027 (Jul 2026 - Jun 2027), urut by id ===\n";
$rows = DB::table('transaksi')
    ->where('siswa_id', 959)
    ->whereBetween('tanggal_transaksi', ['2026-07-01', '2027-06-30'])
    ->orderBy('id')
    ->get(['id','tanggal_transaksi','kode_spp','jumlah']);

foreach ($rows as $r) {
    $spp = '-';
    if ($r->kode_spp) {
        $s = DB::table('spp')->where('kode', $r->kode_spp)->first();
        if ($s) $spp = $s->tanggal ?? 'no-date';
    } else {
        $spp = '(null)';
    }
    echo $r->id.' | '.$r->tanggal_transaksi.' | kode='.$r->kode_spp.' | spp='.$spp."\n";
}
