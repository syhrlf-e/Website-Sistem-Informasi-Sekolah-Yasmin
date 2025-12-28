/**
 * @component BeritaListHeader
 * @description Header untuk halaman daftar berita dengan tombol trash dan tambah
 */

<template>
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">Berita</h1>
      <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Kelola semua berita dan artikel</p>
    </div>

    <div class="flex items-center gap-3">
      <router-link
        to="/yasmin-panel/berita/trash"
        class="flex items-center gap-2 px-5 py-3 bg-gray-400 hover:bg-gray-500 text-white rounded-xl font-medium transition-colors duration-200 shadow-lg hover:shadow-xl font-poppins"
      >
        <Trash2 class="w-5 h-5" />
      </router-link>

      <router-link
        to="/yasmin-panel/berita/create"
        class="flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl font-medium transition-colors duration-200 shadow-lg hover:shadow-xl font-poppins"
      >
        <Plus class="w-5 h-5" />
        Tambah Berita
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { Plus, Trash2 } from 'lucide-vue-next'
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
