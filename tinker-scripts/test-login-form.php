<?php
$ch = curl_init('http://demo.sabit.test/login');
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_HEADER => false]);
$body = curl_exec($ch);
curl_close($ch);
if (preg_match_all('/<input[^>]+name="([^"]+)"/i', $body, $m)) {
    print_r(array_unique($m[1]));
}
echo "\n";
echo "Has Login form: " . (str_contains($body, 'login') ? 'YES' : 'NO') . "\n";
echo "Body preview:\n" . substr($body, 0, 800) . "\n";
