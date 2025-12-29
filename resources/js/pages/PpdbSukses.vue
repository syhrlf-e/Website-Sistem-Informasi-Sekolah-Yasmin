<!--
  @component PpdbSukses
  @description Success page after PPDB registration with credentials
  @route /ppdb/sukses
-->

<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-12 px-4">
    <div class="max-w-xl mx-auto">
      <!-- Success Card -->
      <div class="success-card bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-xl border border-gray-200 dark:border-gray-700 text-center">
        <!-- Icon -->
        <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full flex items-center justify-center shadow-lg">
          <CheckCircle class="w-14 h-14 text-white" />
        </div>

        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-2 font-poppins">
          Pendaftaran Berhasil!
        </h1>
        <p class="text-gray-600 dark:text-gray-300 mb-8">
          Selamat, <strong>{{ nama }}</strong>! Data kamu telah terkirim.
        </p>

        <!-- Credentials Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border-2 border-blue-500 mb-6">
          <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Nomor Registrasi</p>
          <p class="text-2xl font-bold font-poppins text-gray-900 dark:text-white mb-4">{{ registrationNumber }}</p>
          
          <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Kode Akses</p>
          <p class="text-3xl font-bold font-poppins tracking-widest text-gray-900 dark:text-white">{{ token }}</p>
        </div>

        <!-- Important Notice -->
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-4 mb-6 text-left">
          <div class="flex gap-3">
            <AlertTriangle class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" />
            <div class="text-sm text-yellow-800 dark:text-yellow-200">
              <p class="font-semibold mb-1">Simpan data ini baik-baik!</p>
              <p>Nomor Registrasi dan Token dibutuhkan untuk mengecek status pendaftaran Anda.</p>
            </div>
          </div>
        </div>

        <!-- Action Buttons Row -->
        <div class="grid grid-cols-2 gap-2 mb-4">
          <button
            @click="copyCredentials"
            class="py-2.5 px-3 border-2 border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl text-xs md:text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors flex items-center justify-center gap-1.5"
          >
            <Copy class="w-4 h-4" />
            {{ copied ? 'Tersalin!' : 'Salin' }}
          </button>
          <button
            @click="takeScreenshot"
            class="py-2.5 px-3 border-2 border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl text-xs md:text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors flex items-center justify-center gap-1.5"
          >
            <Camera class="w-4 h-4" />
            Screenshot
          </button>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-3">
          <a
            :href="ppdbUrl('/status')"
            class="block w-full py-3 px-6 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl font-medium transition-colors"
          >
            Cek Status Pendaftaran
          </a>
          
          <a
            href="/"
            class="block w-full py-3 px-6 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 font-medium transition-colors"
          >
            Kembali ke Beranda
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useHead } from '@vueuse/head'
import { AlertTriangle, Camera, CheckCircle, Copy } from 'lucide-vue-next'
import { computed, ref } from 'vue'

const copied = ref(false)
const cardRef = ref(null)

// Helper to generate PPDB URLs based on current domain
const ppdbUrl = (path) => {
  const isSubdomain = window.location.hostname.startsWith('ppdb.')
  if (isSubdomain) return path
  return '/ppdb' + (path === '/' ? '/landing' : path)
}

// Get query params from URL
const urlParams = new URLSearchParams(window.location.search)
const registrationNumber = computed(() => urlParams.get('reg') || 'PPDB-2024-0001')
const token = computed(() => urlParams.get('token') || 'ABC123DEF456GH78')
const nama = computed(() => urlParams.get('nama') || 'Pendaftar')

useHead({
  title: 'Pendaftaran Berhasil - PPDB SMA Mutiara Insan Nusantara',
  meta: [
    { name: 'robots', content: 'noindex' }
  ]
})

const copyCredentials = async () => {
  const text = `Nomor Registrasi: ${registrationNumber.value}\nKode Akses: ${token.value}`
  try {
    await navigator.clipboard.writeText(text)
    copied.value = true
    setTimeout(() => { copied.value = false }, 2000)
  } catch (error) {
    console.error('Failed to copy:', error)
  }
}

const takeScreenshot = async () => {
  try {
    // Use html2canvas library (needs to be loaded)
    const html2canvas = (await import('html2canvas')).default
    const card = document.querySelector('.success-card')
    if (!card) return
    
    const canvas = await html2canvas(card, {
      backgroundColor: '#ffffff',
      scale: 2
    })
    
    // Download image
    const link = document.createElement('a')
    link.download = `PPDB-${registrationNumber.value}.png`
    link.href = canvas.toDataURL('image/png')
    link.click()
  } catch (error) {
    console.error('Failed to take screenshot:', error)
    alert('Gagal mengambil screenshot. Silakan gunakan fitur screenshot bawaan perangkat.')
  }
}
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
