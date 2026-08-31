<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('transaksi')) {
            return;
        }

        DB::statement('DROP TRIGGER IF EXISTS `create_saldo_debit`');
        DB::statement('DROP TRIGGER IF EXISTS `update_saldo_debit`');
        DB::statement('DROP TRIGGER IF EXISTS `delete_saldo_debit`');

        DB::unprepared(<<<'SQL'
CREATE TRIGGER `create_saldo_debit` AFTER INSERT ON `transaksi`
FOR EACH ROW
BEGIN
    INSERT INTO saldo (`id`, `kode_akun`, `tahun`, `bulan`, `debit`, `kredit`)
    VALUES (
        CONCAT(REPLACE(NEW.rekening_debit, '.', ''), YEAR(NEW.tanggal_transaksi), LPAD(MONTH(NEW.tanggal_transaksi), 2, '0')),
        NEW.rekening_debit,
        YEAR(NEW.tanggal_transaksi),
        LPAD(MONTH(NEW.tanggal_transaksi), 2, '0'),
        COALESCE((SELECT SUM(jumlah) FROM transaksi WHERE rekening_debit = NEW.rekening_debit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(NEW.tanggal_transaksi), '-01-01') AND LAST_DAY(NEW.tanggal_transaksi)
            AND deleted_at IS NULL), 0),
        COALESCE((SELECT SUM(jumlah) FROM transaksi WHERE rekening_kredit = NEW.rekening_debit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(NEW.tanggal_transaksi), '-01-01') AND LAST_DAY(NEW.tanggal_transaksi)
            AND deleted_at IS NULL), 0)
    )
    ON DUPLICATE KEY UPDATE
        debit  = COALESCE((SELECT SUM(jumlah) FROM transaksi WHERE rekening_debit = NEW.rekening_debit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(NEW.tanggal_transaksi), '-01-01') AND LAST_DAY(NEW.tanggal_transaksi)
            AND deleted_at IS NULL), 0),
        kredit = COALESCE((SELECT SUM(jumlah) FROM transaksi WHERE rekening_kredit = NEW.rekening_debit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(NEW.tanggal_transaksi), '-01-01') AND LAST_DAY(NEW.tanggal_transaksi)
            AND deleted_at IS NULL), 0);

    INSERT INTO saldo (`id`, `kode_akun`, `tahun`, `bulan`, `debit`, `kredit`)
    VALUES (
        CONCAT(REPLACE(NEW.rekening_kredit, '.', ''), YEAR(NEW.tanggal_transaksi), LPAD(MONTH(NEW.tanggal_transaksi), 2, '0')),
        NEW.rekening_kredit,
        YEAR(NEW.tanggal_transaksi),
        LPAD(MONTH(NEW.tanggal_transaksi), 2, '0'),
        COALESCE((SELECT SUM(jumlah) FROM transaksi WHERE rekening_debit = NEW.rekening_kredit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(NEW.tanggal_transaksi), '-01-01') AND LAST_DAY(NEW.tanggal_transaksi)
            AND deleted_at IS NULL), 0),
        COALESCE((SELECT SUM(jumlah) FROM transaksi WHERE rekening_kredit = NEW.rekening_kredit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(NEW.tanggal_transaksi), '-01-01') AND LAST_DAY(NEW.tanggal_transaksi)
            AND deleted_at IS NULL), 0)
    )
    ON DUPLICATE KEY UPDATE
        debit  = COALESCE((SELECT SUM(jumlah) FROM transaksi WHERE rekening_debit = NEW.rekening_kredit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(NEW.tanggal_transaksi), '-01-01') AND LAST_DAY(NEW.tanggal_transaksi)
            AND deleted_at IS NULL), 0),
        kredit = COALESCE((SELECT SUM(jumlah) FROM transaksi WHERE rekening_kredit = NEW.rekening_kredit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(NEW.tanggal_transaksi), '-01-01') AND LAST_DAY(NEW.tanggal_transaksi)
            AND deleted_at IS NULL), 0);
