<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$central = DB::connection('central');

// 1) cek kolom data
$cols = $central->select("SHOW COLUMNS FROM tenants LIKE 'data'");
if (empty($cols)) {
    $central->statement("ALTER TABLE tenants ADD COLUMN data JSON NULL AFTER email");
    echo "[+] Kolom `data` ditambahkan.\n";
} else {
    echo "[=] Kolom `data` sudah ada.\n";
}

// 2) set tenancy_db_name untuk tenant existing
$prefix = config('tenancy.database.prefix', 'sinkrone_sabit_');
$suffix = config('tenancy.database.suffix', '');
$tenants = $central->table('tenants')->get();
foreach ($tenants as $t) {
    $dbName = $prefix . $t->id . $suffix;
    $data = $t->data ? json_decode($t->data, true) : [];
    if (!$data) $data = [];
    if (($data['tenancy_db_name'] ?? null) !== $dbName) {
        $data['tenancy_db_name'] = $dbName;
        $central->table('tenants')->where('id', $t->id)->update(['data' => json_encode($data)]);
        echo "[+] {$t->id} -> {$dbName}\n";
    } else {
        echo "[=] {$t->id} sudah = {$dbName}\n";
    }
}
