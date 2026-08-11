<title>{{ $title }}</title>
@extends('laporan-keuangan.layout.dasar')
@section('content')
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }


        table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-data th,
        .table-data td {
            border: 1px solid #000;
            padding: 5px;
        }

        .table-data th {
            background-color: #f2f2f2;
            text-align: center;
            /* header center */
        }
    </style>

    <table border="0" width="100%" cellspacing="0" cellpadding="0" style="font-size: 11px;">
        <tr>
            <td colspan="8" align="center">
                <div style="font-size: 18px; font-weight: bold;">DAFTAR SISWA</div>
            </td>
        </tr>
        <tr>
            <td colspan="8" height="8"></td>
        </tr>
    </table>

    <body>
        <br>

        <table class="table-data">
            <thead>
                <tr>
                    <th style="text-align:center;">No</th>
                    <th style="text-align:center;">NIPD</th>
                    <th style="text-align:center;">NISN</th>
                    <th style="text-align:center;">Nama Siswa</th>
                    <th style="text-align:center;">Jenis Kelamin</th>
                    <th style="text-align:center;">Tahun Akademik</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($siswa as $index => $s)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $s->nipd }}</td>
                        <td>{{ $s->nisn }}</td>
                        <td>{{ $s->nama }}</td>
                        <td align="center">{{ $s->jenis_kelamin == 'L' ? 'Laki-laki' : ($s->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</td>
                        <td align="center">{{ $s->tahun_akademik ?: '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </body>
@endsection
