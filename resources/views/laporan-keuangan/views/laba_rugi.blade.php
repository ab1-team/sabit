@php
    function formatAngka($value)
    {
        if ($value < 0) {
            return '(' . \App\Utils\Angka::format(abs($value), 2) . ')';
        }
        return \App\Utils\Angka::format($value, 2);
    }
@endphp
<title>{{ $title }} ({{ $title_bulan }})</title>

@extends('laporan-keuangan.layout.dasar')

@section('content')
    <style>
        table {
            border-collapse: collapse;
            font-size: 11px;
            width: 100%;
            page-break-inside: auto;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
    </style>

    <table border="0" cellspacing="0" cellpadding="0" style="margin-bottom: 15px;">
        <tr>
            <td colspan="4" align="center">
                <div style="font-size: 18px; font-weight:bold; line-height: 1.2;">{{ strtoupper($judul) }}</div>
                <div style="font-size: 16px; font-weight:bold; line-height: 1.2;">{{ $sub_judul ?? '' }}</div>
            </td>
        </tr>
        <tr>
            <td colspan="4" height="5"></td>
        </tr>
    </table>

    <table border="0" cellspacing="0" cellpadding="0">
        <thead>
            <tr style="background: rgb(232,232,232); font-weight:bold; font-size:12px;">
                <td width="55%" align="center">Rekening</td>
                <td width="15%" align="center">s.d. Bulan Lalu</td>
                <td width="15%" align="center">Bulan Ini</td>
                <td width="15%" align="center">s.d. Bulan Ini</td>
            </tr>
        </thead>
        <tbody>
            @php
                $kategori = [
                    'Pendapatan Operasional' => $pendapatan,
                    'Beban Operasional' => $beban,
                    'Beban Barang Dagang' => $bp,
                    'Pendapatan Lain-lain' => $pen,
                    'Pendapatan Lain Detail' => $pendl,
                    'Beban Lain-lain' => $beb,
                    'Pajak' => $ph,
                ];
            @endphp

            @foreach ($kategori as $judul_kategori => $items)
                <tr style="background: rgb(200,200,200); font-weight:bold; text-transform: uppercase;">
                    <td colspan="4" height="14">{{ $loop->iteration }}. {{ $judul_kategori }}</td>
                </tr>

                @php
                    $total_lalu = 0;
                    $total_ini = 0;
                    $total_sd = 0;
                @endphp

                @foreach ($items as $item)
                    @php
                        $bg = $loop->iteration % 2 == 0 ? 'rgb(255,255,255)' : 'rgb(230,230,230)';
                        $saldo = $item->saldo ?? 0;
                        $lalu = 0; // bisa ganti sesuai data bulan lalu
                        $ini = $saldo - $lalu;

                        $total_lalu += $lalu;
                        $total_ini += $ini;
                        $total_sd += $saldo;
                    @endphp
                    <tr style="background: {{ $bg }}">
                        <td>{{ $item->kode_akun }}. {{ $item->nama_akun }}</td>
                        <td align="right">{{ formatAngka($lalu) }}</td>
                        <td align="right">{{ formatAngka($ini) }}</td>
                        <td align="right">{{ formatAngka($saldo) }}</td>
                    </tr>
                @endforeach

                <tr style="background: rgb(150,150,150); font-weight:bold;">
                    <td>Jumlah {{ $judul_kategori }}</td>
                    <td align="right">{{ formatAngka($total_lalu) }}</td>
                    <td align="right">{{ formatAngka($total_ini) }}</td>
                    <td align="right">{{ formatAngka($total_sd) }}</td>
                </tr>
                <tr>
                    <td colspan="4" height="6"></td>
                </tr>
                @endforeach
        </tbody>
    </table>
    <table class="table table-bordered" width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <div class="ttd-wrapper">
                    {!! $ttd->tanda_tangan ?? '' !!}
                </div>
            </td>
        </tr>
    </table>

@endsection
