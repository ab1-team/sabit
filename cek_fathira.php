<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$tenants = DB::connection('central')->table('tenants')->get();
foreach ($tenants as $t) {
    $dbname = env('TENANT_DB_PREFIX', 'sabit_') . $t->id;
    try {
        DB::statement("USE `$dbname`");
        $siswaIds = DB::table('siswa')->where('nama', 'like', '%FATHIRA%')->pluck('id')->all();
        if (!$siswaIds) { echo "$dbname: no siswa\n"; continue; }
        $count = DB::table('transaksi')->whereIn('siswa_id', $siswaIds)->count();
        if ($count > 0) echo "FOUND in $dbname: $count rows (siswa_ids: ".implode(',', $siswaIds).")\n";
        if ($count > 0) {
            echo "FOUND in $dbname: $count rows\n";
        } else {
            echo "$dbname: 0\n";
        }
    } catch (\Throwable $e) {
        echo "$dbname: ".$e->getMessage()."\n";
    }
}
