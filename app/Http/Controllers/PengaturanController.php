<?php

namespace App\Http\Controllers;

use App\Models\AdminInvoice;
use App\Models\AkunLevel1;
use App\Models\Profil;
use App\Models\Tanda_tangan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengaturanController extends Controller
{
    public function sop()
    {
        $profil = Profil::first();

        $title = 'Personalisasi SOP';

        return view('pengaturan.index', compact('title', 'profil'));
    }

    public function coa()
    {
        $title = 'Chart Of Account (CoA)';
        $akun1 = AkunLevel1::with([
            'akun2',
            'akun2.akun3',
            'akun2.akun3.rek',
        ])->get();

        return view('pengaturan.coa')->with(compact('title', 'akun1'));
    }

    public function ttdPelaporan()
    {
        $title = 'Pengaturan Tanda Tangan Pelaporan';

        if (! Tanda_tangan::first()) {
            Tanda_tangan::create([
                'tanda_tangan' => '<table class="p0" border="0" width="100%" cellspacing="0" cellpadding="0" style="font-size: 11px;">
<tbody>
<tr>
<td style="width: 33.3333%;">&nbsp;</td>
<td style="width: 33.3333%;">&nbsp;</td>
<td style="width: 33.3333%; text-align: center;">{tanggal}</td>
</tr>
</tbody>
<tbody>
<tr>
<td style="text-align: center;">Diperiksa Oleh</td>
<td style="text-align: center;">Diketahui</td>
<td style="text-align: center;">Dilaporkan</td>
</tr>
<tr>
<td style="text-align: center;">
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</td>
<td style="text-align: center;">&nbsp;</td>
<td style="text-align: center;">&nbsp;</td>
</tr>
<tr>
<td style="text-align: center;">..........rrr.....rrr.............</td>
<td style="text-align: center;">...............................................</td>
<td style="text-align: center;"><strong>......................................</strong></td>
</tr>
<tr>
<td style="text-align: center;"><strong>Badan Pengawas</strong></td>
<td style="text-align: center;"><strong>Manager DBM</strong></td>
<td style="text-align: center;"><strong>Bendahara</strong></td>
</tr>
<tr>
<td style="text-align: center;">Disetujui Oleh</td>
<td style="text-align: center;" colspan="2">&nbsp;</td>
</tr>
<tr>
<td style="text-align: center;">
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</td>
<td style="text-align: center;">&nbsp;</td>
<td style="text-align: center;">&nbsp;</td>
</tr>
<tr>
<td style="text-align: center;"><strong>......................................</strong></td>
<td style="text-align: center;" colspan="2">&nbsp;</td>
</tr>
<tr>
<td style="text-align: center;"><strong>Direktur</strong></td>
<td style="text-align: center;" colspan="2">&nbsp;</td>
</tr>
</tbody>
</table>',
            ]);
        }

        $ttd = Tanda_tangan::first();

        $tanggal = false;
        if ($ttd) {
            $str = strpos($ttd->tanda_tangan, '{tanggal}');

            if ($str !== false) {
                $tanggal = true;
            }
        }

        return view('pengaturan.tanda_tangan')->with(compact('title', 'ttd', 'tanggal'));
    }

    public function ttdPelaporanStore(Request $request)
    {
        $request->validate([
            'tanda_tangan' => 'required',
        ]);

        $ttd = Tanda_tangan::first();

        $data = [
            'tanda_tangan' => $request->tanda_tangan,
        ];

        if ($ttd) {
            $ttd->update($data);
        } else {
            Tanda_tangan::create($data);
        }

        return response()->json([
            'success' => true,
            'msg' => 'Tanda tangan pelaporan berhasil disimpan',
        ]);
    }

    public function lembaga(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
            'alamat' => 'required',
            'telpon' => 'required',
            'penanggung_jawab' => 'required',
        ]);
        Profil::findOrFail($id)->update($request->all());
        Profil::flushCache();

        return response()->json([
            'success' => true,
            'msg' => 'Update Lembaga berhasil diproses!',

        ]);
    }

    public function logo(Request $request, $id)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'logo.required' => 'Pilih file logo terlebih dahulu.',
            'logo.image' => 'File harus berupa gambar.',
            'logo.mimes' => 'Format logo harus JPG, JPEG, PNG, atau WebP.',
            'logo.max' => 'Ukuran logo maksimal 2MB.',
        ]);

        $profil = Profil::findOrFail($id);

        $disk = Storage::disk('public');
        $dir = $disk->path('logo');
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        if ($profil->logo && $disk->exists('logo/'.$profil->logo)) {
            $disk->delete('logo/'.$profil->logo);
        }

        $file = $request->file('logo');
        $ext = strtolower($file->getClientOriginalExtension() ?: $file->extension());
        $name = 'logo_' . $profil->id . '_' . time() . '.' . $ext;
        $file->storeAs('logo', $name, 'public');

        $profil->update(['logo' => $name]);
        Profil::flushCache();

        return response()->json([
            'success' => true,
            'msg' => 'Logo berhasil diperbarui',
            'logo' => $disk->url('logo/'.$name),
        ]);
    }

    public function jatuhTempo(Request $request, $id)
    {
        $request->validate([
            'jatuh_tempo' => 'required|integer|min:0',
        ]);
        Profil::findOrFail($id)->update($request->all());

        return response()->json([
            'success' => true,
            'msg' => 'Update Jatuh Tempo berhasil diproses!',
        ]);
    }

    public function sopPembayaran(Request $request, $id)
    {
        $request->validate([
            'cetak_pts' => 'required|integer|min:0|max:12',
            'cetak_pas' => 'required|integer|min:0|max:12',
        ]);

        Profil::findOrFail($id)->update($request->only(['cetak_pts', 'cetak_pas']));

        return response()->json([
            'success' => true,
            'msg' => 'SOP Pembayaran berhasil diperbarui!',
        ]);
    }

    public function invoice(Request $request)
    {
        $title = 'Daftar Invoice';

        if ($request->ajax()) {
            $tenantId = $this->currentTenantId($request);

            $data = AdminInvoice::query()
                ->when($tenantId, fn ($q) => $q->where('admin_invoice.tenant_id', $tenantId))
                ->latest('tgl_invoice')
                ->latest('id');

            return \Yajra\DataTables\Facades\DataTables::eloquent($data)
                ->addIndexColumn()
                ->addColumn('tgl_invoice_fmt', fn ($row) => $row->tgl_invoice?->format('d/m/Y') ?? '—')
                ->addColumn('tgl_lunas_fmt', fn ($row) => $row->tgl_lunas?->format('d/m/Y') ?? '—')
                ->addColumn('status_badge', function ($row) {
                    $badge = match ($row->status) {
                        'paid'   => 'success',
                        'unpaid' => 'warning',
                        default  => 'secondary',
                    };
                    return '<span class="badge bg-'.$badge.'">'.ucfirst($row->status ?? '—').'</span>';
                })
                ->addColumn('jumlah_fmt', fn ($row) => 'Rp '.number_format((float) $row->jumlah, 0, ',', '.'))
                ->addColumn('aksi', function ($row) {
                    $url = route('app.pengaturan.invoice.print', $row->id);
                    return '<a href="'.$url.'" target="_blank" class="btn btn-sm btn-outline-secondary">'
                        .'<i class="material-icons align-middle" style="font-size:18px;">picture_as_pdf</i></a>';
                })
                ->addColumn('print_url', fn ($row) => route('app.pengaturan.invoice.print', $row->id))
                ->rawColumns(['status_badge', 'aksi'])
                ->toJson();
        }

        return view('pengaturan.invoice', [
            'title' => $title,
            'currentTenantId' => $this->currentTenantId($request),
        ]);
    }

    public function invoicePrint(Request $request, AdminInvoice $invoice)
    {
        $tenantId = $this->currentTenantId($request);

        if ($tenantId && (string) $invoice->tenant_id !== (string) $tenantId) {
            abort(404);
        }

        $invoice->load('user');
        $logoPath = public_path('assets/logo/abt_logo.png');
        $data = ['invoice' => $invoice];
        if (file_exists($logoPath)) {
            $data['logo'] = base64_encode(file_get_contents($logoPath));
            $data['logo_type'] = 'png';
        }
        $pdf = Pdf::loadView('tenant.invoice.print', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('invoice-'.$invoice->id.'.pdf');
    }

    private function currentTenantId(Request $request): ?string
    {
        $tid = $request->attributes->get('current_tenant_id');

        return $tid ? (string) $tid : null;
    }
}
