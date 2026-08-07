<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class HakAksesPusatController extends Controller
{
    public function index(Request $request)
    {
        $tenants = Tenant::query()
            ->with('domains')
            ->orderBy('id')
            ->get();

        $perTenant = [];
        $allMenus = null;
        $firstTenant = null;

        foreach ($tenants as $t) {
            $snapshot = null;
            $dbStatus = 'ok';
            try {
                $snapshot = $this->runInTenant($t, function () {
                    $menus = DB::table('menu')
                        ->where('status', 'aktif')
                        ->orderBy('group')
                        ->orderBy('urutan')
                        ->get();

                    $byId = $menus->keyBy('id');
                    $menus = $menus->map(function ($m) use ($byId) {
                        if (is_null($m->group) && $m->parent_id && isset($byId[$m->parent_id])) {
                            $m->group = $byId[$m->parent_id]->group;
                        }

                        return $m;
                    });

                    $grouped = $menus->groupBy(fn ($m) => $m->group ?: 'Lainnya')->map(function ($items) {
                        return [
                            'parents' => $items->whereNull('parent_id')->values(),
                            'children' => $items->whereNotNull('parent_id')->groupBy('parent_id'),
                        ];
                    });

                    $users = User::orderBy('nama')->get();

                    return [
                        'grouped' => $grouped,
                        'users' => $users->map(function ($u) {
                            return [
                                'id' => $u->id,
                                'nama' => $u->nama,
                                'username' => $u->username,
                                'email' => $u->email,
                                'telepon' => $u->telepon,
                                'jabatan' => $u->jabatan,
                                'hak_akses' => collect($u->hak_akses ?? [])->map(fn ($v) => (int) $v)->all(),
                            ];
                        })->all(),
                    ];
                });
            } catch (\Throwable $e) {
                \Log::warning('HakAksesPusat: skip tenant', ['tenant' => $t->id, 'err' => $e->getMessage()]);
                $msg = $e->getMessage();
                $dbStatus = str_contains(strtolower($msg), 'users') || str_contains(strtolower($msg), 'menu')
                    ? 'no_tables'
                    : 'no_db';
            }

            $perTenant[$t->id] = array_merge(
                $snapshot ?? ['grouped' => collect(), 'users' => []],
                ['db_status' => $dbStatus]
            );
        }

        return view('tenant.hak-akses.index', [
            'tenants' => $tenants,
            'perTenant' => $perTenant,
            'menusByTenant' => $this->buildMenusByTenant($tenants),
        ]);
    }

    public function storeUser(Request $request, Tenant $tenant)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:120'],
            'username' => ['required', 'string', 'max:60', 'alpha_dash'],
            'email' => ['nullable', 'email', 'max:120'],
            'password' => ['required', 'string', 'min:6', 'max:60'],
            'telepon' => ['nullable', 'string', 'max:30'],
            'hak_akses' => ['nullable'],
        ]);

        return $this->runInTenant($tenant, function () use ($data, $tenant) {
            $exists = User::where('username', $data['username'])->exists();
            if ($exists) {
                return response()->json([
                    'ok' => false,
                    'message' => "Username {$data['username']} sudah dipakai di tenant {$tenant->id}.",
                ], 422);
            }

            $user = new User;
            $user->nama = $data['nama'];
            $user->username = $data['username'];
            $user->email = $data['email'] ?? null;
            $user->password = Hash::make($data['password']);
            $user->telepon = $data['telepon'] ?? null;
            $user->hak_akses = is_array($data['hak_akses'] ?? null)
                ? array_values(array_unique(array_map('intval', $data['hak_akses'])))
                : [];
            $user->save();

            return response()->json([
                'ok' => true,
                'user' => [
                    'id' => $user->id,
                    'nama' => $user->nama,
                    'username' => $user->username,
                ],
            ]);
        });
    }

    public function updateUser(Request $request, Tenant $tenant, User $user)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:120'],
            'telepon' => ['nullable', 'string', 'max:30'],
        ]);

        return $this->runInTenant($tenant, function () use ($data, $user) {
            $user->nama = $data['nama'];
            $user->email = $data['email'] ?? null;
            $user->telepon = $data['telepon'] ?? null;
            $user->save();

            return response()->json(['ok' => true]);
        });
    }

    public function updateHakAkses(Request $request, Tenant $tenant, User $user)
    {
        $menuIds = array_map('intval', (array) $request->input('menu_ids', []));

        return $this->runInTenant($tenant, function () use ($menuIds, $user) {
            $user->hak_akses = array_values(array_unique($menuIds));
            $user->save();

            return response()->json(['ok' => true, 'count' => count($user->hak_akses)]);
        });
    }

    public function destroyUser(Tenant $tenant, User $user)
    {
        return $this->runInTenant($tenant, function () use ($user) {
            if ($user->username === 'admin') {
                return response()->json([
                    'ok' => false,
                    'message' => "User 'admin' bawaan tenant tidak boleh dihapus.",
                ], 422);
            }
            $username = $user->username;
            $user->delete();

            return response()->json(['ok' => true, 'username' => $username]);
        });
    }

    public function resetPassword(Request $request, Tenant $tenant, User $user)
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'min:6', 'max:60'],
        ]);

        return $this->runInTenant($tenant, function () use ($data, $user) {
            $user->password = Hash::make($data['password']);
            $user->save();

            return response()->json(['ok' => true]);
        });
    }

    private function tenantDbName(Tenant $tenant): string
    {
        $internal = $tenant->getInternal('db_name');
        if (! empty($internal)) {
            return $internal;
        }

        $raw = $tenant->getAttributes()['data'] ?? null;
        $data = is_string($raw) ? json_decode($raw, true) : (array) ($raw ?? []);
        if (! empty($data['tenancy_db_name'])) {
            return $data['tenancy_db_name'];
        }

        $prefix = config('tenancy.database.prefix', 'sinkrone_sabit_');
        $suffix = config('tenancy.database.suffix', '');

        return $prefix.$tenant->id.$suffix;
    }

    private function runInTenant(Tenant $tenant, callable $callback)
    {
        $connName = 'tenant_pusat_'.$tenant->id;

        $base = Config::get('database.connections.tenant_template');
        if (! $base) {
            throw new \RuntimeException('Connection tenant_template tidak ada di config/database.php');
        }

        Config::set("database.connections.{$connName}", array_merge($base, [
            'database' => $this->tenantDbName($tenant),
        ]));

        // Purge cache connection agar setting database baru dipakai
        DB::purge($connName);
        Config::set('database.default', $connName);

        try {
            $result = $callback();

            // Reset model connection default ke central (aman untuk request lain)
            return $result;
        } finally {
            Config::set('database.default', config('database.connections.central') ? 'central' : 'mysql');
            DB::purge($connName);
        }
    }

    private function buildMenusByTenant($tenants): array
    {
        $out = [];
        foreach ($tenants as $t) {
            $items = [];
            try {
                $items = $this->runInTenant($t, function () {
                    $menus = DB::table('menu')
                        ->where('status', 'aktif')
                        ->orderBy('group')
                        ->orderBy('urutan')
                        ->get();

                    $byId = $menus->keyBy('id');
                    $items = [];
                    foreach ($menus as $m) {
                        $parentId = $m->parent_id ? (int) $m->parent_id : null;
                        $group = $m->group ?: ($parentId && isset($byId[$parentId]) ? $byId[$parentId]->group : null) ?: 'Lainnya';
                        $items[] = [
                            'id' => (int) $m->id,
                            'nama' => $m->nama_menu,
                            'parent_id' => $parentId,
                            'group' => $group,
                        ];
                    }

                    return $items;
                });
            } catch (\Throwable $e) {
                \Log::warning('HakAksesPusat: skip tenant menus', ['tenant' => $t->id, 'err' => $e->getMessage()]);
            }

            $items = $items ?? [];
            $tree = [];
            foreach ($items as $m) {
                if ($m['parent_id']) {
                    continue;
                }
                $tree[] = [
                    'id' => $m['id'],
                    'nama' => $m['nama'],
                    'group' => $m['group'],
                    'children' => collect($items)
                        ->where('parent_id', $m['id'])
                        ->map(fn ($c) => ['id' => $c['id'], 'nama' => $c['nama']])
                        ->values()
                        ->all(),
                ];
            }

            $out[$t->id] = $tree;
        }

        return $out;
    }
}
