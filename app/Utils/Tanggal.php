<?php

namespace App\Utils;

use Carbon\Carbon;

class Tanggal
{
    public static function tglIndo($tanggal, $format = 'DD/MM/YYYY')
    {
        if ($tanggal instanceof \DateTimeInterface) {
            return Carbon::instance($tanggal)->isoFormat($format);
        }

        if ($tanggal != '') {
            $array_tgl = explode('-', (string) $tanggal);
            $tahun = $array_tgl[0];
            $bulan = $array_tgl[1] ?? '';
            $hari = $array_tgl[2] ?? '';

            if (strlen($hari) > 0 && strlen($bulan) > 0) {
                $tanggal = $tahun . '-' . $bulan . '-' . $hari;
            } elseif (strlen($bulan) > 0) {
                $tanggal = $tahun . '-' . $bulan . '-01';
            } else {
                $tanggal = $tahun . '-12-31';
            }
            $tgl = new Carbon($tanggal);

            return $tgl->isoFormat($format);
        }

        return date('d/m/Y');
    }

    public static function tglNasional($tanggal)
    {
        $tgl = Carbon::createFromFormat('d/m/Y', $tanggal)->format('Y-m-d');
        return $tgl;
    }
    public static function tglKwitansi($tanggal)
    {
        return self::tglLatin($tanggal);
    }
    public static function tglRomawi($tanggal)
    {
        $keuangan = new Keuangan;
        $array_tgl = explode('-', $tanggal);
        $tahun = $array_tgl[0];
        $bulan = $array_tgl[1];
        $hari = $array_tgl[2];

        $bulan_rom = $keuangan->romawi($bulan);
        $hari_rom = $keuangan->romawi($hari);

        return $bulan_rom . '/' . $tahun;
    }

    public static function tglLatin($tanggal)
    {
        if ($tanggal instanceof \DateTimeInterface) {
            $carbon = Carbon::instance($tanggal);

            return $carbon->format('d') . ' ' . self::namaBulan($carbon->format('Y-m-d')) . ' ' . $carbon->format('Y');
        }

        $tgl = explode('-', (string) $tanggal);
        $tahun = $tgl[0] ?? '';
        $bulan = $tgl[1] ?? '';
        $hari = $tgl[2] ?? '';

        // Tanggal tidak lengkap (mis. hanya "2026-07") → pakai hari pertama.
        if ($tahun === '' || $bulan === '') {
            $carbon = Carbon::now();

            return $carbon->format('d') . ' ' . self::namaBulan($carbon->format('Y-m-d')) . ' ' . $carbon->format('Y');
        }

        if ($hari === '') {
            $hari = '01';
        }

        return $hari . ' ' . self::namaBulan($tanggal) . ' ' . $tahun;
    }

    public static function tahun($tanggal)
    {
        if ($tanggal instanceof \DateTimeInterface) {
            return Carbon::instance($tanggal)->format('Y');
        }

        $tgl = explode('-', (string) $tanggal);
        return $tgl[0] ?? '';
    }

    public static function bulan($tanggal)
    {
        if ($tanggal instanceof \DateTimeInterface) {
            return Carbon::instance($tanggal)->format('m');
        }

        $tgl = explode('-', (string) $tanggal);
        return $tgl[1] ?? '';
    }

    public static function namaBulan($tanggal)
    {
        if ($tanggal instanceof \DateTimeInterface) {
            $bln = Carbon::instance($tanggal)->format('m');
        } else {
            $tgl = explode('-', (string) $tanggal);
            $bln = $tgl[1] ?? '';
        }

        switch ($bln) {
            case '01':
                return 'Januari';
            case '02':
                return 'Februari';
            case '03':
                return 'Maret';
            case '04':
                return 'April';
            case '05':
                return 'Mei';
            case '06':
                return 'Juni';
            case '07':
                return 'Juli';
            case '08':
                return 'Agustus';
            case '09':
                return 'September';
            case '10':
                return 'Oktober';
            case '11':
                return 'November';
            case '12':
                return 'Desember';
            default:
                return '';
        }
    }

    public static function namaBulanNew($bulan)
    {
        $bulan = (int) $bulan;

        $map = [
            1  => 'Januari',
            2  => 'Februari',
            3  => 'Maret',
            4  => 'April',
            5  => 'Mei',
            6  => 'Juni',
            7  => 'Juli',
            8  => 'Agustus',
            9  => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return $map[$bulan] ?? '';
    }

    public static function hari($tanggal)
    {
        if ($tanggal instanceof \DateTimeInterface) {
            return Carbon::instance($tanggal)->format('d');
        }

        $tgl = explode('-', (string) $tanggal);
        return $tgl[2] ?? '';
    }

    public static function namaHari($tanggal)
    {
        $hari = date('D', strtotime($tanggal));

        switch ($hari) {
            case 'Sun':
                return "Minggu";
            case 'Mon':
                return "Senin";
            case 'Tue':
                return "Selasa";
            case 'Wed':
                return "Rabu";
            case 'Thu':
                return "Kamis";
            case 'Fri':
                return "Jumat";
            case 'Sat':
                return "Sabtu";
            default:
                return "Tidak di ketahui";
        }
    }
}
