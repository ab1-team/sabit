<?php

namespace App\Http\Controllers\Tenant;

use App\Models\Tahun_Akademik;
use Illuminate\Http\Request;
use Stancl\Tenancy\Database\Models\Tenant;

class TahunAkademikController extends BaseSchoolController
{
    public function index(Tenant $tenant)
    {
        return $this->runInTenant($tenant, function () use ($tenant) {
            $items = Tahun_Akademik::orderByDesc('id')->get();

            return view('tenant.tenant.tahun-akademik', [
                'tenant' => $tenant,
                'items'  => $items,
            ]);
        });
    }

    public function store(Request $request, Tenant $tenant)
    {
        return $this->runInTenant($tenant, function () use ($request, $tenant) {
            $data = $request->validate([
                'nama_tahun' => ['required', 'string', 'max:30'],
                'keterangan' => ['nullable', 'string', 'max:191'],
                'status'     => ['required', 'in:aktif,nonaktif'],
            ]);

            $ta = Tahun_Akademik::create($data);

            if ($data['status'] === 'aktif') {
                $ta->aktifkan();
            }

            return redirect()->route('tenant.tenant.tahun-akademik.index', $tenant)
                ->with('success', "Tahun akademik {$ta->nama_tahun} ditambah");
        });
    }

    public function update(Request $request, Tenant $tenant, Tahun_Akademik $tahun_akademik)
    {
        return $this->runInTenant($tenant, function () use ($request, $tahun_akademik, $tenant) {
            $data = $request->validate([
                'nama_tahun' => ['required', 'string', 'max:30'],
                'keterangan' => ['nullable', 'string', 'max:191'],
                'status'     => ['required', 'in:aktif,nonaktif'],
            ]);

            $tahun_akademik->update($data);
            if ($data['status'] === 'aktif') {
                $tahun_akademik->aktifkan();
            }

            return redirect()->route('tenant.tenant.tahun-akademik.index', $tenant)
                ->with('success', "Tahun akademik diperbarui");
        });
    }

    public function destroy(Tenant $tenant, Tahun_Akademik $tahun_akademik)
    {
        return $this->runInTenant($tenant, function () use ($tahun_akademik, $tenant) {
            $nama = $tahun_akademik->nama_tahun;
            $tahun_akademik->delete();

            return redirect()->route('tenant.tenant.tahun-akademik.index', $tenant)
                ->with('success', "Tahun akademik {$nama} dihapus");
        });
    }

    public function aktifkan(Tenant $tenant, Tahun_Akademik $tahun_akademik)
    {
        return $this->runInTenant($tenant, function () use ($tahun_akademik, $tenant) {
            $tahun_akademik->aktifkan();

            return redirect()->route('tenant.tenant.tahun-akademik.index', $tenant)
                ->with('success', "Tahun akademik {$tahun_akademik->nama_tahun} diaktifkan");
        });
    }
}
