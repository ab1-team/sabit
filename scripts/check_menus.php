<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Tenancy;

$tenancy = app(Tenancy::class);
$t = \App\Models\Tenant::find('demo');
$tenancy->initialize($t);

$menus = DB::table('menu')->where('status', 'aktif')->orderBy('group')->orderBy('urutan')->get();
echo "menus=" . $menus->count() . "\n";
foreach ($menus as $m) {
    echo " - id={$m->id} parent={$m->parent_id} group={$m->group} nama={$m->nama_menu}\n";
}
$tenancy->end();
