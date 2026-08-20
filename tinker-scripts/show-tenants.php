<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$tenants = \Illuminate\Support\Facades\DB::table('tenants')->get();
foreach ($tenants as $t) {
    echo $t->id . ' | ' . ($t->tenancy_db_name ?? '-') . ' | ' . ($t->id) . PHP_EOL;
}
