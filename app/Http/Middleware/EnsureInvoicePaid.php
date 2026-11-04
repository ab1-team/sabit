<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\TenantInvoiceStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Notifikasi tagihan lisensi untuk aplikasi sekolah (host tenant).
 *
 * Perilaku:
 *   - Sekolah yang punya invoice berstatus 'unpaid' → akses aplikasi
 *     dibatasi. Login BARU ditolak (lihat AuthController::login) dan user
 *     yang sudah login tetap boleh masuk tetapi melihat popup pemberitahuan
 *     (sekali per sesi) plus banner permanen di halaman.
 *   - Host pusat (konsol master) TIDAK pernah terpengaruh.
 *   - Landing page publik tenant tidak memakai middleware ini.
 */
class EnsureInvoicePaid
{
    /**
     * Key session untuk ringkasan tagihan aktif.
     */
    public const SESSION_KEY = 'tenant_tagihan';

    /**
     * Key session penanda popup notifikasi sudah pernah ditampilkan di
     * sesi ini (agar tidak muncul ulang di setiap navigasi).
     *
     * Di-set saat popup benar-benar dirender (bukan di middleware), supaya
     * request yang berakhir sebagai redirect tidak ikut menghabiskan popup.
     */
    public const SESSION_POPUP_SHOWN = 'tenant_tagihan_popup_shown';

    public function __construct(private readonly TenantInvoiceStatus $invoiceStatus)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        // Host pusat (konsol master) → tidak pernah diblokir.
        if (\App\Support\HostContext::isCentral($request->getHost())) {
            return $next($request);
        }

        $tenantId = $this->tenantId($request);

        // Tenant belum terinisialisasi (mis. host tidak dikenal) → biarkan
        // middleware lain yang menolak dengan 404.
        if ($tenantId === null || $tenantId === '') {
            return $next($request);
        }

        $summary = $this->invoiceStatus->summary($tenantId);

        if (! $summary['blocked']) {
            // Sudah lunas → bersihkan sisa penanda tagihan di session.
            $request->session()->forget(self::SESSION_KEY);
            $request->session()->forget(self::SESSION_POPUP_SHOWN);

            return $next($request);
        }

        // Simpan ringkasan tagihan supaya layout tenant bisa menampilkannya
        // tanpa query ulang. Dipakai juga oleh halaman login.
        $request->session()->put(self::SESSION_KEY, $summary);

        return $next($request);
    }

    /**
     * ID tenant aktif. Setelah middleware tenancy berjalan, `tenant('id')`
     * sudah terisi. Fallback: cari lewat host pada tabel domains (central).
     */
    private function tenantId(Request $request): ?string
    {
        $id = tenant('id');

        if (! empty($id)) {
            return (string) $id;
        }

        return \App\Support\HostContext::tenantForHost($request->getHost())?->id;
    }
}
