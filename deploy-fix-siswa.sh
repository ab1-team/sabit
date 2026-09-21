#!/bin/bash
# deploy-fix-siswa.sh
# Jalankan di production: bash deploy-fix-siswa.sh

set -e

cd /var/www/sim-akademik

echo "============================================"
echo "LANGKAH 1: Cek kondisi SEBELUM deploy"
echo "============================================"
echo "[1.1] Git HEAD saat ini:"
git log --oneline -1 || echo "GIT ERROR"

echo ""
echo "[1.2] Cek method resolveDefaultSppNominal di SiswaService:"
if grep -q "function resolveDefaultSppNominal" app/Services/SiswaService.php 2>/dev/null; then
    echo "  ✅ METHOD ADA di app/Services/SiswaService.php"
    grep -n "function resolveDefaultSppNominal" app/Services/SiswaService.php
else
    echo "  ❌ METHOD TIDAK ADA di app/Services/SiswaService.php — perlu pull!"
fi

echo ""
echo "[1.3] Cek apakah SiswaController masih panggil service->resolveDefaultSppNominal:"
if grep -q "service->resolveDefaultSppNominal" app/Http/Controllers/SiswaController.php 2>/dev/null; then
    echo "  ❌ MASIH PANGGIL — perlu pull!"
else
    echo "  ✅ TIDAK PANGGIL langsung (aman)"
fi

echo ""
echo "[1.4] Cek file SiswaService duplikat (selain yang di app/Services):"
find /var/www/sim-akademik -name "SiswaService.php" -not -path "*/vendor/*" 2>/dev/null

echo ""
echo "============================================"
echo "LANGKAH 2: Pull kode terbaru"
echo "============================================"
git fetch origin
git reset --hard origin/main
echo "[2.1] HEAD sekarang: $(git log --oneline -1)"

echo ""
echo "============================================"
echo "LANGKAH 3: Regenerate autoload"
echo "============================================"
composer dump-autoload --optimize 2>&1 | tail -5
composer dump-autoload --classmap-authoritative 2>&1 | tail -5

echo ""
echo "============================================"
echo "LANGKAH 4: Clear semua cache Laravel"
echo "============================================"
rm -f bootstrap/cache/services.php
rm -f bootstrap/cache/packages.php
rm -f bootstrap/cache/config.php
rm -f bootstrap/cache/routes-v7.php
find bootstrap/cache -type f -name "*.php" -delete 2>/dev/null || true
php artisan optimize:clear 2>&1
php artisan clear-compiled 2>&1
php artisan package:discover 2>&1 | tail -3

echo ""
echo "============================================"
echo "LANGKAH 5: Restart PHP-FPM untuk clear opcache"
echo "============================================"
PHP_VER=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')
echo "[5.1] PHP version terdeteksi: $PHP_VER"
FPM_SERVICE="php${PHP_VER}-fpm"

if systemctl list-unit-files | grep -q "${FPM_SERVICE}.service"; then
    sudo systemctl reload "$FPM_SERVICE" 2>&1
    echo "[5.2] $FPM_SERVICE di-reload"
elif systemctl list-unit-files | grep -q "php-fpm.service"; then
    sudo systemctl reload php-fpm 2>&1
    echo "[5.2] php-fpm di-reload"
elif pgrep -x apache2 > /dev/null; then
    sudo systemctl reload apache2 2>&1
    echo "[5.2] apache2 di-reload"
elif pgrep -x nginx > /dev/null; then
    sudo systemctl reload nginx 2>&1
    echo "[5.2] nginx di-reload"
else
    echo "[5.2] ⚠️ Web server tidak terdeteksi. Restart manual:"
    echo "      sudo systemctl reload php${PHP_VER}-fpm"
fi

echo ""
echo "============================================"
echo "LANGKAH 6: VERIFIKASI SETELAH deploy"
echo "============================================"
echo "[6.1] Git HEAD:"
git log --oneline -1

echo ""
echo "[6.2] Method di SiswaService:"
if grep -q "function resolveDefaultSppNominal" app/Services/SiswaService.php; then
    echo "  ✅ METHOD ADA"
    grep -n "function resolveDefaultSppNominal" app/Services/SiswaService.php
else
    echo "  ❌ METHOD HILANG — INVESTIGASI!"
fi

echo ""
echo "[6.3] SiswaController tidak panggil service->resolveDefaultSppNominal:"
if grep -q "service->resolveDefaultSppNominal" app/Http/Controllers/SiswaController.php; then
    echo "  ❌ MASIH PANGGIL"
