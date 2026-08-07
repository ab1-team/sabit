<?php
$pdo = new PDO('mysql:host=103.112.245.8;dbname=sinkrone_sabit', 'sinkrone_alm_app', 'sinkrone_alm_app', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$rows = $pdo->query("SELECT t.id, t.nama_sekolah, GROUP_CONCAT(d.domain SEPARATOR ',') as domains FROM tenants t LEFT JOIN domains d ON d.tenant_id = t.id GROUP BY t.id")->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo $r['id'] . ' | ' . ($r['nama_sekolah'] ?? '-') . ' | ' . ($r['domains'] ?? 'NO-DOMAIN') . PHP_EOL;
}