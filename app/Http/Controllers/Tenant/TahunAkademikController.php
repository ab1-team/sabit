<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Tenant\BaseSchoolController;

use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use App\Models\Tenant;

class TahunAkademikController extends BaseSchoolController
{
    public function index(Tenant $tenant)
    {
        return $this->runInTenant($tenant, function () use ($tenant) {
            $items = TahunAkademik::orderByDesc('id')->get();

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

            $ta = TahunAkademik::create($data);

            if ($data['status'] === 'aktif') {
                $ta->aktifkan();
            }

            return redirect()->route('tenant.tenant.tahun-akademik.index', $tenant)
                ->with('success', "Tahun akademik {$ta->nama_tahun} ditambah");
        });
    }

    public function update(Request $request, Tenant $tenant, TahunAkademik $TahunAkademik)
    {
        return $this->runInTenant($tenant, function () use ($request, $TahunAkademik, $tenant) {
            $data = $request->validate([
                'nama_tahun' => ['required', 'string', 'max:30'],
                'keterangan' => ['nullable', 'string', 'max:191'],
                'status'     => ['required', 'in:aktif,nonaktif'],
            ]);

            $TahunAkademik->update($data);
            if ($data['status'] === 'aktif') {
                $TahunAkademik->aktifkan();
            }

            return redirect()->route('tenant.tenant.tahun-akademik.index', $tenant)
                ->with('success', "Tahun akademik {$TahunAkademik->nama_tahun} diperbarui");
        });
    }

    public function destroy(Tenant $tenant, TahunAkademik $TahunAkademik)
    {
        return $this->runInTenant($tenant, function () use ($TahunAkademik, $tenant) {
            $nama = $TahunAkademik->nama_tahun;
            $TahunAkademik->delete();

            return redirect()->route('tenant.tenant.tahun-akademik.index', $tenant)
                ->with('success', "Tahun akademik {$nama} dihapus");
        });
    }

    public function aktifkan(Tenant $tenant, TahunAkademik $TahunAkademik)
    {
        return $this->runInTenant($tenant, function () use ($TahunAkademik, $tenant) {
            $TahunAkademik->aktifkan();

            return redirect()->route('tenant.tenant.tahun-akademik.index', $tenant)
                ->with('success', "Tahun akademik {$TahunAkademik->nama_tahun} diaktifkan");
        });
    }
}



