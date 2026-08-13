<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class HakAksesPusatController extends Controller
{
    private const SNAPSHOT_TTL = 600;

    public function index(Request $request)
    {
        $tenants = Tenant::query()
            ->with('domains')
            ->orderBy('id')
            ->get();

        $perTenant = [];

        foreach ($tenants as $t) {
            $snapshot = null;
            $dbStatus = 'ok';
            try {
                $snapshot = $this->getTenantSnapshot($t);
            } catch (\Throwable $e) {
                \Log::warning('HakAksesPusat: skip tenant', ['tenant' => $t->id, 'err' => $e->getMessage()]);
                $msg = $e->getMessage();
                $dbStatus = str_contains(strtolower($msg), 'users') || str_contains(strtolower($msg), 'menu')
                    ? 'no_tables'
                    : 'no_db';
            }

            $perTenant[$t->id] = array_merge(
                $snapshot ?? ['grouped' => collect(), 'users' => [], 'menus' => []],
                ['db_status' => $dbStatus]
            );
        }

        return view('tenant.hak-akses.index', [
            'tenants' => $tenants,
            'perTenant' => $perTenant,
            'menusByTenant' => $this->buildMenusByTenantFromSnapshot($tenants, $perTenant),
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
            $user->hak_akses = $this->normalizeMenuIds($data['hak_akses'] ?? []);
            $user->save();

            $this->flushTenantCache($tenant);

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

    public function updateUser(Request $request, Tenant $tenant, $userId)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:120'],
            'telepon' => ['nullable', 'string', 'max:30'],
        ]);

        return $this->runInTenant($tenant, function () use ($data, $userId, $tenant) {
            $user = User::find($userId);
            if (!$user) {
                return response()->json(['ok' => false, 'message' => 'User tidak ditemukan.'], 404);
            }
            $user->nama = $data['nama'];
            $user->email = $data['email'] ?? null;
            $user->telepon = $data['telepon'] ?? null;
            $user->save();

            $this->flushTenantCache($tenant);

            return response()->json(['ok' => true]);
        });
    }

    public function updateHakAkses(Request $request, Tenant $tenant, $userId)
    {
        $raw = $request->input('menu_ids', []);
        $menuIds = $this->normalizeMenuIds($raw);

        return $this->runInTenant($tenant, function () use ($menuIds, $userId, $tenant) {
            $user = User::find($userId);
            if (!$user) {
                return response()->json(['ok' => false, 'message' => 'User tidak ditemukan.'], 404);
            }
            $user->hak_akses = $menuIds;
            $user->save();

            $this->flushTenantCache($tenant);

            return response()->json(['ok' => true, 'count' => count($user->hak_akses)]);
        });
    }

    private function normalizeMenuIds($raw): array
    {
        if (is_string($raw)) {
            $raw = preg_split('/[,;|]/', $raw) ?: [];
        }
        $raw = (array) $raw;

        $ids = [];
        foreach ($raw as $v) {
            if (is_array($v)) {
                foreach ($v as $sub) {
                    foreach (preg_split('/[,;|]/', (string) $sub) ?: [] as $piece) {
                        $piece = trim($piece);
                        if ($piece !== '' && is_numeric($piece)) {
                            $ids[] = (int) $piece;
                        }
                    }
                }
            } else {
                foreach (preg_split('/[,;|]/', (string) $v) ?: [] as $piece) {
                    $piece = trim($piece);
                    if ($piece !== '' && is_numeric($piece)) {
                        $ids[] = (int) $piece;
                    }
                }
            }
        }

        return array_values(array_unique($ids));
    }

    private function flattenDescendants(int $parentId, $childrenMap, array &$visited = []): array
    {
        if (isset($visited[$parentId])) {
            return [];
        }
        $visited[$parentId] = true;

        $kids = $childrenMap->get($parentId, collect());
        $ids = [];
        foreach ($kids as $k) {
            $ids[] = (int) $k->id;
            $ids = array_merge($ids, $this->flattenDescendants((int) $k->id, $childrenMap, $visited));
        }
        return $ids;
    }

    public function destroyUser(Tenant $tenant, $userId)
    {
        return $this->runInTenant($tenant, function () use ($userId, $tenant) {
            $user = User::find($userId);
            if (!$user) {
                return response()->json(['ok' => false, 'message' => 'User tidak ditemukan.'], 404);
            }
            if ($user->username === 'admin') {
                return response()->json([
                    'ok' => false,
                    'message' => "User 'admin' bawaan tenant tidak boleh dihapus.",
                ], 422);
            }
            $username = $user->username;
            $user->delete();

            $this->flushTenantCache($tenant);

            return response()->json(['ok' => true, 'username' => $username]);
        });
    }

    public function resetPassword(Request $request, Tenant $tenant, $userId)
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'min:6', 'max:60'],
        ]);

        return $this->runInTenant($tenant, function () use ($data, $userId, $tenant) {
            $user = User::find($userId);
            if (!$user) {
                return response()->json(['ok' => false, 'message' => 'User tidak ditemukan.'], 404);
            }
            $user->password = Hash::make($data['password']);
            $user->save();

            $this->flushTenantCache($tenant);

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

        DB::purge($connName);

        $prevDefault = Config::get('database.default');
        Config::set('database.default', $connName);

        $resolver = \Illuminate\Database\Eloquent\Model::getConnectionResolver();
        \Illuminate\Database\Eloquent\Model::setConnectionResolver(new class($connName) implements \Illuminate\Database\ConnectionResolverInterface {
            public function __construct(private string $conn) {}
            public function connection($name = null) { return \DB::connection($this->conn); }
            public function getDefaultConnection() { return $this->conn; }
            public function setDefaultConnection($name) { /* no-op */ }
        });

        try {
            $result = $callback();

            return $result;
        } finally {
            \Illuminate\Database\Eloquent\Model::setConnectionResolver($resolver);
            Config::set('database.default', $prevDefault);
            DB::purge($connName);
        }
    }

    private function getTenantSnapshot(Tenant $t): array
    {
        $cacheKey = "tenant:hak_akses:{$t->id}";

        return Cache::remember($cacheKey, self::SNAPSHOT_TTL, function () use ($t) {
            return $this->runInTenant($t, function () {
                $menus = DB::table('menu')
                    ->where('status', 'aktif')
                    ->orderBy('group')
                    ->orderBy('urutan')
                    ->get(['id', 'parent_id', 'group', 'nama_menu', 'urutan']);

                $byId = $menus->keyBy('id');

                $menus = $menus->map(function ($m) use ($byId) {
                    if ($m->parent_id && isset($byId[$m->parent_id])) {
                        $m->group = $byId[$m->parent_id]->group;
                    }
                    return $m;
                });

                $grouped = $menus->groupBy(fn ($m) => $m->group ?: 'Lainnya')->map(function ($items) use ($byId) {
                    $topLevel = $items->filter(function ($m) use ($byId) {
                        if ($m->parent_id === null) return true;
                        return !isset($byId[$m->parent_id]);
                    })->values();

                    $children = $items->whereNotNull('parent_id')->groupBy('parent_id');

                    $descendantIds = [];
                    foreach ($children as $pid => $kids) {
                        $descendantIds[$pid] = $this->flattenDescendants($pid, $children);
                    }

                    return [
                        'parents' => $topLevel,
                        'children' => $children,
                        'descendant_ids' => $descendantIds,
                    ];
                });

                $users = User::orderBy('nama')
                    ->get(['id', 'nama', 'username', 'email', 'telepon', 'id_jabatan', 'hak_akses']);

                $usersArr = $users->map(function ($u) {
                    return [
                        'id' => $u->id,
                        'nama' => $u->nama,
                        'username' => $u->username,
                        'email' => $u->email,
                        'telepon' => $u->telepon,
                        'id_jabatan' => $u->id_jabatan,
                        'hak_akses' => collect($u->hak_akses ?? [])->map(fn ($v) => (int) $v)->all(),
                    ];
                })->all();

                $menusArr = $menus->map(function ($m) use ($byId) {
                    $parentId = $m->parent_id ? (int) $m->parent_id : null;
                    $group = null;
                    if ($parentId && isset($byId[$parentId])) {
                        $group = $byId[$parentId]->group;
                    } else {
                        $group = $m->group;
                    }
                    return [
                        'id' => (int) $m->id,
                        'nama' => $m->nama_menu,
                        'parent_id' => $parentId,
                        'group' => $group,
                    ];
                })->all();

                return [
                    'grouped' => $grouped,
                    'users' => $usersArr,
                    'menus' => $menusArr,
                ];
            });
        });
    }

    private function buildMenusByTenantFromSnapshot($tenants, array $perTenant): array
    {
        $out = [];
        foreach ($tenants as $t) {
            $items = $perTenant[$t->id]['menus'] ?? [];
            $out[$t->id] = $this->buildMenuTree($items);
        }
        return $out;
    }

    private function buildMenuTree(array $items): array
    {
        $byParent = [];
        foreach ($items as $m) {
            $pid = $m['parent_id'] ?? 0;
            $byParent[$pid][] = $m;
        }

        $build = function ($parentId) use (&$build, $byParent) {
            $kids = $byParent[$parentId] ?? [];
            $out = [];
            foreach ($kids as $k) {
                $children = $build($k['id']);
                $out[] = [
                    'id' => $k['id'],
                    'nama' => $k['nama'],
                    'group' => $k['group'],
                    'children' => $children,
                ];
            }
            return $out;
        };

        return $build(0);
    }

    private function flushTenantCache(Tenant $tenant): void
    {
        Cache::forget("tenant:hak_akses:{$tenant->id}");
    }
}
