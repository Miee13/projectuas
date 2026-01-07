<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Panggil TrendSeeder agar dijalankan saat proses migrasi --seed
        $this->call([
            TrendSeeder::class,
        ]);
    }
}