<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('nama_sekolah')->nullable()->after('id');
            $table->string('email')->nullable()->after('nama_sekolah');
        });

        // Migrate existing data from JSON column to real columns
        $rows = DB::table('tenants')->whereNotNull('data')->get(['id', 'data']);
        foreach ($rows as $row) {
            $decoded = json_decode($row->data, true);
            if (! is_array($decoded)) {
                continue;
            }

            $first = $decoded[0] ?? $decoded;

            DB::table('tenants')
                ->where('id', $row->id)
                ->update([
                    'nama_sekolah' => $first['nama'] ?? null,
                    'email'        => $first['email'] ?? null,
                ]);
        }

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('data');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->json('data')->nullable();
        });

        $rows = DB::table('tenants')->get(['id', 'nama_sekolah', 'email']);
        foreach ($rows as $row) {
            DB::table('tenants')
                ->where('id', $row->id)
                ->update([
                    'data' => json_encode(array_filter([
                        'nama'  => $row->nama_sekolah,
                        'email' => $row->email,
                    ])),
                ]);
        }

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['nama_sekolah', 'email']);
        });
    }
};