END
SQL);

        DB::unprepared(<<<'SQL'
CREATE TRIGGER `update_saldo_debit` AFTER UPDATE ON `transaksi`
FOR EACH ROW
BEGIN
    INSERT INTO saldo (`id`, `kode_akun`, `tahun`, `bulan`, `debit`, `kredit`)
    VALUES (
        CONCAT(REPLACE(NEW.rekening_debit, '.', ''), YEAR(NEW.tanggal_transaksi), LPAD(MONTH(NEW.tanggal_transaksi), 2, '0')),
        NEW.rekening_debit,
        YEAR(NEW.tanggal_transaksi),
        LPAD(MONTH(NEW.tanggal_transaksi), 2, '0'),
        COALESCE((SELECT SUM(jumlah) FROM transaksi WHERE rekening_debit = NEW.rekening_debit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(NEW.tanggal_transaksi), '-01-01') AND LAST_DAY(NEW.tanggal_transaksi)
            AND deleted_at IS NULL), 0),
        COALESCE((SELECT SUM(jumlah) FROM transaksi WHERE rekening_kredit = NEW.rekening_debit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(NEW.tanggal_transaksi), '-01-01') AND LAST_DAY(NEW.tanggal_transaksi)
            AND deleted_at IS NULL), 0)
    )
    ON DUPLICATE KEY UPDATE
        debit  = COALESCE((SELECT SUM(jumlah) FROM transaksi WHERE rekening_debit = NEW.rekening_debit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(NEW.tanggal_transaksi), '-01-01') AND LAST_DAY(NEW.tanggal_transaksi)
            AND deleted_at IS NULL), 0),
        kredit = COALESCE((SELECT SUM(jumlah) FROM transaksi WHERE rekening_kredit = NEW.rekening_debit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(NEW.tanggal_transaksi), '-01-01') AND LAST_DAY(NEW.tanggal_transaksi)
            AND deleted_at IS NULL), 0);

    INSERT INTO saldo (`id`, `kode_akun`, `tahun`, `bulan`, `debit`, `kredit`)
    VALUES (
        CONCAT(REPLACE(NEW.rekening_kredit, '.', ''), YEAR(NEW.tanggal_transaksi), LPAD(MONTH(NEW.tanggal_transaksi), 2, '0')),
        NEW.rekening_kredit,
        YEAR(NEW.tanggal_transaksi),
        LPAD(MONTH(NEW.tanggal_transaksi), 2, '0'),
        COALESCE((SELECT SUM(jumlah) FROM transaksi WHERE rekening_debit = NEW.rekening_kredit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(NEW.tanggal_transaksi), '-01-01') AND LAST_DAY(NEW.tanggal_transaksi)
            AND deleted_at IS NULL), 0),
        COALESCE((SELECT SUM(jumlah) FROM transaksi WHERE rekening_kredit = NEW.rekening_kredit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(NEW.tanggal_transaksi), '-01-01') AND LAST_DAY(NEW.tanggal_transaksi)
            AND deleted_at IS NULL), 0)
    )
    ON DUPLICATE KEY UPDATE
        debit  = COALESCE((SELECT SUM(jumlah) FROM transaksi WHERE rekening_debit = NEW.rekening_kredit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(NEW.tanggal_transaksi), '-01-01') AND LAST_DAY(NEW.tanggal_transaksi)
            AND deleted_at IS NULL), 0),
        kredit = COALESCE((SELECT SUM(jumlah) FROM transaksi WHERE rekening_kredit = NEW.rekening_kredit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(NEW.tanggal_transaksi), '-01-01') AND LAST_DAY(NEW.tanggal_transaksi)
            AND deleted_at IS NULL), 0);
END
SQL);

        DB::unprepared(<<<'SQL'
CREATE TRIGGER `delete_saldo_debit` AFTER DELETE ON `transaksi`
FOR EACH ROW
BEGIN
    INSERT INTO saldo (`id`, `kode_akun`, `tahun`, `bulan`, `debit`, `kredit`)
    VALUES (
        CONCAT(REPLACE(OLD.rekening_debit, '.', ''), YEAR(OLD.tanggal_transaksi), LPAD(MONTH(OLD.tanggal_transaksi), 2, '0')),
        OLD.rekening_debit,
        YEAR(OLD.tanggal_transaksi),
        LPAD(MONTH(OLD.tanggal_transaksi), 2, '0'),
        COALESCE((SELECT SUM(jumlah) FROM transaksi WHERE rekening_debit = OLD.rekening_debit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(OLD.tanggal_transaksi), '-01-01') AND LAST_DAY(OLD.tanggal_transaksi)
            AND deleted_at IS NULL), 0),
        COALESCE((SELECT SUM(jumlah) FROM transaksi WHERE rekening_kredit = OLD.rekening_debit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(OLD.tanggal_transaksi), '-01-01') AND LAST_DAY(OLD.tanggal_transaksi)
            AND deleted_at IS NULL), 0)
    )
    ON DUPLICATE KEY UPDATE
        debit  = COALESCE((SELECT SUM(jumlah) FROM transaksi WHERE rekening_debit = OLD.rekening_debit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(OLD.tanggal_transaksi), '-01-01') AND LAST_DAY(OLD.tanggal_transaksi)
            AND deleted_at IS NULL), 0),
        kredit = COALESCE((SELECT SUM(jumlah) FROM transaksi WHERE rekening_kredit = OLD.rekening_debit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(OLD.tanggal_transaksi), '-01-01') AND LAST_DAY(OLD.tanggal_transaksi)
            AND deleted_at IS NULL), 0);

    INSERT INTO saldo (`id`, `kode_akun`, `tahun`, `bulan`, `debit`, `kredit`)
    VALUES (
        CONCAT(REPLACE(OLD.rekening_kredit, '.', ''), YEAR(OLD.tanggal_transaksi), LPAD(MONTH(OLD.tanggal_transaksi), 2, '0')),
        OLD.rekening_kredit,
        YEAR(OLD.tanggal_transaksi),
        LPAD(MONTH(OLD.tanggal_transaksi), 2, '0'),
        COALESCE((SELECT SUM(jumlah) FROM transaksi WHERE rekening_debit = OLD.rekening_kredit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(OLD.tanggal_transaksi), '-01-01') AND LAST_DAY(OLD.tanggal_transaksi)
            AND deleted_at IS NULL), 0),
        COALESCE((SELECT SUM(jumlah) FROM transaksi WHERE rekening_kredit = OLD.rekening_kredit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(OLD.tanggal_transaksi), '-01-01') AND LAST_DAY(OLD.tanggal_transaksi)
            AND deleted_at IS NULL), 0)
    )
    ON DUPLICATE KEY UPDATE
        debit  = COALESCE((SELECT SUM(jumlah) FROM transaksi WHERE rekening_debit = OLD.rekening_kredit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(OLD.tanggal_transaksi), '-01-01') AND LAST_DAY(OLD.tanggal_transaksi)
            AND deleted_at IS NULL), 0),
        kredit = COALESCE((SELECT SUM(jumlah) FROM transaksi WHERE rekening_kredit = OLD.rekening_kredit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(OLD.tanggal_transaksi), '-01-01') AND LAST_DAY(OLD.tanggal_transaksi)
            AND deleted_at IS NULL), 0);
END
SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS `create_saldo_debit`');
        DB::statement('DROP TRIGGER IF EXISTS `update_saldo_debit`');
        DB::statement('DROP TRIGGER IF EXISTS `delete_saldo_debit`');
    }
};
