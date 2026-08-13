<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

tenancy()->initialize('demo');

$disk = \Illuminate\Support\Facades\Storage::disk('public');

echo "=== Storage::disk('public') in tenant demo context ===" . PHP_EOL;
echo "Root: " . config('filesystems.disks.public.root') . PHP_EOL;
echo "URL: " . config('filesystems.disks.public.url') . PHP_EOL;
echo "url('landing/hero-bg-2OYQfJmhSm.jpg'): " . $disk->url('landing/hero-bg-2OYQfJmhSm.jpg') . PHP_EOL;
echo "exists('landing/hero-bg-2OYQfJmhSm.jpg'): " . ($disk->exists('landing/hero-bg-2OYQfJmhSm.jpg') ? 'yes' : 'no') . PHP_EOL;
echo "path('landing/hero-bg-2OYQfJmhSm.jpg'): " . $disk->path('landing/hero-bg-2OYQfJmhSm.jpg') . PHP_EOL;

echo PHP_EOL . "=== PengaturanLanding::heroBackgroundUrl() ===" . PHP_EOL;
$setting = \App\Models\Landing\PengaturanLanding::current();
echo "hero_background stored: " . ($setting->hero_background ?: '(null)') . PHP_EOL;
echo "heroBackgroundUrl(): " . $setting->heroBackgroundUrl() . PHP_EOL;
echo "hasThemeBackground(): " . ($setting->hasThemeBackground() ? 'yes' : 'no') . PHP_EOL;

echo PHP_EOL . "=== Lokasi file di disk ===" . PHP_EOL;
$fullPath = $disk->path('landing/hero-bg-2OYQfJmhSm.jpg');
echo "Full path: $fullPath" . PHP_EOL;
echo "File exists: " . (file_exists($fullPath) ? 'yes' : 'no') . PHP_EOL;
if (file_exists($fullPath)) {
    [$w, $h] = getimagesize($fullPath);
    echo "Dimensions: {$w}x{$h}, size=" . filesize($fullPath) . "B" . PHP_EOL;
}
