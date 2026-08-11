<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$host = env('DB_HOST_CENTRAL', env('DB_HOST'));
$port = env('DB_PORT_CENTRAL', env('DB_PORT', '3306'));
$database = env('DB_DATABASE_CENTRAL', env('DB_DATABASE'));
$username = env('DB_USERNAME_CENTRAL', env('DB_USERNAME'));
$password = env('DB_PASSWORD_CENTRAL', env('DB_PASSWORD'));

if (!$host || !$database || !$username) {
    fwrite(STDERR, "Missing DB credentials in .env (DB_HOST_CENTRAL / DB_DATABASE_CENTRAL / DB_USERNAME_CENTRAL).\n");
    exit(1);
}

$dsn = "mysql:host={$host};port={$port};dbname={$database}";
$pdo = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$rows = $pdo->query("SELECT t.id, t.nama_sekolah, GROUP_CONCAT(d.domain SEPARATOR ',') as domains FROM tenants t LEFT JOIN domains d ON d.tenant_id = t.id GROUP BY t.id")->fetchAll(PDO::FETCH_ASSOC);

foreach ($rows as $r) {
    echo $r['id'] . ' | ' . ($r['nama_sekolah'] ?? '-') . ' | ' . ($r['domains'] ?? 'NO-DOMAIN') . PHP_EOL;
}
