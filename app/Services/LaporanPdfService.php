<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use setasign\Fpdi\Fpdi;

/**
 * Service untuk merender laporan keuangan menjadi PDF.
 *
 * Latar belakang:
 *   dompdf membangun keseluruhan dokumen di memori. Untuk laporan dengan
 *   ribuan baris (mis. Jurnal Transaksi 1.300 transaksi = 2.700 baris tabel)
 *   pemakaian memori bisa mencapai ~1 GB sehingga memicu
 *   "Allowed memory size of ... exhausted" → HTTP 500.
 *   Server production memakai memory_limit = 256M (lihat FIX-413-NGINX.md),
 *   sehingga laporan besar akan selalu gagal di production.
 *
 * Solusi:
 *   Laporan besar dipecah menjadi beberapa chunk. Tiap chunk dirender menjadi
 *   PDF terpisah (memori tetap datar, tidak menumpuk), lalu seluruh chunk
 *   digabung kembali dengan FPDI menjadi satu dokumen PDF.
 */
class LaporanPdfService
{
    /**
     * Jumlah transaksi per chunk PDF.
     *
     * 150 transaksi ≈ 300 baris tabel ≈ 90 MB peak memory.
     * Nilai ini dipilih agar aman pada server dengan memory_limit 256M.
     */
    public const CHUNK_TRANSAKSI = 150;

    /**
     * Batas waktu eksekusi (detik) untuk proses render PDF.
     *
     * Render laporan besar bisa memakan waktu > 30 detik (default
     * max_execution_time PHP), sehingga batas waktu perlu dinaikkan.
     */
    public const TIMEOUT_DETIK = 300;

    /**
     * Kirim PDF sebagai response inline (ditampilkan di browser).
     *
     * @param string|iterable $html HTML laporan. Jika iterable, setiap elemen
     *                               adalah satu chunk yang digabung menjadi
     *                               satu dokumen PDF.
     */
    public function stream(string|iterable $html, bool $landscape = false, ?string $filename = null): Response
    {
        $pdf = $this->output($html, $landscape);

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . ($filename ?? 'laporan.pdf') . '"',
            'Cache-Control'       => 'max-age=0',
            'Pragma'              => 'public',
        ]);
    }

    /**
     * Render HTML menjadi binary PDF.
     *
     * @param string|iterable $html HTML laporan (satu chunk atau beberapa chunk)
     *
     * @return string binary PDF
     */
    public function output(string|iterable $html, bool $landscape = false): string
    {
        $files = [];
        $first = null;

        try {
            foreach ($this->chunks($html) as $chunk) {
                $bytes = $this->renderChunk($chunk, $landscape);

                if ($first === null && $files === []) {
                    // Simpan dulu; kalau ternyata hanya satu chunk, tidak perlu
                    // menulis berkas temporer sama sekali.
                    $first = $bytes;
                } else {
                    if ($first !== null) {
                        $files[] = $this->writeTempFile($first);
                        $first = null;
                    }
                    $files[] = $this->writeTempFile($bytes);
                }

                // Bebaskan memori segera sebelum render chunk berikutnya.
                unset($bytes);
                if (function_exists('gc_collect_cycles')) {
                    gc_collect_cycles();
                }
            }

            if ($first !== null) {
                return $first;
            }

            if ($files === []) {
                return $this->renderChunk('<html><body></body></html>', $landscape);
            }

            return $this->merge($files);
        } finally {
            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
        }
    }

    /**
     * Ubah input menjadi generator chunk HTML yang tidak kosong.
     *
     * @param string|iterable $html
     */
    private function chunks(string|iterable $html): \Generator
    {
        if (is_string($html)) {
            $html = [$html];
        }

        foreach ($html as $chunk) {
            $chunk = trim((string) $chunk);

            if ($chunk !== '') {
                yield $chunk;
            }
        }
    }

    /**
     * Render satu chunk HTML menjadi binary PDF.
     */
    private function renderChunk(string $html, bool $landscape): string
    {
        $this->setTimeLimit();
        $this->ensureFontDir();

        $pdf = Pdf::loadHTML($html)
            ->setPaper('a4', $landscape ? 'landscape' : 'portrait')
            ->setOptions([
                // Keamanan: laporan hanya memuat HTML yang dibuat aplikasi
                // sendiri, jadi tidak perlu PHP/JS/akses resource remote.
                'enable_php'        => false,
                'enable_javascript' => false,
                'enable_remote'     => false,
            ], true);

        return $pdf->output();
    }

    /**
     * Gabungkan beberapa file PDF menjadi satu dokumen.
     *
     * @param array<int, string> $files daftar path berkas PDF
     */
    private function merge(array $files): string
    {
        $pdf = new Fpdi();

        foreach ($files as $file) {
            $pageCount = $pdf->setSourceFile($file);

            for ($page = 1; $page <= $pageCount; $page++) {
                $templateId = $pdf->importPage($page);
                $size       = $pdf->getTemplateSize($templateId);

                $pdf->addPage(
                    $size['orientation'] ?? 'P',
                    [$size['width'], $size['height']]
                );
                $pdf->useTemplate($templateId);
            }
        }

        return $pdf->Output('S');
    }

    /**
     * Simpan binary PDF ke berkas temporer.
     */
    private function writeTempFile(string $bytes): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'laporan-pdf-');

        if ($tmp === false || file_put_contents($tmp, $bytes) === false) {
            throw new \RuntimeException('Gagal membuat berkas sementara untuk PDF laporan.');
        }

        return $tmp;
    }

    /**
     * Pastikan direktori cache font dompdf tersedia (writable).
     */
    private function ensureFontDir(): void
    {
        $dir = storage_path('fonts');

        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
    }

    /**
     * Naikkan batas waktu eksekusi PHP untuk proses render PDF.
     */
    private function setTimeLimit(): void
    {
        if (function_exists('set_time_limit')) {
            @set_time_limit(self::TIMEOUT_DETIK);
        }
    }
}
