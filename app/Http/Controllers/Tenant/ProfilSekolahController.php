<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Tenant\BaseSchoolController;

use App\Models\Profil;
use Illuminate\Http\Request;
use Stancl\Tenancy\Database\Models\Tenant;

class ProfilSekolahController extends BaseSchoolController
{
    public function index(Tenant $tenant)
    {
        return $this->runInTenant($tenant, function () use ($tenant) {
            $profil = Profil::first();

            return view('tenant.tenant.profil', [
                'tenant' => $tenant,
                'profil' => $profil,
            ]);
        });
    }

    public function update(Request $request, Tenant $tenant)
    {
        return $this->runInTenant($tenant, function () use ($request, $tenant) {
            $data = $request->validate([
                'nama'        => ['required', 'string', 'max:191'],
                'alamat'      => ['nullable', 'string'],
                'telpon'      => ['nullable', 'string', 'max:30'],
                'email'       => ['nullable', 'email', 'max:191'],
                'jatuh_tempo' => ['nullable', 'integer', 'min:1', 'max:31'],
            ]);

            $profil = Profil::first();
            if (! $profil) {
                $profil = new Profil();
            }
            $profil->fill($data);
            $profil->save();

            return redirect()->route('tenant.tenant.profil.index', $tenant)
                ->with('success', "Profil sekolah tenant {$tenant->id} berhasil diperbarui");
        });
    }
}


