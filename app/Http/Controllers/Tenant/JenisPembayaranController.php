<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Tenant\BaseSchoolController;

use App\Models\JenisPembayaran;
use Illuminate\Http\Request;
use App\Models\Tenant;

class JenisPembayaranController extends BaseSchoolController
{
    public function index(Tenant $tenant)
    {
        return $this->runInTenant($tenant, function () use ($tenant) {
            $items = JenisPembayaran::orderBy('nama')->get();

            return view('tenant.tenant.jenis-pembayaran', [
                'tenant' => $tenant,
                'items'  => $items,
            ]);
        });
    }

    public function store(Request $request, Tenant $tenant)
    {
        return $this->runInTenant($tenant, function () use ($request, $tenant) {
            $data = $request->validate([
                'nama'      => ['required', 'string', 'max:120'],
                'kode_akun' => ['nullable', 'string', 'max:30'],
                'jumlah'    => ['nullable', 'numeric', 'min:0'],
            ]);
            $data['jumlah'] = $this->normalizeJumlah($data['jumlah'] ?? null);

            $item = JenisPembayaran::create($data);

            return redirect()->route('tenant.tenant.jenis-pembayaran.index', $tenant)
                ->with('success', "Jenis Pembayaran {$item->nama} ditambah");
        });
    }

    public function update(Request $request, Tenant $tenant, JenisPembayaran $jenis_pembayaran)
    {
        return $this->runInTenant($tenant, function () use ($request, $jenis_pembayaran, $tenant) {
            $data = $request->validate([
                'nama'      => ['required', 'string', 'max:120'],
                'kode_akun' => ['nullable', 'string', 'max:30'],
                'jumlah'    => ['nullable', 'numeric', 'min:0'],
            ]);
            $data['jumlah'] = $this->normalizeJumlah($data['jumlah'] ?? null);

            $jenis_pembayaran->update($data);

            return redirect()->route('tenant.tenant.jenis-pembayaran.index', $tenant)
                ->with('success', "Jenis Pembayaran {$jenis_pembayaran->nama} diperbarui");
        });
    }

    /**
     * Normalisasi nilai jumlah: terima "1.500.000" (dari maskMoney) atau
     * "1500000" (native) dan kembalikan float.
     */
    protected function normalizeJumlah($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_string($value)) {
            $value = str_replace(['.', ','], ['', '.'], $value);
        }
        return (float) $value;
    }

    public function destroy(Tenant $tenant, JenisPembayaran $jenis_pembayaran)
    {
        return $this->runInTenant($tenant, function () use ($jenis_pembayaran, $tenant) {
            $nama = $jenis_pembayaran->nama;
            $jenis_pembayaran->delete();

            return redirect()->route('tenant.tenant.jenis-pembayaran.index', $tenant)
                ->with('success', "Jenis Pembayaran {$nama} dihapus");
        });
    }
}


