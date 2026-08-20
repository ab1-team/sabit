<?php

$tmpDir = getenv('TEMP') ?: 'C:\\Windows\\Temp';
$png = $tmpDir . '\\tinymce-test-' . uniqid() . '.png';
$mp4 = $tmpDir . '\\tinymce-test-' . uniqid() . '.mp4';
$txt = $tmpDir . '\\tinymce-test-' . uniqid() . '.txt';
$big = $tmpDir . '\\tinymce-test-' . uniqid() . '.png';

$im = imagecreatetruecolor(80, 60);
imagefill($im, 0, 0, imagecolorallocate($im, 30, 100, 200));
imagepng($im, $png);
imagedestroy($im);
file_put_contents($mp4, hex2bin('0000001C667479706D703432000000006D70343269736F6D'));
file_put_contents($txt, 'hello');
copy($png, $big);
$h = fopen($big, 'ab');
for ($i = 0; $i < 60 * 1024; $i++) fwrite($h, str_repeat('X', 1024));
fclose($h);

\Stancl\Tenancy\Facades\Tenancy::initialize('demo');

$controller = app(\App\Http\Controllers\AdminLandingController::class);

function callUpload($controller, string $field, string $path, string $mime, string $origName, ?string $type = null) {
    $req = \Illuminate\Http\Request::create('/app/admin-landing/posts/upload-content', 'POST');
    $file = new \Illuminate\Http\UploadedFile($path, $origName, $mime, null, true);
    $req->files = new \Symfony\Component\HttpFoundation\FileBag([$field => $file]);
    if ($type) $req->request->set('type', $type);
    return $controller->postUploadContent($req);
}

echo "=== 1) Upload gambar (PNG 80x60) ===\n";
$r = callUpload($controller, 'file', $png, 'image/png', 'test.png', 'image');
echo "  HTTP: " . $r->status() . "\n";
echo "  body: " . $r->getContent() . "\n";
$j = json_decode($r->getContent(), true);
if (!($j['success'] ?? false) || ($j['kind'] ?? '') !== 'image') { echo "  GAGAL\n"; exit(1); }
$imgRel = str_replace('/storage/', '', parse_url($j['location'], PHP_URL_PATH));
$imgFull = storage_path('app/public/' . $imgRel);
echo "  file exists: " . (file_exists($imgFull) ? 'YES' : 'NO') . " -> $imgFull\n";
if (!file_exists($imgFull)) { echo "  GAGAL\n"; exit(1); }

echo "=== 2) Upload video (MP4 dummy) ===\n";
$r = callUpload($controller, 'file', $mp4, 'video/mp4', 'test.mp4', 'video');
echo "  HTTP: " . $r->status() . "\n";
echo "  body: " . $r->getContent() . "\n";
$j = json_decode($r->getContent(), true);
if (!($j['success'] ?? false) || ($j['kind'] ?? '') !== 'video') { echo "  GAGAL\n"; exit(1); }
$vidRel = str_replace('/storage/', '', parse_url($j['location'], PHP_URL_PATH));
$vidFull = storage_path('app/public/' . $vidRel);
echo "  file exists: " . (file_exists($vidFull) ? 'YES' : 'NO') . " -> $vidFull\n";
if (!file_exists($vidFull)) { echo "  GAGAL\n"; exit(1); }

echo "=== 3) Negative: tipe text/plain ===\n";
$r = callUpload($controller, 'file', $txt, 'text/plain', 'notes.txt');
echo "  HTTP: " . $r->status() . "\n";
echo "  body: " . $r->getContent() . "\n";
if ($r->status() !== 422) { echo "  GAGAL harus 422\n"; exit(1); }

echo "=== 4) Negative: file > 50MB ===\n";
try {
    $r = callUpload($controller, 'file', $big, 'image/png', 'huge.png');
    echo "  HTTP: " . $r->status() . "\n";
    echo "  body: " . substr($r->getContent(), 0, 200) . "\n";
    if ($r->status() !== 422) { echo "  GAGAL harus 422\n"; exit(1); }
} catch (\Illuminate\Validation\ValidationException $e) {
    echo "  HTTP: 422 (via ValidationException)\n";
    echo "  body: " . json_encode($e->errors()) . "\n";
}

echo "=== 5) Negative: tanpa file ===\n";
$req = \Illuminate\Http\Request::create('/app/admin-landing/posts/upload-content', 'POST');
$req->files = new \Symfony\Component\HttpFoundation\FileBag([]);
try {
    $r = $controller->postUploadContent($req);
    echo "  HTTP: " . $r->status() . "\n";
    echo "  body: " . substr($r->getContent(), 0, 200) . "\n";
    if ($r->status() !== 422) { echo "  GAGAL harus 422\n"; exit(1); }
} catch (\Throwable $e) {
    echo "  Exception: " . get_class($e) . " (acceptable)\n";
}

\Stancl\Tenancy\Facades\Tenancy::end();

@unlink($png);
@unlink($mp4);
@unlink($txt);
@unlink($big);

echo "\nSEMUA TES BERHASIL.\n";
