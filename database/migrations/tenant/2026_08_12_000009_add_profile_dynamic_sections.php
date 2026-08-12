<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('lp_struktur_organisasi')) {
            Schema::create('lp_struktur_organisasi', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name', 150);
                $table->string('role', 150);
                $table->string('photo', 255)->nullable();
                $table->boolean('is_lead')->default(false);
                $table->integer('sort_order')->default(0);
                $table->boolean('is_published')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('lp_fasilitas')) {
            Schema::create('lp_fasilitas', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('title', 150);
                $table->text('description')->nullable();
                $table->string('icon', 80)->nullable();
                $table->string('color_key', 30)->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_published')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('lp_profile_sections')) {
            Schema::create('lp_profile_sections', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('section_key', 50)->unique();
                $table->string('title', 200);
                $table->string('subtitle', 255)->nullable();
                $table->longText('content')->nullable();
                $table->string('badge_text', 100)->nullable();
                $table->string('badge_icon', 80)->nullable();
                $table->string('badge_extra', 100)->nullable();
                $table->string('extra_label', 100)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        foreach (['lp_profile_sections', 'lp_fasilitas', 'lp_struktur_organisasi'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
