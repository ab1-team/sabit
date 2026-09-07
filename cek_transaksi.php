<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$siswa = App\Models\Siswa::where('nama', 'like', '%FATHIRA%')->first();
if (!$siswa) { echo "siswa not found\n"; exit; }
echo "siswa_id={$siswa->id}\n";

$rows = App\Models\Transaksi::where('siswa_id', $siswa->id)
    ->whereBetween('tanggal_transaksi', ['2026-07-01', '2027-06-30'])
    ->orderBy('id')
    ->get(['id','tanggal_transaksi','jumlah','kode_spp']);

foreach ($rows as $r) {
    $ket = '-';
    if ($r->kode_spp) {
        $s = App\Models\Spp::where('kode', $r->kode_spp)->first();
        if ($s && $s->tanggal) $ket = $s->tanggal->format('Y-m');
    } else {
        $ket = '(no-kode-spp)';
    }
    echo "{$r->id} | {$r->tanggal_transaksi->format('Y-m-d')} | {$r->kode_spp} | {$ket}\n";
}
