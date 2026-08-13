<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('lp_ppdb_settings')) {
            Schema::create('lp_ppdb_settings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('school_name', 150)->nullable();
                $table->string('eyebrow', 100)->nullable();
                $table->string('title', 200);
                $table->text('subtitle')->nullable();
                $table->string('cta_text', 100)->nullable();
                $table->string('cta_url', 255)->nullable();
                $table->string('secondary_text', 100)->nullable();
                $table->string('secondary_url', 255)->nullable();
                $table->string('hero_image', 255)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('lp_ppdb_requirements')) {
            Schema::create('lp_ppdb_requirements', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('group', 50)->default('umum');
                $table->string('title', 200);
                $table->text('items');
                $table->integer('sort_order')->default(0);
                $table->boolean('is_published')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('lp_ppdb_stages')) {
            Schema::create('lp_ppdb_stages', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('step_label', 50)->nullable();
                $table->string('title', 200);
                $table->text('description')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_published')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('lp_ppdb_schedules')) {
            Schema::create('lp_ppdb_schedules', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('gelombang', 100);
                $table->date('start_date');
                $table->date('end_date');
                $table->string('biaya_daftar', 100)->nullable();
                $table->string('spp_bulanan', 100)->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_published')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('lp_ppdb_faqs')) {
            Schema::create('lp_ppdb_faqs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('question', 255);
                $table->text('answer');
                $table->integer('sort_order')->default(0);
                $table->boolean('is_published')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        foreach ([
            'lp_ppdb_faqs',
            'lp_ppdb_schedules',
            'lp_ppdb_stages',
            'lp_ppdb_requirements',
            'lp_ppdb_settings',
        ] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
