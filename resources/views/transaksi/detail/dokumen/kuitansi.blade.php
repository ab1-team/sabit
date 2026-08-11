<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>KUITANSI</title>
    @php use App\Utils\Tanggal; use App\Utils\Terbilang; @endphp
    <style>
        body { font-size: 9px; font-family: Arial, Helvetica, sans-serif; padding: 20px; }
        .container { width: 14cm; min-height: 9cm; }
        .box { width: 14cm; min-height: 9cm; border: 2px solid #000; padding: 16px 22px 12px 12px; }
        .header { width: 100%; padding-bottom: 8px; border-bottom: 1px solid #000; }
        .header > div:first-child { width: 65%; }
        .header img { vertical-align: middle; width: 50px; height: 50px; }
        .header .info { display: inline-block; vertical-align: middle; margin-left: 8px; text-align: center; line-height: 1.3; }
        .header .fw-bold { font-size: 10px; font-weight: bold; }
        .header .small { font-size: 8px; }
        .header table { float: right; font-size: 9px; }
        .body { padding: 12px; font-size: 12px; }
        h1 { font-size: 16px; margin: 4px 0; }
        .body table { width: 100%; }
        .keterangan { padding: 2px 4px; }
        .jajargenjang { background-color: rgba(0,0,0,0.2); transform: skew(-20deg); text-align: center; }
        .sign-table { width: 100%; margin-top: 16px; }
        .center { text-align: center; }
        .small { font-size: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="box">
            <div class="header">
                <div>
                    @if (!empty($logo))
                        <img src="data:image/{{ $logo_type }};base64,{{ $logo }}">
                    @endif
                    <div class="info">
                        <div class="fw-bold">{{ strtoupper($profil->nama) }}</div>
                        <div class="small">{{ $profil->alamat }}</div>
                        <div class="small">Telp. {{ $profil->telpon }}</div>
                    </div>
                </div>
                <table>
                    <tr><td>Nomor</td><td>:</td><td>{{ $trx->idtp }} / {{ $jenis }}</td></tr>
                    <tr><td>Tanggal</td><td>:</td><td>{{ Tanggal::tglLatin($trx->tanggal_transaksi) }}</td></tr>
                </table>
            </div>
            <div class="body">
                <h1 class="center">KUITANSI</h1>
                <table>
                    <tr><td colspan="3">&nbsp;</td></tr>
                    <tr>
                        <td>Telah Terima Dari</td>
                        <td>:</td>
                        <td class="keterangan">{{ $trx->keterangan }}</td>
                    </tr>
                    <tr><td colspan="3">&nbsp;</td></tr>
                    <tr>
                        <td>Uang Sejumlah</td>
                        <td>:</td>
                        <td class="jajargenjang">
                            <h4 style="margin:4px 0;">{{ ucwords(Terbilang::rupiah((float) $trx->jumlah)) }} Rupiah</h4>
                        </td>
                    </tr>
                    <tr>
                        <td>Untuk Pembayaran</td>
                        <td>:</td>
                        <td class="keterangan">{{ $trx->keterangan }}</td>
                    </tr>
                    <tr><td colspan="3">&nbsp;</td></tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td class="keterangan text-end fw-bold">
                            <strong>Rp. {{ number_format((float) $trx->jumlah, 2, ',', '.') }}</strong>
                        </td>
                    </tr>
                </table>
                <table class="sign-table">
                    <tr>
                        <td class="center" colspan="2">{{ $profil->alamat ? explode(',', $profil->alamat)[0] : 'Tempat' }}, {{ Tanggal::tglLatin($trx->tanggal_transaksi) }}</td>
                    </tr>
                    <tr>
                        <td class="center" width="50%">Yang Menerima,</td>
                        <td class="center">Disiapkan Oleh,</td>
                    </tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td class="center">........................</td>
                        <td class="center">{{ auth()->user()->name ?? '........................' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
