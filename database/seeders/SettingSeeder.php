<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultSettings = [
            // Informasi Sekolah
            ['key' => 'nama_sekolah', 'value' => 'SMA Yasmin', 'type' => 'text'],
            ['key' => 'alamat', 'value' => 'Jl. Pendidikan No. 123, Jakarta Selatan', 'type' => 'text'],
            ['key' => 'telepon', 'value' => '021-12345678', 'type' => 'tel'],
            ['key' => 'email', 'value' => 'info@smayasmin.sch.id', 'type' => 'email'],
            ['key' => 'logo', 'value' => null, 'type' => 'image'],

            // Social Media
            ['key' => 'facebook', 'value' => 'https://facebook.com/smayasmin', 'type' => 'url'],
            ['key' => 'instagram', 'value' => '@smayasmin', 'type' => 'text'],
            ['key' => 'youtube', 'value' => 'https://youtube.com/@smayasmin', 'type' => 'url'],
            ['key' => 'twitter', 'value' => '@smayasmin', 'type' => 'text'],

            // Additional (opsional)
            ['key' => 'website', 'value' => 'https://smayasmin.sch.id', 'type' => 'url'],
            ['key' => 'tagline', 'value' => 'Unggul dalam Prestasi, Berkarakter Islami', 'type' => 'text'],
        ];

        foreach ($defaultSettings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'type' => $setting['type']]
            );
        }
    }
}