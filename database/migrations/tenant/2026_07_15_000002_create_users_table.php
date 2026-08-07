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
            Schema::create('users', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('nama');
                $table->string('nik', 32)->nullable();
                $table->string('jabatan', 100)->nullable();
                $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
                $table->string('telepon', 30)->nullable();
                $table->text('alamat')->nullable();
                $table->string('foto')->nullable();
                $table->string('email');
                $table->string('username');
                $table->string('password');
                $table->string('remember_token', 100)->nullable();
                $table->timestamps();
            });

            DB::statement('ALTER TABLE `users` AUTO_INCREMENT = 5');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
