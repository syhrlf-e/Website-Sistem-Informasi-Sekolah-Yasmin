# 🏫 SMA Mutiara Insan Nusantara - Website & CMS

Website sistem informasi sekolah SMA Yayasan Mutiara Insan Nusantara dengan fitur PPDB Online, CMS Admin, dan Landing Page modern.

**Live Production:** [sma-mutiarainsannusantara.my.id](https://sma-mutiarainsannusantara.my.id)

---

## 🚀 Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 11, PHP 8.2+ |
| Frontend | Vue.js 3 (Composition API) |
| Styling | Tailwind CSS 3 |
| State | Pinia |
| Routing | Vue Router 4 |
| Build | Vite 6 |
| Auth | Laravel Sanctum |
| Rich Text | TipTap Editor |
| Icons | Lucide Icons |
| Animation | GSAP, Lenis |

---

## ✨ Fitur Utama

### 🌐 Public Website
- **Landing Page** - Homepage dengan animasi smooth scroll
- **Berita** - Artikel/berita sekolah dengan kategori
- **Galeri** - Galeri foto kegiatan sekolah
- **Prestasi** - Showcase prestasi siswa
- **Ekstrakurikuler** - Daftar ekskul dengan pendaftaran online
- **Profil Sekolah** - Visi misi, sambutan kepala sekolah

### 📝 PPDB (Pendaftaran Peserta Didik Baru)
- **Form Pendaftaran Multi-step** - 6 section (Identitas, Alamat, Pendidikan, Orang Tua, Wali, Kesehatan)
- **Gelombang Pendaftaran** - Multiple waves dengan kuota & deadline
- **Cek Status** - Halaman cek status pendaftaran via nomor registrasi
- **Pengumuman** - Pengumuman hasil seleksi

### 🔐 Admin Panel (`/yasmin-panel`)
- **Dashboard** - Overview statistik PPDB, Ekskul, Konten Website
- **PPDB Management** - Kelola pendaftar, gelombang, seleksi, pengumuman
- **Content Management** - Kelola berita, galeri, prestasi, pengumuman
- **Ekstrakurikuler** - Kelola ekskul dengan sistem token pendaftaran
- **User Management** - Kelola admin/users
- **Activity Logs** - Log aktivitas admin
- **Calendar** - Agenda sekolah dengan sidebar kalender

---

## 📁 Struktur Project

```
├── app/
│   ├── Http/Controllers/
│   │   ├── Api/             # API Controllers
│   │   └── Admin/           # Admin Controllers
│   └── Models/              # Eloquent Models
├── resources/
│   ├── js/
│   │   ├── components/      # Vue Components
│   │   ├── pages/           # Vue Pages
│   │   ├── views/           # Admin Views
│   │   ├── stores/          # Pinia Stores
│   │   ├── composables/     # Vue Composables
│   │   └── router/          # Vue Router Config
│   └── css/
│       └── app.css          # Tailwind + Custom Styles
├── routes/
│   ├── api.php              # API Routes
│   └── web.php              # Web Routes
└── public/
    └── build/               # Built Assets (Vite)
```

---

## 🛠️ Installation

### Prerequisites
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8+

### Setup

```bash
# Clone repository
git clone https://github.com/syhrlf-e/Website-Sistem-Informasi-Sekolah-Yasmin.git
cd Website-Sistem-Informasi-Sekolah-Yasmin

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database in .env
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Run migrations & seeders
php artisan migrate --seed

# Create storage link
php artisan storage:link
```

### Development

```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Vite dev server
npm run dev
```

### Production Build

```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🤝 Contributors

| Role | Name |
|------|------|
| Developer | Tim Pengabdian Masyarakat Universitas Pamulang |

---

## 📄 License

MIT License - see [LICENSE](LICENSE) for details.

---

**Built with ❤️ for SMA Yayasan Mutiara Insan Nusantara**
