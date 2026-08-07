<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's central database (only central/shared tables).
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            AdminRekeningSeeder::class,
            AdminTransaksiSeeder::class,
            AdminInvoiceSeeder::class,
        ]);
    }
}
