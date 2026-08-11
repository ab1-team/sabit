<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Tenant\BaseSchoolController;

use App\Models\JenisPembayaran;
use Illuminate\Http\Request;
use Stancl\Tenancy\Database\Models\Tenant;

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

            $jenis_pembayaran->update($data);

            return redirect()->route('tenant.tenant.jenis-pembayaran.index', $tenant)
                ->with('success', "Jenis Pembayaran diperbarui");
        });
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


