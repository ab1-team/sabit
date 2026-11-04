<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tenant\TenantInvoice;
use Illuminate\Support\Facades\Cache;

/**
 * Status tagihan lisensi sebuah tenant (sekolah).
 *
 * Invoice disimpan pada koneksi CENTRAL (tabel `admin_invoice`) karena
 * tagihan dibuat dari konsol pusat, bukan dari aplikasi sekolah. Karena itu
 * pengecekan invoice TIDAK boleh memakai model tenant biasa.
 *
 * Cache memakai store 'central' (bukan cache default) supaya:
 *   - data tagihan tidak ikut ter-scope tenant (tagged cache),
 *   - dan tetap terbaca dari host pusat maupun host tenant.
 */
class TenantInvoiceStatus
{
    public const CACHE_STORE = 'central';

    /**
     * Cache key per tenant. TTL pendek supaya status tagihan cepat sinkron
     * dengan perubahan di konsol pusat (pembayaran / edit invoice).
     */
    private const CACHE_PREFIX = 'tenant:invoice_status:';

    private const CACHE_TTL = 60;

    /**
     * Invoice yang belum lunas milik sebuah tenant.
     *
     * Semua invoice berstatus 'unpaid' memblokir — bukan hanya yang sudah
     * lewat jatuh tempo.
     */
    public function unpaidInvoices(string $tenantId): array
    {
        if ($tenantId === '') {
            return [];
        }

        return Cache::store(self::CACHE_STORE)->remember(
            self::CACHE_PREFIX . $tenantId,
            self::CACHE_TTL,
            fn () => TenantInvoice::query()
                ->where('tenant_id', $tenantId)
                ->where('status', TenantInvoice::STATUS_UNPAID)
                ->orderBy('tgl_lunas')
                ->orderBy('id')
                ->get(['id', 'jenis_pembayaran', 'tgl_invoice', 'tgl_lunas', 'jumlah'])
                ->map(fn ($inv) => [
                    'id'               => (int) $inv->id,
                    'nomor'            => 'INV-' . str_pad((string) $inv->id, 5, '0', STR_PAD_LEFT),
                    'jenis_pembayaran' => (string) $inv->jenis_pembayaran,
                    'tgl_invoice'      => $inv->tgl_invoice?->format('d/m/Y'),
                    'tgl_lunas'        => $inv->tgl_lunas?->format('d/m/Y'),
                    'terlambat'        => $inv->isOverdue(),
                    'sisa_hari'        => $inv->daysToDue(),
                    'jumlah'           => (float) $inv->jumlah,
                    'jumlah_fmt'       => 'Rp ' . number_format((float) $inv->jumlah, 0, ',', '.'),
                ])
                ->all()
        );
    }

    /**
     * True bila tenant punya invoice yang belum lunas → akses diblokir.
     */
    public function isBlocked(string $tenantId): bool
    {
        return $this->unpaidInvoices($tenantId) !== [];
    }

    /**
     * Ringkasan untuk ditampilkan di popup notifikasi / halaman tagihan.
     */
    public function summary(string $tenantId): array
    {
        $invoices = $this->unpaidInvoices($tenantId);
        $nominal = (float) array_sum(array_column($invoices, 'jumlah'));

        return [
            'blocked'     => $invoices !== [],
            'total'       => count($invoices),
            'terlambat'   => count(array_filter($invoices, fn ($i) => $i['terlambat'])),
            'nominal'     => $nominal,
            'nominal_fmt' => 'Rp ' . number_format($nominal, 0, ',', '.'),
            'invoices'    => $invoices,
        ];
    }

    /**
     * Bersihkan cache status tagihan sebuah tenant.
     */
    public static function flush(?string $tenantId = null): void
    {
        if ($tenantId === null || $tenantId === '') {
            return;
        }

        Cache::store(self::CACHE_STORE)->forget(self::CACHE_PREFIX . $tenantId);
    }
}
