@php
    use App\Utils\Tanggal;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Peserta {{ $jenis_ujian ?? 'Ujian' }} - {{ $siswa->nama }}</title>
    <style>
        @page { margin: 15mm 15mm; }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 13px;
            margin: 0;
        }

        .kartu {
            border: 2px solid #000;
            padding: 14px 18px;
            max-width: 720px;
            margin: 0 auto;
        }

        .header {
            display: table;
            width: 100%;
            border-bottom: 1.5px solid #000;
            padding-bottom: 8px;
        }
        .header .logo,
        .header .info {
            display: table-cell;
            vertical-align: middle;
        }
        .header .logo { width: 70px; }
        .header .logo img { width: 60px; height: auto; }
        .header .info { text-align: center; padding-right: 70px; }
        .header .info .t1 {
            font-weight: bold;
            font-size: 18px;
            letter-spacing: 2px;
        }
        .header .info .t2 {
            font-size: 12px;
            font-weight: bold;
            margin-top: 2px;
        }
        .header .info .t3 {
            font-weight: bold;
            font-size: 13px;
            text-decoration: underline;
            margin-top: 2px;
        }

        .body {
            margin-top: 14px;
        }

        table.identitas {
            width: 100%;
            border-collapse: collapse;
        }
        table.identitas td {
            padding: 3px 4px;
            vertical-align: top;
        }
        table.identitas td.label {
            width: 170px;
            font-weight: bold;
        }
        table.identitas td.sep {
            width: 12px;
        }

        .ttd-wrapper {
            margin-top: 20px;
            display: table;
            width: 100%;
        }
        .ttd-wrapper .stamp,
        .ttd-wrapper .ttd {
            display: table-cell;
            vertical-align: top;
        }
        .ttd-wrapper .stamp {
            width: 40%;
            text-align: center;
        }
        .stamp-circle {
            display: inline-block;
            width: 110px;
            height: 110px;
            border: 2px solid #555;
            border-radius: 50%;
            line-height: 110px;
            color: #555;
            font-size: 10px;
            text-align: center;
        }
        .ttd-wrapper .ttd {
            text-align: center;
            padding-right: 6px;
        }
        .ttd-wrapper .ttd .lokasi {
            margin-bottom: 4px;
        }
        .ttd-wrapper .ttd .jabatan {
            font-weight: bold;
            margin-bottom: 60px;
        }
        .ttd-wrapper .ttd .nama {
            font-weight: bold;
            text-decoration: underline;
        }
        .ttd-wrapper .ttd .nip {
            font-size: 11px;
        }
    </style>
</head>
<body>

    <div class="kartu">
        <div class="header">
            <div class="logo">
                @if (!empty($logo))
                    <img src="data:image/{{ $logo_type }};base64,{{ $logo }}" alt="logo">
                @endif
            </div>
            <div class="info">
                <div class="t1">KARTU PESERTA</div>
                <div class="t2">{{ strtoupper($jenis_ujian ?? '') }}</div>
                <div class="t3">{{ strtoupper($profil->nama ?? '') }}</div>
            </div>
        </div>

        <div class="body">
            <table class="identitas">
                <tr>
                    <td class="label">No. Peserta</td>
                    <td class="sep">:</td>
                    <td>{{ $no_peserta }}</td>
                </tr>
                <tr>
                    <td class="label">Nama Peserta</td>
                    <td class="sep">:</td>
                    <td><strong>{{ strtoupper($siswa->nama) }}</strong></td>
                </tr>
                <tr>
                    <td class="label">NIS / NISN</td>
                    <td class="sep">:</td>
                    <td>{{ $siswa->nipd ?: '-' }} / {{ $siswa->nisn ?: '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Tempat, Tgl. Lahir</td>
                    <td class="sep">:</td>
                    <td>
                        {{ $siswa->tempat_lahir ?: '-' }},
                        {{ $siswa->tanggal_lahir ? Tanggal::tglIndo($siswa->tanggal_lahir) : '-' }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Sekolah Asal</td>
                    <td class="sep">:</td>
                    <td>{{ strtoupper($profil->nama ?? '-') }}</td>
                </tr>
            </table>

            <div class="ttd-wrapper">
                <div class="stamp">
                    <div class="stamp-circle">STEMPEL<br>SEKOLAH</div>
                </div>
                <div class="ttd">
                    <div class="lokasi">{{ $lokasi ?? 'Tempat' }}, {{ Tanggal::tglIndo(now()) }}</div>
                    <div class="jabatan">Kepala Sekolah</div>
                    <div class="nama">{{ $kepsek_nama ?? '________________' }}</div>
                    <div class="nip">NIP. {{ $kepsek_nip ?? '________________' }}</div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>