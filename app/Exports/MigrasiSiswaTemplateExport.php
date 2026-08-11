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
            'no_telepon_ayah',
            'nama_ibu',
            'tahun_lahir_ibu',
            'pendidikan_ibu',
            'pekerjaan_ibu',
            'penghasilan_ibu',
            'no_telepon_ibu',
            'kode_kelas',
            'ruang',
            'tingkat',
        ];
    }

    public function array(): array
    {
        return [
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
                '081234567891',
                'Siti Aminah',
                '1982',
                'SMA',
                'Ibu Rumah Tangga',
                '0',
                '081234567892',
                'X-TKJ-1',
                'R-101',
                '10',
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
                $sheet->getComment('A1')->getText()->createTextRun(
                    "Petunjuk:\n" .
                    "- Kolom bertanda * wajib diisi.\n" .
                    "- jenis_kelamin: L / P.\n" .
                    "- tanggal_lahir: format YYYY-MM-DD (cth: 2010-05-12).\n" .
                    "- jenis_tinggal: salah satu dari orang_tua / asrama / kost / wali.\n" .
                    "- kode_kelas: harus ada di tabel kelas tenant ini.\n" .
                    "- password: kosongkan jika ingin default = nipd.\n" .
                    "- Baris NISN yang sama akan update data lama, bukan duplikat."
                );
            },
        ];
    }
}
