<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Tenant\BaseSchoolController;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Tenant;

class UserOperatorController extends BaseSchoolController
{
    public function index(Tenant $tenant)
    {
        return $this->runInTenant($tenant, function () use ($tenant) {
            $users = User::orderBy('nama')->get();

            return view('tenant.tenant.user', [
                'tenant' => $tenant,
                'users'  => $users,
            ]);
        });
    }

    public function store(Request $request, Tenant $tenant)
    {
        return $this->runInTenant($tenant, function () use ($request, $tenant) {
            $data = $request->validate([
                'nama'     => ['required', 'string', 'max:120'],
                'username' => ['required', 'string', 'max:60', 'alpha_dash', Rule::unique('users', 'username')],
                'email'    => ['nullable', 'email', 'max:120'],
                'password' => ['required', 'string', 'min:6', 'max:60'],
                'telepon'  => ['nullable', 'string', 'max:30'],
                'hak_akses'=> ['nullable'],
            ]);

            $user = new User();
            $user->nama      = $data['nama'];
            $user->username  = $data['username'];
            $user->email     = $data['email'] ?? null;
            $user->password  = Hash::make($data['password']);
            $user->telepon   = $data['telepon'] ?? null;
            $user->hak_akses = is_array($data['hak_akses'] ?? null) ? array_values(array_unique(array_map('intval', $data['hak_akses']))) : ['*'];
            $user->save();

            return redirect()->route('tenant.tenant.user.index', $tenant)
                ->with('success', "User operator {$user->username} berhasil dibuat");
        });
    }

    public function update(Request $request, Tenant $tenant, User $user)
    {
        return $this->runInTenant($tenant, function () use ($request, $user, $tenant) {
            $data = $request->validate([
                'nama'     => ['required', 'string', 'max:120'],
                'email'    => ['nullable', 'email', 'max:120'],
                'telepon'  => ['nullable', 'string', 'max:30'],
                'hak_akses'=> ['nullable'],
            ]);

            $user->nama    = $data['nama'];
            $user->email   = $data['email'] ?? null;
            $user->telepon = $data['telepon'] ?? null;
            if (is_array($data['hak_akses'] ?? null)) {
                $user->hak_akses = array_values(array_unique(array_map('intval', $data['hak_akses'])));
            }
            $user->save();

            return redirect()->route('tenant.tenant.user.index', $tenant)
                ->with('success', "User {$user->username} diperbarui");
        });
    }

    public function destroy(Tenant $tenant, User $user)
    {
        return $this->runInTenant($tenant, function () use ($user, $tenant) {
            $username = $user->username;
            if ($user->username === 'admin') {
                return redirect()->route('tenant.tenant.user.index', $tenant)
                    ->with('error', "User 'admin' bawaan tenant tidak boleh dihapus.");
            }
            $user->delete();

            return redirect()->route('tenant.tenant.user.index', $tenant)
                ->with('success', "User {$username} dihapus");
        });
    }

    public function resetPassword(Request $request, Tenant $tenant, User $user)
    {
        return $this->runInTenant($tenant, function () use ($request, $user, $tenant) {
            $data = $request->validate([
                'password' => ['required', 'string', 'min:6', 'max:60'],
            ]);
            $user->password = Hash::make($data['password']);
            $user->save();

            return redirect()->route('tenant.tenant.user.index', $tenant)
                ->with('success', "Password user {$user->username} direset");
        });
    }
}


