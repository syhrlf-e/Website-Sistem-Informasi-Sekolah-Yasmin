/**
 * @component PendaftarListStats
 * @description Stats cards untuk halaman pendaftar (pending, approved, rejected)
 */

<template>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-2xl p-4">
      <div class="flex items-center gap-3">
        <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center">
          <Clock class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
        </div>
        <div>
          <p class="text-sm text-gray-600 dark:text-gray-400 font-poppins">Menunggu</p>
          <p class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">{{ stats.pending || 0 }}</p>
        </div>
      </div>
    </div>

    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-2xl p-4">
      <div class="flex items-center gap-3">
        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
          <CheckCircle class="w-6 h-6 text-green-600 dark:text-green-400" />
        </div>
        <div>
          <p class="text-sm text-gray-600 dark:text-gray-400 font-poppins">Diterima</p>
          <p class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">{{ stats.approved || 0 }}</p>
        </div>
      </div>
    </div>

    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl p-4">
      <div class="flex items-center gap-3">
        <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center">
          <XCircle class="w-6 h-6 text-red-600 dark:text-red-400" />
        </div>
        <div>
          <p class="text-sm text-gray-600 dark:text-gray-400 font-poppins">Ditolak</p>
          <p class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">{{ stats.rejected || 0 }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { CheckCircle, Clock, XCircle } from 'lucide-vue-next'

defineProps({
  stats: {
    type: Object,
    required: true
  }
})
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
