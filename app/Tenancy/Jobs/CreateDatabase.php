<?php

declare(strict_types=1);

namespace App\Tenancy\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB as DBFacade;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\DatabaseManager;
use Stancl\Tenancy\Events\CreatingDatabase;
use Stancl\Tenancy\Events\DatabaseCreated;

/**
 * Replace default stancl/tenancy CreateDatabase job.
 *
 * Paket default menolak CREATE DATABASE bila DB sudah ada (throw
 * TenantDatabaseAlreadyExistsException). Bila pembuatan tenant sebelumnya
 * gagal setengah jadi — mis. FK constraint violation di migrasi — DB
 * tertinggal tanpa row tenants di central. Supaya user tidak terjebak,
 * job ini drop DB lama sebelum di-create ulang.
 */
class CreateDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var TenantWithDatabase|Model */
    protected $tenant;

    public function __construct(TenantWithDatabase $tenant)
    {
        $this->tenant = $tenant;
    }

    public function handle(DatabaseManager $databaseManager)
    {
        event(new CreatingDatabase($this->tenant));

        if ($this->tenant->getInternal('create_database') === false) {
            return false;
        }

        $this->tenant->database()->makeCredentials();
        $database = $tenantDb = $this->tenant->database()->getName();

        // Drop DB jika sudah ada. Pakai connection 'mysql' (central).
        $exists = (bool) DBFacade::connection('mysql')->select(
            "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?",
            [$database]
        );
        if ($exists) {
            DBFacade::connection('mysql')->statement("DROP DATABASE `{$database}`");
        }

        // Bypass ensureTenantCanBeCreated di parent (yang throw kalau exists).
        $this->tenant->database()->manager()->createDatabase($this->tenant);

        event(new DatabaseCreated($this->tenant));
    }
}
