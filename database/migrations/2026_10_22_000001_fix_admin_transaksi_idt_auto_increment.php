<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * `admin_transaksi.idt` dibuat WITHOUT AUTO_INCREMENT sehingga setiap INSERT
 * gagal dengan "Field 'idt' doesn't have a default value".
 *
 * Migration ini memperbaiki tabel yang SUDAH ADA (bukan membuat tabel baru):
 * primary key `idt` dijadikan AUTO_INCREMENT dan nilainya diselaraskan dengan
 * urutan (`urutan`) supaya tidak bentrok.
 */
return new class extends Migration
{
    private function connection()
    {
        return DB::connection('central');
    }

    public function up(): void
    {
        $connection = $this->connection();

        if (! $connection->getSchemaBuilder()->hasTable('admin_transaksi')) {
            return;
        }

        $column = $connection->selectOne(
            "SELECT EXTRA AS extra FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'admin_transaksi' AND COLUMN_NAME = 'idt'"
        );

        if (! $column) {
            return;
        }

        // Jangan sentuh tabel yang sudah benar.
        if (str_contains(strtolower((string) $column->extra), 'auto_increment')) {
            return;
        }

        // Pastikan tidak ada id bentrok dengan urutan tertinggi.
        $maxUrutan = (int) $connection->table('admin_transaksi')->max('urutan');
        $maxIdt    = (int) $connection->table('admin_transaksi')->max('idt');

        if ($maxIdt < $maxUrutan) {
            $connection->statement(
                'UPDATE admin_transaksi SET idt = idt + '.$maxUrutan.' WHERE idt > 0'
            );
        }

        $connection->statement(
            'ALTER TABLE admin_transaksi MODIFY `idt` int(11) NOT NULL AUTO_INCREMENT'
        );
    }

    public function down(): void
    {
        // Perbaikan AUTO_INCREMENT tidak dikembalikan (aman & tidak merusak data).
    }
};
