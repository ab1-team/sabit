<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "central db: " . \DB::connection('central')->getDatabaseName() . PHP_EOL;
echo "domains count: " . \App\Models\Domain::count() . PHP_EOL;
foreach (\App\Models\Domain::select('domain','type')->get() as $d) {
    echo "  - {$d->domain} ({$d->type})" . PHP_EOL;
}
