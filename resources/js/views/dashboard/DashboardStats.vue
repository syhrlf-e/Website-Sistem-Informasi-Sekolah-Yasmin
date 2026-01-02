/**
 * @component DashboardStats
 * @description Unified stats card untuk dashboard (berita, ekskul, galeri, prestasi)
 */

<template>
  <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <!-- Header -->
    <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center">
          <LayoutGrid class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
        </div>
        <div>
          <h2 class="text-base font-bold text-gray-900 dark:text-white font-poppins">Konten Website</h2>
          <p class="text-xs text-gray-500 dark:text-gray-400 font-poppins">Publikasi & Kegiatan</p>
        </div>
      </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4">
      <!-- Berita -->
      <div class="py-8 px-6 flex items-start justify-between relative">
        <div>
          <h3 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">{{ stats.berita }}</h3>
          <p class="text-sm text-gray-500 dark:text-gray-400 font-poppins">Total Berita</p>
        </div>
        <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
          <Newspaper class="w-5 h-5 text-gray-500 dark:text-gray-400" />
        </div>
        <!-- Divider -->
        <div class="absolute right-0 top-1/2 -translate-y-1/2 h-10 w-px bg-gray-200 dark:bg-gray-700 hidden lg:block" />
      </div>

      <!-- Ekskul -->
      <div class="py-8 px-6 flex items-start justify-between relative border-l border-gray-200 dark:border-gray-700 lg:border-l-0">
        <div>
          <h3 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">{{ stats.ekskul }}</h3>
          <p class="text-sm text-gray-500 dark:text-gray-400 font-poppins">Ekstrakurikuler</p>
        </div>
        <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
          <Target class="w-5 h-5 text-gray-500 dark:text-gray-400" />
        </div>
        <!-- Divider -->
        <div class="absolute right-0 top-1/2 -translate-y-1/2 h-10 w-px bg-gray-200 dark:bg-gray-700 hidden lg:block" />
      </div>

      <!-- Galeri -->
      <div class="py-8 px-6 flex items-start justify-between relative border-t border-gray-200 dark:border-gray-700 lg:border-t-0">
        <div>
          <h3 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">{{ stats.galeri }}</h3>
          <p class="text-sm text-gray-500 dark:text-gray-400 font-poppins">Foto Galeri</p>
        </div>
        <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
          <Image class="w-5 h-5 text-gray-500 dark:text-gray-400" />
        </div>
        <!-- Divider -->
        <div class="absolute right-0 top-1/2 -translate-y-1/2 h-10 w-px bg-gray-200 dark:bg-gray-700 hidden lg:block" />
      </div>

      <!-- Prestasi -->
      <div class="py-8 px-6 flex items-start justify-between border-t border-l border-gray-200 dark:border-gray-700 lg:border-t-0 lg:border-l-0">
        <div>
          <h3 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">{{ stats.prestasi }}</h3>
          <p class="text-sm text-gray-500 dark:text-gray-400 font-poppins">Total Prestasi</p>
        </div>
        <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
          <Trophy class="w-5 h-5 text-gray-500 dark:text-gray-400" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Image, LayoutGrid, Newspaper, Target, Trophy } from 'lucide-vue-next'

defineProps({
  stats: { type: Object, required: true }
})
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