else
    echo "  ✅ AMAN — tidak panggil langsung"
fi

echo ""
echo "[6.4] Case-sensitive cek (Linux sensitive):"
ACTUAL_FILE=$(find app/Services -iname "siswaservice.php" 2>/dev/null | head -1)
EXPECTED_FILE="app/Services/SiswaService.php"
if [ "$ACTUAL_FILE" = "$EXPECTED_FILE" ]; then
    echo "  ✅ Filename case-sensitive OK: $ACTUAL_FILE"
else
    echo "  ❌ Case mismatch! actual=$ACTUAL_FILE expected=$EXPECTED_FILE"
fi

echo ""
echo "[6.4b] Cek deklarasi CLASS di dalam file (PHP case-sensitive):"
ACTUAL_CLASS=$(grep -E "^class\s+SiswaService\b" app/Services/SiswaService.php 2>/dev/null | awk '{print $2}' | tr -d ':' | head -1)
EXPECTED_CLASS="SiswaService"
if [ "$ACTUAL_CLASS" = "$EXPECTED_CLASS" ]; then
    echo "  ✅ Class declaration OK: class $ACTUAL_CLASS"
else
    echo "  ❌ Class declaration salah! actual='$ACTUAL_CLASS' expected='$EXPECTED_CLASS'"
fi

ACTUAL_CTRL_CLASS=$(grep -E "^class\s+SiswaController\b" app/Http/Controllers/SiswaController.php 2>/dev/null | awk '{print $2}' | tr -d ':' | head -1)
EXPECTED_CTRL_CLASS="SiswaController"
if [ "$ACTUAL_CTRL_CLASS" = "$EXPECTED_CTRL_CLASS" ]; then
    echo "  ✅ Controller class OK: class $ACTUAL_CTRL_CLASS"
else
    echo "  ❌ Controller class salah! actual='$ACTUAL_CTRL_CLASS' expected='$EXPECTED_CTRL_CLASS'"
fi

echo ""
echo "[6.4c] Cek apakah ada file SiswaService/siswaservice ganda di app/:"
find app/ -iname "siswaservice.php" -not -path "*/vendor/*" 2>/dev/null | while read f; do
    echo "  found: $f"
done

echo ""
echo "[6.4d] PSR-4 sanity check untuk semua class SiswaService-related:"
check_psr4() {
    local class_name="$1"
    local expected_path="$2"
    if [ -f "$expected_path" ]; then
        local actual_class=$(grep -E "^class\s+${class_name}\b" "$expected_path" 2>/dev/null | awk '{print $2}' | tr -d ':' | head -1)
        if [ "$actual_class" = "$class_name" ]; then
            echo "  ✅ $class_name @ $expected_path"
        else
            echo "  ❌ $class_name @ $expected_path — but actual class declaration: '$actual_class'"
        fi
    else
        echo "  ❌ $class_name — file not found: $expected_path"
    fi
}
check_psr4 "SiswaService"          "app/Services/SiswaService.php"
check_psr4 "SiswaController"       "app/Http/Controllers/SiswaController.php"
check_psr4 "SiswaRequest"          "app/Http/Requests/SiswaRequest.php"
check_psr4 "Siswa"                 "app/Models/Siswa.php"
check_psr4 "MigrasiSiswaImport"    "app/Imports/MigrasiSiswaImport.php"
check_psr4 "MigrasiSiswaController" "app/Http/Controllers/Tenant/MigrasiSiswaController.php"
check_psr4 "MigrasiSiswaTemplateExport" "app/Exports/MigrasiSiswaTemplateExport.php"
check_psr4 "JenisBiaya"            "app/Models/JenisBiaya.php"
check_psr4 "TahunAkademik"         "app/Models/TahunAkademik.php"

echo ""
echo "[6.5] Class di autoload:"
grep -c "SiswaService" vendor/composer/autoload_classmap.php 2>/dev/null || echo "  classmap belum ada SiswaService"

echo ""
echo "[6.6] Opcache status:"
php -r 'echo function_exists("opcache_reset") ? (opcache_get_status(false)["opcache_enabled"] ?? false ? "opcache ON" : "opcache OFF") : "opcache not loaded"; echo PHP_EOL;'

echo ""
echo "============================================"
echo "SELESAI. Coba edit siswa di browser."
echo "Lalu cek log:"
echo "  tail -20 storage/logs/laravel.log"
echo "  cat storage/logs/debug-siswa.log 2>/dev/null"
echo "============================================"
