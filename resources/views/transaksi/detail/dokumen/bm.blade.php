<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>BUKTI MEMORIAL</title>
    @php use App\Utils\Tanggal; @endphp
    <style>
        body { font-size: 9px; font-family: Arial, Helvetica, sans-serif; padding: 20px; }
        .box { width: 14cm; min-height: 9cm; border: 2px solid #000; padding: 14px 18px; box-sizing: border-box; }
        .header { width: 100%; padding-bottom: 8px; border-bottom: 1px solid #000; }
        .header::after { content: ""; display: block; clear: both; }
        .header .logo { float: left; width: 60px; }
        .header .logo img { width: 60px; height: 60px; object-fit: contain; }
        .header .info { margin-left: 70px; text-align: center; line-height: 1.35; }
        .header .fw-bold { font-size: 13px; font-weight: bold; text-transform: uppercase; }
        .header .small { font-size: 9px; }
        .header .meta-wrap { clear: both; padding-top: 4px; text-align: right; font-size: 9px; }
        .header .meta-wrap td { padding: 0 4px; }
        h1 { font-size: 14px; margin: 8px 0 6px; letter-spacing: 1px; }
        .body { padding: 4px 6px 0; font-size: 11px; }
        .body table.kop { width: 100%; border-collapse: collapse; }
        .body table.kop td { vertical-align: top; padding: 2px 4px; }
        .sign-table { width: 100%; margin-top: 14px; font-size: 11px; border-collapse: collapse; }
        .sign-table td { text-align: center; padding: 2px; }
        .center { text-align: center; }
        .small { font-size: 10px; }
    </style>
</head>
<body>
    <div class="box">
        <div class="header">
            <div class="logo">
                @if (!empty($logo))
                    <img src="data:image/{{ $logo_type }};base64,{{ $logo }}">
                @endif
            </div>
            <div class="info">
                <div class="fw-bold">{{ strtoupper($profil->nama) }}</div>
                <div class="small">{{ $profil->alamat }}</div>
                <div class="small">Telp. {{ $profil->telpon }}</div>
            </div>
        </div>
        <table class="meta-wrap">
            <tr><td>Nomor</td><td>:</td><td>{{ $trx->idtp }} / BM</td></tr>
            <tr><td>Tanggal</td><td>:</td><td>{{ Tanggal::tglIndo($trx->tanggal_transaksi) }}</td></tr>
        </table>
        <div class="body">
            <h1 class="center">BUKTI MEMORIAL</h1>
            <table class="kop">
                <tr>
                    <td width="28%">Keterangan</td>
                    <td width="2%">:</td>
                    <td>{{ $trx->keterangan }}</td>
                </tr>
                <tr>
                    <td>Jumlah</td>
                    <td>:</td>
                    <td>Rp. {{ number_format((float) $trx->jumlah, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Kode Akun (D/K)</td>
                    <td></td>
                    <td>Debit {{ $trx->rekening_debit }} - {{ $trx->rekeningDebit->nama_akun ?? '' }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>Kredit {{ $trx->rekening_kredit }} - {{ $trx->rekeningKredit->nama_akun ?? '' }}</td>
                </tr>
            </table>
            <table class="sign-table">
                <tr>
                    <td>Disetujui,</td>
                    <td>Diverifikasi,</td>
                    <td>Disiapkan Oleh,</td>
                </tr>
                <tr><td colspan="3">&nbsp;</td></tr>
                <tr><td colspan="3">&nbsp;</td></tr>
                <tr><td colspan="3">&nbsp;</td></tr>
                <tr>
                    <td>{{ $profil->penanggung_jawab ?? '........................' }}</td>
                    <td>........................</td>
                    <td>{{ auth()->user()->name ?? '........................' }}</td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
