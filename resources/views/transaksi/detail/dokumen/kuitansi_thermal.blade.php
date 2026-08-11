@php use App\Utils\Tanggal; use App\Utils\Terbilang; @endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kuitansi Thermal</title>
    <style>
        @page { size: 80mm 90mm; margin: 4mm; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 9px; padding: 4px; margin: 0; }
        .top { border-top: 1px solid #000; }
        .bottom { border-bottom: 1px solid #000; }
        .center { text-align: center; }
        .small { font-size: 8px; }
    </style>
</head>
<body>
    <table width="100%" border="0" cellpadding="1" cellspacing="0">
        <tr>
            <td colspan="3" class="bottom center">
                @if (!empty($logo))
                    <img src="data:image/{{ $logo_type }};base64,{{ $logo }}" style="height:30px;">
                @endif
                <div class="fw-bold">{{ strtoupper($profil->nama) }}</div>
                <div class="small">{{ $profil->alamat }}</div>
                <div class="small">{{ $profil->telpon }}</div>
            </td>
        </tr>
        <tr>
            <td colspan="3" class="bottom center fw-bold"><b>K U I T A N S I</b></td>
        </tr>
        <tr>
            <td width="32%">No</td>
            <td width="2%" class="center">:</td>
            <td>{{ $trx->idtp }} / {{ $jenis }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td class="center">:</td>
            <td>{{ Tanggal::tglLatin($trx->tanggal_transaksi) }}</td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td>Telah Terima Dari</td>
            <td class="center">:</td>
            <td>{{ $trx->keterangan }}</td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td>Uang Sejumlah</td>
            <td class="center">:</td>
            <td><b>{{ ucwords(Terbilang::rupiah((float) $trx->jumlah)) }} RUPIAH</b></td>
        </tr>
        <tr>
            <td>Untuk</td>
            <td class="center">:</td>
            <td>{{ $trx->keterangan }}</td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="3" class="center">
                <b>Rp. {{ number_format((float) $trx->jumlah, 2, ',', '.') }}</b>
            </td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="3" class="center">
                {{ $profil->alamat ? explode(',', $profil->alamat)[0] : 'Tempat' }}, {{ Tanggal::tglLatin($trx->tanggal_transaksi) }}
            </td>
        </tr>
        <tr>
            <td colspan="3" class="center fw-bold">
                Yang Menerima,
            </td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="3" class="center">{{ auth()->user()->name ?? '........................' }}</td>
        </tr>
    </table>
</body>
</html>
