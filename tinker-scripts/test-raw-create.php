<?php
$ch = curl_init('http://demo.sabit.test/app/admin-landing/posts/create');
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HEADER => true]);
$r = curl_exec($ch);
$hsize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
$headers = substr($r, 0, $hsize);
$body = substr($r, $hsize);
curl_close($ch);

echo "Status: $status\n";
echo "Final URL: $url\n";
echo "Body length: " . strlen($body) . "\n";
echo "---\n";
echo $headers;
echo "---\n";
echo "First 500 chars of body:\n";
echo substr($body, 0, 500);
echo "\n\n--- body markers ---\n";
foreach (['lp-tinymce', 'lpPostContentFile', 'lpfileopen', 'lpyt', 'upload-content', 'csrf-token', 'Login', 'login', 'name="email"', 'name="_token"', 'tinyMCE', 'tinymce'] as $m) {
    echo "  $m: " . (str_contains($body, $m) ? 'YES' : 'no') . "\n";
}
