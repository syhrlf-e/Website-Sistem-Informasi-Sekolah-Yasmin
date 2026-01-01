<!--
  @component PpdbSukses
  @description Success page after PPDB registration with credentials
  @route /ppdb/sukses
-->

<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 pt-6 pb-12 px-4">
    <div class="max-w-xl mx-auto">
      <!-- Success Card -->
      <div class="success-card bg-white dark:bg-gray-800 rounded-3xl py-8 px-4 shadow-xl border border-gray-200 dark:border-gray-700 text-center">

        <CheckCircle class="w-8 h-8 text-green-500 mx-auto mb-2" />
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-4 font-poppins">
          Pendaftaran Berhasil!
        </h1>
        <p class="text-gray-600 dark:text-gray-300 mb-8">
          Selamat, <strong>{{ nama }}</strong>! Data kamu telah terkirim.
        </p>

        <!-- Credentials Card -->
        <div class="bg-gray-50 dark:bg-gray-900 rounded-2xl p-5 border border-gray-200 dark:border-gray-700 mb-6 text-left">
          <!-- Nomor Registrasi -->
          <div class="mb-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Nomor Registrasi</p>
            <div class="flex items-center justify-between">
              <p class="text-base font-normal font-poppins text-gray-900 dark:text-white">{{ registrationNumber }}</p>
              <button @click="copyText(registrationNumber)" class="p-1.5 text-gray-400 hover:text-blue-500 transition-colors">
                <Copy class="w-4 h-4" />
              </button>
            </div>
          </div>
          
          <!-- Kode Akses -->
          <div class="mb-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Kode Akses</p>
            <div class="flex items-center justify-between">
              <p class="text-base font-normal font-poppins tracking-wider text-gray-900 dark:text-white">{{ token }}</p>
              <button @click="copyText(token)" class="p-1.5 text-gray-400 hover:text-blue-500 transition-colors">
                <Copy class="w-4 h-4" />
              </button>
            </div>
          </div>

          <!-- Important Notice (inside credentials card) -->
          <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-3">
            <div class="text-xs text-yellow-800 dark:text-yellow-200">
              <p class="font-semibold">Simpan data ini baik-baik!</p>
              <p>Nomor Registrasi dan Token dibutuhkan untuk mengecek status pendaftaran.</p>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-3">
          <a
            href="/"
            class="p-3 border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-full transition-colors"
          >
            <ChevronLeft class="w-5 h-5" />
          </a>
          <a
            :href="ppdbUrl('/status')"
            class="flex-1 py-3 px-6 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-full font-medium transition-colors text-center"
          >
            Cek Status Pendaftaran
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useHead } from '@vueuse/head'
import { AlertTriangle, CheckCircle, ChevronLeft, Copy } from 'lucide-vue-next'
import { computed, ref } from 'vue'

const copiedField = ref('')

// Helper to generate PPDB URLs based on current domain
const ppdbUrl = (path) => {
  const isSubdomain = window.location.hostname.startsWith('ppdb.')
  if (isSubdomain) return path
  return '/ppdb' + (path === '/' ? '/landing' : path)
}

// Get query params from URL
const urlParams = new URLSearchParams(window.location.search)
const registrationNumber = computed(() => urlParams.get('reg') || 'PPDB-2024-0001')
const token = computed(() => urlParams.get('token') || 'XX-XX-XX')
const nama = computed(() => urlParams.get('nama') || 'Pendaftar')

useHead({
  title: 'Pendaftaran Berhasil - PPDB SMA Mutiara Insan Nusantara',
  meta: [
    { name: 'robots', content: 'noindex' }
  ]
})

const copyText = async (text) => {
  try {
    await navigator.clipboard.writeText(text)
    copiedField.value = text
    setTimeout(() => { copiedField.value = '' }, 2000)
  } catch (error) {
    console.error('Failed to copy:', error)
  }
}
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
