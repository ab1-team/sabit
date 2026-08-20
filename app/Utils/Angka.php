<?php

namespace App\Utils;

class Angka
{
    /**
     * Format angka untuk tampilan.
     * Default: format Indonesia ("100.000,00") — desimal koma, ribuan titik.
     */
    public static function format($value, int $decimals = 2, string $thousands = '.', string $decimal = ','): string
    {
        if ($value === null || $value === '') {
            return $value === '' ? '' : ($decimals > 0 ? '0' : '0');
        }
        return number_format((float) $value, $decimals, $decimal, $thousands);
    }

    public static function parseInt($value): int
    {
        return (int) self::parseFloat($value);
    }

    public static function parseFloat($value): float
    {
        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }
        $str = (string) $value;
        if ($str === '' || $str === null) {
            return 0.0;
        }

        $hasDot = str_contains($str, '.');
        $hasComma = str_contains($str, ',');

        if ($hasDot && $hasComma) {
            $lastDot = strrpos($str, '.');
            $lastComma = strrpos($str, ',');
            if ($lastComma > $lastDot) {
                $str = str_replace('.', '', $str);
                $str = str_replace(',', '.', $str);
            } else {
                $str = str_replace(',', '', $str);
            }
        } elseif ($hasComma) {
            $parts = explode(',', $str);
            if (count($parts) > 1 && strlen(end($parts)) <= 2) {
                $str = str_replace('.', '', $str);
                $str = str_replace(',', '.', $str);
            } else {
                $str = str_replace(',', '', $str);
            }
        } else {
            $str = str_replace(',', '', $str);
        }

        return (float) $str;
    }
}
