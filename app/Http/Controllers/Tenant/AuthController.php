<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        if (Auth::guard('tenant')->check()) {
            return redirect()->route('tenant.dashboard');
        }
        return view('tenant.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('tenant')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $request->session()->put('auth_portal', 'central');
            $request->session()->forget('central_tenant_id');
            return redirect()->intended(route('tenant.dashboard'));
        }

        $user = \App\Models\Tenant\TenantUser::where('email', $request->email)->first();
        \Log::info('Pusat login failed', [
            'email' => $request->email,
            'user_found' => (bool) $user,
            'password_check' => $user ? \Hash::check($request->password, $user->password) : null,
        ]);

        return back()->withErrors(['email' => 'Email atau password salah.'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::guard('tenant')->logout();
        $request->session()->forget('auth_portal');
        $request->session()->regenerate();
        $request->session()->regenerateToken();
        return redirect()->route('tenant.login');
    }
}


