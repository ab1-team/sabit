<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('admin_transaksi')) {
            DB::statement("CREATE TABLE `admin_transaksi` (
                `idt` int(11) NOT NULL AUTO_INCREMENT,
                `tgl_transaksi` date DEFAULT NULL,
                `rekening_debit` varchar(10) DEFAULT '0',
                `rekening_kredit` varchar(10) DEFAULT '0',
                `idv` int(11) DEFAULT 0,
                `keterangan_transaksi` text DEFAULT NULL,
                `jumlah` varchar(15) DEFAULT '0',
                `urutan` int(11) DEFAULT 0,
                `id_user` int(11) DEFAULT 0,
                PRIMARY KEY (`idt`) USING BTREE,
                KEY `tgl_transaksi` (`tgl_transaksi`,`rekening_debit`,`rekening_kredit`,`idv`,`jumlah`,`urutan`,`id_user`) USING BTREE,
                FULLTEXT KEY `keterangan_transaksi` (`keterangan_transaksi`)
            ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC");

            DB::statement('ALTER TABLE `admin_transaksi` AUTO_INCREMENT = 2');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_transaksi');
    }
};
