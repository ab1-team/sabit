<?php

namespace App\Support;

class HostContext
{
    /**
     * Daftar host yang dianggap pusat/central.
     *
     * Sumber TUNGGAL: config('tenancy.central_domains') — yang dibaca dari
     * env CENTRAL_DOMAIN & CENTRAL_BASE_DOMAIN di .env.
     *
     * Tidak ada fallback hard-coded. Setiap domain sekolah
     * (al-islam.sch.id, smk-pertiwi.sch.id, dll) TIDAK masuk sini —
     * didaftarkan via tabel `domains` di central DB.
     *
     * PENTING untuk setup:
     *   - Di .env WAJIB set CENTRAL_DOMAIN & CENTRAL_BASE_DOMAIN
     *     (mis. CENTRAL_DOMAIN=sabit.test).
     *   - Tanpa .env, helper ini mengembalikan array kosong dan SEMUA host
     *     diperlakukan sebagai tenant host — berguna untuk mode development
     *     path-based (mis. /tenant/{id}/...) tanpa DNS.
     */
    public static function centralHosts(): array
    {
        $configured = (array) config('tenancy.central_domains', []);

        return array_values(array_unique(array_filter($configured)));
    }

    /**
     * True kalau host yang sedang diakses adalah host pusat (master console).
     */
    public static function isCentral(?string $host): bool
    {
        if (! $host) {
            return false;
        }

        return in_array(strtolower($host), array_map('strtolower', self::centralHosts()), true);
    }

    /**
     * Pilih tujuan login sesuai host yang sedang diakses:
     *   - central host -> route pusat (tenant.login)
     *   - selain itu   -> route sekolah (login)
     *
     * Pakai ini di controller/middleware supaya tidak ada lagi redirect
     * ke host yang salah (mis. al-maruf.sch.id -> sabit.test).
     */
    public static function loginRoute(?string $host): string
    {
        return self::isCentral($host) ? 'tenant.login' : 'login';
    }

    /**
     * Lookup sekolah (tenant) berdasarkan host request. Memakai tabel `domains`
     * di central DB. Return null kalau host tidak dikenal (artinya bukan
     * domain sekolah yang valid).
     */
    public static function tenantForHost(?string $host): ?\App\Models\Tenant
    {
        if (! $host || self::isCentral($host)) {
            return null;
        }

        try {
            $row = \Illuminate\Support\Facades\DB::connection(
                config('tenancy.database.central_connection')
            )->table('domains')
                ->where('domain', strtolower($host))
                ->first();

            if (! $row || ! $row->tenant_id) {
                return null;
            }

            return \App\Models\Tenant::find($row->tenant_id);
        } catch (\Throwable $e) {
            return null;
        }
    }
}