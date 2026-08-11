<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tenant = App\Models\Tenant::find('demo');
echo json_encode($tenant?->toArray() ?: ['error' => 'tenant demo not found']) . PHP_EOL;
echo 'database()->getName()=' . ($tenant?->database()->getName() ?? 'n/a') . PHP_EOL;
