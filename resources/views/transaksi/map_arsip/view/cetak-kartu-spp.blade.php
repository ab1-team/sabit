@php
    use App\Utils\Angka;

    $ttdPath = public_path('storage/ttd/bendahara.png');
    $ttdBase64 = file_exists($ttdPath) ? base64_encode(file_get_contents($ttdPath)) : null;
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kartu SPP - {{ $siswa->nama }}</title>
    <style type="text/css">
        <!--
        .style6 {
            font-family: Verdana, Arial, Helvetica, sans-serif;
            font-size: 8px;
        }

        .style9 {
            font-family: Verdana, Arial, Helvetica, sans-serif;
            font-size: 9px;
        }

        .style1 {
            font-family: Verdana, Arial, Helvetica, sans-serif;
            font-size: 18px;
            text-align: center;
            font-weight: bold;
        }

        .top {
            border-top: 1px solid #000000;
        }

        .bottom {
            border-bottom: 1px solid #000000;
        }

        .left {
            border-left: 1px solid #000000;
        }

        .right {
            border-right: 1px solid #000000;
        }

        .style27 {
            font-family: Verdana, Arial, Helvetica, sans-serif;
            font-size: 10px;
            font-weight: bold;
        }

        .style2 {
            font-family: Verdana, Arial, Helvetica, sans-serif;
            font-size: 12px;
            font-weight: bold;
        }

        .ttd-wrap {
            position: relative;
        }

        .ttd-title {
            position: relative;
            z-index: 2;
            margin: 0;
        }

        .ttd-img {
            display: block;
            position: relative;
            z-index: -1;
            margin: -20px auto -30px;
            width: 110px;
            height: auto;
            max-height: 80px;
        }

        .ttd-nama {
            position: relative;
            z-index: 2;
            font-weight: bold;
            line-height: 1;
            margin: 0;
        }

        body {
            margin: 0;
        }

        table.kop {
            margin-top: -6px;
        }
        -->
    </style>
</head>

<body onload="window.print()">

    <table class="kop" width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
        <tr align="center">
            <td colspan="3" class="style6">
                @if (!empty($logo))
                    <img src="data:image/{{ $logo_type }};base64,{{ $logo }}"
                        style="width:60px; float:left;" /><br>
                @endif
                <div class="style27">
                    <b>SEKOLAH DASAR ISLAM TERPADU (SDIT)</b><br>
                    <span class="style2">{{ strtoupper($profil->nama ?? '') }}</span><br>
                    <span class="style9">
                        {{ $profil->alamat ?? '' }} <br>Telp. {{ $profil->telpon ?? '' }}</span>
                </div>
            </td>
        </tr>
    </table>

    <div class="style1 top bottom">KARTU SPP</div>

    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td width="20%" class="style9">Nama Siswa</td>
            <td class="style27">: {{ strtoupper($siswa->nama) }}</td>
        </tr>
        <tr>
            <td class="style9">Kelas</td>
            <td class="style27">: {{ $anggotaAktif->kode_kelas ?? $siswa->kode_kelas }}</td>
        </tr>
        <tr>
            <td class="style9">Ta.Pel</td>
            <td class="style27">: {{ $tahun_pel }}</td>
        </tr>
        <tr>
            <td class="style9">Nominal</td>
            <td class="style27">: {{ Angka::format($spp_perbulan ?? 0, 0) }}</td>
        </tr>
    </table>

    <table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
        <tr>
            <th width="7%" class="style9 top left">NO</th>
            <th width="30%" class="style9 left top">TANGGAL</th>
            <th class="style9 left top">KETERANGAN</th>
            <th width="20%" class="style9 left top">JUMLAH</th>
            <th width="15%" class="style9 left right top" style="border-right:2px solid #000;">SIGN</th>
        </tr>

        <tr>
            <th height="29" class="style9 top left bottom">
                <p>&nbsp;</p>
                <p>&nbsp;</p>
                <p>&nbsp;</p>
                <p>&nbsp;</p>
                <p>&nbsp;</p>
                <p>&nbsp;</p>
                <p>&nbsp;</p>
                <p>&nbsp;</p>
                <p>&nbsp;</p>
                <p>&nbsp;</p>
                <p>&nbsp;</p>
                <p>&nbsp;</p>
            </th>
            <th class="style9 left top bottom">&nbsp;</th>
            <th class="style9 left top bottom">
                <p>&nbsp;</p>
            </th>
            <th class="style9 left top bottom">
                <p>&nbsp;</p>
            </th>
            <th class="style9 left top bottom right" style="border-right:2px solid #000;">
                <p>&nbsp;</p>
            </th>
        </tr>
    </table>

    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td height="15" class="style6" colspan="2">
                <ol><b>Keterangan:</b>
                    <li>Pembayaran paling lambat tanggal 10 tiap bulan, dimulai bulan Juli.</li>
                    <li>Bawalah kartu dan Mintalah kwitansi setiap kali pembayaran</li>
                    <li>Cek status pembayaran melalui aplikasi SAbIT di www.sabit.sditat.sch.id</li>
                </ol>
            </td>
        </tr>
        <tr>
            <th width="40%">
            </th>
            <th class="style9">
                <div class="ttd-wrap">
                    <p align="center" class="ttd-title">Bendahara</p>
                    @if ($ttdBase64)
                        <img src="data:image/png;base64,{{ $ttdBase64 }}" alt="" class="ttd-img">
                    @else
                        <br><br><br><br>
                    @endif
                    <div class="ttd-nama">MASLAKHATUL UMAH</div>
                </div>
            </th>
        </tr>
    </table>

</body>

</html>
