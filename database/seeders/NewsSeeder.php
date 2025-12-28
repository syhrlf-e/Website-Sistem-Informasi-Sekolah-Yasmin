<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\News;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $newsData = [
            [
                'title' => 'Studi Tour Museum Nasional',
                'excerpt' => 'Siswa kelas XI mengunjungi Museum Nasional untuk memperdalam pengetahuan sejarah Indonesia',
                'content' => 'Pada tanggal 15 Januari 2024, siswa-siswi kelas XI SMAS Mutiara melaksanakan studi tour ke Museum Nasional Jakarta. Kegiatan ini bertujuan untuk memberikan pengalaman langsung kepada siswa dalam mempelajari sejarah dan budaya Indonesia. Selama kunjungan, siswa dipandu oleh kurator museum yang menjelaskan berbagai koleksi bersejarah, mulai dari prasasti kuno, arca, hingga artefak dari berbagai era.',
                'location' => 'Museum Nasional',
                'image' => 'news/museum-nasional.jpg',
                'category' => 'event',
                'author' => 'Admin',
                'is_featured' => true,
                'published' => true,
                'published_at' => now()->subDays(5)
            ],
            [
                'title' => 'Prestasi Juara Olimpiade Matematika',
                'excerpt' => 'Siswa SMAS Mutiara meraih medali emas pada Olimpiade Matematika tingkat Nasional',
                'content' => 'Kami dengan bangga mengumumkan bahwa Muhammad Rizki, siswa kelas XII IPA, berhasil meraih medali emas pada Olimpiade Matematika Nasional yang diselenggarakan di Jakarta. Pencapaian ini merupakan hasil dari kerja keras, dedikasi, dan bimbingan intensif dari para guru. Rizki akan mewakili Indonesia pada Olimpiade Matematika Internasional tahun depan.',
                'location' => 'Jakarta Convention Center',
                'image' => 'news/olimpiade-matematika.jpg',
                'category' => 'achievement',
                'author' => 'Admin',
                'is_featured' => true,
                'published' => true,
                'published_at' => now()->subDays(10)
            ],
            [
                'title' => 'Kunjungan ke Candi Borobudur',
                'excerpt' => 'Eksplorasi warisan budaya dunia bersama siswa kelas X',
                'content' => 'Siswa kelas X mengunjungi Candi Borobudur sebagai bagian dari program pembelajaran sejarah dan budaya. Mereka belajar tentang arsitektur candi, relief-relief yang menceritakan kisah Buddha, dan makna filosofis dari struktur candi yang megah ini.',
                'location' => 'Candi Borobudur',
                'image' => 'news/borobudur.jpg',
                'category' => 'event',
                'author' => 'Admin',
                'is_featured' => true,
                'published' => true,
                'published_at' => now()->subDays(15)
            ],
            [
                'title' => 'Pengumuman Penerimaan Siswa Baru',
                'excerpt' => 'Pendaftaran siswa baru tahun ajaran 2024/2025 telah dibuka',
                'content' => 'SMAS Mutiara membuka pendaftaran siswa baru untuk tahun ajaran 2024/2025. Kami menyediakan berbagai program unggulan seperti kelas sains, bahasa, dan seni. Fasilitas lengkap dan tenaga pengajar berpengalaman siap mendampingi siswa mencapai prestasi terbaik. Pendaftaran dapat dilakukan secara online melalui website sekolah.',
                'location' => 'SMAS Mutiara',
                'image' => 'news/penerimaan.jpg',
                'category' => 'announcement',
                'author' => 'Admin',
                'is_featured' => true,
                'published' => true,
                'published_at' => now()->subDays(3)
            ],
            // 🔥 NEW: Berita Featured #5
            [
                'title' => 'Lomba Debat Bahasa Inggris Tingkat Nasional',
                'excerpt' => 'Tim debat SMAS Mutiara meraih juara 2 pada kompetisi nasional',
                'content' => 'Tim debat bahasa Inggris SMAS Mutiara berhasil meraih juara 2 pada National English Debate Competition yang diselenggarakan di Surabaya. Tim kami menunjukkan kemampuan argumentasi yang kuat dan kemampuan berbahasa Inggris yang sangat baik.',
                'location' => 'Surabaya Convention Hall',
                'image' => 'news/debat-inggris.jpg',
                'category' => 'achievement',
                'author' => 'Admin',
                'is_featured' => true,
                'published' => true,
                'published_at' => now()->subDays(7)
            ],
            // 🔥 NEW: Berita Featured #6
            [
                'title' => 'Kegiatan Bakti Sosial di Panti Asuhan',
                'excerpt' => 'OSIS mengadakan kegiatan berbagi kepada anak-anak panti asuhan',
                'content' => 'OSIS SMAS Mutiara mengadakan kegiatan bakti sosial di Panti Asuhan Kasih Ibu. Siswa membawa donasi berupa buku, alat tulis, pakaian, dan makanan. Kegiatan ini bertujuan menumbuhkan rasa empati dan kepedulian sosial kepada sesama.',
                'location' => 'Panti Asuhan Kasih Ibu',
                'image' => 'news/bakti-sosial.jpg',
                'category' => 'event',
                'author' => 'Admin',
                'is_featured' => true,
                'published' => true,
                'published_at' => now()->subDays(12)
            ],
            // 🔥 NEW: Berita Featured #7
            [
                'title' => 'Juara 1 Lomba Karya Ilmiah Remaja',
                'excerpt' => 'Penelitian siswa tentang energi terbarukan raih juara pertama',
                'content' => 'Tiga siswa kelas XI IPA berhasil meraih juara 1 pada Lomba Karya Ilmiah Remaja tingkat provinsi dengan penelitian tentang pemanfaatan energi surya untuk penerangan ramah lingkungan. Karya ini akan dipresentasikan pada kompetisi tingkat nasional.',
                'location' => 'Balai Kota',
                'image' => 'news/karya-ilmiah.jpg',
                'category' => 'achievement',
                'author' => 'Admin',
                'is_featured' => true,
                'published' => true,
                'published_at' => now()->subDays(18)
            ],
            // 🔥 NEW: Berita Featured #8
            [
                'title' => 'Pentas Seni Akhir Tahun 2024',
                'excerpt' => 'Siswa menampilkan beragam bakat seni di acara tahunan sekolah',
                'content' => 'SMAS Mutiara menggelar Pentas Seni Akhir Tahun 2024 yang menampilkan berbagai pertunjukan dari siswa, mulai dari tari modern, band, paduan suara, hingga drama musikal. Acara berlangsung meriah dan dihadiri oleh orang tua, guru, dan undangan.',
                'location' => 'Auditorium Sekolah',
                'image' => 'news/pentas-seni.jpg',
                'category' => 'event',
                'author' => 'Admin',
                'is_featured' => true,
                'published' => true,
                'published_at' => now()->subDays(2)
            ],
            [
                'title' => 'Workshop Coding untuk Siswa',
                'excerpt' => 'Mengembangkan keterampilan programming sejak dini',
                'content' => 'Sekolah mengadakan workshop coding intensif untuk siswa yang tertarik dengan dunia programming. Workshop ini mengajarkan dasar-dasar Python, web development, dan pembuatan aplikasi sederhana.',
                'location' => 'Lab Komputer',
                'image' => 'news/workshop-coding.jpg',
                'category' => 'event',
                'author' => 'Admin',
                'is_featured' => false,
                'published' => true,
                'published_at' => now()->subDays(20)
            ],
            [
                'title' => 'Juara Festival Tari Tradisional',
                'excerpt' => 'Tim tari SMAS Mutiara meraih juara 1',
                'content' => 'Tim tari SMAS Mutiara berhasil meraih juara pertama pada Festival Tari Tradisional tingkat provinsi. Mereka menampilkan tari Saman dengan penuh semangat dan kekompakan.',
                'location' => 'Gedung Kesenian',
                'image' => 'news/tari-tradisional.jpg',
                'category' => 'achievement',
                'author' => 'Admin',
                'is_featured' => false,
                'published' => true,
                'published_at' => now()->subDays(25)
            ]
        ];

        foreach ($newsData as $data) {
            News::create($data);
        }

        // Generate additional dummy news
        for ($i = 1; $i <= 5; $i++) {
            News::create([
                'title' => "Berita Sekolah #{$i}",
                'excerpt' => "Excerpt untuk berita nomor {$i}",
                'content' => "Konten lengkap untuk berita nomor {$i}. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",
                'location' => 'SMAS Mutiara',
                'image' => 'news/dummy-' . $i . '.jpg',
                'category' => ['event', 'achievement', 'announcement', 'other'][rand(0, 3)],
                'author' => 'Admin',
                'is_featured' => false,
                'published' => true,
                'published_at' => now()->subDays(rand(1, 60))
            ]);
        }
    }
}