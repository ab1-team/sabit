@php
    use App\Utils\Tanggal;
    $tglUtil = new Tanggal();
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .border-box {
            border: 2px solid black;
            width: 90%;
            height: 90%;
            margin: 10px auto;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 10px;
            box-sizing: border-box;
        }

        .header {
            text-align: center;
            margin-top: 60px;
        }

        .header .judul {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .header .subjudul {
            font-size: 18px;
            font-weight: bold;
            color: grey;
            margin-bottom: 200px;
        }

        .content {
            text-align: center;
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .content img {
            max-height: 300px;
            margin-bottom: 200px;
        }

        .footer {
            font-size: 14px;
            text-align: center;
            margin-top: 10px;
        }

        .footer hr {
            border: 1px solid black;
            margin-bottom: 5px;
        }

        .footer div {
            margin: 2px 0;
        }
    </style>
</head>

<body>
    <div class="border-box">
        <div class="header">
            <div class="judul">{{ strtoupper($judul ?? '') }}</div>
            <div class="subjudul">{{ strtoupper($sub_judul ?? '') }}</div>
        </div>

        <div class="content">
            @if (!empty($logo))
                <img src="data:image/{{ $logo_type }};base64,{{ $logo }}">
            @endif
        </div>
        <div class="footer">
            <hr>
            <div style="font-size: 16px"><b>{{ $profil->nama }}</b></div>
            <div style="font-size: 12px">Alamat, {{ $profil->alamat }}, Telfon:{{ $profil->telpon }}</div>
            <div style="font-size: 12px">Tahun {{ $tahun }}</div>
        </div>
    </div>
</body>

</html>
