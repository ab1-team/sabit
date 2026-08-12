<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Profil;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        // Auto-logout user lama jika berbeda, karena tab browser berbagi
        // session. Login user baru akan menimpa session auth yang sedang
        // aktif di tab lain. Untuk login bersamaan dengan 2 user berbeda,
        // gunakan browser berbeda (incognito atau browser lain).
        $currentUser = Auth::guard('web')->user();
        $switchedUser = $currentUser && (int) $currentUser->id !== (int) $user->id;

        if ($switchedUser) {
            // Invalidate session ID lama supaya tab lain yang pegang
            // cookie sesi lama otomatis logout (request berikutnya ke
            // server dengan cookie ID lama akan dianggap anonymous).
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        Auth::guard('web')->login($user);
        $request->session()->put('auth_portal', 'school');
        $request->session()->regenerate();
        $profil = Profil::first();
        session()->put('profil', $profil);
        $profil = Profil::first();
        session()->put('profil', $profil);

        // Tentukan tujuan redirect setelah login.
        // - User yang punya hak akses menu Beranda (id=1) -> ke dashboard utama.
        // - User tanpa hak akses Beranda (mis. administrator landing) -> langsung
        //   ke /app/landing agar tidak masuk halaman yang bukan wewenangnya.
        $hakAkses = (array) ($user->hak_akses ?? []);
        $berandaMenu = DB::table('menu')->where('nama_menu', 'Beranda')->first();
        $hasBerandaAccess = $berandaMenu && in_array((int) $berandaMenu->id, array_map('intval', $hakAkses), true);
        $landingRoute = $hasBerandaAccess ? 'app.dashboard' : 'app.landing.index';

        $showPiutangPrompt = false;
        $bulanLabel = null;
        $jt = (int) ($profil->jatuh_tempo ?? 0);
        if ($jt > 0 && (int) date('d') === $jt && $hasBerandaAccess) {
            $bulanLalu = Carbon::now()->subMonthNoOverflow();
            $bulanLabel = \App\Utils\Tanggal::namaBulanNew((int) $bulanLalu->format('m'))
                . ' ' . $bulanLalu->format('Y');
            $showPiutangPrompt = true;
        }

        if ($showPiutangPrompt) {
            $job = uniqid('gen_', true);
            session()->put('piutang_token_' . $job, true);
            return redirect()->route($landingRoute)->with([
                'piutang_prompt' => [
                    'bulan' => $bulanLabel,
                    'job' => $job,
                ],
            ]);
        }

        return redirect()->route($landingRoute)->with([
            'icon' => 'success',
            'msg'  => 'Selamat datang ' . ($user->nama ?? $user->username ?? 'Pengguna'),
        ]);
    }

    public function logout(Request $request)
    {
        session()->forget('profil');
        session()->forget('piutang_prompt');
        Auth::guard('web')->logout();
        session()->forget('auth_portal');
        $request->session()->regenerate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar');
    }
}
