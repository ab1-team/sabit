@php
use App\Utils\Tanggal;
$kas = fn($x) => str_starts_with((string) $x, '1.1.01');
$bank = fn($x) => str_starts_with((string) $x, '1.1.02');
$beban = fn($x) => str_starts_with((string) $x, '5.');
$pendapatan = fn($x) => str_starts_with((string) $x, '4.');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Bukti Transaksi</title>
    <style>
        @page { size: A4 landscape; margin: 6mm; }
        body { font-size: 9px; font-family: Arial, Helvetica, sans-serif; margin: 0; padding: 0; }
        .container { width: 100%; }
        .row { display: table; table-layout: fixed; border-collapse: separate; border-spacing: 3px; width: 100%; }
        .row.single-box { width: 50%; }
        .row.single-box .box { width: 100%; }
        .box {
            box-sizing: border-box;
            display: table-cell;
            width: 50%;
            height: 7.9cm;
            border: 2px solid #000;
            padding: 6px 8px;
            vertical-align: top;
        }
        h1 { font-size: 13px; margin: 3px 0; text-align: center; }
        .header { display: table; table-layout: fixed; width: 100%; border-bottom: 1px solid #000; padding-bottom: 4px; margin-bottom: 4px; }
        .header img { display: table-cell; width: 50px; height: 50px; vertical-align: middle; }
        .header .info { display: table-cell; width: auto; text-align: center; vertical-align: middle; font-size: 9px; line-height: 1.3; }
        .header .info .small { font-size: 8px; }
        .header .info .fw-bold { font-size: 10px; }
        .header .nomor { display: table-cell; width: 50px; }
        .nomor-row { text-align: right; font-size: 9px; margin-top: 4px; margin-bottom: 2px; }
        .body table { width: 100%; }
        .body td { padding: 1px 3px; vertical-align: top; }
        .keterangan { font-weight: normal; }
        .sign-table { width: 100%; margin-top: 8px; }
        .sign-table td { padding: 0; text-align: center; }
        .fw-bold { font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        @foreach ($transaksi->chunk(4) as $page)
            @foreach ($page->chunk(2) as $row)
                <div class="row{{ $row->count() === 1 ? ' single-box' : '' }}">
                    @foreach ($row as $trx)
                        @php
                            $d = $trx->rekening_debit;
                            $k = $trx->rekening_kredit;
                            $jenis = 'bm';
                            $judul = 'BUKTI MEMORIAL';
                            if ($kas($d) && !$kas($k)) { $jenis = 'bkm'; $judul = 'BUKTI KAS MASUK'; }
                            elseif ($bank($d) && ($kas($k) || $bank($k))) { $jenis = 'bkm'; $judul = 'BUKTI KAS MASUK'; }
                            elseif ($beban($d) && ($kas($k) || $bank($k))) { $jenis = 'bkk'; $judul = 'BUKTI KAS KELUAR'; }
                            elseif ($kas($k) && !$kas($d)) { $jenis = 'bkk'; $judul = 'BUKTI KAS KELUAR'; }
                            elseif (($kas($d) || $bank($d)) && $pendapatan($k)) { $jenis = 'bkm'; $judul = 'BUKTI KAS MASUK'; }
                            elseif ($kas($d) && $bank($k)) { $jenis = 'bm'; $judul = 'BUKTI MEMORIAL'; }
                        @endphp
                        <div class="box">
                            <div class="header">
                                @if (!empty($logo))
                                    <img src="data:image/{{ $logo_type }};base64,{{ $logo }}">
                                @endif
                                <div class="info">
                                    <div class="fw-bold">{{ strtoupper($profil->nama) }}</div>
                                    <div class="small">{{ $profil->alamat }}</div>
                                    <div class="small">Telp. {{ $profil->telpon }}</div>
                                </div>
                                <div class="nomor">&nbsp;</div>
                            </div>
                            <div class="nomor-row">
                                <table align="right" style="font-size:9px;">
                                    <tr><td>Nomor</td><td>:</td><td>{{ $trx->idtp }} / {{ strtoupper($jenis) }}</td></tr>
                                    <tr><td>Tanggal</td><td>:</td><td>{{ Tanggal::tglIndo($trx->tanggal_transaksi) }}</td></tr>
                                </table>
                            </div>
                            <div class="body">
                                <h1>{{ $judul }}</h1>
                                <table>
                                    @if ($jenis != 'bm')
                                        <tr>
                                            <td width="28%">{{ $jenis == 'bkk' ? 'Dibayar Kepada' : 'Terima Dari' }}</td>
                                            <td width="2%">:</td>
                                            <td colspan="3" class="keterangan">{{ $trx->keterangan }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td>Keterangan</td>
                                        <td>:</td>
                                        <td colspan="3" class="keterangan">{{ $trx->keterangan }}</td>
                                    </tr>
                                    <tr>
                                        <td>Jumlah</td>
                                        <td>:</td>
                                        <td colspan="3" class="keterangan">Rp. {{ number_format((float) $trx->jumlah, 2, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Kode Akun (D/K)</td>
                                        <td></td>
                                        <td colspan="3" class="keterangan">Debit {{ $trx->rekening_debit }} - {{ $trx->rekeningDebit->nama_akun ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td colspan="3" class="keterangan">Kredit {{ $trx->rekening_kredit }} - {{ $trx->rekeningKredit->nama_akun ?? '' }}</td>
                                    </tr>
                                </table>
                                <table class="sign-table">
                                    <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                                    <tr>
                                        <td class="center">Disetujui,</td>
                                        <td class="center">Diverifikasi,</td>
                                        <td class="center">Disiapkan Oleh,</td>
                                    </tr>
                                    <tr><td colspan="3">&nbsp;</td></tr>
                                    <tr><td colspan="3">&nbsp;</td></tr>
                                    <tr><td colspan="3">&nbsp;</td></tr>
                                    <tr>
                                        <td class="center">{{ $profil->penanggung_jawab ?? '........................' }}</td>
                                        <td class="center">........................</td>
                                        <td class="center">{{ auth()->user()->name ?? '........................' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
            <div style="page-break-after: always;"></div>
        @endforeach
    </div>
</body>
</html>
