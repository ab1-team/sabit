<?php

namespace App\Http\Controllers;


use App\Models\JenisLaporan;
use App\Models\Rekening;
use App\Models\Transaksi;
use App\Models\Profil;
use App\Models\Calk;
use App\Models\AkunLevel1;
use App\Models\MasterArusKas;
use App\Models\TandaTangan;
use App\Models\TahunAkademik;
use App\Models\Kelas;
use App\Models\Spp;
use App\Models\AnggotaKelas;
use App\Models\SubLaporan;
use App\Models\JenisBiaya;
use App\Models\Saldo;
use App\Services\LaporanPdfService;
use App\Utils\Keuangan;
use App\Utils\Tanggal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    private static array $aggCache = [];

    /**
     * Kirim laporan ke browser.
     *
     * @param string|iterable $viewHtml HTML laporan. Jika iterable (mis. array
     *                               berisi beberapa chunk), tiap chunk dirender
     *                               menjadi PDF terpisah lalu digabung menjadi
     *                               satu dokumen. Ini dipakai untuk laporan
     *                               dengan ribuan baris agar pemakaian memori
     *                               tetap datar (aman di server 256M).
     */
    private function respond(string|iterable $viewHtml, array $data, ?string $filename = null)
    {
        if (request('action') === 'excel') {
            $html = is_string($viewHtml)
                ? $viewHtml
                : implode('', is_array($viewHtml) ? $viewHtml : iterator_to_array($viewHtml, false));

            return response($html, 200, [
                'Content-Type'        => 'application/vnd.ms-excel; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . ($filename ?? 'laporan.xls') . '"',
                'Cache-Control'       => 'max-age=0',
                'Pragma'              => 'public',
            ]);
        }

        $landscape = !empty($data['_landscape']);

        // Nama berkas PDF harus berakhiran .pdf (nama aslinya berakhiran .xls
        // karena dipakai untuk export excel).
        $pdfName = pathinfo($filename ?? 'laporan.pdf', PATHINFO_FILENAME) . '.pdf';

        return app(LaporanPdfService::class)->stream($viewHtml, $landscape, $pdfName);
    }

    /**
     * Normalisasi parameter periode (tahun / bulan / hari) dari request.
     *
     * Parameter laporan bisa datang dari user (form) maupun dari request
     * tanpa nilai (mis. dipanggil langsung via URL). Nilai yang tidak valid
     * harus dinormalkan supaya tidak menyebabkan error 500, misalnya:
     *   - tahun kosong / 0 / "abc"  → memakai tahun berjalan
     *   - bulan kosong / 0 / 13     → memakai bulan berjalan
     *   - hari kosong / > 31        → null (artinya seluruh hari)
     *
     * @param  array  $data  data laporan (diubah di tempat)
     * @return array  data laporan yang sudah ternormalisasi
     */
    private function normalisasiPeriode(array $data): array
    {
        $sekarang = Carbon::now();

        $tahun = isset($data['tahun']) ? (int) $data['tahun'] : 0;
        if ($tahun < 1900 || $tahun > 9999) {
            $tahun = (int) $sekarang->format('Y');
        }

        $bulan = isset($data['bulan']) ? (int) $data['bulan'] : 0;
        if ($bulan < 1 || $bulan > 12) {
            $bulan = (int) $sekarang->format('m');
        }

        $hari = isset($data['hari']) && $data['hari'] !== '' ? (int) $data['hari'] : 0;
        if ($hari < 1 || $hari > 31) {
            $hari = 0;
        }

        $data['tahun'] = (string) $tahun;
        $data['bulan'] = str_pad((string) $bulan, 2, '0', STR_PAD_LEFT);
        $data['hari']  = $hari > 0 ? str_pad((string) $hari, 2, '0', STR_PAD_LEFT) : '';

        return $data;
    }

    public function index()
    {
        $title = 'Laporan Keuangan';
        $laporan = JenisLaporan::where('file', '!=', '0')
            ->orderBy('urut', 'ASC')
            ->get();
        return view('laporan-keuangan.daftar', compact('title', 'laporan'));
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

            $rekening = Rekening::where('kode_akun', 'NOT LIKE', '%.00')
                ->orderBy('kode_akun', 'ASC')
                ->get();
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

            $periode = $this->normalisasiPeriode([
                'tahun' => request('tahun'),
                'bulan' => request('bulan'),
            ]);

            $tanggal = "{$periode['tahun']}-{$periode['bulan']}-01";

            $calk = Calk::where('tanggal', $tanggal)->first();

            return view('laporan-keuangan.partials.sub_laporan', [
                'type'       => 'textarea',
                'keterangan' => $calk->catatan ?? ''
            ]);
} elseif (in_array($file, ['pembayaran_spp', 'daftar_ulang', 'pembangunan', 'ujian_semester', 'bantuan_yayasan'], true)) {

            $tahunAkademikId = request('tahun_akademik_id');
            $tahunAkademikNama = null;
            if (!empty($tahunAkademikId)) {
                $tahunAkademikNama = TahunAkademik::whereKey($tahunAkademikId)->value('nama_tahun');
            }

            $kodeKelasList = AnggotaKelas::query()
                ->when($tahunAkademikNama, function ($q) use ($tahunAkademikNama) {
                    $q->where('tahun_akademik', $tahunAkademikNama);
                })
                ->select('kode_kelas')
                ->distinct()
                ->orderBy('kode_kelas')
                ->pluck('kode_kelas')
                ->all();

            $kelasMap = Kelas::whereIn('kode_kelas', $kodeKelasList)->get()->keyBy('kode_kelas');

            $sub_laporan = [['value' => '', 'title' => '--- Semua Kelas ---']];
            foreach ($kodeKelasList as $kodeKelas) {
                $k = $kelasMap[$kodeKelas] ?? null;
                $sub_laporan[] = [
                    'value' => (string) $kodeKelas,
                    'title' => $kodeKelas . ' - ' . ($k->nama_kelas ?? $kodeKelas),
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

        // ================= PROFIL =================
        $data['profil'] = Profil::first();

        // ================= PERIODE (tahun/bulan/hari) =================
        $data = $this->normalisasiPeriode($data);

        // ================= SIMPAN CALK =================
        if ($laporan === 'calk' && $request->action !== 'excel') {

            $tanggal = "{$data['tahun']}-{$data['bulan']}-01";

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
        $data = $this->normalisasiPeriode($data);

        $thn  = $data['tahun'];
        $bln  = $data['bulan'];
        $hari = $data['hari'] !== '' ? $data['hari'] : '01';

        $tgl = $thn . '-' . $bln . '-' . $hari;

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
        $data = $this->normalisasiPeriode($data);

        $thn  = $data['tahun'];
        $bln  = $data['bulan'];

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
        $data['title'] = "Buku Besar " . ($rek->nama_akun ?? ($rek->kode_akun ?? '-'));

$kode = $rek->kode_akun;
        $isDebet = (strtolower((string) $rek->jenis_mutasi) === 'debet');

        // 1) Saldo awal tahun: 1 query aggregate
        $saldo_awal_raw = (float) DB::table('transaksi')
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

        $saldo_awal = $isDebet ? $saldo_awal_raw : -$saldo_awal_raw;
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
        //
        // CATATAN: kolom pada tabel `users` (tenant) adalah `nama`, bukan
        // `nama_lengkap` (kolom `nama_lengkap` hanya ada pada tabel pusat
        // `admin_user`). Meminta kolom `nama_lengkap` di sini membuat query
        // eager load gagal → HTTP 500 untuk rekening yang memiliki transaksi.
        $transaksi_bulan_ini = Transaksi::with('user:id,nama')
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
        $ttd = TandaTangan::first();
        $ttd?->applyDatePlaceholders($tgl_akhir_bulan);

        // Laporan PDF dipecah menjadi beberapa bagian agar pemakaian memori
        // tetap datar (aman untuk server production dengan memory_limit 256M).
        $pecah = request('action') !== 'excel';
        $potongan = $this->bukuBesarPotongan($transaksi_bulan_ini, $kode, $isDebet, $komulatif_saldo, $pecah);

        $views = [];
        foreach ($potongan as $potonganData) {
            $data['transaksi']         = $potonganData['transaksi'];
            $data['nomor_awal']       = $potonganData['nomor_awal'];
            $data['saldo_awal_chunk'] = $potonganData['saldo_awal'];
            $data['tampilkan_awal']   = $potonganData['tampilkan_awal'];
            $data['tampilkan_akhir']  = $potonganData['tampilkan_akhir'];
            $data['ttd']              = $potonganData['tampilkan_akhir'] ? $ttd : null;

            $views[] = view('laporan-keuangan.views.buku_besar', $data)->render();
        }

        return $this->respond($pecah ? $views : $views[0], $data, 'buku-besar.xls');
    }

    /**
     * Susun potongan (chunk) data untuk render Buku Besar.
     *
     * Setiap bagian membawa saldo awal bagian tersebut supaya kolom "Saldo"
     * tetap kontinyu meskipun laporan dipecah menjadi beberapa PDF.
     *
     * @param \Illuminate\Support\Collection $transaksi
     * @return array<int, array>
     */
    private function bukuBesarPotongan($transaksi, string $kode, bool $isDebet, float $saldoAwal, bool $pecah): array
    {
        if (! $pecah) {
            return [[
                'transaksi'        => $transaksi,
                'nomor_awal'      => 1,
                'saldo_awal'      => $saldoAwal,
                'tampilkan_awal'  => true,
                'tampilkan_akhir' => true,
            ]];
        }

        $potongan = [];
        $nomor    = 1;
        $index    = 0;
        $chunks   = $transaksi->chunk(LaporanPdfService::CHUNK_TRANSAKSI);
        $total    = $chunks->count();

        if ($total === 0) {
            // Tidak ada transaksi pada bulan ini → tetap ada satu bagian kosong
            // supaya laporan bisa dicetak (tanpa error).
            return [[
                'transaksi'        => $transaksi,
                'nomor_awal'      => 1,
                'saldo_awal'      => $saldoAwal,
                'tampilkan_awal'  => true,
                'tampilkan_akhir' => true,
            ]];
        }

        foreach ($chunks as $chunk) {
            // Collection::chunk() mempertahankan key asli, jadi di-reset agar
            // penomoran baris di view selalu dimulai dari 0 per bagian.
            $chunk = $chunk->values();

            $potongan[] = [
                'transaksi'        => $chunk,
                'nomor_awal'      => $nomor,
                'saldo_awal'      => $saldoAwal,
                'tampilkan_awal'  => $index === 0,
                'tampilkan_akhir' => $index === $total - 1,
            ];

            foreach ($chunk as $trx) {
                $saldoAwal += $this->bukuBesarMutasi($trx, $kode, $isDebet);
            }

            $nomor += $chunk->count();
            $index++;
        }

        return $potongan;
    }

    /**
     * Perubahan saldo (debit - kredit atau kredit - debit) dari satu transaksi.
     */
    private function bukuBesarMutasi($trx, string $kode, bool $isDebet): float
    {
        $debit  = $trx->rekening_debit == $kode ? (float) $trx->jumlah : 0.0;
        $kredit = $trx->rekening_kredit == $kode ? (float) $trx->jumlah : 0.0;

        return $isDebet ? ($debit - $kredit) : ($kredit - $debit);
    }

    private function jurnal_transaksi(array $data)
    {
        $data = $this->normalisasiPeriode($data);

        $thn  = $data['tahun'];
        $bln  = $data['bulan'];
        $hari = $data['hari'];

        $tgl = $thn . '-' . $bln . '-' . ($hari !== '' ? $hari : '01');

        $data['judul']     = 'Jurnal Transaksi';
        $data['sub_judul'] = 'Tahun ' . Tanggal::tahun($tgl);
        $data['tgl']       = Tanggal::tahun($tgl);
        $data['title']     = 'Jurnal Transaksi';
        if (!empty($data['bulan'])) {
            $data['sub_judul'] = 'Bulan ' . Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
            $data['tgl']       = Tanggal::namaBulan($tgl) . ' ' . Tanggal::tahun($tgl);
        }


        $transaksis = Transaksi::with(['rekeningDebit', 'rekeningKredit', 'user'])
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

        $ttd = TandaTangan::first();
        $ttd?->applyDatePlaceholders($tgl);

        // Total keseluruhan (bukan per bagian) supaya baris "Total" tetap
        // konsisten walaupun laporan dipecah menjadi beberapa bagian.
        $totalDebit  = (float) $transaksis->sum('jumlah');
        $totalKredit = (float) $transaksis->sum('jumlah');

        // Jurnal Transaksi bisa berisi ribuan transaksi (setiap transaksi
        // menghasilkan 2 baris tabel). Agar pemakaian memori tetap datar dan
        // aman di server production (memory_limit 256M), laporan dipecah
        // menjadi beberapa bagian lalu digabung menjadi satu PDF.
        $pecah = request('action') !== 'excel';
        $chunks = $pecah
            ? $transaksis->chunk(LaporanPdfService::CHUNK_TRANSAKSI)
            : collect([$transaksis]);

        $views = [];
        $index = 0;
        $total = $chunks->count();

        foreach ($chunks as $chunk) {
            $chunk = $chunk->values();

            $data['transaksis']      = $chunk;
            $data['nomor_awal']     = $index * LaporanPdfService::CHUNK_TRANSAKSI + 1;
            $data['total_debit']    = $totalDebit;
            $data['total_kredit']   = $totalKredit;
            $data['tampilkan_akhir'] = $index === $total - 1;
            $data['ttd']            = $data['tampilkan_akhir'] ? $ttd : null;

            $views[] = view('laporan-keuangan.views.jurnal_transaksi', $data)->render();

            $index++;
        }

        if ($views === []) {
            $data['transaksis']       = $transaksis;
            $data['nomor_awal']      = 1;
            $data['total_debit']     = $totalDebit;
            $data['total_kredit']    = $totalKredit;
            $data['tampilkan_akhir'] = true;
            $data['ttd']             = $ttd;
            $views[] = view('laporan-keuangan.views.jurnal_transaksi', $data)->render();
        }

        return $this->respond($pecah ? $views : $views[0], $data, 'jurnal-transaksi.xls');
    }

    private function arus_kas(array $data)
    {
        $data = $this->normalisasiPeriode($data);

        $thn  = $data['tahun'];
        $bln  = $data['bulan'];
        $hari = $data['hari'];

        $tgl_awal_tahun  = "{$thn}-01-01";
        $tgl_awal_bulan  = "{$thn}-{$bln}-01";
        $tgl_akhir_bulan = sprintf('%s-%s-%02d', $thn, $bln, cal_days_in_month(CAL_GREGORIAN, (int) $bln, (int) $thn));

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
        $arusKas = MasterArusKas::with(['child', 'child.rek_debit', 'child.rek_kredit'])
            ->where('parent_id', 0)
            ->get();

        foreach ($arusKas as $top) {
            foreach ($top->child as $child) {
                $akun3 = $child->rek_debit ?: $child->rek_kredit;
                if (!$akun3) continue;

                $kodeList = $akun3->rekeningByPrefix()->pluck('kode_akun')->all();
                if (empty($kodeList)) {
                    $child->setAttribute('transaksi_list', collect());
                    continue;
                }

                $debitTrx = DB::table('transaksi')
                    ->whereIn('rekening_debit', $kodeList)
                    ->whereBetween('tanggal_transaksi', [$tgl_awal_bulan, $tgl_akhir_bulan])
                    ->whereNull('deleted_at')
                    ->orderBy('tanggal_transaksi')
                    ->get();

                $kreditTrx = DB::table('transaksi')
                    ->whereIn('rekening_kredit', $kodeList)
                    ->whereBetween('tanggal_transaksi', [$tgl_awal_bulan, $tgl_akhir_bulan])
                    ->whereNull('deleted_at')
                    ->orderBy('tanggal_transaksi')
                    ->get();

                $child->setAttribute('transaksi_list', collect()->merge($debitTrx)->merge($kreditTrx));
            }
        }

        $data['arus_kas'] = $arusKas;
        $data['ttd'] = TandaTangan::first();
        $data['ttd']?->applyDatePlaceholders($tgl_akhir_bulan);

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
        $data = $this->normalisasiPeriode($data);

        $thn  = $data['tahun'];
        $bln  = $data['bulan'];
        $hari = $data['hari'] !== '' ? $data['hari'] : null;

        $tgl = $thn
            . '-' . $bln
            . '-' . ($hari ?: sprintf('%02d', cal_days_in_month(CAL_GREGORIAN, (int) $bln, (int) $thn)));

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
        $data['ttd'] = TandaTangan::first();
        $data['ttd']?->applyDatePlaceholders($tgl);

        $view = view('laporan-keuangan.views.laba_rugi', $data)->render();

        return $this->respond($view, $data, 'laba-rugi.xls');
    }

    private function neraca(array $data)
    {
        $data = $this->normalisasiPeriode($data);

        $thn  = $data['tahun'];
        $bln  = $data['bulan'];

        $tgl_awal  = "{$thn}-01-01";
        $tgl_akhir = sprintf('%s-%s-%02d', $thn, $bln, cal_days_in_month(CAL_GREGORIAN, (int) $bln, (int) $thn));

        $data['judul'] = 'Neraca';
        $namaBulan = Tanggal::namaBulan("{$thn}-{$bln}-01");
        $lastDay   = date('t', strtotime("{$thn}-{$bln}-01"));

        $data['sub_judul'] = !empty($data['bulan'])
            ? 'per ' . $lastDay . ' ' . $namaBulan . ' ' . $thn
            : 'Tahun ' . $thn;

        $data['title'] = !empty($data['bulan']) ? $data['judul'] . ' (' . $namaBulan . ' ' . $thn . ')' : $data['judul'] . ' Tahun ' . $thn;

        $rekeningIdsYangPunyaTrx = DB::table('transaksi')
            ->whereNull('deleted_at')
            ->whereBetween('tanggal_transaksi', [$tgl_awal, $tgl_akhir])
            ->where(function ($q) {
                $q->whereNotNull('rekening_debit')->where('rekening_debit', '!=', '')
                  ->orWhereNotNull('rekening_kredit')->where('rekening_kredit', '!=', '');
            })
            ->selectRaw('DISTINCT rekening_debit as kode')
            ->unionAll(
                DB::table('transaksi')
                    ->whereNull('deleted_at')
                    ->whereBetween('tanggal_transaksi', [$tgl_awal, $tgl_akhir])
                    ->selectRaw('DISTINCT rekening_kredit as kode')
            )
            ->pluck('kode')
            ->filter()
            ->unique()
            ->values()
            ->all();

$data['akun1'] = AkunLevel1::whereIn('kode_akun', ['1.0.00.00', '2.0.00.00', '3.0.00.00'])
            ->with(['akun2.akun3.rek' => function ($q) use ($rekeningIdsYangPunyaTrx) {
                $q->whereNull('tgl_nonaktif');
                if (!empty($rekeningIdsYangPunyaTrx)) {
                    $q->whereIn('kode_akun', $rekeningIdsYangPunyaTrx);
                }
            }])
            ->orderBy('kode_akun', 'ASC')
            ->get();

        $keuangan = new Keuangan();
        $lr = $keuangan->listLabaRugi($tgl_akhir);

        $sumSaldo = function ($coll) {
            $sum = 0;
            foreach ($coll as $rek) {
                $sum += (float) ($rek->saldo ?? 0);
            }
            return $sum;
        };
        $pendapatan = $sumSaldo($lr['pendapatan']);
        $beban      = $sumSaldo($lr['beban']);
        $bp         = $sumSaldo($lr['bp']);
        $pen        = $sumSaldo($lr['pen']);
        $pendl      = $sumSaldo($lr['pendl']);
        $beb        = $sumSaldo($lr['beb']);
        $ph         = $sumSaldo($lr['ph']);

        $laba_rugi_berjalan = ($pendapatan + $pen + $pendl) - ($beban + $bp + $beb + $ph);

        $data['laba_rugi_berjalan'] = $laba_rugi_berjalan;

$data['tgl_awal']  = $tgl_awal;
        $data['tgl_akhir'] = $tgl_akhir;
        $data['ttd'] = TandaTangan::first();
        $data['ttd']?->applyDatePlaceholders($tgl_akhir);

        $view = view('laporan-keuangan.views.neraca', $data)->render();

        return $this->respond($view, $data, 'neraca.xls');
    }

    private function neraca_saldo(array $data)
    {
        $data = $this->normalisasiPeriode($data);

        $thn  = $data['tahun'];
        $bln  = $data['bulan'];
        $hari = $data['hari'];

        $tgl_awal  = "{$thn}-01-01";
        $tgl_akhir = sprintf('%s-%s-%02d', $thn, $bln, cal_days_in_month(CAL_GREGORIAN, (int) $bln, (int) $thn));

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

        $kodeList = Rekening::whereNull('tgl_nonaktif')->pluck('kode_akun')->all();
        $rekenings = Rekening::whereNull('tgl_nonaktif')->orderBy('kode_akun')->get(['kode_akun', 'nama_akun', 'jenis_mutasi', 'lev1']);

        $debits = DB::table('transaksi')
            ->whereNull('deleted_at')
            ->whereBetween('tanggal_transaksi', [$tgl_awal, $tgl_akhir])
            ->whereIn('rekening_debit', $kodeList)
            ->groupBy('rekening_debit')
            ->selectRaw('rekening_debit as kode_akun, SUM(jumlah) as total')
            ->pluck('total', 'kode_akun');

        $kredits = DB::table('transaksi')
            ->whereNull('deleted_at')
            ->whereBetween('tanggal_transaksi', [$tgl_awal, $tgl_akhir])
            ->whereIn('rekening_kredit', $kodeList)
            ->groupBy('rekening_kredit')
            ->selectRaw('rekening_kredit as kode_akun, SUM(jumlah) as total')
            ->pluck('total', 'kode_akun');

        $data['rekening'] = $rekenings->map(function ($r) use ($debits, $kredits) {
            $r->total_debit  = (float) ($debits[$r->kode_akun] ?? 0);
            $r->total_kredit = (float) ($kredits[$r->kode_akun] ?? 0);
return $r;
        });
        $data['ttd'] = TandaTangan::first();
        $data['ttd']?->applyDatePlaceholders($tgl_akhir);

        $view = view('laporan-keuangan.views.neraca_saldo', $data)->render();

        return $this->respond($view, $data, 'neraca-saldo.xls');
    }

    private function calk(array $data)
    {
        $data = $this->normalisasiPeriode($data);

        $thn  = $data['tahun'];
        $bln  = $data['bulan'];

        $tgl_awal  = "{$thn}-01-01";
        $tgl_akhir = sprintf('%s-%s-%02d', $thn, $bln, cal_days_in_month(CAL_GREGORIAN, (int) $bln, (int) $thn));

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

        $data['akun1'] = AkunLevel1::whereIn('kode_akun', ['1.0.00.00', '2.0.00.00', '3.0.00.00'])
            ->with(['akun2.akun3.rek' => function ($q) {
                $q->whereNull('tgl_nonaktif');
            }])
            ->orderBy('kode_akun', 'ASC')
            ->get();
        
        $data['tgl_awal']  = $tgl_awal;
        $data['tgl_akhir'] = $tgl_akhir;

        $tanggal = "{$thn}-{$bln}-01";

        $calk = Calk::where('tanggal', $tanggal)->first();

$data['catatan'] = $calk ? $calk->catatan : '';
        $data['ttd'] = TandaTangan::first();
        $data['ttd']?->applyDatePlaceholders($tgl_akhir);

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

if (empty($data['tahun_akademik_id'])) {
            $taAktif = TahunAkademik::aktif();
            if ($taAktif) {
                $data['tahun_akademik_id'] = $taAktif->id;
            }
        }

        $namaTahunAkademik = null;
        if (!empty($data['tahun_akademik_id'])) {
            static $taCache = [];
            if (isset($taCache[$data['tahun_akademik_id']])) {
                $namaTahunAkademik = $taCache[$data['tahun_akademik_id']];
            } else {
                $namaTahunAkademik = TahunAkademik::whereKey($data['tahun_akademik_id'])->value('nama_tahun');
                $taCache[$data['tahun_akademik_id']] = $namaTahunAkademik;
            }
        }

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

        $akQuery = DB::table('anggota_kelas as ak')
            ->select('ak.*', 's.nama as siswa_nama', 's.nisn as siswa_nisn')
            ->leftJoin('siswa as s', 's.id', '=', 'ak.id_siswa')
            ->when(!empty($data['sub_laporan']), function ($q) use ($data) {
                $q->where('ak.kode_kelas', $data['sub_laporan']);
            })
            ->when($namaTahunAkademik, function ($q) use ($namaTahunAkademik) {
                $q->where('ak.tahun_akademik', $namaTahunAkademik);
            })
            ->orderBy('ak.kode_kelas')
            ->orderBy('s.nama')
            ->orderBy('ak.id');

        $anggotaKelas = $akQuery->get();

        $aggByAk = [];
        if ($anggotaKelas->isNotEmpty()) {
            $cacheKey = 'lap:sppa:' . md5(($namaTahunAkademik ?? '') . '|' . ($data['sub_laporan'] ?? '') . '|' . $tglAwal->toDateString() . '|' . $tglAkhir->toDateString());

            if (!isset(self::$aggCache[$cacheKey])) {
                self::$aggCache[$cacheKey] = DB::table('spp')
                    ->join('anggota_kelas as ak', 'ak.id', '=', 'spp.anggota_kelas')
                    ->when(!empty($data['sub_laporan']), function ($q) use ($data) {
                        $q->where('ak.kode_kelas', $data['sub_laporan']);
                    })
                    ->when($namaTahunAkademik, function ($q) use ($namaTahunAkademik) {
                        $q->where('ak.tahun_akademik', $namaTahunAkademik);
                    })
                    ->whereBetween('spp.tanggal', [$tglAwal, $tglAkhir])
                    ->select('spp.anggota_kelas', 'spp.status', DB::raw('SUM(CAST(spp.nominal AS UNSIGNED)) as total'))
                    ->groupBy('spp.anggota_kelas', 'spp.status')
                    ->get()
                    ->toArray();
            }
            $rows = self::$aggCache[$cacheKey];

            foreach ($rows as $r) {
                $key = (int) $r->anggota_kelas;
                $aggByAk[$key][$r->status] = (float) $r->total;
            }
        }

        $anggotaKelas->transform(function ($row) use (&$aggByAk) {
            $row->per_bulan = (int) ($row->spp_nominal ?? 0);

            // Total tagihan = jumlah semua baris spp (Lunas maupun Belum). Pembayaran = hanya yang Lunas.
            // Sisa = Tagihan - Pembayaran (>=0; berarti "sisa yang belum dibayar").
            $row->target_sd_saat_ini = ($aggByAk[$row->id]['B'] ?? 0) + ($aggByAk[$row->id]['L'] ?? 0);
            $row->sd_periode_ini    = $aggByAk[$row->id]['L'] ?? 0;
            $row->sisa = $row->target_sd_saat_ini - $row->sd_periode_ini;

            return $row;
        });

$totals = [
            'per_bulan'          => 0,
            'target_sd_saat_ini' => 0,
            'sd_periode_ini'     => 0,
            'sisa'               => 0,
        ];
        foreach ($anggotaKelas as $row) {
            $totals['per_bulan']          += (int) ($row->per_bulan ?? 0);
            $totals['target_sd_saat_ini'] += (int) ($row->target_sd_saat_ini ?? 0);
            $totals['sd_periode_ini']     += (int) ($row->sd_periode_ini ?? 0);
            $totals['sisa']               += (int) ($row->sisa ?? 0);
        }

$data['totals'] = $totals;
        $data['anggotaKelas']     = $anggotaKelas;
        $data['bulanList']        = $bulanList;
        $data['namaTahunAkademik'] = $namaTahunAkademik;
        $data['profil'] = Profil::first();

        $logoPath = \App\Models\Profil::logoPath();
        if (file_exists($logoPath)) {
            $data['logo'] = base64_encode(file_get_contents($logoPath));
            $data['logo_type'] = pathinfo($logoPath, PATHINFO_EXTENSION);
        }

        $data['_landscape'] = true;

        return $this->respond(
            $this->renderBertingkat('laporan-keuangan.views.pembayaran_spp', $data, $anggotaKelas),
            $data,
            'laporan-spp.xls'
        );
    }

    /**
     * Render sebuah view menjadi satu atau beberapa bagian HTML.
     *
     * Laporan dengan banyak baris (mis. pembayaran SPP, daftar ulang, dll.)
     * dipecah menjadi beberapa bagian agar pemakaian memori saat dirender oleh
     * dompdf tetap datar dan aman pada server production (memory_limit 256M).
     * Bagian-bagian ini kemudian digabung menjadi satu PDF oleh
     * {@see LaporanPdfService}.
     *
     * @param  string  $view       nama view blade
     * @param  array   $data       data view
     * @param  mixed   $rows       koleksi baris yang dipecah (nullable)
     * @return array<int, string> HTML per bagian
     */
    private function renderBertingkat(string $view, array $data, $rows = null): array
    {
        // Export excel tidak dipecah (tanpa dompdf, tidak ada masalah memori).
        if (request('action') === 'excel' || $rows === null) {
            $data['nomor_awal']         = $data['nomor_awal'] ?? 1;
            $data['tampilkan_ringkasan'] = $data['tampilkan_ringkasan'] ?? true;

            return [view($view, $data)->render()];
        }

        $chunks = collect($rows)->chunk(LaporanPdfService::CHUNK_TRANSAKSI);
        $total  = $chunks->count();
        $views  = [];
        $index  = 0;

        foreach ($chunks as $chunk) {
            $data['anggotaKelas'] = $chunk->values();
            $data['nomor_awal']   = $index * LaporanPdfService::CHUNK_TRANSAKSI + 1;
            $data['tampilkan_ringkasan'] = ($index === $total - 1);

            $views[] = view($view, $data)->render();

            $index++;
        }

        if ($views === []) {
            $data['anggotaKelas'] = collect();
            $data['nomor_awal']   = 1;
            $data['tampilkan_ringkasan'] = true;
            $views[] = view($view, $data)->render();
        }

        return $views;
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
            '4.1.01.03',
            'Laporan Pembayaran Pembangunan',
            'laporan-pembangunan.pdf',
            fn($row) => $this->nominalJenisBiaya($row, 3)
        );
    }

    public function ujian_semester(Request $request)
    {
        return $this->laporanPembayaranNonSpp(
            $request,
            '4.1.01.04',
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

if (empty($data['tahun_akademik_id'])) {
            $taAktif = TahunAkademik::aktif();
            if ($taAktif) {
                $data['tahun_akademik_id'] = $taAktif->id;
            }
        }

        $namaTahunAkademik = null;
        if (!empty($data['tahun_akademik_id'])) {
            static $taCache2 = [];
            if (isset($taCache2[$data['tahun_akademik_id']])) {
                $namaTahunAkademik = $taCache2[$data['tahun_akademik_id']];
            } else {
                $namaTahunAkademik = TahunAkademik::whereKey($data['tahun_akademik_id'])->value('nama_tahun');
                $taCache2[$data['tahun_akademik_id']] = $namaTahunAkademik;
            }
        }

        $data['kelas'] = !empty($data['sub_laporan'])
            ? Kelas::where('kode_kelas', $data['sub_laporan'])->first()
            : null;

        $tglAwal  = Carbon::parse($data['tgl_awal'])->startOfDay();
        $tglAkhir = Carbon::parse($data['tgl_akhir'])->endOfDay();

        $data['periode'] = [
            'awal'  => $tglAwal->locale('id'),
            'akhir' => $tglAkhir->locale('id'),
        ];

        $anggotaKelas = DB::table('anggota_kelas as ak')
            ->select('ak.*', 's.id as siswa_id', 's.nama as siswa_nama', 's.nisn as siswa_nisn')
            ->leftJoin('siswa as s', 's.id', '=', 'ak.id_siswa')
            ->when(!empty($data['sub_laporan']), function ($q) use ($data) {
                $q->where('ak.kode_kelas', $data['sub_laporan']);
            })
            ->when($namaTahunAkademik, function ($q) use ($namaTahunAkademik) {
                $q->where('ak.tahun_akademik', $namaTahunAkademik);
            })
            ->orderBy('ak.id')
            ->get();

        $siswaIds = $anggotaKelas->pluck('siswa_id')->filter()->unique()->values()->all();

        $trxBySiswa = [];
        if (!empty($siswaIds)) {
            $cacheKey = 'lap:trxa:' . md5($kodeAkun . '|' . ($namaTahunAkademik ?? '') . '|' . ($data['sub_laporan'] ?? '') . '|' . $tglAwal->toDateString() . '|' . $tglAkhir->toDateString());

            if (!isset(self::$aggCache[$cacheKey])) {
                self::$aggCache[$cacheKey] = DB::table('transaksi')
                    ->join('siswa as s', 's.id', '=', 'transaksi.siswa_id')
                    ->join('anggota_kelas as ak', 'ak.id_siswa', '=', 's.id')
                    ->where('transaksi.rekening_kredit', $kodeAkun)
                    ->whereBetween('transaksi.tanggal_transaksi', [$tglAwal, $tglAkhir])
                    ->whereNull('transaksi.deleted_at')
                    ->when(!empty($data['sub_laporan']), function ($q) use ($data) {
                        $q->where('ak.kode_kelas', $data['sub_laporan']);
                    })
                    ->when($namaTahunAkademik, function ($q) use ($namaTahunAkademik) {
                        $q->where('ak.tahun_akademik', $namaTahunAkademik);
                    })
                    ->selectRaw('transaksi.siswa_id, MAX(transaksi.tanggal_transaksi) as max_tgl, SUM(transaksi.jumlah) as total')
                    ->groupBy('transaksi.siswa_id')
                    ->get()
                    ->toArray();
            }
            $rows = self::$aggCache[$cacheKey];

            foreach ($rows as $r) {
                $sid = (int) $r->siswa_id;
                $trxBySiswa[$sid] = ['max' => $r->max_tgl, 'sum' => (float) $r->total];
            }
        }

        foreach ($anggotaKelas as $row) {
            $sid = (int) $row->siswa_id;
            $stat = $trxBySiswa[$sid] ?? null;
            $row->tgl_bayar_terakhir = $stat['max'] ?? null;
            $row->realisasi = $stat['sum'] ?? 0;
            $row->sudah_bayar = $stat !== null;
        }

        $data['anggotaKelas'] = $anggotaKelas;
        $data['profil'] = Profil::first();

        // Ringkasan memakai data lengkap agar tetap konsisten ketika laporan
        // dipecah menjadi beberapa bagian.
        $data['total_siswa']      = $anggotaKelas->count();
        $data['total_sudah_bayar'] = $anggotaKelas->filter(fn($r) => $r->sudah_bayar ?? false)->count();
        $data['total_realisasi']  = $anggotaKelas->sum(fn($r) => $r->sudah_bayar ? ($r->realisasi ?? 0) : 0);

        $logoPath = \App\Models\Profil::logoPath();
        if (file_exists($logoPath)) {
            $data['logo'] = base64_encode(file_get_contents($logoPath));
            $data['logo_type'] = pathinfo($logoPath, PATHINFO_EXTENSION);
        }

        return $this->respond(
            $this->renderBertingkat('laporan-keuangan.views.daftar_ulang', $data, $anggotaKelas),
            $data,
            str_replace('.pdf', '.xls', $filename)
        );
    }

    private function nominalJenisBiaya($row, int $idJp): float
    {
        if (!isset($row->siswa_tahun_akademik)) {
            return 0;
        }

        $biaya = JenisBiaya::where('id_jp', $idJp)
            ->where('angkatan', (string) $row->siswa_tahun_akademik)
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

        $rekening = Rekening::whereNull('tgl_nonaktif')->orderBy('kode_akun')->get(['kode_akun']);
        $kodeList = $rekening->pluck('kode_akun')->all();

        if (!empty($kodeList)) {
            $debits = DB::table('transaksi')
                ->whereNull('deleted_at')
                ->whereBetween('tanggal_transaksi', [$start, $end])
                ->whereIn('rekening_debit', $kodeList)
                ->groupBy('rekening_debit')
                ->selectRaw('rekening_debit as kode_akun, SUM(jumlah) as total')
                ->pluck('total', 'kode_akun');

            $kredits = DB::table('transaksi')
                ->whereNull('deleted_at')
                ->whereBetween('tanggal_transaksi', [$start, $end])
                ->whereIn('rekening_kredit', $kodeList)
                ->groupBy('rekening_kredit')
                ->selectRaw('rekening_kredit as kode_akun, SUM(jumlah) as total')
                ->pluck('total', 'kode_akun');

            $rows = [];
            $now = now();
            foreach ($kodeList as $kode) {
                $rows[] = [
                    'kode_akun' => $kode,
                    'tahun'     => (int) $tahun,
                    'bulan'     => (int) $bulan,
                    'debit'     => (float) ($debits[$kode] ?? 0),
                    'kredit'    => (float) ($kredits[$kode] ?? 0),
                    'updated_at' => $now,
                    'created_at' => $now,
                ];
            }

            if (!empty($rows)) {
                DB::table('saldo')->upsert(
                    $rows,
                    ['kode_akun', 'tahun', 'bulan'],
                    ['debit', 'kredit', 'updated_at']
                );
            }
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


