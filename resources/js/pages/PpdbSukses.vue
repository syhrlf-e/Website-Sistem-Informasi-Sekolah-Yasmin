<!--
  @component PpdbSukses
  @description Success page after PPDB registration with credentials
  @route /ppdb/sukses
-->

<template>
  <div class="min-h-screen bg-gradient-to-br from-green-50 via-emerald-50 to-teal-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-12 px-4">
    <div class="max-w-xl mx-auto">
      <!-- Success Card -->
      <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-xl border border-gray-200 dark:border-gray-700 text-center">
        <!-- Icon -->
        <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full flex items-center justify-center shadow-lg">
          <CheckCircle class="w-14 h-14 text-white" />
        </div>

        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-2 font-poppins">
          Pendaftaran Berhasil! 🎉
        </h1>
        <p class="text-gray-600 dark:text-gray-300 mb-8">
          Selamat, <strong>{{ nama }}</strong>! Data kamu telah tersimpan.
        </p>

        <!-- Credentials Card -->
        <div class="bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl p-6 text-white mb-6">
          <p class="text-sm opacity-80 mb-1">Nomor Registrasi</p>
          <p class="text-2xl font-bold font-mono mb-4">{{ registrationNumber }}</p>
          
          <p class="text-sm opacity-80 mb-1">Token Akses</p>
          <p class="text-3xl font-bold font-mono tracking-widest">{{ token }}</p>
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

        <!-- Copy Button -->
        <button
          @click="copyCredentials"
          class="w-full py-3 px-6 border-2 border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors flex items-center justify-center gap-2 mb-4"
        >
          <Copy class="w-5 h-5" />
          {{ copied ? 'Tersalin!' : 'Salin Nomor & Token' }}
        </button>

        <!-- Action Buttons -->
        <div class="space-y-3">
          <router-link
            to="/ppdb/status"
            class="block w-full py-3 px-6 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl font-medium transition-colors"
          >
            Cek Status Pendaftaran
          </router-link>
          
          <router-link
            to="/"
            class="block w-full py-3 px-6 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 font-medium transition-colors"
          >
            Kembali ke Beranda
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useHead } from '@vueuse/head'
import { AlertTriangle, CheckCircle, Copy } from 'lucide-vue-next'
import { computed, ref } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const copied = ref(false)

const registrationNumber = computed(() => route.query.reg || 'PPDB-2024-0001')
const token = computed(() => route.query.token || 'ABC123')
const nama = computed(() => route.query.nama || 'Pendaftar')

useHead({
  title: 'Pendaftaran Berhasil - PPDB SMA Mutiara Insan Nusantara',
  meta: [
    { name: 'robots', content: 'noindex' }
  ]
})

const copyCredentials = async () => {
  const text = `Nomor Registrasi: ${registrationNumber.value}\nToken: ${token.value}`
  try {
    await navigator.clipboard.writeText(text)
    copied.value = true
    setTimeout(() => { copied.value = false }, 2000)
  } catch (error) {
    console.error('Failed to copy:', error)
  }
}
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
