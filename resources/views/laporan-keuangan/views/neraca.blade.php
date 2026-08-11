@php
    use App\Utils\Keuangan;
    $i = 0;
@endphp
<title>{{ $title }}</title>

@extends('laporan-keuangan.layout.dasar')

@section('content')
    <table border="0" width="100%" cellspacing="0" cellpadding="0" style="font-size: 11px;">
        <tr>
            <td colspan="3" align="center">
                <div style="font-size: 18px; font-weight:bold; margin: 0; line-height: 1.2;">
                    {{ strtoupper($judul) }}
                </div>
                <div style="font-size: 16px; font-weight:bold; margin: 0; line-height: 1.2;">
                    {{ strtoupper($sub_judul) }}
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="3" height="8"></td>
        </tr>
        <tr style="background:#000; color:#fff; font-weight:600;">
            <td width="10%" style="padding:4px;">Kode</td>
            <td width="70%" style="padding:4px;">Nama Akun</td>
            <td width="20%" style="padding:4px;" align="right">Saldo</td>
        </tr>
        <tr>
            <td colspan="3" height="6"></td>
        </tr>
        @foreach ($akun1 as $lev1)
            @php $total_akun1 = 0; @endphp

            <tr style="background:#4a4a4a; color:#fff;">
                <td colspan="3" align="center" style="font-weight:bold;">
                    {{ $lev1->kode_akun }}. {{ $lev1->nama_akun }}
                </td>
            </tr>
            @foreach ($lev1->akun2 as $lev2)
                <tr style="background:#a7a7a7; font-weight:bold;">
                    <td>{{ $lev2->kode_akun }}.</td>
                    <td colspan="2">{{ $lev2->nama_akun }}</td>
                </tr>
                @foreach ($lev2->akun3 as $lev3)
                    @php
                        $saldo_akun3 = Keuangan::hitungSaldo($lev3);
                        $total_akun1 += $saldo_akun3;
                        $bg = $i % 2 === 0 ? '#e6e6e6' : '#ffffff';
                        $i++;
                    @endphp

                    <tr style="background:{{ $bg }};">
                        <td>{{ $lev3->kode_akun }}.</td>
                        <td>{{ $lev3->nama_akun }}</td>
                        <td align="right">{{ Keuangan::formatSaldo($saldo_akun3) }}</td>
                    </tr>
                @endforeach
            @endforeach
            <tr style="background:#a7a7a7; font-weight:bold;">
                <td colspan="2" align="left">Jumlah {{ $lev1->nama_akun }}</td>
                <td align="right">{{ Keuangan::formatSaldo($total_akun1) }}</td>
            </tr>
            <tr>
                <td colspan="3" height="6"></td>
            </tr>
        @endforeach
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
