<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$row = DB::table('tenants')->where('id', 'sabit-demo')->first();
echo json_encode($row) . PHP_EOL;
