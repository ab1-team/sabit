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

                    // Untuk tampilan hak-akses, tempatkan child selalu di bawah
                    // parent-nya (abaikan group child jika parent ada). Group
                    // child tetap dipakai pada logika middleware (lihat
                    // EnsureHakAkses), sehingga opsi hak-akses tetap konsisten
                    // dengan akses route yang sebenarnya.
                    $menus = $menus->map(function ($m) use ($byId) {
                        if ($m->parent_id && isset($byId[$m->parent_id])) {
                            $parentGroup = $byId[$m->parent_id]->group;
                            // Selalu pakai group parent untuk konsistensi UI,
                            // termasuk jika parent tidak punya group (null).
                            $m->group = $parentGroup;
                        }

                        return $m;
                    });

                    $grouped = $menus->groupBy(fn ($m) => $m->group ?: 'Lainnya')->map(function ($items) use ($byId) {
                        // Tentukan top-level parents: menu dengan parent_id NULL,
                        // atau menu yang parent-nya bukan menu aktif manapun.
                        // Ini memungkinkan nested children (mis. PPDB > sub-menu
                        // PPDB) tetap tampil sebagai sub-dropdown di dalam
                        // group yang sama.
                        $topLevel = $items->filter(function ($m) use ($byId) {
                            if ($m->parent_id === null) {
                                return true;
                            }
                            // Parent ada tapi di group berbeda → orphan, anggap top-level
                            // (logic group mutation di atas sudah menyamakan group,
                            // jadi这种情况 shouldn't happen; ini fallback).
                            return !isset($byId[$m->parent_id]);
                        })->values();

                        // Bangun tree children per parent_id, dengan support
                        // multi-level (sub-anak dikumpulkan di children[key]
                        // bersama cucu, dst).
                        $children = $items->whereNotNull('parent_id')->groupBy('parent_id');

                        // Pre-compute descendant ids per parent (termasuk cucu/cicit)
                        // supaya view bisa menandai semua sub-children saat
                        // parent di-select-all.
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
                                'id_jabatan' => $u->id_jabatan,
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
            $user->hak_akses = $this->normalizeMenuIds($data['hak_akses'] ?? []);
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

    public function updateUser(Request $request, Tenant $tenant, $userId)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:120'],
            'telepon' => ['nullable', 'string', 'max:30'],
        ]);

        return $this->runInTenant($tenant, function () use ($data, $userId) {
            $user = User::find($userId);
            if (!$user) {
                return response()->json(['ok' => false, 'message' => 'User tidak ditemukan.'], 404);
            }
            $user->nama = $data['nama'];
            $user->email = $data['email'] ?? null;
            $user->telepon = $data['telepon'] ?? null;
            $user->save();

            return response()->json(['ok' => true]);
        });
    }

    public function updateHakAkses(Request $request, Tenant $tenant, $userId)
    {
        $raw = $request->input('menu_ids', []);
        $menuIds = $this->normalizeMenuIds($raw);

        return $this->runInTenant($tenant, function () use ($menuIds, $userId) {
            $user = User::find($userId);
            if (!$user) {
                return response()->json(['ok' => false, 'message' => 'User tidak ditemukan.'], 404);
            }
            $user->hak_akses = $menuIds;
            $user->save();

            return response()->json(['ok' => true, 'count' => count($user->hak_akses)]);
        });
    }

    /**
     * Normalisasi input menu_ids ke array<int> yang bersih.
     * Terima: [1,2], ["1","2"], "1,2", atau nilai gabungan.
     */
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

    /**
     * Kumpulkan semua id descendant dari sebuah parent secara rekursif
     * (anak, cucu, cicit, ...) dari map children[parent_id => Collection].
     * Return: array<int>.
     */
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
        return $this->runInTenant($tenant, function () use ($userId) {
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

            return response()->json(['ok' => true, 'username' => $username]);
        });
    }

    public function resetPassword(Request $request, Tenant $tenant, $userId)
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'min:6', 'max:60'],
        ]);

        return $this->runInTenant($tenant, function () use ($data, $userId) {
            $user = User::find($userId);
            if (!$user) {
                return response()->json(['ok' => false, 'message' => 'User tidak ditemukan.'], 404);
            }
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

        DB::purge($connName);

        $prevDefault = Config::get('database.default');
        Config::set('database.default', $connName);

        // Override global default connection resolver agar Eloquent model
        // (termasuk User) otomatis pakai koneksi tenant selama callback.
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
                        // Untuk konsistensi UI, group child mengikuti group parent
                        // (sama dengan logika index()). Group asli child tetap
                        // dipakai oleh middleware EnsureHakAkses.
                        $group = null;
                        if ($parentId && isset($byId[$parentId])) {
                            $group = $byId[$parentId]->group;
                        } else {
                            $group = $m->group;
                        }
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
            $tree = $this->buildMenuTree($items);
            $out[$t->id] = $tree;
        }

        return $out;
    }

    /**
     * Bangun tree nested dari flat list menu. Top-level adalah menu dengan
     * parent_id NULL; child/anak/cucu/cicit disusun bertingkat.
     */
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
}

