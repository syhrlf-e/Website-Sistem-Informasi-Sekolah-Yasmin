<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // PENTING: Admin & Category DULU sebelum yang lain!
        $this->call(AdminSeeder::class);
        $this->call(CategorySeeder::class);
        
        // Baru setelahnya berita, prestasi, ekskul
        $this->call(NewsSeeder::class);
        $this->call(PrestasiSeeder::class);
        $this->call(EkstrakurikulerSeeder::class);
        
        // PostSeeder bisa dihapus (udah gak dipake)
        // $this->call(PostSeeder::class);
    }
}