<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Prestasi;
use Illuminate\Support\Str;

class PrestasiSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = ['Akademik', 'Olahraga', 'Seni']; // Sesuai enum di migration
        $tingkat = ['Sekolah', 'Kota', 'Provinsi', 'Nasional', 'Internasional']; // Sesuai enum
        
        for ($i = 1; $i <= 15; $i++) {
            $nama = fake()->sentence(4);
            
            Prestasi::create([
                'nama_prestasi' => $nama,
                'slug' => Str::slug($nama), // Tambah slug
                'kategori' => fake()->randomElement($kategori),
                'tingkat' => fake()->randomElement($tingkat),
                'tahun' => fake()->numberBetween(2020, 2024),
                'peserta' => json_encode([fake()->name(), fake()->name()]), // JSON array
                'image' => 'https://picsum.photos/600/400?random=' . $i, // Nama kolom: image
                'deskripsi' => fake()->paragraph(),
            ]);
        }
    }
}