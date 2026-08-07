<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');

        $tenants = Tenant::query()
            ->with('domains')
            ->when($q, function ($query) use ($q) {
                $query->where('id', 'like', "%{$q}%")
                    ->orWhere('nama_sekolah', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            })
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('tenant.tenant.index', [
            'tenants' => $tenants,
            'q' => $q,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id' => ['required', 'string', 'max:64', Rule::unique('tenants', 'id')],
            'nama_sekolah' => ['required', 'string', 'max:191'],
            'domain' => ['required', 'string', 'max:191', 'regex:/^[a-z0-9.-]+$/i', Rule::unique('domains', 'domain')],
            'email' => ['nullable', 'email', 'max:191'],
        ]);

        $tenant = Tenant::create([
            'id' => $data['id'],
            'nama_sekolah' => $data['nama_sekolah'],
            'email' => $data['email'] ?? null,
        ]);

        $tenant->domains()->create([
            'domain' => $data['domain'],
        ]);

        return redirect()->route('tenant.tenant.index')
            ->with('success', "Tenant {$tenant->id} berhasil dibuat.");
    }

    public function update(Request $request, Tenant $tenant)
    {
        $data = $request->validate([
            'nama_sekolah' => ['required', 'string', 'max:191'],
            'email' => ['nullable', 'email', 'max:191'],
            'domain' => ['required', 'string', 'max:191', 'regex:/^[a-z0-9.-]+$/i'],
        ]);

        $tenant->nama_sekolah = $data['nama_sekolah'];
        $tenant->email = $data['email'] ?? null;
        $tenant->save();

        $first = $tenant->domains()->first();
        if ($first) {
            $first->update(['domain' => $data['domain']]);
        } else {
            $tenant->domains()->create(['domain' => $data['domain']]);
        }

        return redirect()->route('tenant.tenant.index')
            ->with('success', "Tenant {$tenant->id} berhasil diperbarui.");
    }

    public function destroy(Tenant $tenant)
    {
        $id = $tenant->id;

        foreach ($tenant->domains as $domain) {
            $domain->delete();
        }

        $tenant->delete();

        return redirect()->route('tenant.tenant.index')
            ->with('success', "Tenant {$id} berhasil dihapus.");
    }
}
