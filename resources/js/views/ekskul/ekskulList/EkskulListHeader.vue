/**
 * @component EkskulListHeader
 * @description Header untuk halaman daftar ekstrakurikuler
 */

<template>
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">
        Ekstrakurikuler
      </h1>
      <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Kelola semua ekstrakurikuler</p>
    </div>
    <button
      @click="$emit('add')"
      class="flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl font-medium transition-colors duration-200 shadow-lg hover:shadow-xl font-poppins"
    >
      <Plus class="w-5 h-5" />
      Tambah Ekskul
    </button>
  </div>
</template>

<script setup>
import { Plus } from 'lucide-vue-next'

defineEmits(['add'])
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
