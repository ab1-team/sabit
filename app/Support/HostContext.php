<?php

namespace App\Support;

class HostContext
{
    /**
     * Daftar host yang dianggap pusat/central. Sumber prioritas:
     *   1. config('tenancy.central_domains') — env CENTRAL_DOMAIN & CENTRAL_BASE_DOMAIN.
     *   2. fallback hard-coded untuk domain produksi & lokal agar tidak pernah
     *      salah deteksi walau .env kosong / cache belum dibersihkan.
     */
    public static function centralHosts(): array
    {
        $configured = (array) config('tenancy.central_domains', []);

        $fallback = [
            'al-maruf.sch.id',
            'sabit.test',
            'localhost',
        ];

        $merged = array_values(array_unique(array_filter(array_merge(
            $configured,
            $fallback
        ))));

        return $merged;
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
}