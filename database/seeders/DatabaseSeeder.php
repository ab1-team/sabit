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
            TenantAdminUserSeeder::class,
            TenantRekeningSeeder::class,
            TenantTransaksiSeeder::class,
            TenantInvoiceSeeder::class,
        ]);
    }
}
