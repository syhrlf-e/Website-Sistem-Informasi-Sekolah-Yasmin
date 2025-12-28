# SMA - Yayasan Mutiara Insan Nusantara - Website Sekolah

Website sistem informasi yayasan mutiara insan nusantara yang dibangun dengan **Laravel**, **VueJs**, dan **TailwindCss**.

## 🚀 Tech Stack

- **Backend**: Laravel 11
- **Frontend**: Vue.js 3 (Composition API)
- **Styling**: Tailwind CSS 3
- **State Management**: Pinia
- **Routing**: Vue Router
- **Build Tool**: Vite

## 📁 Struktur Project

```
resources/
├── css/
│   └── app.css                 # Tailwind CSS & Custom Styles
├── js/
│   ├── app.js                  # Entry Point
│   ├── App.vue                 # Root Component
│   ├── components/             # Vue Components
│   │   ├── Navbar.vue
│   │   ├── HeroSection.vue
│   │   ├── InfoCards.vue
│   │   ├── SambutanSection.vue
│   │   ├── VisiMisiSection.vue
│   │   ├── PrestasiSection.vue
│   │   ├── EkskulSection.vue
│   │   ├── GaleriSection.vue
│   │   ├── StudiTourSection.vue
│   │   └── Footer.vue
│   ├── pages/
│   │   └── Home.vue            # Home Page
│   ├── router/
│   │   └── index.js            # Vue Router Config
│   └── stores/
│       └── theme.js            # Pinia Store (Dark Mode)
```

## 🎨 Fitur

### ✅ Dark Mode

- Toggle dark mode di navbar
- Persistent dengan localStorage
- Deteksi system preference
- Smooth transition

### ✅ Dynamic Hero Image

- Gambar hero berubah otomatis saat dark mode
- Smooth fade transition

### ✅ Smooth Scroll

- Smooth scroll antar section
- Offset untuk fixed navbar
- Custom scrollbar dengan tema

### ✅ Responsive Design

- Mobile-first approach
- Breakpoints: mobile (90%), tablet (85%), desktop (80%)
- Max-width: calc(100vw - 480px)

### ✅ Animations

- Hover effects pada cards
- Fade-in animations
- Transform transitions
- Bounce scroll indicator

## 📸 Setup Images

Untuk menampilkan website dengan sempurna, Anda perlu menambahkan gambar-gambar berikut ke folder `public/images/`:

### Required Images:

1. **Logo Sekolah**
   - Path: `public/images/logo.png`
   - Ukuran: 200x200px (square)

2. **Hero Images (Light & Dark Mode)**
   - Light: `public/images/hero-light.jpg`
   - Dark: `public/images/hero-dark.jpg`
   - Ukuran: 1920x1080px (landscape)

3. **Kepala Sekolah**
   - Path: `public/images/kepala-sekolah.jpg`
   - Ukuran: 600x800px (portrait)

4. **Prestasi**
   - Path: `public/images/prestasi.jpg`
   - Ukuran: 800x600px (landscape)

5. **Galeri** (8 images)
   - Path: `public/images/gallery/1.jpg` sampai `8.jpg`
   - Ukuran: 600x400px (landscape)

6. **Studi Tour** (4 images)
   - Path: `public/images/tour/1.jpg` sampai `4.jpg`
   - Ukuran: 800x600px (landscape)

### Cara Setup:

```bash
# Buat folder images
mkdir public/images
mkdir public/images/gallery
mkdir public/images/tour

# Copy gambar Anda ke folder yang sesuai
```

## 🛠️ Installation

1. **Install Dependencies**

```bash
npm install
composer install
```

2. **Setup Environment**

```bash
cp .env.example .env
php artisan key:generate
```

3. **Run Development Server**

```bash
# Terminal 1: Laravel
php artisan serve

# Terminal 2: Vite
npm run dev
```

4. **Build for Production**

```bash
npm run build
```

## 🎯 Sections

Website ini terdiri dari beberapa section:

1. **Navbar** - Fixed navbar dengan dark mode toggle
2. **Hero** - Full-screen hero dengan gambar gedung sekolah
3. **Info Cards** - 3 cards (Sambutan, Logo, Visi Misi)
4. **Sambutan** - Sambutan Kepala Sekolah
5. **Visi & Misi** - Visi dan Misi sekolah
6. **Prestasi** - Prestasi dan penghargaan
7. **Ekstrakurikuler** - Grid cards ekstrakurikuler
8. **Galeri** - Galeri foto kegiatan
9. **Studi Tour** - Dokumentasi studi tour
10. **Footer** - Informasi kontak dan social media

## 🎨 Color Scheme

### Light Mode

- Primary: Teal (#14b8a6)
- Secondary: Blue (#3b82f6)
- Background: White (#ffffff)
- Text: Gray (#1f2937)

### Dark Mode

- Primary: Teal (#14b8a6)
- Secondary: Blue (#3b82f6)
- Background: Gray (#111827)
- Text: White (#ffffff)

## 📱 Responsive Breakpoints

- **Mobile**: < 768px (width: 90%)
- **Tablet**: 768px - 1024px (width: 85%)
- **Desktop**: > 1024px (width: 80%, max-width: calc(100vw - 480px))

## 🔧 Customization

### Mengubah Warna

Edit `resources/css/app.css` untuk custom colors.

### Menambah Section

1. Buat component baru di `resources/js/components/ui`
2. Import di `resources/js/pages/Home.vue`
3. Tambahkan menu di `Navbar.vue`

### Mengubah Konten

Edit langsung di component yang sesuai di folder `resources/js/components/ui`.

## 📝 Notes

- Pastikan gambar hero dark mode sudah disiapkan
- Semua link smooth scroll menggunakan hash (#section-id)
- Dark mode state disimpan di localStorage
- Custom scrollbar hanya bekerja di browser berbasis Chromium

## 🤝 Support

Jika ada pertanyaan atau issue, silakan hubungi developer.

---

**Built with ❤️ by Tim Pengabdian Masyarakat Universitas Pamulang. All rights reserved.**
