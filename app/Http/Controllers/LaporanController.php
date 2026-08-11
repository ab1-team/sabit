<?php

namespace App\Http\Controllers;

use App\Models\JenisLaporan;
use App\Models\Rekening;
use App\Models\Transaksi;
use App\Models\Profil;
use App\Models\Calk;
use App\Models\AkunLevel1;
use App\Models\MasterArusKas;
use App\Models\Tanda_tangan;
use App\Models\Tahun_Akademik;
use App\Models\Kelas;
use App\Models\Spp;
use App\Models\Anggota_Kelas;
use App\Models\SubLaporan;
use App\Models\Jenis_Biaya;
use App\Models\Saldo;
use App\Utils\Keuangan;
use App\Utils\Tanggal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    private function respond(string $viewHtml, array $data, ?string $filename = null)
    {
        if (request('action') === 'excel') {
            return response($viewHtml, 200, [
                'Content-Type'        => 'application/vnd.ms-excel; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . ($filename ?? 'laporan.xls') . '"',
                'Cache-Control'       => 'max-age=0',
                'Pragma'              => 'public',
            ]);
        }
        $landscape = !empty($data['_landscape']);
        $pdf = Pdf::loadHTML($viewHtml)
            ->setPaper($landscape ? 'a4' : 'a4', $landscape ? 'landscape' : 'portrait')
            ->setOptions([
                'margin-top'    => 30,
                'margin-bottom' => 15,
                'margin-left'   => 15,
                'margin-right'  => 15,
                'enable-local-file-access' => true,
            ]);
        return $pdf->stream($filename ?? 'laporan.pdf');
    }

    public function index()
    {
        $title = 'Laporan Keuangan';
        $laporan = JenisLaporan::where('file', '!=', '0')
            ->orderBy('urut', 'ASC')
            ->get();
        return view('laporan-keuangan.index', compact('title', 'laporan'));
    }

    public function subLaporan($file)
    {
        $jenis = JenisLaporan::where('file', $file)->first();
        $idLap = $jenis?->id ?? 0;

        $dbSubs = $jenis
            ? SubLaporan::where('id_lap', $idLap)->orderBy('urut')->orderBy('id')->get()
            : collect();

        if ($dbSubs->isNotEmpty()) {
            $sub_laporan = [['value' => '', 'title' => '---']];
            foreach ($dbSubs as $sub) {
                $sub_laporan[] = [
                    'value' => $sub->file === '0' ? $sub->id : $sub->file,
                    'title' => $sub->nama_laporan,
                ];
            }
            return view('laporan-keuangan.partials.sub_laporan', [
                'type' => 'select',
                'sub_laporan' => $sub_laporan,
            ]);
        }

        if ($file == 'buku_besar') {

            $rekening = Rekening::orderBy('kode_akun', 'ASC')->get();
            $sub_laporan = [];

            foreach ($rekening as $rek) {
                $sub_laporan[] = [
                    'value' => $rek->kode_akun,
                    'title' => $rek->kode_akun . '. ' . $rek->nama_akun
                ];
            }

            return view('laporan-keuangan.partials.sub_laporan', [
                'type' => 'select',
                'sub_laporan' => $sub_laporan
            ]);
        } elseif ($file == 'calk') {

            $tahun = request('tahun');
            $bulan = str_pad(request('bulan'), 2, '0', STR_PAD_LEFT);

            $tanggal = "{$tahun}-{$bulan}-01";

            $calk = Calk::where('tanggal', $tanggal)->first();

            return view('laporan-keuangan.partials.sub_laporan', [
                'type'       => 'textarea',
                'keterangan' => $calk->catatan ?? ''
            ]);
        } elseif (in_array($file, ['pembayaran_spp', 'daftar_ulang', 'pembangunan', 'ujian_semester', 'bantuan_yayasan'], true)) {

            $kelas = Anggota_Kelas::where('status', 'aktif')
                ->select('kode_kelas')
                ->distinct()
                ->orderBy('kode_kelas')
                ->get()
                ->map(function ($row) {
                    $k = Kelas::where('kode_kelas', $row->kode_kelas)->first();
                    return (object) [
                        'kode_kelas' => $row->kode_kelas,
                        'nama_kelas' => $k->nama_kelas ?? $row->kode_kelas,
                    ];
                });

            $sub_laporan = [];
            foreach ($kelas as $k) {
                $sub_laporan[] = [
                    'value' => (string) $k->kode_kelas,
                    'title' => $k->kode_kelas . ' - ' . $k->nama_kelas,
                ];
            }

            return view('laporan-keuangan.partials.sub_laporan', [
                'type' => 'select',
                'sub_laporan' => $sub_laporan,
            ]);
        } else {

            return view('laporan-keuangan.partials.sub_laporan', [
                'type' => 'select',
                'sub_laporan' => [
                    ['value' => '', 'title' => '---']
                ]
            ]);
        }
    }

    public function preview(Request $request)
    {
        $laporan = $request->laporan;
        $data    = $request->all();

        // ================= LOGO =================
        $logoPath = \App\Models\Profil::logoPath();
        if (file_exists($logoPath)) {
            $data['logo'] = base64_encode(file_get_contents($logoPath));
            $data['logo_type'] = pathinfo($logoPath, PATHINFO_EXTENSION);
        }

        // ================= SIMPAN CALK =================
        if ($laporan === 'calk' && $request->action !== 'excel') {

            $tahun = $request->tahun;
            $bulan = str_pad($request->bulan, 2, '0', STR_PAD_LEFT);
            $tanggal = "{$tahun}-{$bulan}-01";

            Calk::updateOrCreate(
                ['tanggal' => $tanggal],
                ['catatan' => $request->sub_laporan]
            );
        }

        // ================= BUKU BESAR =================
        if ($laporan === 'buku_besar') {
            $data['kode_akun'] = $request->sub_laporan;
            $data['laporan']   = 'buku_besar';
            return $this->buku_besar($data);
        }

        // ================= SPP / DAFTAR ULANG / BUILD =================
        if (in_array($laporan, ['pembayaran_spp', 'daftar_ulang', 'pembangunan', 'ujian_semester', 'bantuan_yayasan'], true)) {
            return $this->{$laporan}($request);
        }

        if (method_exists($this, $laporan)) {
            return $this->$laporan($data);
        }

        if (view()->exists("laporan-keuangan.views.{$laporan}")) {
            return view("laporan-keuangan.views.{$laporan}", $data);
        }

        abort(404, 'Laporan tidak ditemukan');
    }


    private function cover(array $data)
    {
        $thn  = $data['tahun'];
        $bln  = str_pad($data['bulan'], 2, '0', STR_PAD_LEFT);
        $hari = str_pad($data['hari'], 2, '0', STR_PAD_LEFT);

        $tgl = $thn . '-' . $bln . '-' . $hari;

        $data['tahun']     = $thn;
        $data['judul']     = 'LAPORAN KEUANGAN';
        $data['sub_judul'] = 'Tahun ' . Tanggal::tahun($tgl);
        $data['tgl']       = Tanggal::tahun($tgl);
        $data['title']     = 'LAPORAN KEUANGAN';
        if (!empty($data['bulan'])) {
            $data['sub_judul'] = 'Bulan ' . Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
            $data['tgl']       = Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
        }

        $data['profil'] = Profil::first();
        $view = view('laporan-keuangan.views.cover', $data)->render();

        return $this->respond($view, $data, 'cover.xls');
    }

    private function buku_besar(array $data)
    {
        $thn  = $data['tahun'];
        $bln  = str_pad($data['bulan'], 2, '0', STR_PAD_LEFT);

        $tgl_awal_tahun  = "$thn-01-01";
        $tgl_awal_bulan  = "$thn-$bln-01";
        $tgl_akhir_bulan = "$thn-$bln-" . cal_days_in_month(CAL_GREGORIAN, (int) $bln, (int) $thn);
        $tgl_akhir_sebelum = date('Y-m-d', strtotime("$tgl_awal_bulan -1 day"));

        $rek = Rekening::where('kode_akun', $data['kode_akun'])->first();
        if (!$rek) {
            return abort(404, 'Rekening tidak ditemukan!');
        }
        $data['rek'] = $rek;
        $data['judul'] = "Buku Besar " . ($rek->kode_akun ?? '-') . " (" . Tanggal::namaBulan($tgl_awal_bulan) . " $thn)";

        $kode = $rek->kode_akun;
        $isDebet = ($rek->jenis_mutasi == 'debet');

        // Helper inline — saldo: +(debit) atau -(kredit) untuk akun Debet-normal,
        // atau kebalikannya untuk akun Kredit-normal.
        $sumExpr = fn ($col) => $isDebet
            ? "COALESCE(SUM(CASE WHEN {$col} = ? THEN jumlah ELSE 0 END),0) - COALESCE(SUM(CASE WHEN {$col} = ? THEN jumlah ELSE 0 END),0)"
            : "COALESCE(SUM(CASE WHEN {$col} = ? THEN jumlah ELSE 0 END),0) - COALESCE(SUM(CASE WHEN {$col} = ? THEN jumlah ELSE 0 END),0)";

        // 1) Saldo awal tahun: 1 query aggregate
        $saldo_awal = (float) DB::table('transaksi')
            ->whereNull('deleted_at')
            ->where('tanggal_transaksi', '<', $tgl_awal_tahun)
            ->where(function ($q) use ($kode) {
                $q->where('rekening_debit', $kode)->orWhere('rekening_kredit', $kode);
            })
            ->selectRaw($sumExpr('rekening_debit'), [$kode, $kode])
            ->value(DB::raw('1')); // trick agar selectRaw dipakai

        // value() butuh raw lagi — kita ambil langsung via DB::select
        $saldo_awal = (float) DB::table('transaksi')
            ->whereNull('deleted_at')
            ->where('tanggal_transaksi', '<', $tgl_awal_tahun)
            ->where(function ($q) use ($kode) {
                $q->where('rekening_debit', $kode)->orWhere('rekening_kredit', $kode);
            })
            ->selectRaw('
                COALESCE(SUM(CASE WHEN rekening_debit = ? THEN jumlah ELSE 0 END),0) -
                COALESCE(SUM(CASE WHEN rekening_kredit = ? THEN jumlah ELSE 0 END),0) AS net
            ', [$kode, $kode])
            ->value('net');
        // Koreksi sign berdasarkan normal akun
        if (! $isDebet) {
            $saldo_awal = -$saldo_awal;
        }
        $data['saldo_awal'] = $saldo_awal;

        // 2) Kumulatif s/d bulan lalu (Jan s/d akhir bulan lalu) — 1 query
        $blLalu = DB::table('transaksi')
            ->whereNull('deleted_at')
            ->whereBetween('tanggal_transaksi', [$tgl_awal_tahun, $tgl_akhir_sebelum])
            ->where(function ($q) use ($kode) {
                $q->where('rekening_debit', $kode)->orWhere('rekening_kredit', $kode);
            })
            ->selectRaw('
                COALESCE(SUM(CASE WHEN rekening_debit = ? THEN jumlah ELSE 0 END),0) AS debit,
                COALESCE(SUM(CASE WHEN rekening_kredit = ? THEN jumlah ELSE 0 END),0) AS kredit
            ', [$kode, $kode])
            ->first();

        $komulatif_debit  = (float) $blLalu->debit;
        $komulatif_kredit = (float) $blLalu->kredit;
        $komulatif_saldo  = $saldo_awal + ($isDebet
            ? $komulatif_debit - $komulatif_kredit
            : $komulatif_kredit - $komulatif_debit);

        $data['komulatif_bulan_lalu_debit']  = $komulatif_debit;
        $data['komulatif_bulan_lalu_kredit'] = $komulatif_kredit;
        $data['komulatif_bulan_lalu_saldo']  = $komulatif_saldo;

        // 3) Transaksi bulan ini (untuk daftar di view) + totals
        $transaksi_bulan_ini = Transaksi::with('user:id,nama_lengkap')
            ->whereNull('deleted_at')
            ->where(function ($q) use ($kode) {
                $q->where('rekening_debit', $kode)->orWhere('rekening_kredit', $kode);
            })
            ->whereBetween('tanggal_transaksi', [$tgl_awal_bulan, $tgl_akhir_bulan])
            ->orderBy('tanggal_transaksi')
            ->select(['id', 'tanggal_transaksi', 'keterangan', 'rekening_debit', 'rekening_kredit', 'jumlah', 'user_id'])
            ->get();

        $data['transaksi']     = $transaksi_bulan_ini;
        $total_bulan_ini_debit = 0.0;
        $total_bulan_ini_kredit = 0.0;
        foreach ($transaksi_bulan_ini as $trx) {
            if ($trx->rekening_debit == $kode) {
                $total_bulan_ini_debit += (float) $trx->jumlah;
            } elseif ($trx->rekening_kredit == $kode) {
                $total_bulan_ini_kredit += (float) $trx->jumlah;
            }
        }
        $data['total_bulan_ini'] = ['debit' => $total_bulan_ini_debit, 'kredit' => $total_bulan_ini_kredit];

        $data['total_sampai_bulan_ini'] = [
            'debit'  => $komulatif_debit + $total_bulan_ini_debit,
            'kredit' => $komulatif_kredit + $total_bulan_ini_kredit,
            'saldo'  => $komulatif_saldo + ($isDebet
                ? $total_bulan_ini_debit - $total_bulan_ini_kredit
                : $total_bulan_ini_kredit - $total_bulan_ini_debit),
        ];

        // 4) Total kumulatif tahun (sampai Des) — 1 query aggregate
        $thnIni = DB::table('transaksi')
            ->whereNull('deleted_at')
            ->whereBetween('tanggal_transaksi', [$tgl_awal_tahun, "$thn-12-31"])
            ->where(function ($q) use ($kode) {
                $q->where('rekening_debit', $kode)->orWhere('rekening_kredit', $kode);
            })
            ->selectRaw('
                COALESCE(SUM(CASE WHEN rekening_debit = ? THEN jumlah ELSE 0 END),0) AS debit,
                COALESCE(SUM(CASE WHEN rekening_kredit = ? THEN jumlah ELSE 0 END),0) AS kredit
            ', [$kode, $kode])
            ->first();

        $data['total_tahun_ini'] = [
            'debit'  => (float) $thnIni->debit,
            'kredit' => (float) $thnIni->kredit,
        ];

        // Sub Judul + tanggal
        $data['sub_judul'] = 'Bulan ' . Tanggal::namaBulan($tgl_awal_bulan) . ' ' . $thn;
        $data['tgl_awal_bulan']  = $tgl_awal_bulan;
        $data['tgl_akhir_bulan'] = $tgl_akhir_bulan;
        $data['tahun'] = $thn;
        $data['bulan'] = $bln;
        $data['ttd'] = Tanda_tangan::first();

        $view = view('laporan-keuangan.views.buku_besar', $data)->render();

        return $this->respond($view, $data, 'buku-besar.xls');
    }

    private function jurnal_transaksi(array $data)
    {
        $thn  = $data['tahun'];
        $bln  = str_pad($data['bulan'], 2, '0', STR_PAD_LEFT);
        $hari = str_pad($data['hari'], 2, '0', STR_PAD_LEFT);

        $tgl = $thn . '-' . $bln . '-' . $hari;

        $data['judul']     = 'Jurnal Transaksi';
        $data['sub_judul'] = 'Tahun ' . Tanggal::tahun($tgl);
        $data['tgl']       = Tanggal::tahun($tgl);
        $data['title']     = 'Jurnal Transaksi';
        if (!empty($data['bulan'])) {
            $data['sub_judul'] = 'Bulan ' . Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
            $data['tgl']       = Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
        }


        $data['transaksis'] = Transaksi::with(['rekeningDebit', 'rekeningKredit', 'user'])
            ->when(!empty($data['bulan']), function ($q) use ($thn, $bln) {
                $q->whereBetween('tanggal_transaksi', [
                    "$thn-$bln-01",
                    date('Y-m-t', strtotime("$thn-$bln-01"))
                ]);
            })
            ->when(!empty($data['hari']), function ($q) use ($thn, $bln, $hari) {
                $q->whereDate('tanggal_transaksi', "$thn-$bln-$hari");
            })
            ->orderBy('tanggal_transaksi', 'asc')
            ->get();
        $data['ttd'] = Tanda_tangan::first();
        $view = view('laporan-keuangan.views.jurnal_transaksi', $data)->render();

        return $this->respond($view, $data, 'jurnal-transaksi.xls');
    }

    private function arus_kas(array $data)
    {
        $thn  = $data['tahun'];
        $bln  = str_pad($data['bulan'], 2, '0', STR_PAD_LEFT);
        $hari = str_pad($data['hari'], 2, '0', STR_PAD_LEFT);

        $tgl_awal_tahun  = "{$thn}-01-01";
        $tgl_awal_bulan  = "{$thn}-{$bln}-01";
        $tgl_akhir_bulan = "{$thn}-{$bln}-" . cal_days_in_month(CAL_GREGORIAN, (int)$bln, (int)$thn);

        $data['judul'] = 'Laporan Arus Kas';

        $data['tgl_awal_bulan'] = $tgl_awal_bulan;
        $data['tgl_akhir_bulan'] = $tgl_akhir_bulan;

        $namaBulan = Tanggal::namaBulan("{$thn}-{$bln}-01");
        $lastDay   = date('t', strtotime("{$thn}-{$bln}-01"));

        $data['sub_judul'] = !empty($data['bulan'])
            ? 'bulan '  . ' ' . $namaBulan . ' ' . $thn
            : 'Tahun ' . $thn;

        $data['tgl'] = $data['sub_judul'];
        $data['title'] = !empty($data['bulan'])
            ? 'Arus Kas (' . $namaBulan . ' ' . $thn . ')'
            : 'Arus Kas (Tahun ' . $thn . ')';

        // ambil arus kas dengan transaksi bulan berjalan
        $data['arus_kas'] = MasterArusKas::with([
            'child',
            'child.rek_debit.rek.transaksiDebit' => function ($q) use ($tgl_awal_bulan, $tgl_akhir_bulan) {
                $q->whereBetween('tanggal_transaksi', [$tgl_awal_bulan, $tgl_akhir_bulan])
                    ->where('rekening_kredit', 'like', '1.1.01%');
            },
            'child.rek_kredit.rek.transaksiKredit' => function ($q) use ($tgl_awal_bulan, $tgl_akhir_bulan) {
                $q->whereBetween('tanggal_transaksi', [$tgl_awal_bulan, $tgl_akhir_bulan])
                    ->where('rekening_debit', 'like', '1.1.01%');
            }
        ])->where('parent_id', 0)->get();
        $data['ttd'] = Tanda_tangan::first();

        // hitung saldo kas sampai akhir bulan sebelumnya
        $keuangan = new Keuangan;
        $tgl_saldo_lalu = date('Y-m-d', strtotime("-1 day", strtotime($tgl_awal_bulan)));
        $saldo_bulan_lalu = $keuangan->saldoKas($tgl_saldo_lalu);
        $data['saldo_bulan_lalu'] = $saldo_bulan_lalu;

        $view = view('laporan-keuangan.views.arus_kas', $data)->render();

        return $this->respond($view, $data, 'arus-kas.xls');
    }

    private function laba_rugi(array $data)
    {
        $thn  = $data['tahun'];
        $bln  = $data['bulan'] ?? null;
        $hari = $data['hari'] ?? null;

        $tgl = $thn
            . ($bln ? '-' . $bln : '-12')
            . ($hari ? '-' . $hari : '-' . date('t', strtotime("$thn-" . ($bln ?? '12') . "-01")));

        $keuangan = new Keuangan();
        $lr = $keuangan->listLabaRugi($tgl);

        $data['judul'] = 'Laporan Laba Rugi';
        $namaBulanAkhir = Tanggal::namaBulan("{$thn}-{$bln}-01");
        $lastDay        = date('t', strtotime("{$thn}-{$bln}-01"));

        // Awal selalu 01 Januari
        $awal = '01 Januari ' . $thn;
        $akhir = $lastDay . ' ' . $namaBulanAkhir . ' ' . $thn;

        $data['sub_judul'] = !empty($data['bulan'])
            ? 'PERIODE ' . $awal . ' S.D. ' . $akhir
            : 'TAHUN ' . $thn;

        $data['pendapatan'] = $lr['pendapatan'];
        $data['beban']      = $lr['beban'];
        $data['bp']         = $lr['bp'];
        $data['pen']        = $lr['pen'];
        $data['pendl']      = $lr['pendl'];
        $data['beb']        = $lr['beb'];
        $data['ph']         = $lr['ph'];

        $data['title'] = 'Laba Rugi';
        $data['title_bulan'] = 'Tahun ' . Tanggal::tahun($tgl);
        if (!empty($data['bulan'])) {
            $data['title_bulan'] = Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
            $data['tgl']       = Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
        }
        $data['ttd'] = Tanda_tangan::first();

        $view = view('laporan-keuangan.views.laba_rugi', $data)->render();

        return $this->respond($view, $data, 'laba-rugi.xls');
    }

    private function neraca(array $data)
    {
        $thn  = $data['tahun'];
        $bln  = str_pad($data['bulan'], 2, '0', STR_PAD_LEFT);

        $tgl_awal  = "{$thn}-01-01";
        $tgl_akhir = "{$thn}-{$bln}-" . cal_days_in_month(CAL_GREGORIAN, (int) $bln, (int) $thn);

        $data['judul'] = 'Neraca';
        $namaBulan = Tanggal::namaBulan("{$thn}-{$bln}-01");
        $lastDay   = date('t', strtotime("{$thn}-{$bln}-01"));

        $data['sub_judul'] = !empty($data['bulan'])
            ? 'per ' . $lastDay . ' ' . $namaBulan . ' ' . $thn
            : 'Tahun ' . $thn;

        $data['title'] = !empty($data['bulan']) ? $data['judul'] . ' (' . $namaBulan . ' ' . $thn . ')' : $data['judul'] . ' Tahun ' . $thn;

        $data['akun1'] = AkunLevel1::where('lev1', '<=', 3)
            ->with(['akun2.akun3.rek' => function ($q) use ($tgl_awal, $tgl_akhir) {
                $q->whereHas('transaksiDebit', fn($q2) => $q2->whereBetween('tanggal_transaksi', [$tgl_awal, $tgl_akhir]))
                    ->orWhereHas('transaksiKredit', fn($q2) => $q2->whereBetween('tanggal_transaksi', [$tgl_awal, $tgl_akhir]));
            }])
            ->orderBy('kode_akun', 'ASC')
            ->get();


        $data['tgl_awal']  = $tgl_awal;
        $data['tgl_akhir'] = $tgl_akhir;
        $data['ttd'] = Tanda_tangan::first();

        $view = view('laporan-keuangan.views.neraca', $data)->render();

        return $this->respond($view, $data, 'neraca.xls');
    }

    private function neraca_saldo(array $data)
    {
        $thn  = $data['tahun'];
        $bln  = str_pad($data['bulan'], 2, '0', STR_PAD_LEFT);
        $hari = str_pad($data['hari'], 2, '0', STR_PAD_LEFT);

        $tgl_awal  = "{$thn}-01-01";
        $tgl_akhir = "{$thn}-{$bln}-" . cal_days_in_month(CAL_GREGORIAN, (int) $bln, (int) $thn);

        $data['judul'] = 'Neraca ';

        $namaBulan = Tanggal::namaBulan("{$thn}-{$bln}-01");
        $lastDay   = date('t', strtotime("{$thn}-{$bln}-01"));

        $data['sub_judul'] = !empty($data['bulan'])
            ? $namaBulan . ' ' . $thn
            : 'Tahun ' . $thn;

        $data['tgl'] = $data['sub_judul'];


        $data['title'] = !empty($data['bulan'])
            ? 'Neraca Saldo (' . $namaBulan . ' ' . $thn . ')'
            : 'Neraca Saldo (Tahun ' . $thn . ')';

        $data['rekening'] = Rekening::with([
            'transaksiDebit' => function ($q) use ($tgl_awal, $tgl_akhir) {
                $q->whereBetween('tanggal_transaksi', [$tgl_awal, $tgl_akhir]);
            },
            'transaksiKredit' => function ($q) use ($tgl_awal, $tgl_akhir) {
                $q->whereBetween('tanggal_transaksi', [$tgl_awal, $tgl_akhir]);
            }
        ])
            ->orderBy('kode_akun')
            ->get()
            ->transform(function ($rek) {
                $rek->total_debit  = $rek->transaksiDebit->sum('jumlah');
                $rek->total_kredit = $rek->transaksiKredit->sum('jumlah');
                return $rek;
            });
        $data['ttd'] = Tanda_tangan::first();

        $view = view('laporan-keuangan.views.neraca_saldo', $data)->render();

        return $this->respond($view, $data, 'neraca-saldo.xls');
    }

    private function calk(array $data)
    {
        $thn  = $data['tahun'];
        $bln  = str_pad($data['bulan'], 2, '0', STR_PAD_LEFT);

        $tgl_awal  = "{$thn}-01-01";
        $tgl_akhir = "{$thn}-{$bln}-" . cal_days_in_month(CAL_GREGORIAN, (int)$bln, (int)$thn);

        $data['judul'] = 'Calk';

        $namaBulanNormal = Tanggal::namaBulan("{$thn}-{$bln}-01");
        $namaBulanCaps   = strtoupper($namaBulanNormal);

        $data['sub_judul'] = !empty($data['bulan'])
            ? 'BULAN ' . $namaBulanCaps . ' TAHUN ' . $thn
            : 'TAHUN ' . $thn;

        $data['title'] = !empty($data['bulan'])
            ? $data['judul'] . ' (' . $namaBulanNormal . ' ' . $thn . ')'
            : $data['judul'] . ' Tahun ' . $thn;

        $data['profil'] = Profil::first();

        $data['akun1'] = AkunLevel1::where('lev1', '<=', 3)
            ->with(['akun2.akun3.rek'])
            ->orderBy('kode_akun', 'ASC')
            ->get();
        
        $data['tgl_awal']  = $tgl_awal;
        $data['tgl_akhir'] = $tgl_akhir;

        $tanggal = "{$thn}-{$bln}-01";

        $calk = Calk::where('tanggal', $tanggal)->first();

        $data['catatan'] = $calk ? $calk->catatan : '';
        $data['ttd'] = Tanda_tangan::first();

        $view = view('laporan-keuangan.views.calk', $data)->render();

        return $this->respond($view, $data, 'calk.xls');
    }

    public function pembayaran_spp(Request $request)
    {
        $request->validate([
            'tgl_awal'          => 'required|date',
            'tgl_akhir'         => 'required|date',
            'tahun_akademik_id' => 'nullable|exists:tahun_akademik,id',
            'sub_laporan'       => 'nullable',
        ]);

        $data = [
            'tgl_awal'          => $request->tgl_awal,
            'tgl_akhir'         => $request->tgl_akhir,
            'tahun_akademik_id' => $request->tahun_akademik_id,
            'sub_laporan'       => $request->sub_laporan,
            'title'             => 'Laporan Pembayaran SPP',
        ];

        $data['kelas'] = !empty($data['sub_laporan'])
            ? Kelas::where('kode_kelas', $data['sub_laporan'])->first()
            : null;

        $data['periode'] = [
            'awal'  => Carbon::parse($data['tgl_awal'])->locale('id'),
            'akhir' => Carbon::parse($data['tgl_akhir'])->locale('id'),
        ];

        $tglAwal  = Carbon::parse($data['tgl_awal'])->startOfMonth();
        $tglAkhir = Carbon::parse($data['tgl_akhir'])->endOfMonth();

        $excludeRaw = (string) $request->exclude_months;
        $hideAllMonths = $excludeRaw === 'ALL';
        $exclude = collect(explode(',', $excludeRaw))
            ->map(fn($m) => trim($m))
            ->filter()
            ->values();

        $bulanList = [];
        $cursor = $tglAwal->copy();
        while ($cursor->lte($tglAkhir)) {
            $key = $cursor->format('Y-m');
            if (!$hideAllMonths && !$exclude->contains($key)) {
                $bulanList[] = $cursor->copy();
            }
            $cursor->addMonth();
        }

        $anggotaKelas = Anggota_Kelas::with(['getSiswa'])
            ->when(!empty($data['sub_laporan']), function ($q) use ($data) {
                $q->where('kode_kelas', $data['sub_laporan']);
            })
            ->when(!empty($data['tahun_akademik_id']), function ($q) use ($data) {
                $tahun = Tahun_Akademik::find($data['tahun_akademik_id']);
                if ($tahun) {
                    $q->where('tahun_akademik', $tahun->nama_tahun);
                }
            })
            ->orderBy('id')
            ->get()
            ->map(function ($row) use ($tglAwal, $tglAkhir, $bulanList) {

                $row->per_bulan = (int) ($row->spp_nominal ?? 0);

                $sppRows = Spp::where('anggota_kelas', $row->id)
                    ->whereBetween('tanggal', [$tglAwal, $tglAkhir])
                    ->orderBy('tanggal')
                    ->get()
                    ->keyBy(fn($s) => Carbon::parse($s->tanggal)->format('Y-m'));

                $row->bulan_list = collect($bulanList)->map(function ($bln) use ($sppRows, $row) {
                    $key = $bln->format('Y-m');
                    $s = $sppRows->get($key);
                    $nominalTagihan = (int) ($s->nominal ?? $row->per_bulan);
                    $lunas = $s && $s->status === 'L';
                    return (object) [
                        'bulan'   => $bln,
                        'tagihan' => $nominalTagihan,
                        'bayar'   => $lunas ? $nominalTagihan : 0,
                        'status'  => $s ? ($lunas ? 'L' : 'B') : null,
                    ];
                });

                $row->target_sd_saat_ini = Spp::where('anggota_kelas', $row->id)
                    ->where('status', 'B')
                    ->whereBetween('tanggal', [$tglAwal, $tglAkhir])
                    ->sum('nominal');

                $row->sd_periode_ini = Spp::where('anggota_kelas', $row->id)
                    ->where('status', 'L')
                    ->whereBetween('tanggal', [$tglAwal, $tglAkhir])
                    ->sum('nominal');

                $row->sisa = $row->target_sd_saat_ini - $row->sd_periode_ini;

                return $row;
            });

        $data['anggotaKelas'] = $anggotaKelas;
        $data['bulanList']    = $bulanList;

        $logoPath = \App\Models\Profil::logoPath();
        if (file_exists($logoPath)) {
            $data['logo'] = base64_encode(file_get_contents($logoPath));
            $data['logo_type'] = pathinfo($logoPath, PATHINFO_EXTENSION);
        }

        $view = view('laporan-keuangan.views.pembayaran_spp', $data)->render();
        $data['_landscape'] = true;
        return $this->respond($view, $data, 'laporan-spp.xls');
    }

    public function daftar_ulang(Request $request)
    {
        return $this->laporanPembayaranNonSpp(
            $request,
            '4.1.01.02',
            'Laporan Pembayaran Daftar Ulang',
            'laporan-daftar-ulang.pdf',
            fn($row) => (float) ($row->spp_nominal ?? 0)
        );
    }

    public function pembangunan(Request $request)
    {
        return $this->laporanPembayaranNonSpp(
            $request,
            '4.1.01.04',
            'Laporan Pembayaran Pembangunan',
            'laporan-pembangunan.pdf',
            fn($row) => $this->nominalJenisBiaya($row, 3)
        );
    }

    public function ujian_semester(Request $request)
    {
        return $this->laporanPembayaranNonSpp(
            $request,
            '4.1.01.03',
            'Laporan Pembayaran Ujian Semester',
            'laporan-ujian-semester.pdf',
            fn($row) => $this->nominalJenisBiaya($row, 4)
        );
    }

    public function bantuan_yayasan(Request $request)
    {
        return $this->laporanPembayaranNonSpp(
            $request,
            '4.1.01.05',
            'Laporan Pembayaran Bantuan Yayasan',
            'laporan-bantuan-yayasan.pdf',
            fn($row) => $this->nominalJenisBiaya($row, 5)
        );
    }

    private function laporanPembayaranNonSpp(
        Request $request,
        string $kodeAkun,
        string $title,
        string $filename,
        \Closure $targetResolver
    ) {
        $request->validate([
            'tgl_awal'          => 'required|date',
            'tgl_akhir'         => 'required|date',
            'tahun_akademik_id' => 'nullable|exists:tahun_akademik,id',
            'sub_laporan'       => 'nullable',
        ]);

        $data = [
            'tgl_awal'          => $request->tgl_awal,
            'tgl_akhir'         => $request->tgl_akhir,
            'tahun_akademik_id' => $request->tahun_akademik_id,
            'sub_laporan'       => $request->sub_laporan,
            'kode_akun'         => $kodeAkun,
            'title'             => $title,
        ];

        $data['kelas'] = !empty($data['sub_laporan'])
            ? Kelas::where('kode_kelas', $data['sub_laporan'])->first()
            : null;

        $tglAwal  = Carbon::parse($data['tgl_awal'])->startOfDay();
        $tglAkhir = Carbon::parse($data['tgl_akhir'])->endOfDay();

        $data['periode'] = [
            'awal'  => $tglAwal->locale('id'),
            'akhir' => $tglAkhir->locale('id'),
        ];

        $anggotaKelas = Anggota_Kelas::with(['getSiswa', 'getTahunAkademik'])
            ->whereHas('getSiswa.transaksi', function ($q) use ($kodeAkun, $tglAwal, $tglAkhir) {
                $q->where('rekening_kredit', $kodeAkun)
                    ->whereBetween('tanggal_transaksi', [$tglAwal, $tglAkhir]);
            })
            ->when(!empty($data['sub_laporan']), function ($q) use ($data) {
                $q->where('kode_kelas', $data['sub_laporan']);
            })
            ->when(!empty($data['tahun_akademik_id']), function ($q) use ($data) {
                $tahun = Tahun_Akademik::find($data['tahun_akademik_id']);
                if ($tahun) {
                    $q->where('tahun_akademik', $tahun->nama_tahun);
                }
            })
            ->orderBy('id')
            ->get()
            ->map(function ($row) use ($tglAwal, $tglAkhir, $kodeAkun) {

                $trx = Transaksi::where('siswa_id', $row->getSiswa->id ?? 0)
                    ->where('rekening_kredit', $kodeAkun)
                    ->whereBetween('tanggal_transaksi', [$tglAwal, $tglAkhir])
                    ->orderByDesc('tanggal_transaksi')
                    ->get();

                $row->tgl_bayar_terakhir = $trx->max('tanggal_transaksi');
                $row->realisasi = (float) $trx->sum('jumlah');

                return $row;
            });

        $data['anggotaKelas'] = $anggotaKelas;

        $logoPath = \App\Models\Profil::logoPath();
        if (file_exists($logoPath)) {
            $data['logo'] = base64_encode(file_get_contents($logoPath));
            $data['logo_type'] = pathinfo($logoPath, PATHINFO_EXTENSION);
        }

        $view = view('laporan-keuangan.views.daftar_ulang', $data)->render();
        return $this->respond($view, $data, str_replace('.pdf', '.xls', $filename));
    }

    private function nominalJenisBiaya(Anggota_Kelas $row, int $idJp): float
    {
        $siswa = $row->getSiswa;
        if (!$siswa) {
            return 0;
        }

        $biaya = Jenis_Biaya::where('id_jp', $idJp)
            ->where('angkatan', (string) $siswa->tahun_akademik)
            ->first();

        return (float) ($biaya->total_beban ?? 0);
    }

    public function simpanSaldo()
    {
        $tahun = request()->get('tahun') ?: date('Y');
        $bulan = str_pad(request()->get('bulan') ?: date('m'), 2, '0', STR_PAD_LEFT);

        if ($bulan === '00') {
            $bulan = 12;
            $tahun = (int) $tahun - 1;
        }

        $start = "$tahun-01-01";
        $end   = date('Y-m-t', strtotime("$tahun-$bulan-01"));

        $rekening = Rekening::whereNull('tgl_nonaktif')->orderBy('kode_akun')->get();
        foreach ($rekening as $rek) {
            $d = (float) DB::table('transaksi')
                ->whereNull('deleted_at')
                ->whereBetween('tanggal_transaksi', [$start, $end])
                ->where('rekening_debit', $rek->kode_akun)
                ->sum('jumlah');
            $k = (float) DB::table('transaksi')
                ->whereNull('deleted_at')
                ->whereBetween('tanggal_transaksi', [$start, $end])
                ->where('rekening_kredit', $rek->kode_akun)
                ->sum('jumlah');

            Saldo::updateOrCreate(
                ['kode_akun' => $rek->kode_akun, 'tahun' => (int) $tahun, 'bulan' => (int) $bulan],
                ['debit' => $d, 'kredit' => $k]
            );
        }

        $nextBulan = (int) $bulan + 1;
        $nextTahun = (int) $tahun;
        if ($nextBulan > 12) {
            return '<script>window.opener.postMessage("closed","*");window.close();</script>';
        }

        $url = url('/app/laporan-keuangan/simpan-saldo')
            . '?tahun=' . $nextTahun
            . '&bulan=' . str_pad($nextBulan, 2, '0', STR_PAD_LEFT);

        return '<a id="next" href="' . $url . '"></a><script>document.getElementById("next").click()</script>';
    }
}
