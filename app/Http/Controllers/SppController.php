<?php

namespace App\Http\Controllers;


use App\Models\AnggotaKelas;
use App\Models\JenisBiaya;
use App\Models\JenisPembayaran;
use App\Models\Profil;
use App\Models\Rekening;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\TahunAkademik;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SppController extends Controller
{
    public function CariSiswaAktif(Request $request)
    {
        $params = $request->input('query');
        $ta    = $request->input('tahun_akademik');
        $kelas = $request->input('kelas');
        if ($kelas === '__all__') {
            $kelas = null;
        }

        $query = Siswa::query()
            ->Aktif()
            ->where(function ($q) use ($params) {
                $q->where('nama', 'LIKE', "%{$params}%")
                    ->orWhere('nisn', 'LIKE', "%{$params}%");
            })
            ->with(['anggotaKelas' => function ($q) use ($ta, $kelas) {
                $q->where('status', 'aktif');
                if ($ta)    $q->where('tahun_akademik', $ta);
                if ($kelas) $q->where('kode_kelas', $kelas);
                $q->orderByDesc('id');
            }])
            ->orderBy('nama')
            ->limit(20)
            ->get(['id', 'nama', 'nisn', 'kode_kelas']);

        $results = $query->map(function ($s) use ($ta, $kelas) {
            $ak = $s->anggotaKelas->first();
            $resolvedKelas = ($ak && $ak->kode_kelas)
                ? $ak->kode_kelas
                : (($ta || $kelas) ? null : $s->kode_kelas);

            return [
                'id_siswa' => $s->id,
                'nama' => $s->nama,
                'nisn' => $s->nisn,
                'kode_kelas' => $resolvedKelas,
                'tahun_akademik' => $ak->tahun_akademik ?? null,
                'package_inisial' => null,
            ];
        })
        ->filter(fn ($r) => $r['kode_kelas'] !== null)
        ->values();

        return response()->json($results);
    }

    public function spp($id, Request $request)
    {
        $kodePiutangSpp = JenisPembayaran::KODE_PIUTANG_DEFAULT;

        $ta    = $request->input('tahun_akademik');
        $kelas = $request->input('kelas');
        if ($kelas === '__all__') {
            $kelas = null;
        }

        if ((string) $id === '0') {
            $siswa = new Siswa;
            $spp = collect();
            $spp_perbulan = 0;
            $target_bulan = 0;
            $sd_bulan_ini = 0;
            $sumber_dana = collect();
$tahun_angkatan = '';
            $jenis_biaya = collect();
            $nominalMap = collect();
            $kode_tunggakan = collect();
            $bulan_lunas = 0;
        } else {
            $akQuery = AnggotaKelas::where('id_siswa', $id)
                ->with([
                    'siswa:id,nama,nisn,kode_kelas,ruang',
                    'spp',
                ])->where('status', 'aktif');

            if ($ta)    $akQuery->where('tahun_akademik', $ta);
            if ($kelas) $akQuery->where('kode_kelas', $kelas);
            $anggota_kelas = $akQuery->orderByDesc('id')->first();

            if (! $anggota_kelas) {
                return response()->json([
                    'success' => false,
                    'view' => '<div class="text-center py-5">'
                        . '<i class="bi bi-exclamation-triangle text-danger fs-1"></i>'
                        . '<h6 class="mt-2 mb-1">Siswa belum bisa dimuat</h6>'
                        . '<div class="small text-muted mx-auto" style="max-width:380px;">'
                        . 'Data siswa ditemukan, tetapi <strong>belum memiliki Anggota Kelas ber-status Aktif</strong>.'
                        . '<div class="mt-2"><strong>Penyebab:</strong> siswa tidak terdaftar di kelas aktif periode ini (nonaktif / pindah / belum dialokasikan).</div>'
                        . '<div class="mt-2"><strong>Solusi:</strong> buka menu <em>Kelas &rarr; Anggota Kelas</em>, tambahkan siswa ke kelas dengan status <em>Aktif</em>, lalu muat ulang halaman.</div>'
                        . '</div></div>',
                ]);
            }

            $siswa = $anggota_kelas->siswa;
            $spp = $anggota_kelas->spp;
            $spp_perbulan = $anggota_kelas->spp_nominal;
            $target_bulan = $spp->sum('nominal');
            $sd_bulan_ini = $spp->where('status', 'L')->sum('nominal');
            $bulan_lunas = $spp->where('status', 'L')->count();
            $sumber_dana = Cache::remember('sumber_dana_1.1.01', 3600, fn () =>
                Rekening::where('kode_akun', 'like', '1.1.01.%')->whereNull('tgl_nonaktif')->get(['kode_akun', 'nama_akun']));
            $tahun_angkatan = TahunAkademik::where('status', 'aktif')->value('nama_tahun') ?? date('Y');
            $jenis_biaya = JenisPembayaran::orderBy('id')->get(['id', 'nama', 'kode_akun']);
            $nominalMap = JenisBiaya::where('angkatan', (string) $tahun_angkatan)
                ->get(['id_jp', 'angkatan', 'total_beban'])
                ->groupBy(fn ($i) => $i->id_jp.'|'.$i->angkatan);
            $sppJP = $jenis_biaya->first(fn ($jp) => $jp->isSpp());
            $kodeAkunSpp = $sppJP->kode_akun ?? '';
            $kode_tunggakan = $kodeAkunSpp
                ? Transaksi::where('rekening_debit', $kodePiutangSpp)
                    ->where('rekening_kredit', $kodeAkunSpp)
                    ->where('siswa_id', $siswa->id)
                    ->whereNull('deleted_at')
                    ->orderBy('tanggal_transaksi')
                    ->get(['id', 'tanggal_transaksi', 'jumlah', 'keterangan'])
                : collect();
        }

        $profil = Profil::first();

        return response()->json([
            'success' => true,
            'view' => view('transaksi.map_arsip.formulir-spp')
                ->with([
                    'siswa' => $siswa,
                    'anggota_kelas' => $anggota_kelas ?? null,
                    'spp' => $spp,
                    'spp_perbulan' => $spp_perbulan,
                    'target_bulan' => $target_bulan,
                    'sd_bulan_ini' => $sd_bulan_ini,
                    'bulan_lunas' => $bulan_lunas,
                    'sop_pts' => (int) ($profil->cetak_pts ?? 3),
                    'sop_pas' => (int) ($profil->cetak_pas ?? 3),
                    'sumber_dana' => $sumber_dana,
                    'jenis_biaya' => $jenis_biaya,
                    'tahun_angkatan' => $tahun_angkatan,
                    'nominalMap' => $nominalMap,
                    'kode_tunggakan' => $kode_tunggakan,
                    'kode_piutang_spp' => $kodePiutangSpp,
                ])
                ->render(),
        ]);
    }
}



