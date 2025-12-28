<!--
  @component Footer
  @description Footer section untuk landing page dengan logo, quick links, contact info, dan social media
  @section id="kontak" - Target scroll untuk navigasi
  @data footerData - Static data sekolah (nama, alamat, telepon, email, social media)
  @data quickLinks - Array link akses cepat (Beranda, Profil, Guru, PPDB, Berita)
  @data informationLinks - Array link informasi (Prestasi, Galeri, Ekskul, Testimoni, Fasilitas)
-->

<template>
  <footer
    id="kontak"
    class="relative bg-gray-900 dark:bg-black text-white py-20 overflow-hidden transition-colors duration-300"
  >
    <!-- Background decorative blobs -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div
        class="absolute -top-40 -right-40 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl animate-pulse"
      ></div>
      <div
        class="absolute -bottom-40 -left-40 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl animate-pulse"
        style="animation-delay: 1s"
      ></div>
      <div
        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-indigo-500/5 rounded-full blur-3xl animate-pulse"
        style="animation-delay: 2s"
      ></div>
    </div>

    <div class="container-content relative z-10">
      <!-- Header: Logo & Description -->
      <div class="mb-12">
        <div class="space-y-6">
          <div class="flex items-center gap-3">
            <div class="relative">
              <img
                :src="footerData.logo_url"
                :alt="`Logo ${footerData.nama_sekolah}`"
                class="relative h-12 w-12 object-contain p-2 rounded-xl"
              />
            </div>
            <div>
              <h3
                class="text-xl font-bold font-poppins bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent"
              >
                {{ footerData.nama_sekolah }}
              </h3>
              <p class="text-sm text-gray-400">
                {{ footerData.tagline }}
              </p>
            </div>
          </div>
          <p class="text-gray-400 leading-relaxed text-sm">
            {{ footerData.deskripsi }}
          </p>
        </div>
      </div>

      <!-- Links Grid: Quick Links, Information, Contact -->
      <div class="grid grid-cols-2 lg:grid-cols-3 gap-12 mb-12">
        <!-- Quick Links -->
        <div>
          <h3 class="text-lg font-bold mb-6 font-poppins flex items-center gap-2">
            <div class="w-1 h-6 bg-gradient-to-b from-blue-500 to-purple-600 rounded-full"></div>
            <span class="bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent"
              >Akses Cepat</span
            >
          </h3>
          <ul class="space-y-3">
            <li v-for="link in quickLinks" :key="link.id">
              <a
                :href="link.href"
                class="group text-gray-400 hover:text-blue-400 transition-all duration-200 text-sm flex items-center gap-2"
              >
                <ChevronRight
                  class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-200 text-blue-500"
                />
                {{ link.label }}
              </a>
            </li>
          </ul>
        </div>

        <!-- Information Links -->
        <div>
          <h3 class="text-lg font-bold mb-6 font-poppins flex items-center gap-2">
            <div class="w-1 h-6 bg-gradient-to-b from-blue-500 to-purple-600 rounded-full"></div>
            <span class="bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent"
              >Informasi</span
            >
          </h3>
          <ul class="space-y-3">
            <li v-for="link in informationLinks" :key="link.id">
              <a
                :href="link.href"
                class="group text-gray-400 hover:text-purple-400 transition-all duration-200 text-sm flex items-center gap-2"
              >
                <ChevronRight
                  class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-200 text-purple-500"
                />
                {{ link.label }}
              </a>
            </li>
          </ul>
        </div>

        <!-- Contact Information -->
        <div class="col-span-2 lg:col-span-1">
          <h3 class="text-lg font-bold mb-6 font-poppins flex items-center gap-2">
            <div class="w-1 h-6 bg-gradient-to-b from-blue-500 to-purple-600 rounded-full"></div>
            <span class="bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent"
              >Hubungi Kami</span
            >
          </h3>
          <ul class="space-y-4">
            <li class="flex items-start gap-3 group">
              <div
                class="p-2 bg-gradient-to-br from-blue-500/20 to-purple-500/20 rounded-lg backdrop-blur-sm"
              >
                <MapPin class="w-4 h-4 text-blue-400 flex-shrink-0" />
              </div>
              <p class="text-sm text-gray-400 group-hover:text-gray-300 transition-colors">
                {{ footerData.alamat }}
              </p>
            </li>
            <li class="flex items-center gap-3 group">
              <div
                class="p-2 bg-gradient-to-br from-blue-500/20 to-purple-500/20 rounded-lg backdrop-blur-sm"
              >
                <Phone class="w-4 h-4 text-blue-400 flex-shrink-0" />
              </div>
              <a
                :href="`tel:${footerData.telepon_raw}`"
                class="text-sm text-gray-400 hover:text-blue-400 transition-colors"
              >
                {{ footerData.telepon }}
              </a>
            </li>
            <li class="flex items-center gap-3 group">
              <div
                class="p-2 bg-gradient-to-br from-blue-500/20 to-purple-500/20 rounded-lg backdrop-blur-sm"
              >
                <Mail class="w-4 h-4 text-blue-400 flex-shrink-0" />
              </div>
              <a
                :href="`mailto:${footerData.email}`"
                class="text-sm text-gray-400 hover:text-blue-400 transition-colors break-all"
              >
                {{ footerData.email }}
              </a>
            </li>
            <li class="flex items-center gap-3 group">
              <div
                class="p-2 bg-gradient-to-br from-blue-500/20 to-purple-500/20 rounded-lg backdrop-blur-sm"
              >
                <Clock class="w-4 h-4 text-blue-400 flex-shrink-0" />
              </div>
              <p class="text-sm text-gray-400">
                {{ footerData.jam_operasional }}
              </p>
            </li>
          </ul>
        </div>
      </div>

      <!-- Gradient Divider -->
      <div class="relative mb-8">
        <div class="absolute inset-0 flex items-center">
          <div
            class="w-full h-px bg-gradient-to-r from-transparent via-blue-500/50 to-transparent"
          ></div>
        </div>
      </div>

      <!-- Bottom Bar: Social Media & Copyright -->
      <div class="flex flex-col md:flex-row items-center justify-between gap-6">
        <!-- Social Media Links -->
        <div class="flex items-center gap-4">
          <span class="text-sm text-gray-400 font-medium">Ikuti Kami:</span>
          <div class="flex gap-3">
            <a
              v-if="footerData.facebook"
              :href="footerData.facebook"
              target="_blank"
              rel="noopener noreferrer"
              class="group relative w-10 h-10 bg-gradient-to-br from-blue-500/10 to-purple-500/10 hover:from-blue-600 hover:to-blue-700 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 hover:shadow-lg hover:shadow-blue-500/50 backdrop-blur-sm border border-blue-500/20"
              aria-label="Facebook"
            >
              <Facebook class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors" />
            </a>
            <a
              v-if="footerData.instagram"
              :href="footerData.instagram"
              target="_blank"
              rel="noopener noreferrer"
              class="group relative w-10 h-10 bg-gradient-to-br from-blue-500/10 to-purple-500/10 hover:from-purple-600 hover:to-pink-600 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 hover:shadow-lg hover:shadow-purple-500/50 backdrop-blur-sm border border-purple-500/20"
              aria-label="Instagram"
            >
              <Instagram class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors" />
            </a>
            <a
              v-if="footerData.youtube"
              :href="footerData.youtube"
              target="_blank"
              rel="noopener noreferrer"
              class="group relative w-10 h-10 bg-gradient-to-br from-blue-500/10 to-purple-500/10 hover:from-red-600 hover:to-red-700 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 hover:shadow-lg hover:shadow-red-500/50 backdrop-blur-sm border border-red-500/20"
              aria-label="YouTube"
            >
              <Youtube class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors" />
            </a>
            <a
              v-if="footerData.twitter"
              :href="footerData.twitter"
              target="_blank"
              rel="noopener noreferrer"
              class="group relative w-10 h-10 bg-gradient-to-br from-blue-500/10 to-purple-500/10 hover:from-blue-500 hover:to-blue-600 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 hover:shadow-lg hover:shadow-blue-500/50 backdrop-blur-sm border border-blue-500/20"
              aria-label="Twitter"
            >
              <Twitter class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors" />
            </a>
          </div>
        </div>

        <!-- Copyright -->
        <div class="text-center md:text-right">
          <p class="text-sm text-gray-400">
            &copy; {{ currentYear }}
            <span
              class="font-semibold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent"
              >{{ footerData.nama_sekolah }}</span
            >
          </p>
          <p class="text-xs text-gray-500 mt-1">
            Powered by Tim Pengabdian kepada Masyarakat | Universitas Pamulang
          </p>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
