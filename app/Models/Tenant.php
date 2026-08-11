<?php

namespace App\Models;

use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\DatabaseConfig;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $table = 'tenants';

    protected $fillable = [
        'id',
        'nama_sekolah',
        'email',
        'tenancy_db_name',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'nama_sekolah',
            'email',
            'tenancy_db_name',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * Disable VirtualColumn trait — columns are real DB columns.
     */
    protected function encodeAttributes(): void
    {
        // no-op: skip encoding attributes to JSON column
    }

    /**
     * Strip the (no-longer-existing) 'data' JSON column from the attributes
     * before save so Eloquent doesn't try to UPDATE a column that was dropped
     * by the 2026_08_07_000002 migration. The parent trait still sets
     * attributes['data'] = null on retrieved events.
     */
    public function save(array $options = [])
    {
        $attrs = $this->getAttributes();
        if (array_key_exists('data', $attrs) && ! array_key_exists('data', static::getCustomColumns())) {
            unset($this->attributes['data']);
        }

        return parent::save($options);
    }

    public function database(): DatabaseConfig
    {
        return new DatabaseConfig($this);
    }

    /**
     * Domain admin tenant (tempat /login dan /app/*).
     */
    public function adminDomain(): ?Domain
    {
        return $this->domains()->where('type', Domain::TYPE_ADMIN)->first();
    }

    /**
     * Domain landing page publik tenant.
     */
    public function landingDomain(): ?Domain
    {
        return $this->domains()->where('type', Domain::TYPE_LANDING)->first();
    }

    /**
     * URL absolut ke domain admin. Dipakai landing page untuk tombol "Login".
     * Diperlukan karena route() tidak bisa melintasi domain berbeda.
     */
    public function adminUrl(string $path = '/'): ?string
    {
        $domain = $this->adminDomain();

        return $domain ? $this->buildUrl($domain->domain, $path) : null;
    }

    /**
     * URL absolut ke domain landing. Dipakai admin untuk link "Lihat Website".
     */
    public function landingUrl(string $path = '/'): ?string
    {
        $domain = $this->landingDomain();

        return $domain ? $this->buildUrl($domain->domain, $path) : null;
    }

    private function buildUrl(string $host, string $path): string
    {
        $scheme = str_starts_with((string) config('app.url'), 'https://') ? 'https' : 'http';

        return $scheme . '://' . $host . '/' . ltrim($path, '/');
    }
}