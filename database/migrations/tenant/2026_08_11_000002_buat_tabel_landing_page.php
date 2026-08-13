<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('lp_settings')) {
            Schema::create('lp_settings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('school_name', 150);
                $table->string('tagline', 255)->nullable();
                $table->string('logo', 255)->nullable();
                $table->string('favicon', 255)->nullable();
                $table->string('email', 150)->nullable();
                $table->string('phone', 30)->nullable();
                $table->string('whatsapp', 30)->nullable();
                $table->text('address')->nullable();
                $table->text('google_maps_url')->nullable();
                $table->string('facebook', 255)->nullable();
                $table->string('instagram', 255)->nullable();
                $table->string('youtube', 255)->nullable();
                $table->string('tiktok', 255)->nullable();
                $table->string('meta_description', 255)->nullable();
                $table->string('meta_keywords', 255)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('lp_menus')) {
            Schema::create('lp_menus', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('parent_id')->nullable()->index();
                $table->string('title', 100);
                $table->string('url', 255);
                $table->enum('position', ['header', 'footer'])->default('header');
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('lp_pages')) {
            Schema::create('lp_pages', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('title', 200);
                $table->string('slug', 200)->unique();
                $table->longText('content');
                $table->string('image', 255)->nullable();
                $table->boolean('is_published')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('lp_posts')) {
            Schema::create('lp_posts', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('title', 200);
                $table->string('slug', 200)->unique();
                $table->text('excerpt')->nullable();
                $table->longText('content');
                $table->string('image', 255)->nullable();
                $table->string('category', 100)->nullable();
                $table->boolean('is_featured')->default(false);
                $table->boolean('is_published')->default(true);
                $table->dateTime('published_at')->nullable()->index();
                $table->unsignedInteger('views')->default(0);
                $table->string('tags', 255)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('lp_galleries')) {
            Schema::create('lp_galleries', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('title', 200);
                $table->text('description')->nullable();
                $table->string('image', 255);
                $table->string('album', 100)->nullable()->index();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_published')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('lp_videos')) {
            Schema::create('lp_videos', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('title', 200);
                $table->text('description')->nullable();
                $table->string('youtube_url', 500);
                $table->string('thumbnail', 255)->nullable();
                $table->boolean('is_published')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('lp_events')) {
            Schema::create('lp_events', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('title', 200);
                $table->text('description')->nullable();
                $table->string('location', 255)->nullable();
                $table->string('image', 255)->nullable();
                $table->date('start_date')->index();
                $table->date('end_date')->nullable();
                $table->time('start_time')->nullable();
                $table->boolean('is_published')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('lp_announcements')) {
            Schema::create('lp_announcements', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('title', 200);
                $table->longText('content');
                $table->string('file', 255)->nullable();
                $table->dateTime('published_at')->nullable();
                $table->boolean('is_published')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('lp_contact_messages')) {
            Schema::create('lp_contact_messages', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name', 100);
                $table->string('email', 150);
                $table->string('subject', 200)->nullable();
                $table->text('message');
                $table->boolean('is_read')->default(false);
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('lp_hero_slides')) {
            Schema::create('lp_hero_slides', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('title', 200)->nullable();
                $table->string('subtitle', 255)->nullable();
                $table->string('image', 255);
                $table->string('button_text', 100)->nullable();
                $table->string('button_url', 255)->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        foreach ([
            'lp_hero_slides',
            'lp_contact_messages',
            'lp_announcements',
            'lp_events',
            'lp_videos',
            'lp_galleries',
            'lp_posts',
            'lp_pages',
            'lp_menus',
            'lp_settings',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
