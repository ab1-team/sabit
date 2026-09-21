<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MigrasiSiswaTemplateExport implements FromArray, WithHeadings, WithTitle, WithStyles, WithEvents
{
    public function headings(): array
    {
        return [
            'nik',
            'nama',
            'jenis_kelamin',
            'nipd',
            'nisn',
            'no_kk',
            'tempat_lahir',
            'tanggal_lahir',
            'agama',
            'password',
            'alamat',
            'rt',
            'rw',
            'dusun',
            'kelurahan',
            'kecamatan',
            'kode_pos',
            'kebutuhan_khusus',
            'jenis_tinggal',
            'alat_transportasi',
            'hp',
            'email',
            'nama_ayah',
            'tahun_lahir_ayah',
            'pendidikan_ayah',
            'pekerjaan_ayah',
            'penghasilan_ayah',
            'kebutuhan_khusus_ayah',
            'no_telepon_ayah',
            'nama_ibu',
            'tahun_lahir_ibu',
            'pendidikan_ibu',
            'pekerjaan_ibu',
            'penghasilan_ibu',
            'kebutuhan_khusus_ibu',
            'no_telepon_ibu',
            'nama_wali',
            'tahun_lahir_wali',
            'pendidikan_wali',
            'pekerjaan_wali',
            'penghasilan_wali',
            'kebutuhan_khusus_wali',
            'no_telepon_wali',
            'kode_kelas',
            'kode_jurusan',
            'kode_ruangan',
            'status_awal',
            'tgl_masuk',
            'spp_nominal',
        ];
    }

    public function array(): array
    {
        return [
            // Contoh 1 — SMK dengan jurusan: kode kelas menggunakan romawi + kode jurusan + nomor.
            [
                '3501234567890001',
                'Ahmad Fauzi',
                'L',
                '2026001',
                '0091234567',
                '3501234567890002',
                'Surabaya',
                '2010-05-12',
                'Islam',
                '123456',
                'Jl. Merdeka No.10',
                '001',
                '002',
                'Dusun Krajan',
                'Sukamaju',
                'Sukorejo',
                '60123',
                'Tidak',
                'orang_tua',
                'Sepeda Motor',
                '081234567890',
                'siswa@mail.com',
                'Budi Santoso',
                '1980',
                'SMA',
                'Wiraswasta',
                '3000000',
                'Tidak',
                '081234567891',
                'Siti Aminah',
                '1982',
                'SMA',
                'Ibu Rumah Tangga',
                '0',
                'Tidak',
                '081234567892',
                '-',
                '-',
                '-',
                '-',
                '-',
                '-',
                '-',
                'X-TKJ-1',
                'TKJ',
                'R-101',
                'baru',
                '2026-07-15',
                '250000',
            ],
            // Contoh 2 — SD tanpa jurusan: kode_kelas cukup "IV-2" (tingkat IV, nomor 2).
            // kode_jurusan dikosongkan (tidak ada konsep jurusan di SD).
            [
                '3501234567890003',
                'Siti Rahayu',
                'P',
                '2026002',
                '0091234568',
                '3501234567890004',
                'Malang',
                '2014-08-22',
                'Islam',
                '',
                'Jl. Pendidikan No.5',
                '002',
                '003',
                'Dusun Mawar',
                'Sukamaju',
                'Sukorejo',
                '60124',
                'Tidak',
                'orang_tua',
                'Jalan Kaki',
                '081234567893',
                '',
                'Hendra Wijaya',
                '1982',
                'S1',
                'PNS',
                '4000000',
                'Tidak',
                '081234567894',
                'Dewi Lestari',
                '1985',
                'SMA',
                'Ibu Rumah Tangga',
                '0',
                'Tidak',
                '081234567895',
                '-',
                '-',
                '-',
                '-',
                '-',
                '-',
                '-',
                'IV-2',
                '',
                'R-202',
                'baru',
                '2026-07-15',
                '',
            ],
            // Contoh 3 — 1 kelas per angkatan tanpa strip: kode_kelas hanya "II" (tingkat 2, tidak bernomor).
            // Tingkat otomatis di-infer dari token pertama kode_kelas saat kelas baru dibuat via modal.
            [
                '3501234567890005',
                'Budi Pratama',
                'L',
                '2026003',
                '0091234569',
                '3501234567890006',
                'Sidoarjo',
                '2012-03-10',
                'Kristen',
                '',
                'Jl. Cendrawasih No.12',
                '003',
                '004',
                'Dusun Melati',
                'Sidokare',
                'Sidoarjo',
                '61215',
                'Tidak',
                'orang_tua',
                'Sepeda',
                '081234567896',
                '',
                'Joko Susilo',
                '1978',
                'SMA',
                'Petani',
                '2500000',
                'Tidak',
                '081234567897',
                'Sri Wahyuni',
                '1980',
                'SMA',
                'Ibu Rumah Tangga',
                '0',
                'Tidak',
                '081234567898',
                '-',
                '-',
                '-',
                '-',
                '-',
                '-',
                '-',
                'II',
                'AKL',
                'R-301',
                'pindahan',
                '2026-07-15',
                '200000',
            ],
        ];
    }

    public function title(): string
    {
        return 'Template Migrasi Siswa';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => Color::COLOR_WHITE]],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF4F46E5'],
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestColumn = $sheet->getHighestColumn();
                $highestRow = $sheet->getHighestRow();
                $lastColIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

                $sheet->getStyle('A1:' . $highestColumn . '1')->getAlignment()->setHorizontal('center');
                for ($i = 1; $i <= $lastColIndex; $i++) {
                    $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
                $sheet->getStyle('A1:' . $highestColumn . '1')->getAlignment()->setWrapText(true);
                $sheet->getRowDimension(1)->setRowHeight(28);
                $sheet->getStyle('A2:' . $highestColumn . $highestRow)->getAlignment()->setWrapText(true);

                $columnNotes = $this->columnNotes();
                $headings = $this->headings();
                foreach ($headings as $colIdx => $heading) {
                    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
                    $cell = $colLetter . '1';
                    $note = $columnNotes[$heading] ?? null;
                    $base = '*Kolom wajib diisi.';

                    if ($note !== null) {
                        $text = $note['text'] . "\n\n" . $base;
                        if (!empty($note['optional'])) {
                            $text = $note['text'] . "\n\n(Boleh dikosongkan.)";
                        }
                    } else {
                        $text = $base;
                    }

                    $sheet->getComment($cell)->getText()->createTextRun($text);
                }
            },
        ];
    }

    private function columnNotes(): array
    {
        return [
            'nik' => ['text' => 'Nomor Induk Kependudukan (16 digit). Wajib diisi. Bisa "-" jika belum ada.', 'optional' => false],
            'nama' => ['text' => 'Nama lengkap siswa. Wajib diisi.', 'optional' => false],
            'jenis_kelamin' => ['text' => "Jenis kelamin. Isi 'L' (Laki-laki) atau 'P' (Perempuan). Wajib diisi.", 'optional' => false],
            'nipd' => ['text' => 'Nomor Induk Peserta Didik (NIS sekolah). Wajib diisi.', 'optional' => false],
            'nisn' => ['text' => 'Nomor Induk Siswa Nasional (10 digit). Wajib diisi. Dipakai sebagai kunci update: baris dengan NISN sama akan memperbarui data lama.', 'optional' => false],
            'no_kk' => ['text' => 'Nomor Kartu Keluarga (16 digit). Wajib diisi. Bisa "-" jika belum ada.', 'optional' => false],
            'tempat_lahir' => ['text' => 'Kota/kabupaten tempat lahir (contoh: Surabaya). Wajib diisi.', 'optional' => false],
            'tanggal_lahir' => ['text' => 'Tanggal lahir. Format YYYY-MM-DD (contoh: 2010-05-12). Juga menerima d/m/Y, d-m-Y, m/d/Y.', 'optional' => false],
            'agama' => ['text' => 'Agama siswa (contoh: Islam, Kristen, Katolik, Hindu, Buddha, Konghucu). Wajib diisi.', 'optional' => false],
            'password' => ['text' => "Password login siswa. Wajib diisi. Kosongkan jika ingin otomatis memakai nilai 'nipd'.", 'optional' => false],
            'alamat' => ['text' => 'Alamat lengkap (jalan, nomor rumah). Wajib diisi.', 'optional' => false],
            'rt' => ['text' => 'Nomor RT (contoh: 001). Wajib diisi.', 'optional' => false],
            'rw' => ['text' => 'Nomor RW (contoh: 002). Wajib diisi.', 'optional' => false],
            'dusun' => ['text' => 'Nama dusun/dukuh. Wajib diisi.', 'optional' => false],
            'kelurahan' => ['text' => 'Nama kelurahan/desa. Wajib diisi.', 'optional' => false],
            'kecamatan' => ['text' => 'Nama kecamatan. Wajib diisi.', 'optional' => false],
            'kode_pos' => ['text' => 'Kode pos (5 digit). Wajib diisi.', 'optional' => false],
            'kebutuhan_khusus' => ['text' => "Kebutuhan khusus siswa. Isi 'Ya' atau 'Tidak'. Wajib diisi.", 'optional' => false],
            'jenis_tinggal' => ['text' => "Tempat tinggal. Pilih salah satu: 'orang_tua', 'asrama', 'kost', atau 'wali'. Default: orang_tua.", 'optional' => false],
            'alat_transportasi' => ['text' => 'Alat transportasi ke sekolah (contoh: Sepeda Motor, Jalan Kaki, Mobil). Wajib diisi.', 'optional' => false],
            'hp' => ['text' => 'Nomor HP siswa (contoh: 081234567890). Wajib diisi. Bisa "-" jika belum ada.', 'optional' => false],
            'email' => ['text' => 'Alamat email siswa. Boleh dikosongkan.', 'optional' => true],
            'nama_ayah' => ['text' => 'Nama ayah kandung. Boleh dikosongkan (isi "-" jika tidak ada).', 'optional' => true],
            'tahun_lahir_ayah' => ['text' => 'Tahun lahir ayah (contoh: 1980). Boleh dikosongkan.', 'optional' => true],
            'pendidikan_ayah' => ['text' => 'Pendidikan terakhir ayah (contoh: SMA, S1). Boleh dikosongkan.', 'optional' => true],
            'pekerjaan_ayah' => ['text' => 'Pekerjaan ayah (contoh: Wiraswasta, PNS, Petani). Boleh dikosongkan.', 'optional' => true],
            'penghasilan_ayah' => ['text' => 'Penghasilan ayah per bulan dalam rupiah (contoh: 3000000). Boleh dikosongkan.', 'optional' => true],
            'kebutuhan_khusus_ayah' => ['text' => "Kebutuhan khusus ayah. Isi 'Ya' atau 'Tidak'. Boleh dikosongkan.", 'optional' => true],
            'no_telepon_ayah' => ['text' => 'Nomor telepon ayah. Boleh dikosongkan.', 'optional' => true],
            'nama_ibu' => ['text' => 'Nama ibu kandung. Boleh dikosongkan (isi "-" jika tidak ada).', 'optional' => true],
            'tahun_lahir_ibu' => ['text' => 'Tahun lahir ibu (contoh: 1982). Boleh dikosongkan.', 'optional' => true],
            'pendidikan_ibu' => ['text' => 'Pendidikan terakhir ibu (contoh: SMA, S1). Boleh dikosongkan.', 'optional' => true],
            'pekerjaan_ibu' => ['text' => 'Pekerjaan ibu. Boleh dikosongkan.', 'optional' => true],
            'penghasilan_ibu' => ['text' => 'Penghasilan ibu per bulan dalam rupiah. Boleh dikosongkan.', 'optional' => true],
            'kebutuhan_khusus_ibu' => ['text' => "Kebutuhan khusus ibu. Isi 'Ya' atau 'Tidak'. Boleh dikosongkan.", 'optional' => true],
            'no_telepon_ibu' => ['text' => 'Nomor telepon ibu. Boleh dikosongkan.', 'optional' => true],
            'nama_wali' => ['text' => 'Nama wali (jika bukan orang tua). Boleh dikosongkan (isi "-").', 'optional' => true],
            'tahun_lahir_wali' => ['text' => 'Tahun lahir wali. Boleh dikosongkan.', 'optional' => true],
            'pendidikan_wali' => ['text' => 'Pendidikan terakhir wali. Boleh dikosongkan.', 'optional' => true],
            'pekerjaan_wali' => ['text' => 'Pekerjaan wali. Boleh dikosongkan.', 'optional' => true],
            'penghasilan_wali' => ['text' => 'Penghasilan wali per bulan. Boleh dikosongkan.', 'optional' => true],
            'kebutuhan_khusus_wali' => ['text' => "Kebutuhan khusus wali. Isi 'Ya' atau 'Tidak'. Boleh dikosongkan.", 'optional' => true],
            'no_telepon_wali' => ['text' => 'Nomor telepon wali. Boleh dikosongkan.', 'optional' => true],
            'kode_kelas' => ['text' => "Kode kelas siswa. Wajib diisi. Harus ada di tabel kelas tenant ini. Token pertama kode (sebelum '-', '.', '_', whitespace) diperlakukan sebagai tingkat romawi (X, XI, XII) atau angka 1-12. Boleh tanpa strip (contoh: 'II') atau dengan strip (contoh: 'IV-2', 'X-TKJ-1').", 'optional' => false],
            'kode_jurusan' => ['text' => "Kode jurusan (opsional). Harus ada di tabel jurusan (contoh: TKJ, AKL). Kosongkan jika tidak ada konsep jurusan (misalnya SD).", 'optional' => true],
            'kode_ruangan' => ['text' => "Kode ruangan. Wajib diisi. Harus ada di tabel ruangan (contoh: R-101).", 'optional' => false],
            'status_awal' => ['text' => "Status awal siswa. Isi 'baru' atau 'pindahan'. Default: baru.", 'optional' => true],
            'tgl_masuk' => ['text' => 'Tanggal masuk sekolah. Format YYYY-MM-DD (contoh: 2026-07-15). Default: hari ini.', 'optional' => true],
            'spp_nominal' => ['text' => "Nominal SPP per bulan dalam rupiah (contoh: 250000, berarti Rp 250.000). Boleh dikosongkan. Jika kosong, akan menggunakan default SPP sekolah untuk tahun akademik yang dipilih (diambil dari setup Jenis Biaya).", 'optional' => true],
        ];
    }
}
