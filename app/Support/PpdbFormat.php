<?php

declare(strict_types=1);

namespace App\Support;

class PpdbFormat
{
    /**
     * Format nilai biaya (tersimpan sebagai string angka dengan/ tanpa
     * pemisah ribuan) menjadi string Rupiah. Contoh:
     *   "50000"     -> "Rp 50.000"
     *   "50.000"    -> "Rp 50.000"
     *   "Rp 50.000" -> "Rp 50.000"
     *   null / ""   -> "-"
     *   "gratis"    -> "gratis"   (teks non-angka dikembalikan apa adanya)
     */
    public static function rupiah(?string $value): string
    {
        if ($value === null) return '-';
        $raw = trim($value);
        if ($raw === '') return '-';

        // Buang awalan "Rp", "IDR", spasi, dan karakter non-digit/koma/titik.
        $stripped = preg_replace('/^(rp|idr)\s*/i', '', $raw);
        // Ambil hanya digit dan pemisah (titik/koma).
        preg_match('/[\d.,]+/', (string) $stripped, $m);
        if (!$m) {
            // Tidak ada angka di string (mis. "gratis", "-"). Kembalikan apa adanya.
            return $raw;
        }
        $numeric = $m[0];
        // Normalisasi: bila ada koma desimal, ganti ke titik lalu bersihkan.
        // Pola input admin: "50.000" atau "50000" (tanpa desimal). Anggap ribuan = titik, desimal = koma.
        $hasComma = strpos($numeric, ',') !== false;
        $hasDot = strpos($numeric, '.') !== false;
        if ($hasComma && $hasDot) {
            // Asumsikan "1.234,56" (id-ID) -> desimal koma, ribuan titik.
            $numeric = str_replace('.', '', $numeric);
            $numeric = str_replace(',', '.', $numeric);
        } elseif ($hasComma) {
            // "12,5" dianggap desimal; "12,500" dianggap ribuan (lebih aman pakai panjang > 3).
            $parts = explode(',', $numeric);
            if (count($parts) === 2 && strlen($parts[1]) <= 2) {
                $numeric = str_replace(',', '.', $numeric);
            } else {
                $numeric = str_replace(',', '', $numeric);
            }
        } elseif ($hasDot) {
            // "50.000" dianggap ribuan; "50.5" dianggap desimal.
            $parts = explode('.', $numeric);
            if (count($parts) > 1 && strlen(end($parts)) === 3) {
                $numeric = str_replace('.', '', $numeric);
            }
            // selain itu biarkan sebagai desimal (akan diformat).
        }

        if (!is_numeric($numeric)) {
            return $raw;
        }

        $num = (float) $numeric;
        // Bulatkan ke integer bila tidak ada pecahan.
        $isInt = floor($num) == $num;
        return 'Rp ' . number_format($num, $isInt ? 0 : 2, ',', '.');
    }
}
