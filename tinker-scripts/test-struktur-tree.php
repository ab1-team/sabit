<?php
use App\Models\Landing\StrukturOrganisasiLanding;
use Illuminate\Support\Facades\Schema;

$count = StrukturOrganisasiLanding::count();
echo "Existing struktur: {$count}\n";
echo "parent_id column exists: " . (Schema::hasColumn('lp_struktur_organisasi', 'parent_id') ? 'YES' : 'NO') . "\n";

// Test the tree JSON logic
$items = StrukturOrganisasiLanding::orderBy('sort_order')->orderBy('id')->get();
$byParent = [];
foreach ($items as $it) {
    $pid = $it->parent_id ?: 0;
    $byParent[$pid][] = $it;
}
echo "Total items loaded: " . $items->count() . "\n";
echo "Root-level items: " . count($byParent[0] ?? []) . "\n";
echo "Children of first root: " . (isset($byParent[0][0]) ? count($byParent[$byParent[0][0]->id] ?? []) : 'n/a') . "\n";
