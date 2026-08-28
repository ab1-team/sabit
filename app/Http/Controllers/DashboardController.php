<?php

namespace App\Http\Controllers;


use App\Models\JenisBiaya;
use App\Models\Siswa;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if ($request->boolean('gen_piutang')) {
            $job = (string) $request->query('job', '');
            $tokenKey = 'piutang_token_' . $job;
            if ($job && session()->has($tokenKey)) {
                session()->forget($tokenKey);
            } else {
                return redirect()->route('app.dashboard');
            }
        }

        $today      = Carbon::today();
        $bulanAwal  = Carbon::now()->startOfMonth();
        $bulanAkhir = Carbon::now()->endOfMonth();

        // ============== STATISTIK SISWA (cache 60 detik untuk hindari full count berulang) ==============
        $dashScope = (tenant('id') ?? 'central') . ':';
        $totalSiswa = Cache::remember('dash:' . $dashScope . 'total_siswa', 60, fn () => (int) DB::table('siswa')->count());
        $aktifSiswa = Cache::remember('dash:' . $dashScope . 'aktif_siswa', 60, fn () => (int) DB::table('anggota_kelas')
            ->where('status', 'aktif')
            ->distinct()
            ->count('id_siswa'));
        $siswaCount    = $totalSiswa;
        $siswaAktif    = $aktifSiswa;
        $siswaNonAktif = max(0, $totalSiswa - $aktifSiswa);
        $siswaBlokir   = 0;

        // ============== JENIS BIAYA ==============
        $jenis_biaya = JenisBiaya::query()
            ->join('jenis_pembayaran', 'jenis_pembayaran.id', '=', 'jenis_biaya.id_jp')
            ->where('jenis_pembayaran.nama', 'like', 'SPP%')
            ->orderBy('jenis_biaya.angkatan', 'desc')
            ->get(['jenis_biaya.id', 'jenis_biaya.angkatan', 'jenis_biaya.total_beban', 'jenis_biaya.id_jp']);

        // ============== PEMASUKAN (1 query aggregate, 2 nilai) ==============
        $pemasukanRow = DB::table('transaksi')
            ->whereNull('deleted_at')
            ->whereBetween('tanggal_transaksi', [$bulanAwal, $bulanAkhir])
            ->where('rekening_debit', 'like', '1.1.01.%')
            ->selectRaw('
                COALESCE(SUM(CASE WHEN DATE(tanggal_transaksi) = ? THEN jumlah ELSE 0 END), 0) AS hari_ini,
                COALESCE(SUM(jumlah), 0) AS bulan_ini
            ', [$today->toDateString()])
            ->first();
        $pemasukanHariIni  = (float) $pemasukanRow->hari_ini;
        $pemasukanBulanIni = (float) $pemasukanRow->bulan_ini;

        // ============== TUNGGAKAN SPP ==============
        [$tunggakanSpp, $totalTunggakanSpp, $jumlahSiswaMenunggak] = $this->hitungTunggakanSpp(false);

        // ============== CHART 12 BULAN (1 query aggregate, 12 kolom CASE) ==============
        $from = Carbon::now()->subMonths(11)->startOfMonth()->toDateString();
        $to   = Carbon::now()->endOfMonth()->toDateString();

        $selects = [];
        $bindings = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i);
            $ym = $m->format('Y-m');
            $selects[] = "COALESCE(SUM(CASE WHEN DATE_FORMAT(tanggal_transaksi, '%Y-%m') = ? THEN jumlah ELSE 0 END), 0) AS m_{$i}";
            $bindings[] = $ym;
        }

        $chartRow = DB::table('transaksi')
            ->whereNull('deleted_at')
            ->whereBetween('tanggal_transaksi', [$from, $to])
            ->where('rekening_debit', 'like', '1.1.01.%')
            ->selectRaw(implode(', ', $selects), $bindings)
            ->first();

        $chartByMonth = [];
        foreach ($chartRow as $key => $val) {
            $chartByMonth[$key] = (float) $val;
        }

        $labelsBulanan = [];
        $pendapatanBulanan = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i);
            $labelsBulanan[]     = $m->translatedFormat('M y');
            $pendapatanBulanan[] = (float) ($chartByMonth["m_{$i}"] ?? 0);
        }

        // ============== RECENT TRANSAKSI (select kolom minimum) ==============
        $recentTransaksi = Transaksi::with([
                'siswa:id,nama,nisn',
                'user:id,nama',
            ])
            ->whereNull('deleted_at')
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%' . $request->search . '%';
                $q->where(function ($w) use ($term) {
                    $w->where('keterangan', 'like', $term)
                      ->orWhereHas('siswa', function ($s) use ($term) {
                          $s->where('nama', 'like', $term)
                            ->orWhere('nisn', 'like', $term);
                      });
                });
            })
            ->select(['id', 'tanggal_transaksi', 'keterangan', 'siswa_id', 'user_id', 'jumlah'])
            ->orderByDesc('tanggal_transaksi')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $title = 'Dashboard';

        return view('dashboard.index', compact(
            'title',
            'siswaCount',
            'siswaAktif',
            'siswaNonAktif',
'siswaBlokir',
            'jenis_biaya',
            'pemasukanHariIni',
            'pemasukanBulanIni',
            'tunggakanSpp',
            'totalTunggakanSpp',
            'jumlahSiswaMenunggak',
            'labelsBulanan',
            'pendapatanBulanan',
            'recentTransaksi'
        ));
    }

    public function siswaAktifTable()
    {
        $rows = Siswa::aktif()
            ->orderBy('nama')
            ->select(['id', 'nisn', 'nama', 'kode_kelas', 'status_siswa', 'tahun_akademik'])
            ->limit(500)
            ->get();
        return view('dashboard.partials.siswa-aktif', ['rows' => $rows]);
    }

    public function siswaMenunggakTable()
    {
        [$rows] = $this->hitungTunggakanSpp(true);
        return view('dashboard.partials.siswa-menunggak', ['rows' => $rows]);
    }

    /**
     * Hitung tunggakan SPP — GROUP BY di MySQL.
     *
     * @param bool $withBulan Jika true, enumerasi bulan per siswa (dipakai
     *                        hanya oleh popup detail; index tidak butuh ini).
     */
    private function hitungTunggakanSpp(bool $withBulan = true): array
    {
        $now      = Carbon::now();
        $tahunIni = (int) $now->format('Y');
        $bulanIni = (int) $now->format('m');

$rows = DB::table('spp')
            ->join('anggota_kelas as ak', 'ak.id', '=', 'spp.anggota_kelas')
            ->join('siswa', 'siswa.id', '=', 'ak.id_siswa')
            ->where('ak.status', 'aktif')
            ->where('spp.status', 'B')
            ->where(function ($q) use ($tahunIni, $bulanIni) {
                $q->whereYear('spp.tanggal', '<', $tahunIni)
                    ->orWhere(function ($q2) use ($tahunIni, $bulanIni) {
                        $q2->whereYear('spp.tanggal', '=', $tahunIni)
                            ->whereMonth('spp.tanggal', '<', $bulanIni);
                    });
            })
            ->groupBy('ak.id_siswa', 'siswa.nisn', 'siswa.nama', 'ak.kode_kelas', 'ak.spp_nominal')
            ->selectRaw('
                ak.id_siswa,
                siswa.nisn,
                siswa.nama,
                ak.kode_kelas,
                ak.spp_nominal,
                COUNT(*) AS jumlah_bulan
            ')
            ->get();

        $result = $rows->map(function ($r) use ($withBulan) {
            $nominal     = (float) ($r->spp_nominal ?? 0);
            $jumlahBulan = (int) $r->jumlah_bulan;

            return (object) [
                'siswa'             => (object) ['nisn' => $r->nisn, 'nama' => $r->nama],
                'kode_kelas'        => $r->kode_kelas,
                'jumlah_bulan'      => $jumlahBulan,
                'nominal_per_bulan' => $nominal,
                'total_tunggakan'   => $nominal * $jumlahBulan,
                'bulan_tunggakan'   => $withBulan
                    ? $this->bulanTunggakanPerSiswa((int) $r->id_siswa)
                    : collect(),
            ];
        })->values();

        $total = (float) $result->sum('total_tunggakan');

        return [$result, $total, $result->count()];
    }

    /**
     * Ambil list bulan tunggakan unik untuk popup detail.
     */
    private function bulanTunggakanPerSiswa(int $idSiswa)
    {
        $now      = Carbon::now();
        $tahunIni = (int) $now->format('Y');
        $bulanIni = (int) $now->format('m');

return DB::table('spp')
            ->join('anggota_kelas as ak', 'ak.id', '=', 'spp.anggota_kelas')
            ->where('ak.id_siswa', $idSiswa)
            ->where('ak.status', 'aktif')
            ->where('spp.status', 'B')
            ->where(function ($q) use ($tahunIni, $bulanIni) {
                $q->whereYear('spp.tanggal', '<', $tahunIni)
                    ->orWhere(function ($q2) use ($tahunIni, $bulanIni) {
                        $q2->whereYear('spp.tanggal', $tahunIni)
                            ->whereMonth('spp.tanggal', '<', $bulanIni);
                    });
            })
            ->orderBy('spp.tanggal')
            ->distinct()
            ->pluck('spp.tanggal')
            ->map(fn ($d) => Carbon::parse($d)->startOfMonth())
            ->values();
    }
}
