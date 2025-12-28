<!--
  @component PpdbLanding
  @description Landing page untuk pendaftaran PPDB dengan Hero, Info Cards, dan CTA
  @route /ppdb/landing (nanti subdomain ppdb.smayasmin.com)
-->

<template>
  <div class="min-h-screen bg-white dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative h-[60vh] min-h-[500px] overflow-hidden">
      <!-- Background Image -->
      <div class="absolute inset-0">
        <img
          src="/storage/images/school-building.jpg"
          alt="Gedung Sekolah"
          class="w-full h-full object-cover"
          @error="onImageError"
        />
        <div class="absolute inset-0 bg-gradient-to-r from-teal-900/90 to-teal-800/70"></div>
      </div>

      <!-- Hero Content -->
      <div class="relative z-10 h-full flex items-center">
        <div class="container mx-auto px-6 md:px-12">
          <div class="max-w-2xl">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4 font-poppins leading-tight">
              Penerimaan Peserta Didik Baru {{ academicYear }}
            </h1>
            <p class="text-lg md:text-xl text-white/90 mb-8 font-poppins">
              Selamat Datang di PPDB SMA Mutiara Insan Nusantara.<br />
              Segera Daftarkan Diri Anda.
            </p>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-4">
              <a
                href="/ppdb/daftar"
                class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-semibold text-lg transition-all shadow-lg hover:shadow-xl font-poppins"
              >
                Daftar Sekarang
                <ArrowRight class="w-5 h-5" />
              </a>
              <a
                href="/ppdb/status"
                class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white/10 backdrop-blur-sm border-2 border-white/50 text-white hover:bg-white/20 rounded-lg font-semibold text-lg transition-all font-poppins"
              >
                <Search class="w-5 h-5" />
                Cek Status Pendaftaran
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Info Cards Section -->
    <section class="py-16 px-6 bg-gray-50 dark:bg-gray-800">
      <div class="container mx-auto max-w-6xl">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-10 font-poppins">
          Informasi PPDB
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Jadwal Pendaftaran -->
          <div class="bg-white dark:bg-gray-700 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-600">
            <div class="flex items-center gap-3 mb-5">
              <div class="w-12 h-12 rounded-xl bg-teal-50 dark:bg-teal-900/30 flex items-center justify-center">
                <Calendar class="w-6 h-6 text-teal-600 dark:text-teal-400" />
              </div>
              <h3 class="text-lg font-bold text-teal-700 dark:text-teal-400 font-poppins">Jadwal Pendaftaran</h3>
            </div>
            <ul class="space-y-3 text-sm text-gray-700 dark:text-gray-300">
              <li class="flex items-start gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mt-2 flex-shrink-0"></span>
                <span>Gelombang 1: <strong>1 Maret - 30 April 2025</strong></span>
              </li>
              <li class="flex items-start gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mt-2 flex-shrink-0"></span>
                <span>Gelombang 2: <strong>1 Mei - 30 Juni 2025</strong></span>
              </li>
              <li class="flex items-start gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mt-2 flex-shrink-0"></span>
                <span>Pengumuman Seleksi: <strong>5 Juli 2025</strong></span>
              </li>
              <li class="flex items-start gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mt-2 flex-shrink-0"></span>
                <span>Daftar Ulang: <strong>6 - 12 Juli 2025</strong></span>
              </li>
            </ul>
          </div>

          <!-- Persyaratan -->
          <div class="bg-white dark:bg-gray-700 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-600">
            <div class="flex items-center gap-3 mb-5">
              <div class="w-12 h-12 rounded-xl bg-teal-50 dark:bg-teal-900/30 flex items-center justify-center">
                <FileText class="w-6 h-6 text-teal-600 dark:text-teal-400" />
              </div>
              <h3 class="text-lg font-bold text-teal-700 dark:text-teal-400 font-poppins">Persyaratan</h3>
            </div>
            <ul class="space-y-3 text-sm text-gray-700 dark:text-gray-300">
              <li class="flex items-start gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mt-2 flex-shrink-0"></span>
                <span>Scan Rapor Semester 1-5 (SMP/MTs)</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mt-2 flex-shrink-0"></span>
                <span>Scan Akta Kelahiran & Kartu Keluarga</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mt-2 flex-shrink-0"></span>
                <span>Pas Foto Terbaru (4x6)</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mt-2 flex-shrink-0"></span>
                <span>Surat Keterangan Lulus (SKL)</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mt-2 flex-shrink-0"></span>
                <span>Sertifikat Prestasi (Jika Ada)</span>
              </li>
            </ul>
          </div>

          <!-- Biaya Pendaftaran -->
          <div class="bg-white dark:bg-gray-700 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-600">
            <div class="flex items-center gap-3 mb-5">
              <div class="w-12 h-12 rounded-xl bg-teal-50 dark:bg-teal-900/30 flex items-center justify-center">
                <Wallet class="w-6 h-6 text-teal-600 dark:text-teal-400" />
              </div>
              <h3 class="text-lg font-bold text-teal-700 dark:text-teal-400 font-poppins">Biaya Pendaftaran</h3>
            </div>
            <div class="space-y-3 text-sm text-gray-700 dark:text-gray-300">
              <div class="flex justify-between">
                <span>Biaya Formulir:</span>
                <span class="font-bold">Rp 250.000</span>
              </div>
              <div class="flex justify-between">
                <span>Uang Pangkal (Gel. 1):</span>
                <span class="font-bold">Rp 8.500.000</span>
              </div>
              <div class="flex justify-between">
                <span>Uang Pangkal (Gel. 2):</span>
                <span class="font-bold">Rp 10.000.000</span>
              </div>
              <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-600">
                <span>SPP Bulanan:</span>
                <span class="font-bold">Rp 850.000</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8 px-6">
      <div class="container mx-auto max-w-6xl">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
          <div>
            <h4 class="font-bold text-lg font-poppins">SMA Mutiara Insan Nusantara</h4>
            <p class="text-gray-400 text-sm">Sekial : (014) 223-1454</p>
            <p class="text-gray-400 text-sm">Sekial : (025) 322-2808</p>
          </div>
          <div class="flex items-center gap-4">
            <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-gray-700 transition-colors">
              <Facebook class="w-5 h-5" />
            </a>
            <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-gray-700 transition-colors">
              <Instagram class="w-5 h-5" />
            </a>
            <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-gray-700 transition-colors">
              <Youtube class="w-5 h-5" />
            </a>
          </div>
        </div>
        <div class="mt-6 pt-6 border-t border-gray-800 text-center text-gray-500 text-sm">
          © {{ new Date().getFullYear() }} SMA Mutiara Insan Nusantara
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { useHead } from '@vueuse/head'
import { ArrowRight, Calendar, Facebook, FileText, Instagram, Search, Wallet, Youtube } from 'lucide-vue-next'
import { computed } from 'vue'

// Calculate academic year
const currentYear = new Date().getFullYear()
const academicYear = computed(() => {
  const month = new Date().getMonth()
  // If July or later, use current/next year
  return month >= 6 ? `${currentYear}/${currentYear + 1}` : `${currentYear}/${currentYear + 1}`
})

useHead({
  title: 'PPDB - SMA Mutiara Insan Nusantara',
  meta: [
    { name: 'description', content: 'Pendaftaran Peserta Didik Baru SMA Mutiara Insan Nusantara. Daftar sekarang!' }
  ]
})

const onImageError = (e) => {
  // Fallback to gradient background if image not found
  e.target.style.display = 'none'
}
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
