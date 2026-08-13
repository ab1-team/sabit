<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\TenantInvoice;
use App\Models\Tenant\TenantRekening;
use App\Models\Tenant\TenantTransaksi;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $tid = $this->currentTenantId($request);
            $data = TenantInvoice::with(['user' => fn($q) => $q->select('id', 'nama_lengkap')])
                ->withCount('hasTransaksi')
                ->where('status', 'unpaid')
                ->when($tid, fn ($q) => $q->where('admin_invoice.tenant_id', $tid))
                ->select('admin_invoice.*');

            return DataTables::eloquent($data)
                ->addIndexColumn()
                ->addColumn('owner', fn ($row) => $row->user->nama_lengkap ?? '—')
                ->addColumn('tgl_invoice_fmt', fn ($row) => $row->tgl_invoice?->format('d/m/Y') ?? '—')
                ->addColumn('jumlah_fmt', fn ($row) => 'Rp ' . number_format((float) $row->jumlah, 0, ',', '.'))
                ->addColumn('action', function ($row) {
                    $url = route('tenant.transaksi.paymentForm', $row->id);
                    return '
                        <button type="button" class="bayar-invoice inline-flex items-center rounded-lg bg-emerald-100 px-2.5 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-200" data-url="'.$url.'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </button>
                    ';
                })
                ->rawColumns(['action'])
                ->toJson();
        }

        $tid = $this->currentTenantId($request);
        $rekenings = TenantRekening::query()
            ->when($tid, fn ($q) => $q->where('tenant_id', $tid))
            ->where('status', 'aktif')
            ->orderBy('nama_rekening')
            ->get(['kd_rekening', 'nama_rekening']);
        return view('tenant.transaksi.index', [
            'rekenings' => $rekenings,
            'tenants' => $request->attributes->get('tenants', []),
            'currentTenantId' => $tid,
            'currentTenant' => $request->attributes->get('current_tenant'),
        ]);
    }

    public function paymentForm(TenantInvoice $invoice)
    {
        return response()->json([
            'id'              => $invoice->id,
            'jenis_pembayaran' => $invoice->jenis_pembayaran,
            'owner'           => $invoice->user->nama_lengkap ?? '—',
            'tgl_invoice'     => $invoice->tgl_invoice?->format('d F Y') ?? '—',
            'jumlah'          => 'Rp ' . number_format((float) $invoice->jumlah, 0, ',', '.'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'idv'             => ['required', 'exists:admin_invoice,id'],
            'rekening_debit'  => ['required', 'exists:admin_rekening,kd_rekening'],
            'jumlah'          => ['required', 'numeric', 'min:0'],
            'tgl_lunas'       => ['nullable', 'date'],
            'keterangan'      => ['nullable', 'string', 'max:255'],
            'mark_paid'       => ['nullable'],
        ]);

        $invoice = TenantInvoice::findOrFail($request->idv);
        $tglLunas = $request->filled('tgl_lunas') ? \Carbon\Carbon::parse($request->tgl_lunas)->toDateString() : now()->toDateString();

        TenantTransaksi::create([
            'tenant_id'            => $invoice->tenant_id,
            'tgl_transaksi'        => $tglLunas,
            'rekening_debit'       => $request->rekening_debit,
            'rekening_kredit'      => $request->rekening_debit,
            'idv'                  => $invoice->id,
            'keterangan_transaksi' => $request->keterangan ?? $invoice->jenis_pembayaran,
            'jumlah'               => $request->jumlah,
            'urutan'               => TenantTransaksi::max('urutan') + 1,
            'id_user'              => auth('tenant')->id(),
        ]);

        if ($request->boolean('mark_paid')) {
            $invoice->update([
                'status'    => 'paid',
                'tgl_lunas' => $tglLunas,
            ]);
        }

        return redirect()->route('tenant.transaksi.index')
            ->with('success', 'Pembayaran berhasil dicatat.');
    }

    private function currentTenantId(Request $request): ?string
    {
        return $request->attributes->get('current_tenant_id');
    }
}
