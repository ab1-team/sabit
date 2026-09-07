<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Cari tenant DB untuk SDIT Al-Maruf Tegalrejo
$central = DB::connection('central');
$tenants = $central->table('tenants')->get();
foreach ($tenants as $t) {
    $data = json_decode($t->data ?? '{}', true);
    $nama = $data['nama_sekolah'] ?? $t->nama_sekolah ?? $t->id;
    echo $t->id." | ".($t->tenancy_db_name ?? '-')." | ".$nama."\n";
}
