<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Tenancy;

$tenancy = app(Tenancy::class);
$t = \App\Models\Tenant::find('sabit-demo');
if (!$t) { echo "no tenant sabit-demo\n"; exit; }
$tenancy->initialize($t);
$rows = DB::table('users')->select('id','nama','username','hak_akses')->limit(20)->get();
foreach ($rows as $r) {
    echo $r->id . ' | ' . $r->nama . ' | ' . $r->username . ' | hak_akses=' . json_encode($r->hak_akses) . "\n";
}
$tenancy->end();
