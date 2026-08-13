<?php

declare(strict_types=1);

namespace App\Tenancy\TenantDatabaseManagers;

use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Exceptions\NoConnectionSetException;
use Stancl\Tenancy\TenantDatabaseManagers\MySQLDatabaseManager;

/**
 * Manager MySQL kustom untuk tenancy: drop DB lama (jika ada) sebelum
 * create ulang. Aman karena TenantDatabaseSeeder bersifat idempotent
 * (firstOrCreate) — jadi drop+create DB dengan id tenant yang sama akan
 * meninggalkan DB kosong yang lalu di-seed ulang.
 *
 * Alasan: paket stancl/tenancy default-nya menolak 'CREATE DATABASE'
 * (throws TenantDatabaseAlreadyExistsException) bila nama DB sudah ada.
 * Ini terjadi saat pembuatan tenant sebelumnya gagal setengah jadi
 * (mis. FK constraint violation di migrasi), sehingga DB tertinggal
 * tanpa row tenants di central. Supaya UI tidak macet, kita drop dulu.
 */
class RecreateMySQLDatabaseManager extends MySQLDatabaseManager
{
    public function createDatabase(TenantWithDatabase $tenant): bool
    {
        $database = $tenant->database()->getName();

        // Drop dulu jika sudah ada. Ini yang membedakan dengan parent class.
        if ($this->databaseExists($database)) {
            $this->database()->statement("DROP DATABASE `{$database}`");
        }

        $charset = $this->database()->getConfig('charset');
        $collation = $this->database()->getConfig('collation');

        return $this->database()->statement("CREATE DATABASE `{$database}` CHARACTER SET `$charset` COLLATE `$collation`");
    }

    public function databaseExists(string $name): bool
    {
        return (bool) $this->database()->select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$name'");
    }
}
