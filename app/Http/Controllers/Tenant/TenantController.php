<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
            'domain' => ['required', 'string', 'max:191', 'regex:/^[a-z0-9.-]+$/i'],
            'email' => ['nullable', 'email', 'max:191'],
        ]);

        $landingDomain = strtolower($data['domain']);
        $adminDomain = $this->adminDomainFor($landingDomain);

        // Kedua domain harus divalidasi sebelum tenant dibuat, agar tidak
        // meninggalkan tenant setengah jadi bila salah satu domain sudah dipakai.
        $conflict = Domain::whereIn('domain', [$landingDomain, $adminDomain])->pluck('domain');

        if ($conflict->isNotEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['domain' => 'Domain sudah digunakan: ' . $conflict->implode(', ')]);
        }

        $tenant = DB::connection(config('tenancy.database.central_connection'))
            ->transaction(function () use ($data, $landingDomain, $adminDomain) {
                $tenant = Tenant::create([
                    'id' => $data['id'],
                    'nama_sekolah' => $data['nama_sekolah'],
                    'email' => $data['email'] ?? null,
                    // Auto-generate nama DB tenant: <prefix><id-sanitized>
                    // Samakan dengan generator di AppServiceProvider agar tidak error "Database ... does not exist".
                    'tenancy_db_name' => config('tenancy.database.prefix')
                        . preg_replace('/[^A-Za-z0-9_]/', '_', $data['id']),
                ]);

                // Landing page publik: sma1.example.test
                $tenant->domains()->create([
                    'domain' => $landingDomain,
                    'type' => Domain::TYPE_LANDING,
                ]);

                // Panel admin: admin-sma1.example.test
                $tenant->domains()->create([
                    'domain' => $adminDomain,
                    'type' => Domain::TYPE_ADMIN,
                ]);

                return $tenant;
            });

        return redirect()->route('tenant.tenant.index')
            ->with('success', "Tenant {$tenant->id} berhasil dibuat. Landing: {$landingDomain} | Admin: {$adminDomain}");
    }

    public function update(Request $request, Tenant $tenant)
    {
        $data = $request->validate([
            'nama_sekolah' => ['required', 'string', 'max:191'],
            'email' => ['nullable', 'email', 'max:191'],
            'domain' => ['required', 'string', 'max:191', 'regex:/^[a-z0-9.-]+$/i'],
        ]);

        $landingDomain = strtolower($data['domain']);
        $adminDomain = $this->adminDomainFor($landingDomain);

        $conflict = Domain::whereIn('domain', [$landingDomain, $adminDomain])
            ->where('tenant_id', '!=', $tenant->id)
            ->pluck('domain');

        if ($conflict->isNotEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['domain' => 'Domain sudah digunakan tenant lain: ' . $conflict->implode(', ')]);
        }

        DB::connection(config('tenancy.database.central_connection'))
            ->transaction(function () use ($tenant, $data, $landingDomain, $adminDomain) {
                $tenant->nama_sekolah = $data['nama_sekolah'];
                $tenant->email = $data['email'] ?? null;
                $tenant->save();

                // Update per tipe secara eksplisit. Memakai domains()->first()
                // tidak aman karena tenant kini punya lebih dari satu domain.
                $this->syncDomain($tenant, Domain::TYPE_LANDING, $landingDomain);
                $this->syncDomain($tenant, Domain::TYPE_ADMIN, $adminDomain);
            });

        return redirect()->route('tenant.tenant.index')
            ->with('success', "Tenant {$tenant->id} berhasil diperbarui.");
    }

    public function destroy(Tenant $tenant)
    {
        $id = $tenant->id;
        $storagePath = storage_path('app/public/tenant/' . $id);

        foreach ($tenant->domains as $domain) {
            $domain->delete();
        }

        $tenant->delete();

        if (is_dir($storagePath)) {
            Storage::disk('public')->deleteDirectory('tenant/' . $id);
        }

        return redirect()->route('tenant.tenant.index')
            ->with('success', "Tenant {$id} berhasil dihapus.");
    }

    /**
     * Turunkan domain admin dari domain landing dengan prefix "admin-".
     * Contoh: sma1.example.test => admin-sma1.example.test
     *
     * Prefix (bukan sub-level "admin.sma1") dipilih agar tetap satu level
     * subdomain sehingga tercakup wildcard DNS/SSL *.example.test.
     */
    private function adminDomainFor(string $landingDomain): string
    {
        return 'admin-' . $landingDomain;
    }

    private function syncDomain(Tenant $tenant, string $type, string $domain): void
    {
        $existing = $tenant->domains()->where('type', $type)->first();

        if ($existing) {
            if ($existing->domain !== $domain) {
                $existing->update(['domain' => $domain]);
            }

            return;
        }

        $tenant->domains()->create([
            'domain' => $domain,
            'type' => $type,
        ]);
    }
}
