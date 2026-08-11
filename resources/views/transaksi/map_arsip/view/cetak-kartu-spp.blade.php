@php
    use App\Utils\Angka;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu SPP - {{ $siswa->nama }}</title>
    <style>
        @page { margin: 20mm 15mm; }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
        }

        .header {
            display: table;
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 6px;
        }
        .header .logo,
        .header .info {
            display: table-cell;
            vertical-align: middle;
        }
        .header .logo { width: 80px; }
        .header .logo img { width: 70px; height: auto; }
        .header .info { text-align: center; }
        .header .info .l1 { font-weight: bold; font-size: 13px; }
        .header .info .l2 { font-weight: bold; font-size: 16px; margin: 2px 0; }
        .header .info .l3 { font-size: 11px; }
        .header .info .l4 { font-size: 11px; }

        .title {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            border-top: 2px solid #000;
            border-bottom: 1px solid #000;
            padding: 6px 0;
            margin-top: 6px;
            letter-spacing: 2px;
        }

        .identitas {
            margin-top: 10px;
            font-size: 12px;
        }
        .identitas table { width: 100%; border: none; }
        .identitas td { padding: 2px 0; vertical-align: top; }
        .identitas td:first-child { width: 110px; }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        table.data th,
        table.data td {
            border: 1px solid #000;
            padding: 6px;
        }
        table.data th { text-align: center; font-weight: bold; }
        table.data td.no { width: 30px; text-align: center; }
        table.data td.tgl { width: 100px; text-align: center; }
        table.data td.sign { width: 90px; }
        table.data td.empty-row { height: 240px; vertical-align: top; }

        .keterangan {
            margin-top: 12px;
            font-size: 11px;
        }
        .keterangan ol { margin: 0; padding-left: 18px; }

        .ttd {
            margin-top: 18px;
            width: 100%;
            border: none;
        }
        .ttd td { border: none; vertical-align: top; }
        .ttd .kanan { width: 220px; text-align: center; padding-left: auto; }
        .ttd .kanan .jabatan { font-weight: bold; }
        .ttd .kanan .space { height: 70px; }
        .ttd .kanan .nama { font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo">
            @if (!empty($logo))
                <img src="data:image/{{ $logo_type }};base64,{{ $logo }}" alt="logo">
            @endif
        </div>
        <div class="info">
            <div class="l1">{{ strtoupper($profil->nama ?? '') }}</div>
            <div class="l3">{{ $profil->alamat ?? '' }}</div>
            <div class="l4">Telp. {{ $profil->telpon ?? '' }}</div>
        </div>
    </div>

    <div class="title">KARTU SPP</div>

    <div class="identitas">
        <table>
            <tr><td>Nama Siswa</td><td>: <strong>{{ strtoupper($siswa->nama) }}</strong></td></tr>
            <tr><td>Kelas</td><td>: {{ $anggotaAktif->kode_kelas ?? $siswa->kode_kelas }}</td></tr>
            <tr><td>Ta.Pel</td><td>: {{ $tahun_pel }}</td></tr>
            <tr><td>Nominal</td><td>: {{ Angka::format($spp_perbulan ?? 0, 0) }}</td></tr>
        </table>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th>NO</th>
                <th>TANGGAL</th>
                <th>KETERANGAN</th>
                <th>JUMLAH</th>
                <th>SIGN</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="no empty-row">&nbsp;</td>
                <td class="tgl empty-row">&nbsp;</td>
                <td class="empty-row">&nbsp;</td>
                <td class="empty-row">&nbsp;</td>
                <td class="sign empty-row">&nbsp;</td>
            </tr>
        </tbody>
    </table>

    <div class="keterangan">
        <strong>Keterangan:</strong>
        <ol>
            <li>Pembayaran paling lambat tanggal 10 tiap bulan, dimulai bulan Juli.</li>
            <li>Bawalah kartu dan Mintalah kwitansi setiap kali pembayaran.</li>
            <li>Cek status pembayaran melalui aplikasi SABIT di www.sabit.sditat.sch.id</li>
        </ol>
    </div>

    <table class="ttd">
        <tr>
            <td></td>
            <td class="kanan">
                <div class="jabatan">Bendahara</div>
                <div class="space">&nbsp;</div>
                <div class="nama">MASLAKHATUL UMAH</div>
            </td>
        </tr>
    </table>

</body>
</html>