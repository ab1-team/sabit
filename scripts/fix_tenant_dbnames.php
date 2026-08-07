<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$central = DB::connection('central');

// Override nama DB tenant sabit-demo -> sinkrone_sabit_demo (yang sudah ada)
$tenants = $central->table('tenants')->get();
foreach ($tenants as $t) {
    $data = $t->data ? json_decode($t->data, true) : [];
    if (!$data) $data = [];

    if ($t->id === 'sabit-demo') {
        $data['tenancy_db_name'] = 'sinkrone_sabit_demo';
    } elseif ($t->id === 'crud-test-1') {
        // tenant test yg DB-nya tidak ada -> tetap pakai default, tapi nanti controller akan skip
    }
    $central->table('tenants')->where('id', $t->id)->update(['data' => json_encode($data)]);
    echo "[ok] {$t->id} -> " . $data['tenancy_db_name'] . "\n";
}
