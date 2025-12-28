/**
 * @component BeritaTrashHeader
 * @description Header untuk halaman tempat sampah berita
 */

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">
          Tempat Sampah Berita
        </h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
          Berita yang dihapus bisa dipulihkan atau dihapus permanen
        </p>
      </div>
      <div class="flex items-center gap-3">
        <router-link
          to="/yasmin-panel/berita"
          class="flex items-center gap-2 px-5 py-3 bg-gray-400 hover:bg-gray-500 text-white rounded-xl font-medium transition-colors duration-200 shadow-lg hover:shadow-xl font-poppins"
        >
          <ArrowLeft class="w-5 h-5" />
          Kembali
        </router-link>
      </div>
    </div>

    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
      <p class="text-sm text-amber-800 dark:text-amber-200 font-poppins">
        Berita di tempat sampah akan otomatis terhapus permanen setelah 30 hari
      </p>
    </div>
  </div>
</template>

<script setup>
import { ArrowLeft } from 'lucide-vue-next'
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
