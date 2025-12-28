/**
 * @component DashboardStats
 * @description Stats cards untuk dashboard (berita, ekskul, galeri, prestasi)
 */

<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Berita -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-300">
      <div class="flex items-center justify-between mb-4">
        <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
          <Newspaper class="w-6 h-6 text-blue-600 dark:text-blue-400" />
        </div>
        <span class="text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 px-3 py-1 rounded-full">Terpublikasi</span>
      </div>
      <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1 font-poppins">{{ stats.berita }}</h3>
      <p class="text-sm text-gray-600 dark:text-gray-400 font-poppins">Total Berita</p>
    </div>

    <!-- Ekskul -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-300">
      <div class="flex items-center justify-between mb-4">
        <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center">
          <Target class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
        </div>
        <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 px-3 py-1 rounded-full">Aktif</span>
      </div>
      <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1 font-poppins">{{ stats.ekskul }}</h3>
      <p class="text-sm text-gray-600 dark:text-gray-400 font-poppins">Ekstrakurikuler</p>
    </div>

    <!-- Galeri -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-300">
      <div class="flex items-center justify-between mb-4">
        <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center">
          <Image class="w-6 h-6 text-purple-600 dark:text-purple-400" />
        </div>
        <span class="text-xs font-medium text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-900/20 px-3 py-1 rounded-full">Foto</span>
      </div>
      <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1 font-poppins">{{ stats.galeri }}</h3>
      <p class="text-sm text-gray-600 dark:text-gray-400 font-poppins">Foto Galeri</p>
    </div>

    <!-- Prestasi -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-300">
      <div class="flex items-center justify-between mb-4">
        <div class="w-12 h-12 rounded-xl bg-yellow-50 dark:bg-yellow-900/20 flex items-center justify-center">
          <Trophy class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
        </div>
        <span class="text-xs font-medium text-yellow-600 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-900/20 px-3 py-1 rounded-full">Pencapaian</span>
      </div>
      <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1 font-poppins">{{ stats.prestasi }}</h3>
      <p class="text-sm text-gray-600 dark:text-gray-400 font-poppins">Total Prestasi</p>
    </div>
  </div>
</template>

<script setup>
import { Image, Newspaper, Target, Trophy } from 'lucide-vue-next'

defineProps({
  stats: { type: Object, required: true }
})
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
