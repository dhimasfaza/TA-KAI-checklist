<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Jalankan semua seeder yang kamu punya di sini.
        // Pastikan UserSeeder & ChecklistSeeder sudah dibuat.
        $this->call([
            UserSeeder::class,
            ChecklistSeeder::class,
        ]);
    }
}
