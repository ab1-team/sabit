<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

// Simulasikan logika runInTenant baru
function runInTenantTest($tenantId) {
    $base = Config::get('database.connections.tenant_template');
    $prefix = Config::get('tenancy.database.prefix', 'sinkrone_sabit_');
    $suffix = Config::get('tenancy.database.suffix', '');
    $data = json_decode(DB::connection('central')->table('tenants')->where('id', $tenantId)->value('data') ?? '[]', true) ?: [];
    $dbName = $data['tenancy_db_name'] ?? ($prefix . $tenantId . $suffix);

    $connName = 'tenant_pusat_' . $tenantId;
    Config::set("database.connections.{$connName}", array_merge($base, ['database' => $dbName]));
    DB::purge($connName);
    Config::set('database.default', $connName);

    $menus = DB::table('menu')->where('status', 'aktif')->count();
    $users = DB::table('users')->count();

    Config::set('database.default', 'central');
    DB::purge($connName);
    return [$dbName, $menus, $users];
}

foreach (['crud-test-1', 'sabit-demo'] as $id) {
    try {
        [$db, $m, $u] = runInTenantTest($id);
        echo "{$id} -> DB={$db} menus={$m} users={$u}\n";
    } catch (\Throwable $e) {
        echo "{$id} ERROR: " . $e->getMessage() . "\n";
    }
}
