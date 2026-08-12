<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        if (!Schema::hasColumn('users', 'jabatan')) {
            if (!Schema::hasColumn('users', 'id_jabatan')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->unsignedBigInteger('id_jabatan')->nullable()->after('nik');
                    $table->foreign('id_jabatan')->references('id')->on('jabatan')->nullOnDelete();
                });
            }
            return;
        }

        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('ALTER TABLE `users` ADD COLUMN `id_jabatan` BIGINT UNSIGNED NULL AFTER `nik`');
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('id_jabatan')->nullable()->after('nik');
            });
        }

        $rows = DB::table('jabatan')->get(['id', 'nama_jabatan']);
        $byLower = [];
        foreach ($rows as $j) {
            $key = mb_strtolower(trim((string) $j->nama_jabatan));
            if ($key !== '') {
                $byLower[$key] = (int) $j->id;
            }
        }

        $users = DB::table('users')->whereNotNull('jabatan')->get(['id', 'jabatan']);
        foreach ($users as $u) {
            $raw = trim((string) $u->jabatan);
            if ($raw === '') {
                continue;
            }
            $match = null;
            if (ctype_digit($raw)) {
                $candidate = (int) $raw;
                if (isset($byLower[$raw]) || DB::table('jabatan')->where('id', $candidate)->exists()) {
                    $match = $candidate;
                }
            }
            if ($match === null) {
                $key = mb_strtolower($raw);
                if (isset($byLower[$key])) {
                    $match = $byLower[$key];
                }
            }
            if ($match !== null) {
                DB::table('users')->where('id', $u->id)->update(['id_jabatan' => $match]);
            }
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('jabatan');
        });

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('ALTER TABLE `users` ADD CONSTRAINT `users_id_jabatan_foreign` FOREIGN KEY (`id_jabatan`) REFERENCES `jabatan`(`id`) ON DELETE SET NULL ON UPDATE CASCADE');
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('id_jabatan')->references('id')->on('jabatan')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        $driver = DB::connection()->getDriverName();
        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('ALTER TABLE `users` DROP FOREIGN KEY `users_id_jabatan_foreign`');
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['id_jabatan']);
            });
        }

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('ALTER TABLE `users` ADD COLUMN `jabatan` VARCHAR(100) NULL AFTER `nik`');
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->string('jabatan', 100)->nullable()->after('nik');
            });
        }

        $users = DB::table('users')->whereNotNull('id_jabatan')->get(['id', 'id_jabatan']);
        $jabatanById = DB::table('jabatan')->pluck('nama_jabatan', 'id');
        foreach ($users as $u) {
            $name = $jabatanById[$u->id_jabatan] ?? null;
            if ($name) {
                DB::table('users')->where('id', $u->id)->update(['jabatan' => $name]);
            }
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('id_jabatan');
        });
    }
};
