<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

tenancy()->initialize('demo');

$rows = \Illuminate\Support\Facades\DB::table('menu')
    ->where(function ($q) {
        $q->where('route', 'like', '%contact%')
          ->orWhere('route', 'like', '%admin-landing%')
          ->orWhere('nama_menu', 'like', '%Kontak%')
          ->orWhere('nama_menu', 'like', '%Pesan%')
          ->orWhere('nama_menu', 'like', '%Landing%');
    })
    ->orderBy('parent_id')
    ->orderBy('urutan')
    ->get(['id', 'nama_menu', 'route', 'parent_id', 'urutan', 'group', 'icon']);

foreach ($rows as $r) {
    echo $r->id . ' | ' . ($r->parent_id ?? 'NULL') . ' | ' . $r->nama_menu . ' | ' . $r->route . ' | ' . $r->group . ' | ' . $r->icon . PHP_EOL;
}
