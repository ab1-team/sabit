<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach (\App\Models\Tenant::all() as $t) {
    $data = $t->data;
    if (is_string($data)) { $data = json_decode($data, true); }
    $fromJson = is_array($data) ? ($data['tenancy_db_name'] ?? null) : null;
    echo $t->id
        . ' | col_data=' . ($t->getAttributes()['data'] ?? 'null')
        . ' | json_tenancy_db=' . ($fromJson ?? 'null')
        . ' | tenant.tenancy_db_name=' . ($t->tenancy_db_name ?? 'null')
        . PHP_EOL;
}
