<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$views = [
    'tenant.dashboard',
    'tenant.login',
    'tenant.invoice.index',
    'tenant.hak-akses.index',
    'tenant.transaksi.index',
    'tenant.migrasi-siswa',
    'tenant.tenant.index',
    'tenant.tenant.profil',
    'dashboard.index',
    'auth.login',
    'transaksi.index',
    'siswa.index',
    'pengaturan.invoice',
    'landing-admin.index',
    'landing.index',
    'landing-admin.pengaturan',
    'landing-admin.hero',
];

foreach ($views as $v) {
    try {
        $exists = view()->exists($v);
        echo ($exists ? 'OK  ' : 'MISS') . "  $v" . PHP_EOL;
    } catch (\Throwable $e) {
        echo 'ERR  ' . $v . '  ' . $e->getMessage() . PHP_EOL;
    }
}
