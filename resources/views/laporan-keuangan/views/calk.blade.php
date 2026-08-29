@php
    use App\Utils\Keuangan;
    $i = 0;
    $totalAset = 0;
    $totalUtangModal = 0;
    foreach ($akun1 as $lev1) {
        $levSum = 0;
        foreach ($lev1->akun2 as $lev2) {
            foreach ($lev2->akun3 as $lev3) {
                $levSum += Keuangan::hitungSaldo($lev3);
            }
        }
        $kode = $lev1->kode_akun ?? '';
        if (str_starts_with($kode, '1.')) {
            $totalAset += $levSum;
        } elseif (str_starts_with($kode, '2.') || str_starts_with($kode, '3.')) {
            $totalUtangModal += $levSum;
        }
    }
    $selisihNeraca = $totalAset - $totalUtangModal;
@endphp

@extends('laporan-keuangan.layout.dasar')
<title>{{ $title }}</title>
@section('content')
    <style>
        ol,
        ul {
            margin-left: unset;
        }
    </style>
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td colspan="3" align="center">
                <div>
                    <span style="font-size:20px; font-weight:bold;">
                        CATATAN ATAS LAPORAN KEUANGAN
                    </span>
                </div>
                <div>
                    <span style="font-size:18px; font-weight:bold; text-transform:uppercase;">{{ $profil->nama }}
                    </span>
                </div>
                <div>
                    <span style="font-size:16px; font-weight:bold;">
                        {{ strtoupper($sub_judul) }}
                    </span>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="3" height="5"></td>
        </tr>
    </table>

    <ol style="list-style: upper-alpha;">
        <li style="margin-top: 12px;">
            <div style="text-transform: uppercase;">
                Informasi Tambahan Laporan Keuangan
            </div>
            <div>
                <table border="0" width="100%" cellspacing="0" cellpadding="0" style="font-size: 11px;">
                    <tr>
                        <td colspan="4" height="3"></td>
                    </tr>
                    <tr style="background-color: #000; color: #fff; font-weight: bold;">
                        <td width="10%">Kode</td>
                        <td width="70%">Nama Akun</td>
                        <td width="20%" align="right">Saldo</td>
                    </tr>
                    <tr>
                        <td colspan="4" height="5"></td>
                    </tr>
                    @foreach ($akun1 as $lev1)
                        @php $total_lev1 = 0; @endphp

                        <tr style="background:#4a4a4a; color:#fff;" align="center">
                            <td colspan="3"><b>{{ $lev1->kode_akun }}. {{ $lev1->nama_akun }}</b></td>
                        </tr>

                        @foreach ($lev1->akun2 as $lev2)
                            <tr style="background:#a7a7a7; font-weight:bold;">
                                <td>{{ $lev2->kode_akun }}.</td>
                                <td colspan="2">{{ $lev2->nama_akun }}</td>
                            </tr>

                            @foreach ($lev2->akun3 as $lev3)
                                {{-- 🔑 SALDO LEVEL 3 = NERACA --}}
                                @php
                                    $saldo_lev3 = Keuangan::hitungSaldo($lev3);
                                    $total_lev1 += $saldo_lev3;
                                @endphp

                                {{-- AKUN LEVEL 3 --}}
                                <tr style="background:#d0d0d0; font-weight:bold;">
                                    <td>{{ $lev3->kode_akun }}.</td>
                                    <td>{{ $lev3->nama_akun }}</td>
                                    <td align="right">{{ Keuangan::formatSaldo($saldo_lev3) }}</td>
                                </tr>

                                {{-- DETAIL REKENING (PENJELASAN SAJA) --}}
                                @foreach ($lev3->rekeningByPrefix() as $rek)
                                    @php
                                        $saldo_rek = Keuangan::hitungSaldoCALK($rek, $tgl_awal, $tgl_akhir);
                                    @endphp
                                    <tr style="background: {{ $i % 2 == 0 ? '#e6e6e6' : '#ffffff' }}">
                                        <td>{{ $rek->kode_akun }}.</td>
                                        <td>{{ $rek->nama_akun }}</td>
                                        <td align="right">{{ Keuangan::formatSaldoCALK($saldo_rek) }}</td>
                                    </tr>
                                    @php $i++; @endphp
                                @endforeach

                                {{-- JUMLAH LEVEL 3 (SAMA DENGAN NERACA) --}}
                                {{-- <tr style="background:#c8c8c8; font-weight:bold;">
                                    <td colspan="2">Jumlah {{ $lev3->nama_akun }}</td>
                                    <td align="right">{{ Keuangan::formatSaldo($saldo_lev3) }}</td>
                                </tr> --}}
                            @endforeach
                        @endforeach

                        <tr style="background:#a7a7a7; font-weight:bold;">
                            <td colspan="2">Jumlah {{ $lev1->nama_akun }}</td>
                            <td align="right">{{ Keuangan::formatSaldo($total_lev1) }}</td>
                        </tr>
                    @endforeach

                </table>
            </div>

            <div style="color: {{ abs($selisihNeraca) < 0.01 ? '#0a7d28' : '#f44335' }}">
                @if (abs($selisihNeraca) < 0.01)
                    Neraca BALANCE: Jumlah Aset = Jumlah Liabilitas + Ekuitas = <b>{{ Keuangan::formatSaldo($totalAset) }}</b>
                @else
                    Ada selisih antara Jumlah Aset dan Jumlah Liabilitas + Ekuitas sebesar
                    <b>{{ Keuangan::formatSaldo($selisihNeraca) }}</b>
                    (Aset: {{ Keuangan::formatSaldo($totalAset) }}, Liabilitas+Ekuitas: {{ Keuangan::formatSaldo($totalUtangModal) }})
                @endif
            </div>
        </li>
        <li style="margin-top: 12px;">
            <table class="table table-bordered" width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td>
                        <div class="ttd-wrapper">
                            {!! $ttd->tanda_tangan ?? '' !!}
                        </div>
                    </td>
                </tr>
            </table>
        </li>
    </ol>
@endsection
