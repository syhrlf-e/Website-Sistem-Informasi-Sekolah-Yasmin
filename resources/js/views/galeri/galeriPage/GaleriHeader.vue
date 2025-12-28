/**
 * @component GaleriHeader
 * @description Header untuk halaman galeri grid
 */

<template>
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">
        Galeri Homepage
      </h1>
      <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
        Kelola 9 slot galeri untuk tampilan homepage
      </p>
    </div>
    <button
      @click="$emit('refresh')"
      :disabled="loading"
      class="flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 disabled:from-gray-400 disabled:to-gray-400 text-white rounded-xl font-medium transition-colors duration-200 shadow-lg hover:shadow-xl font-poppins disabled:opacity-50"
    >
      <RefreshCw class="w-5 h-5" :class="{ 'animate-spin': loading }" />
      Refresh
    </button>
  </div>
</template>

<script setup>
import { RefreshCw } from 'lucide-vue-next'

defineProps({
  loading: Boolean
})

defineEmits(['refresh'])
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