/**
 * @imports Lucide icons untuk contact info dan social media
 */
import {
  ChevronRight,
  Clock,
  Facebook,
  Instagram,
  Mail,
  MapPin,
  Phone,
  Twitter,
  Youtube
} from 'lucide-vue-next'
import { computed } from 'vue'

/**
 * Static footer data - sekolah info dan social media links
 * @todo Fetch dari API/CMS untuk data dinamis
 */
const footerData = {
  logo_url: '/images/logo/logo_yasmin.png',
  nama_sekolah: 'SMA Mutiara Insan Nusantara',
  tagline: 'Membentuk Generasi Berakhlak, Berkarakter, Berprestasi, dan Berbudaya.',
  deskripsi:
    'Sekolah Menengah Atas yang berkomitmen untuk menghasilkan generasi cerdas, berkarakter, dan berprestasi dengan landasan nilai-nilai keislaman.',
  alamat:
    'JL RAJAWALI PULO NO 2 KAMPUNG NAGREK, Rajeg Mulya, Kec. Rajeg, Kab. Tangerang Prov. Banten',
  telepon: '(021) 59391134',
  telepon_raw: '02159391134',
  email: 'nusantaramutiarainsan@gmail.com',
  jam_operasional: 'Senin - Jumat: 08.00 - 15.00',
  facebook: 'https://www.facebook.com/yasminschoolofficial/',
  instagram: 'https://www.instagram.com/yasminschool_official/'
}

/**
 * Computed current year untuk copyright text
 */
const currentYear = computed(() => new Date().getFullYear())

/**
 * Quick access navigation links
 */
const quickLinks = [
  { id: 1, label: 'Beranda', href: '/' },
  { id: 2, label: 'Profil Sekolah', href: '/profil' },
  { id: 3, label: 'Direktori Guru', href: '/guru' },
  { id: 4, label: 'Dokumen PPDB', href: '/spmb' },
  { id: 5, label: 'Berita Sekolah', href: '/news' }
]

/**
 * Information section links (scroll to section dengan hash)
 */
const informationLinks = [
  { id: 1, label: 'Prestasi Siswa', href: '/prestasi' },
  { id: 2, label: 'Galeri Foto', href: '/#galeri' },
  { id: 3, label: 'Ekstrakurikuler', href: '/#ekskul' },
  { id: 4, label: 'Testimoni', href: '/#testimoni' },
  { id: 5, label: 'Fasilitas', href: '/#fasilitas' }
]
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}

@keyframes pulse {
  0%,
  100% {
    opacity: 0.3;
    transform: scale(1);
  }
  50% {
    opacity: 0.5;
    transform: scale(1.1);
  }
}

.animate-pulse {
  animation: pulse 4s ease-in-out infinite;
}
</style>
