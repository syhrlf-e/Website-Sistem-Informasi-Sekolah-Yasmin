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
│   │   │   ├── layout/      # Layout Components
│   │   │   ├── sections/    # Page Sections
│   │   │   └── ui/          # UI Components (Buttons, Modals, etc)
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

## 🚀 Deployment (Hostinger)

### Via SCP

```bash
# Upload Vue files
scp -P 65002 resources/js/views/Dashboard.vue u658831449@153.92.8.118:domains/sma-mutiarainsannusantara.my.id/public_html/resources/js/views/

# Upload build assets
scp -r -P 65002 public/build/* u658831449@153.92.8.118:domains/sma-mutiarainsannusantara.my.id/public_html/public/build/

# Clear caches via SSH
ssh -p 65002 u658831449@153.92.8.118 "cd domains/sma-mutiarainsannusantara.my.id/public_html && php artisan config:cache && php artisan view:cache"
```

---

## 🎨 Color Scheme

| Mode | Primary | Secondary | Background | Text |
|------|---------|-----------|------------|------|
| Light | Teal `#14b8a6` | Blue `#3b82f6` | White `#ffffff` | Gray `#1f2937` |
| Dark | Teal `#14b8a6` | Blue `#3b82f6` | Gray `#111827` | White `#ffffff` |

---

## 📱 Responsive Breakpoints

- **Mobile**: < 768px
- **Tablet**: 768px - 1024px
- **Desktop**: > 1024px

---

## 🔒 Admin Panel Routes

| Route | Description |
|-------|-------------|
| `/yasmin-panel` | Dashboard |
| `/yasmin-panel/berita` | Kelola Berita |
| `/yasmin-panel/galeri` | Kelola Galeri |
| `/yasmin-panel/prestasi` | Kelola Prestasi |
| `/yasmin-panel/ekskul` | Kelola Ekstrakurikuler |
| `/yasmin-panel/ppdb` | PPDB Dashboard |
| `/yasmin-panel/ppdb/gelombang` | Gelombang PPDB |
| `/yasmin-panel/ppdb/pendaftar` | Pendaftar PPDB |
| `/yasmin-panel/users` | Kelola Users |

---

## 📝 Environment Variables

```env
APP_NAME="SMA Mutiara Insan Nusantara"
APP_URL=https://sma-mutiarainsannusantara.my.id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Optional: Pusher for real-time
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_key
PUSHER_APP_SECRET=your_secret
```

---

## 🤝 Contributors

| Role | Name |
|------|------|
| Developer | Tim Pengabdian Masyarakat Universitas Pamulang |
| AI Assistant | Antigravity (Claude) |

---

## 📄 License

MIT License - see [LICENSE](LICENSE) for details.

---

**Built with ❤️ for SMA Yayasan Mutiara Insan Nusantara**
