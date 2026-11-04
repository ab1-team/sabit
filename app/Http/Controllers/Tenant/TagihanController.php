<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Middleware\EnsureInvoicePaid;
use App\Models\Tenant\TenantInvoice;
use App\Services\TenantInvoiceStatus;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Halaman notifikasi tagihan lisensi untuk host tenant (sekolah).
 *
 * Halaman ini TIDAK butuh login — dipakai justru ketika login ditolak
 * karena tagihan belum lunas. User melihat daftar tagihan, instruksi
 * pembayaran, dan bisa mencetak invoice.
 */
class TagihanController extends Controller
{
    public function __construct(private readonly TenantInvoiceStatus $invoiceStatus)
    {
    }

    /**
     * Halaman "tagihan belum lunas".
     */
    public function blocked(Request $request)
    {
        $tenantId = $this->currentTenantId($request);
        $summary = $this->invoiceStatus->summary($tenantId);

        // Sudah lunas semua (mis. user membuka URL lama setelah pembayaran)
        // → arahkan langsung ke aplikasi, tidak perlu menampilkan daftar.
        if (! $summary['blocked']) {
            return redirect()->route('app.dashboard');
        }

        return view('tenant.tagihan.blocked', [
            'summary'  => $summary,
            'tenantId' => $tenantId,
        ]);
    }

    /**
     * Cetak / unduh invoice tagihan (PDF).
     */
    public function print(Request $request, int $invoice)
    {
        $tenantId = $this->currentTenantId($request);

        $inv = TenantInvoice::query()
            ->where('tenant_id', $tenantId)
            ->findOrFail($invoice);

        $data = app(TenantInvoiceController::class)->printData($inv);

        $pdf = Pdf::loadView('tenant.invoice.print', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('invoice-'.$inv->id.'.pdf');
    }

    /**
     * Keluar dari sesi (dipakai tombol "Keluar" di halaman tagihan).
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->forget('auth_portal');
        $request->session()->forget(EnsureInvoicePaid::SESSION_KEY);
        $request->session()->forget(EnsureInvoicePaid::SESSION_POPUP_SHOWN);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(url('/login'));
    }

    /**
     * ID tenant dari context tenancy; fallback ke lookup domain (central).
     */
    private function currentTenantId(Request $request): string
    {
        $id = tenant('id');

        if (! empty($id)) {
            return (string) $id;
        }

        return (string) (\App\Support\HostContext::tenantForHost($request->getHost())?->id ?? '');
    }
}
