<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Profil;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return redirect()
                ->route('login')
                ->withInput(['username' => $request->username])
                ->with('error', 'Username atau password salah');
        }

        Auth::login($user);
        $request->session()->regenerate();
        $profil = Profil::first();
        session()->put('profil', $profil);

        $showPiutangPrompt = false;
        $bulanLabel = null;
        $jt = (int) ($profil->jatuh_tempo ?? 0);
        if ($jt > 0 && (int) date('d') === $jt) {
            $bulanLalu = Carbon::now()->subMonthNoOverflow();
            $bulanLabel = \App\Utils\Tanggal::namaBulanNew((int) $bulanLalu->format('m'))
                . ' ' . $bulanLalu->format('Y');
            $showPiutangPrompt = true;
        }

        if ($showPiutangPrompt) {
            $job = uniqid('gen_', true);
            session()->put('piutang_token_' . $job, true);
            return redirect()->route('app.dashboard')->with([
                'piutang_prompt' => [
                    'bulan' => $bulanLabel,
                    'job' => $job,
                ],
            ]);
        }

        return redirect()->route('app.dashboard')->with([
            'icon' => 'success',
            'msg'  => 'Selamat datang ' . ($user->nama ?? $user->username ?? 'Pengguna'),
        ]);
    }

    public function logout()
    {
        session()->forget('profil');
        session()->forget('piutang_prompt');
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar');
    }
}
