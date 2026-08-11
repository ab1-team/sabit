@php
    use App\Utils\Keuangan;
    $i = 0;
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
        <li>
            <div style="text-transform: uppercase;">Gambaran Umum</div>
            <div style="text-align: justify">
                {{ $profil->nama }} dalah Badan Usaha yang didirikan oleh ......
                ......sebagai tindak lanjut dari amanat Pemerintahan Republik Indonesia yang antara
                lain tertuang dalam UU nomer 6 Tahun 2014 Tentang Desa., Peraturan Menteri Desa Pembangunan Daerah
                Tertinggal dan Transmigrasi Nomor 4 Tahun 2015 tentang Pendirian, Pengurusan dan Pengelolaan, dan
                Pembubaran
                Badan Usaha Milik Desa., Peraturan Menteri Dalam Negeri Nomor 1 Tahun 2016 tentang Pengelolaan Aset Desa.,
                PP No.72 Tahun 2005 tentang Desa Peraturan Pemerintah Nomor 11 Tahun 2011 Tentang Bumdes.
            </div>
            <p style="text-align: justify">
                Sesuai amanat regulasi maka setiap desa bisa berivestasi kepada Bumdes melalu penetapan APBDes sebagai
                modal
                mayoritas dan bisa menerima investasi masyarakat sebagai tambahan modal Bumdes. Modal tersebut digunakan
                untuk meningkatkan produktifitas ekonomi masyarakat dan keuangan desa dengan mengembangkan fungsi dan
                manfaat potensi sumber daya alam dan sumber daya manusia di wilayah desa setempat, disamping mencari sumber
                dukungan pengembangan dari pihak swasta dan pemerintah baik dilingkungan desa sendiri maupun luar desa.
            </p>
            <p style="text-align: justify">
                {{ $profil->nama }} didirikan di ......
                berdasarkan PERATURAN KEPALA DESA NOMOR ...... dan mendapatkan Sertifikat Badan Hukum
                dari Menteri Hukum dan Hak Asasi Manusia No. ....... Dalam perjalanan pengelolaan
                manajeman dan bisnis {{ $profil->nama }} memiliki struktur kepengurusan pusat sebagai berikut :
            </p>
            <table style="margin-top: -10px; margin-left: 15px;">
                <tr>
                    <td style="padding: 0px; 4px;" width="100">......</td>
                    <td style="padding: 0px; 4px;">:</td>
                    <td style="padding: 0px; 4px;">
                        ...... : '......................................'
                    </td>
                </tr>

                <tr>
                    <td style="padding: 0px; 4px;"></td>
                    <td style="padding: 0px; 4px;">:</td>
                    <td style="padding: 0px; 4px;">
                        : '......................................'
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0px; 4px;"></td>
                    <td style="padding: 0px; 4px;">:</td>
                    <td style="padding: 0px; 4px;">
                        ......
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0px; 4px;">......</td>
                    <td style="padding: 0px; 4px;">:</td>
                    <td style="padding: 0px; 4px;">
                        ...... </td>
                </tr>

                {{-- <tr>
                    <td style="padding: 0px; 4px;">Unit Usaha</td>
                    <td style="padding: 0px; 4px;">:</td>
                    <td style="padding: 0px; 4px;">.................................</td>
                </tr> --}}
            </table>
        </li>
        <li style="margin-top: 12px;">
            <div style="text-transform: uppercase;">
                Ikhtisar Kebijakan Akutansi
            </div>
            <ol>
                <li>
                    Pernyataan Kepatuhan
                    <ol style="list-style: lower-alpha;">
                        <li>
                            Laporan keuangan disusun menggunakan Standar Akuntansi Keuangan ETAP dan/atau EP
                        </li>
                        <li>Dasar Penyusunan Kepmendesa 136 Tahun 2022</li>
                        <li>
                            Dasar penyusunan laporan keuangan adalah biaya historis dan menggunakan asumsi dasar akrual.
                            Mata uang penyajian yang digunakan untuk menyusun laporan keuangan ini adalah Rupiah.
                        </li>
                    </ol>
                </li>
                <li>
                    Piutang Usaha
                    <ol style="list-style: lower-alpha;">
                        <li>
                            Piutang usaha disajikan sebesar jumlah saldo pinjaman dikurangi
                            dengan cadangan kerugian pinjaman
                        </li>
                    </ol>
                </li>
                <li>
                    Aset Tetap (berwujud dan tidak berwujud)
                    <ol style="list-style: lower-alpha">
                        <li>
                            Aset tetap dicatat sebesar biaya perolehannya jika aset tersebut dimiliki secara hukum oleh
                            Bumdesma Lkd Aset tetap disusutkan menggunakan metode garis lurus tanpa nilai residu.
                        </li>
                    </ol>
                </li>
                <li>
                    Pengakuan Pendapatan dan Beban
                    <ol style="list-style: lower-alpha;">
                        <li>
                            Laba penjualan dan Jasa piutang yang sudah memasuki jatuh tempo pembayaran diakui sebagai
                            pendapatan meskipun tidak diterbitkan kuitansi sebagai bukti pembayaran jasa piutang. Sedangkan
                            denda keterlambatan pembayaran/pinalti diakui sebagai pendapatan pada saat diterbitkan kuitansi
                            pembayaran.
                        </li>
                        <li>
                            Adapun kewajiban bayar atas kebutuhan operasional, pemasaran
                            maupun non operasional pada suatu periode operasi tertentu sebagai akibat
                            telah menikmati manfaat/menerima fasilitas, maka hal tersebut sudah wajib diakui
                            sebagai beban meskipun belum diterbitkan kuitansi pembayaran.
                        </li>
                    </ol>
                </li>
                <li>
                    Pajak Penghasilan
                    <ol style="list-style: lower-alpha;">
                        <li>
                            Pajak Penghasilan mengikuti ketentuan perpajakan yang berlaku di Indonesia
                        </li>
                    </ol>
                </li>
            </ol>
        </li>
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
                                @foreach ($lev3->rek as $rek)
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

            <div style="color: #f44335">
                Ada selisih antara Jumlah Aset dan Jumlah Liabilitas + Ekuitas sebesar
                <b>......</b>
            </div>
        </li>
        <li style="margin-top: 12px;">
            <div style="text-transform: uppercase;">
                Ketentuan Pembagian Laba Usaha
            </div>
            <div style="text-align: justify">
                Pembagian laba {{ $profil->nama }} ditentukan dalam rapat pertanggungjawaban pengurus dan RUPS.
                Adapun hasil keputusan pembagian laba tahun buku ...... adalah sebagai berikut:
            </div>
            <ol>
                <li>
                    Total Laba bersih .....................
                </li>
                <li>
                    Alokasi penambahan modal {{ $profil->nama }} .................
                </li>
                <li>
                    Alokasi PADes {{ $profil->nama }} .................
                </li>
            </ol>
        </li>

        <li style="margin-top: 12px;">
            <div style="text-transform: uppercase;">
                Lain-lain
            </div>
            <div style="text-align: justify">
                {!! $catatan ?? '' !!}
            </div>
        </li>


        <li style="margin-top: 12px;">
            <table border="0" width="100%" cellspacing="0" cellpadding="0" style="font-size: 11px;">
                <tr>
                    <td align="justify">
                        <div style="text-transform: uppercase;">
                            Penutup
                        </div>
                        <div style="text-align: justify">
                            Laporan Keuangan {{ $profil->nama }} ini disajikan dengan berpedoman pada Keputusan
                            Kementerian
                            Desa Nomor 136/2022 Tentang Panduan Penyusunan Pelaporan Bumdes. Catatan atas Laporan Keuangan
                            (CaLK) ini merupakan bagian tidak terpisahkan dari Laporan Keuangan Badan Usaha Milik Desa
                            (Bumdes) Maju Jaya untuk Laporan Operasi Bulan ...... Tahun ......
                            Selanjutnya Catatan atas Laporan Keuangan ini diharapkan untuk dapat berguna bagi pihak-pihak
                            yang berkepentingan (stakeholders) serta memenuhi prinsip-prinsip transparansi, akuntabilitas,
                            pertanggung jawaban, independensi, dan fairness dalam pengelolaan keuangan
                            {{ $profil->nama }}
                        </div>

                        <table border="0" width="100%" cellspacing="0" cellpadding="0" style="font-size: 11px;"
                            class="p">
                            <tr>
                                <td>
                                    <div style="margin-top: 16px;"></div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
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
        </li>
    </ol>
@endsection
