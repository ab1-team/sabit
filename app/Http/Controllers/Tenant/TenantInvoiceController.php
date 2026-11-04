<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\TenantInvoice;
use App\Services\TenantInvoiceStatus;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class TenantInvoiceController extends Controller
{
    /**
     * Ringkasan invoice untuk kartu statistik di halaman.
     */
    public function summary(Request $request): array
    {
        $tid = $this->currentTenantId($request);

        $base = TenantInvoice::query()
            ->when($tid, fn ($q, $v) => $q->where('admin_invoice.tenant_id', $v));

        $total     = (clone $base)->count();
        $paid      = (clone $base)->where('status', TenantInvoice::STATUS_PAID)->count();
        $unpaid    = (clone $base)->where('status', TenantInvoice::STATUS_UNPAID)->count();
        $overdue   = (clone $base)
            ->where('status', TenantInvoice::STATUS_UNPAID)
            ->whereNotNull('tgl_lunas')
            ->whereDate('tgl_lunas', '<', today())
            ->count();
        $nominal   = (float) (clone $base)->sum('jumlah');
        $nominalUnpaid = (float) (clone $base)->where('status', TenantInvoice::STATUS_UNPAID)->sum('jumlah');

        return [
            'total'        => $total,
            'paid'         => $paid,
            'unpaid'       => $unpaid,
            'overdue'      => $overdue,
            'nominal'      => $nominal,
            'nominal_unpaid' => $nominalUnpaid,
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->data($request);
        }

        $invoice = new TenantInvoice(['status' => TenantInvoice::STATUS_UNPAID]);

        return view('tenant.invoice.index', [
            'invoice'       => $invoice,
            'tenants'       => $request->attributes->get('tenants', []),
            'currentTenantId' => $this->currentTenantId($request),
            'currentTenant' => $request->attributes->get('current_tenant'),
            'summary'       => $this->summary($request),
            'rekenings'     => $this->rekenings(),
            'paymentTypes'  => TenantInvoice::JENIS_PEMBAYARAN,
            // Nilai awal filter status (Lunas / Belum Lunas). Dipakai juga
            // supaya pilihan status tetap aktif saat sekolah diganti.
            'statusFilterValue' => $this->statusFilter($request),
        ]);
    }

    public function data(Request $request)
    {
        $tenantMap = $this->tenantMap();
        $tid = $this->currentTenantId($request);

        $statusFilter = $this->statusFilter($request);

        $data = TenantInvoice::with('user')
            ->select('admin_invoice.*')
            ->when($tid, fn ($q, $v) => $q->where('admin_invoice.tenant_id', $v))
            ->when(in_array($statusFilter, [TenantInvoice::STATUS_PAID, TenantInvoice::STATUS_UNPAID], true),
                fn ($q) => $q->where('admin_invoice.status', $statusFilter));

        return DataTables::eloquent($data)
            ->addIndexColumn()
            // Kolom kontrol detail (Responsive "column" type). Isinya kosong;
            // chevron-nya digambar lewat CSS pada sel kolom ini.
            ->addColumn('control', fn ($row) => '')
            ->addColumn('owner', fn ($row) => $row->user->nama_lengkap ?? '—')
            ->addColumn('tenant_nama', fn ($row) => $tenantMap[(string) $row->tenant_id] ?? '—')
            ->addColumn('tgl_invoice_fmt', fn ($row) => $row->tgl_invoice?->format('d/m/Y') ?? '—')
            ->addColumn('tgl_lunas_fmt', function ($row) {
                if ($row->tgl_lunas === null) {
                    return '<span class="text-slate-400">—</span>';
                }

                $date = e($row->tgl_lunas->format('d/m/Y'));

                if ($row->isPaid()) {
                    return '<span class="block whitespace-nowrap">'.$date.'</span>'
                        .'<span class="block text-[11px] text-emerald-600">Selesai</span>';
                }

                $days = (int) $row->daysToDue();

                if ($days < 0) {
                    $meta = '<span class="block text-[11px] font-medium text-rose-600">Telat '.abs($days).' hari</span>';
                } elseif ($days === 0) {
                    $meta = '<span class="block text-[11px] font-medium text-amber-600">Jatuh tempo hari ini</span>';
                } else {
                    $meta = '<span class="block text-[11px] text-slate-500">'.$days.' hari lagi</span>';
                }

                return '<span class="block whitespace-nowrap">'.$date.'</span>'.$meta;
            })
            ->addColumn('jumlah_fmt', fn ($row) => 'Rp '.number_format((float) $row->jumlah, 0, ',', '.'))
            ->addColumn('status_badge', function ($row) {
                if ($row->status === TenantInvoice::STATUS_PAID) {
                    return '<span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20">'
                        .'<span class="h-1.5 w-1.5 shrink-0 rounded-full bg-emerald-500"></span>Lunas</span>';
                }

                if ($row->isOverdue()) {
                    // Badge cukup menampilkan "Terlambat" — jumlah hari
                    // tetap tersedia di kolom "Jatuh Tempo" ("Telat N hari").
                    return '<span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full bg-rose-50 px-2.5 py-1 text-[11px] font-semibold text-rose-700 ring-1 ring-inset ring-rose-600/20">'
                        .'<span class="h-1.5 w-1.5 shrink-0 rounded-full bg-rose-500"></span>Terlambat</span>';
                }

                return '<span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-semibold text-amber-700 ring-1 ring-inset ring-amber-600/20">'
                    .'<span class="h-1.5 w-1.5 shrink-0 rounded-full bg-amber-500"></span>Belum<span class="hidden sm:inline"> Lunas</span></span>';
            })
            // Kunci di bawah harus sama dengan `columns[i][name]` yang dikirim DataTables.
            ->orderColumn('admin_invoice.jenis_pembayaran', 'admin_invoice.jenis_pembayaran $1')
            ->orderColumn('tenant_nama', 'admin_invoice.tenant_id $1')
            ->orderColumn('owner', 'admin_invoice.user_id $1')
            ->orderColumn('admin_invoice.tgl_invoice', 'admin_invoice.tgl_invoice $1')
            ->orderColumn('admin_invoice.tgl_lunas', 'admin_invoice.tgl_lunas $1')
            ->orderColumn('admin_invoice.jumlah', 'admin_invoice.jumlah $1')
            ->filterColumn('owner', function ($q, $kw) {
                $q->whereHas('user', fn ($qq) => $qq->where('nama_lengkap', 'like', "%{$kw}%")
                    ->orWhere('email', 'like', "%{$kw}%"));
            })
            ->filterColumn('tenant_nama', function ($q, $kw) {
                $ids = \App\Models\Tenant::query()
                    ->where(function ($qq) use ($kw) {
                        $qq->where('id', 'like', "{$kw}%")
                            ->orWhere('nama_sekolah', 'like', "%{$kw}%")
                            ->orWhere('email', 'like', "%{$kw}%");
                    })
                    ->pluck('id')
                    ->all();

                $q->whereIn('admin_invoice.tenant_id', empty($ids) ? ['__none__'] : $ids);
            })
            ->addColumn('action', function ($row) use ($tenantMap) {
                $print  = route('tenant.invoice.print', $row->id);
                $delete = route('tenant.invoice.destroy', $row->id);
                $paid   = $row->status === TenantInvoice::STATUS_PAID;

                $sekolah = $tenantMap[(string) $row->tenant_id] ?? ($row->tenant_id ?? '—');
                $owner   = $row->user->nama_lengkap ?? '—';

                // Label tombol disembunyikan di bawah `lg` (`hidden lg:inline`) agar kolom
                // Aksi tidak memaksa tabel meluber pada lebar tablet/ponsel.
                $payBtn = $paid
                    ? '<span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-1.5 text-xs font-semibold text-slate-400 cursor-not-allowed" aria-disabled="true" title="'.__('tenant.invoice.action.paid').'" aria-label="'.__('tenant.invoice.action.paid').'">'
                        .'<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg><span class="hidden lg:inline">'.__('tenant.invoice.action.paid').'</span></span>'
                    : '<button type="button" class="pay-invoice inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-2.5 py-1.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20 transition hover:bg-emerald-100"'
                        .' title="'.__('tenant.invoice.action.pay').'" aria-label="'.__('tenant.invoice.action.pay').'"'
                        .' data-id="'.$row->id.'" data-jenis="'.e($row->jenis_pembayaran).'" data-owner="'.e($owner).'"'
                        .' data-sekolah="'.e($sekolah).'" data-jumlah="'.e((float) $row->jumlah).'"'
                        .' data-tgl="'.e($row->tgl_invoice?->format('d/m/Y') ?? '—').'" data-tgl-lunas="'.e($row->tgl_lunas?->format('d/m/Y') ?? '').'">'
                        .'<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 4 4 0 014 0z"/></svg>'
                        .'<span class="hidden lg:inline">'.__('tenant.invoice.action.pay').'</span></button>';

                return '<div class="inline-flex items-center gap-1">'
                    .$payBtn
                    .'<button type="button" class="print-invoice inline-flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 transition hover:bg-indigo-100" title="'.__('tenant.invoice.action.print').'" aria-label="'.__('tenant.invoice.action.print').'" data-url="'.$print.'">'
                    .'<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>'
                    .'</button>'
                    .'<button type="button" class="delete-invoice inline-flex h-8 w-8 items-center justify-center rounded-lg bg-rose-50 text-rose-600 transition hover:bg-rose-100" title="'.__('tenant.invoice.action.delete').'" aria-label="'.__('tenant.invoice.action.delete').'" data-action="'.$delete.'">'
                    .'<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.48 0 00-7.5 0"/></svg>'
                    .'</button>'
                    .'</div>';
            })
            ->rawColumns(['tgl_lunas_fmt', 'status_badge', 'action'])
            ->toJson();
    }

    public function store(Request $request)
    {
        $data = $this->validateInvoice($request);
        $data['user_id'] = auth('tenant')->id();

        if ($data['status'] === TenantInvoice::STATUS_PAID && empty($data['tgl_lunas'])) {
            $data['tgl_lunas'] = now()->toDateString();
        }

        TenantInvoice::create($data);

        // Status tagihan tenant berubah → cache status tagihan harus di-refresh.
        TenantInvoiceStatus::flush((string) $data['tenant_id']);

        return redirect()->route('tenant.invoice.index')
            ->with('success', 'Invoice berhasil dibuat.');
    }

    public function print(TenantInvoice $invoice)
    {
        $invoice->load('user');

        $pdf = Pdf::loadView('tenant.invoice.print', $this->printData($invoice));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('invoice-'.$invoice->id.'.pdf');
    }

    public function destroy(TenantInvoice $invoice)
    {
        $tenantId = (string) $invoice->tenant_id;

        $invoice->delete();

        TenantInvoiceStatus::flush($tenantId);

        return redirect()->route('tenant.invoice.index')
            ->with('success', 'Invoice dihapus.');
    }

    /**
     * Data yang dibutuhkan template PDF (dipakai juga PengaturanController).
     */
    public function printData(TenantInvoice $invoice): array
    {
        $data = ['invoice' => $invoice];

        foreach (['abt_logo.png' => 'logo', 'lunas.png' => 'lunas', 'ttd.png' => 'ttd'] as $file => $key) {
            $path = public_path('assets/logo/'.$file);
            if (file_exists($path)) {
                $data[$key] = base64_encode(file_get_contents($path));
            }
        }

        $data['tenant_nama'] = $this->tenantMap()[(string) $invoice->tenant_id] ?? ($invoice->tenant_id ?? '-');

        return $data;
    }

    private function validateInvoice(Request $request): array
    {
        $data = $request->validate([
            'jenis_pembayaran' => ['required', 'string', Rule::in(TenantInvoice::JENIS_PEMBAYARAN)],
            'tenant_id' => ['required', 'string', 'exists:tenants,id'],
            'tgl_invoice' => ['required', 'date'],
            'tgl_lunas' => ['nullable', 'date', 'after_or_equal:tgl_invoice'],
            'status' => ['required', Rule::in([TenantInvoice::STATUS_PAID, TenantInvoice::STATUS_UNPAID])],
            'jumlah' => ['required', 'numeric', 'min:0'],
        ], [
            'tenant_id.required' => 'Pilih sekolah tujuan invoice terlebih dahulu.',
            'tenant_id.exists' => 'Sekolah yang dipilih tidak valid.',
            'jenis_pembayaran.in' => 'Jenis pembayaran yang dipilih tidak valid.',
            'tgl_lunas.after_or_equal' => 'Tanggal jatuh tempo tidak boleh lebih awal dari tanggal invoice.',
        ]);

        foreach (['tgl_lunas'] as $field) {
            if (isset($data[$field]) && $data[$field] === '') {
                $data[$field] = null;
            }
        }

        return $data;
    }

    private function currentTenantId(Request $request): ?string
    {
        return $request->attributes->get('current_tenant_id');
    }

    /**
     * Nilai filter status (Lunas / Belum Lunas).
     *
     * Hanya `paid` dan `unpaid` yang valid; nilai lain (termasuk string
     * kosong dan sentinel `__all__`) berarti "tanpa filter" sehingga semua
     * pembayaran tetap tampil.
     */
    private function statusFilter(Request $request): string
    {
        $value = (string) $request->input('status_filter', '');

        return in_array($value, [TenantInvoice::STATUS_PAID, TenantInvoice::STATUS_UNPAID], true)
            ? $value
            : '';
    }

    /**
     * Daftar rekening kas/bank yang dipakai untuk mencatat pembayaran.
     */
    private function rekenings(): array
    {
        return Cache::remember('tenant_invoice_rekenings', 600, function () {
            return \App\Models\Tenant\TenantRekening::query()
                ->orderBy('nama_rekening')
                ->get(['kd_rekening', 'nama_rekening'])
                ->map(fn ($r) => [
                    'kode' => $r->kd_rekening,
                    'nama' => $r->nama_rekening,
                ])
                ->all();
        });
    }

    private function tenantMap(): array
    {
        return Cache::remember('tenant_map_simple', 600, function () {
            $model = config('tenancy.tenant_model');
            return $model::query()
                ->select('id', 'nama_sekolah')
                ->get()
                ->mapWithKeys(function ($t) {
                    $nama = $t->nama_sekolah ?? $t->id;
                    return [(string) $t->id => $nama];
                })
                ->all();
        });
    }
}
