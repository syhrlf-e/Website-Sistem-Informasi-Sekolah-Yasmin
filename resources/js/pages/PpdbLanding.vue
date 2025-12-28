<!--
  @component PpdbLanding
  @description Landing page untuk pendaftaran PPDB dengan Hero, Info Cards, dan CTA
  @route /ppdb/landing (nanti subdomain ppdb.smayasmin.com)
-->

<template>
  <div class="min-h-screen bg-white dark:bg-gray-900">
    <!-- Navbar -->
    <Navbar />

    <!-- Hero Section -->
    <section class="relative h-[60vh] min-h-[500px] overflow-hidden pt-24">
      <!-- Background Image -->
      <div class="absolute inset-0 bg-white dark:bg-gray-900">
        <img
          src="/storage/images/school-building.jpg"
          alt="Gedung Sekolah"
          class="w-full h-full object-cover"
          @error="onImageError"
        />
      </div>

      <!-- Hero Content -->
      <div class="relative z-10 h-full flex items-center">
        <div class="container-content">
          <div class="max-w-2xl">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white mb-4 font-poppins leading-tight">
              Penerimaan Peserta Didik Baru {{ academicYear }}
            </h1>
            <p class="text-lg md:text-xl text-gray-600 dark:text-gray-300 mb-8 font-poppins">
              Selamat Datang di PPDB SMA Mutiara Insan Nusantara.<br />
              Segera Daftarkan Diri Anda.
            </p>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-4">
              <a
                href="/ppdb/daftar"
                class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-lg font-semibold text-lg transition-all shadow-lg hover:shadow-xl font-poppins"
              >
                Daftar Sekarang
                <ArrowRight class="w-5 h-5" />
              </a>
              <a
                href="/ppdb/status"
                class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-gray-100 dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-600 text-gray-700 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg font-semibold text-lg transition-all font-poppins"
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
    <section class="py-16 bg-gray-50 dark:bg-gray-800">
      <div class="container-content">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-10 font-poppins">
          Informasi PPDB
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Jadwal Pendaftaran -->
          <div class="bg-white dark:bg-gray-700 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-600">
            <div class="flex items-center gap-3 mb-5">
              <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center">
                <Calendar class="w-6 h-6 text-blue-600 dark:text-blue-400" />
              </div>
              <h3 class="text-lg font-bold text-blue-700 dark:text-blue-400 font-poppins">Jadwal Pendaftaran</h3>
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
              <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center">
                <FileText class="w-6 h-6 text-blue-600 dark:text-blue-400" />
              </div>
              <h3 class="text-lg font-bold text-blue-700 dark:text-blue-400 font-poppins">Persyaratan</h3>
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
              <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center">
                <Wallet class="w-6 h-6 text-blue-600 dark:text-blue-400" />
              </div>
              <h3 class="text-lg font-bold text-blue-700 dark:text-blue-400 font-poppins">Biaya Pendaftaran</h3>
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

    <!-- Download Center Section -->
    <section class="py-16 bg-white dark:bg-gray-900">
      <div class="container-content">
        <div class="flex items-center gap-3 mb-8">
          <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
            <Download class="w-6 h-6 text-blue-600 dark:text-blue-400" />
          </div>
          <div>
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white font-poppins">
              Dokumen & Unduhan
            </h2>
            <p class="text-gray-500 dark:text-gray-400 text-sm">Download berkas kelengkapan pendaftaran</p>
          </div>
        </div>

        <!-- Document List -->
        <div v-if="documents && documents.length > 0" class="space-y-3">
          <div
            v-for="doc in documents"
            :key="doc.id"
            class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600 transition-colors"
          >
            <div class="flex items-center gap-4">
              <div class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                <FileText class="w-5 h-5 text-red-600 dark:text-red-400" />
              </div>
              <div>
                <h4 class="font-medium text-gray-900 dark:text-white">{{ doc.title }}</h4>
                <p v-if="doc.description" class="text-sm text-gray-500 dark:text-gray-400">{{ doc.description }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500">{{ formatFileSize(doc.file_size) }}</p>
              </div>
            </div>
            <a
              :href="`/storage/documents/${doc.file_name}`"
              download
              class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-lg text-sm font-medium transition-colors"
            >
              <Download class="w-4 h-4" />
              Download
            </a>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12 bg-gray-50 dark:bg-gray-800 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
          <FileText class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" />
          <p class="text-gray-500 dark:text-gray-400">Belum ada dokumen yang tersedia</p>
          <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Dokumen akan segera ditambahkan</p>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <Footer />
  </div>
</template>

<script setup>
import { useHead } from '@vueuse/head'
import { ArrowRight, Calendar, Download, FileText, Search, Wallet } from 'lucide-vue-next'
import Navbar from '../components/ui/Navbar.vue'
import Footer from '../components/ui/Footer.vue'

// Props from backend
const props = defineProps({
  documents: {
    type: Array,
    default: () => []
  },
  academicYear: {
    type: String,
    default: '2025/2026'
  }
})

useHead({
  title: `PPDB ${props.academicYear} - SMA Mutiara Insan Nusantara`,
  meta: [
    { name: 'description', content: `Pendaftaran Peserta Didik Baru SMA Mutiara Insan Nusantara Tahun Ajaran ${props.academicYear}. Daftar sekarang!` }
  ]
})

const onImageError = (e) => {
  e.target.style.display = 'none'
}

const formatFileSize = (bytes) => {
  if (!bytes) return ''
  const kb = bytes / 1024
  if (kb < 1024) return `${kb.toFixed(1)} KB`
  return `${(kb / 1024).toFixed(1)} MB`
}
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
