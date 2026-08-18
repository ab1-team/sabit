<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tenant\BaseSchoolController;
use App\Models\Domain;
use App\Models\Tenant;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class TenantController extends BaseSchoolController
{
public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->data($request);
        }

        return view('tenant.tenant.index');
    }

    public function data(Request $request)
    {
        $query = Tenant::query()->with('domains');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('id', fn ($row) => '<span class="font-mono">'.e($row->id).'</span>')
            ->addColumn('nama_sekolah', fn ($row) => '<span class="font-semibold text-slate-800">'.e($row->nama_sekolah ?? '—').'</span>')
            ->addColumn('domain_landing_html', function ($row) {
                $landing = optional($row->landingDomain())->domain;
                if (! $landing) {
                    return '<span class="text-xs text-slate-400">—</span>';
                }
                return '<a href="http://'.e($landing).'" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 hover:bg-indigo-100">
                            <span class="font-mono">'.e($landing).'</span>
                            <svg class="h-3 w-3 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>';
            })
            ->addColumn('domain_admin_html', function ($row) {
                $admin = optional($row->adminDomain())->domain;
                if (! $admin) {
                    return '<span class="text-xs text-slate-400">—</span>';
                }
                return '<a href="http://'.e($admin).'" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 rounded-md bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700 hover:bg-amber-100">
                            <span class="font-mono">'.e($admin).'</span>
                            <svg class="h-3 w-3 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>';
            })
            ->addColumn('action', function ($row) {
                $detailUrl = '#';
                $editUrl = route('tenant.tenant.update', $row);
                $deleteUrl = route('tenant.tenant.destroy', $row);
                $manageUrl = route('tenant.tenant.profil.index', $row);
                $landing = optional($row->landingDomain())->domain ?? '—';
                $admin = optional($row->adminDomain())->domain ?? '—';

                return '<div class="inline-flex items-center gap-1">
                    <button type="button" class="open-detail-modal inline-flex items-center rounded-lg bg-slate-100 px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-200" title="Detail"
                        data-id="'.e($row->id).'"
                        data-nama="'.e($row->nama_sekolah ?? '—').'"
                        data-domain-landing="'.e($landing).'"
                        data-domain-admin="'.e($admin).'"
                        data-email="'.e($row->email ?? '—').'"
                        data-db="tenant'.e($row->id).'"
                        data-created="'.e(optional($row->created_at)->format('d/m/Y H:i') ?? '—').'">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </button>
                    <a href="'.$manageUrl.'" class="inline-flex items-center rounded-lg bg-indigo-100 px-2.5 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-200" title="Kelola">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </a>
                    <button type="button" class="open-edit-modal inline-flex items-center rounded-lg bg-amber-100 px-2.5 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-200" title="Ubah"
                        data-edit-url="'.$editUrl.'"
                        data-id="'.e($row->id).'"
                        data-nama="'.e($row->nama_sekolah ?? '').'"
                        data-domain-landing="'.e(optional($row->landingDomain())->domain ?? '').'"
                        data-domain-admin="'.e(optional($row->adminDomain())->domain ?? '').'"
                        data-email="'.e($row->email ?? '').'">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                    </button>
                    <button type="button" class="delete-tenant inline-flex items-center rounded-lg bg-rose-100 px-2.5 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-200" title="Hapus" data-action="'.$deleteUrl.'" data-name="'.e($row->id).'">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 0.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 0 00-7.5 0"/></svg>
                    </button>
                </div>';
            })
            ->rawColumns(['id', 'nama_sekolah', 'domain_landing_html', 'domain_admin_html', 'action'])
            ->orderColumn('id', 'tenants.id $1')
            ->orderColumn('nama_sekolah', 'tenants.nama_sekolah $1')
            ->filterColumn('nama_sekolah', function ($q, $kw) {
                $q->where('nama_sekolah', 'like', "%{$kw}%");
            })
            ->filterColumn('id', function ($q, $kw) {
                $q->where('tenants.id', 'like', "{$kw}%");
            })
            ->filterColumn('domain_landing_html', function ($q, $kw) {
                $q->whereHas('domains', fn ($qq) => $qq->where('type', Domain::TYPE_LANDING)->where('domain', 'like', "%{$kw}%"));
            })
            ->filterColumn('domain_admin_html', function ($q, $kw) {
                $q->whereHas('domains', fn ($qq) => $qq->where('type', Domain::TYPE_ADMIN)->where('domain', 'like', "%{$kw}%"));
            })
            ->toJson();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id' => ['required', 'string', 'max:64', Rule::unique('tenants', 'id')],
            'nama_sekolah' => ['required', 'string', 'max:191'],
            'domain_landing' => ['required', 'string', 'max:191', 'regex:/^[a-z0-9.-]+$/i'],
            'domain_admin' => ['required', 'string', 'max:191', 'regex:/^[a-z0-9.-]+$/i', 'different:domain_landing'],
            'email' => ['nullable', 'email', 'max:191'],
        ], [
            'domain_admin.different' => 'Domain admin dan domain landing harus berbeda.',
        ]);

        $landingDomain = strtolower($data['domain_landing']);
        $adminDomain = strtolower($data['domain_admin']);

        // Kedua domain harus divalidasi sebelum tenant dibuat, agar tidak
        // meninggalkan tenant setengah jadi bila salah satu domain sudah dipakai.
        $conflict = Domain::whereIn('domain', [$landingDomain, $adminDomain])->pluck('domain');

        if ($conflict->isNotEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['domain_landing' => 'Domain sudah digunakan: ' . $conflict->implode(', ')]);
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

                // Landing page publik
                $tenant->domains()->create([
                    'domain' => $landingDomain,
                    'type' => Domain::TYPE_LANDING,
                ]);

                // Panel admin
                $tenant->domains()->create([
                    'domain' => $adminDomain,
                    'type' => Domain::TYPE_ADMIN,
                ]);

                return $tenant;
            });

        return redirect()->route('tenant.tenant.index')
            ->with('success', "Tenant {$tenant->id} berhasil dibuat. Landing: {$landingDomain} | Admin: {$adminDomain}");
    }

    public function show(Tenant $tenant)
    {
        return redirect()->route('tenant.tenant.profil.index', $tenant);
    }

    public function update(Request $request, Tenant $tenant)
    {
        $data = $request->validate([
            'nama_sekolah' => ['required', 'string', 'max:191'],
            'email' => ['nullable', 'email', 'max:191'],
            'domain_landing' => ['required', 'string', 'max:191', 'regex:/^[a-z0-9.-]+$/i'],
            'domain_admin' => ['required', 'string', 'max:191', 'regex:/^[a-z0-9.-]+$/i', 'different:domain_landing'],
        ], [
            'domain_admin.different' => 'Domain admin dan domain landing harus berbeda.',
        ]);

        $landingDomain = strtolower($data['domain_landing']);
        $adminDomain = strtolower($data['domain_admin']);

        $conflict = Domain::whereIn('domain', [$landingDomain, $adminDomain])
            ->where('tenant_id', '!=', $tenant->id)
            ->pluck('domain');

        if ($conflict->isNotEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['domain_landing' => 'Domain sudah digunakan tenant lain: ' . $conflict->implode(', ')]);
        }

        DB::connection(config('tenancy.database.central_connection'))
            ->transaction(function () use ($tenant, $data, $landingDomain, $adminDomain) {
                $tenant->nama_sekolah = $data['nama_sekolah'];
                $tenant->email = $data['email'] ?? null;
                $tenant->save();

                // Update per tipe secara eksplisit.
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

