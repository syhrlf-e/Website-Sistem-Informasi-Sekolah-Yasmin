/**
 * @component BeritaFormHeader
 * @description Header untuk form berita dengan toggle preview dan back button
 */

<template>
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">
        {{ isEdit ? 'Edit Berita' : 'Tambah Berita Baru' }}
      </h1>
      <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
        {{ isEdit ? 'Update informasi berita' : 'Buat berita atau artikel baru' }}
      </p>
    </div>
    <div class="flex items-center gap-3">
      <button
        @click="$emit('toggle-preview')"
        type="button"
        class="hidden lg:flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl transition-colors duration-200 font-poppins"
      >
        <Eye v-if="!showPreview" class="w-4 h-4" />
        <EyeOff v-else class="w-4 h-4" />
        {{ showPreview ? 'Sembunyikan' : 'Tampilkan' }} Preview
      </button>

      <router-link
        to="/yasmin-panel/berita"
        class="flex items-center gap-2 px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors duration-200 font-poppins"
      >
        <ArrowLeft class="w-4 h-4" />
        Kembali
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ArrowLeft, Eye, EyeOff } from 'lucide-vue-next'

defineProps({
  isEdit: {
    type: Boolean,
    default: false
  },
  showPreview: {
    type: Boolean,
    default: true
  }
})

defineEmits(['toggle-preview'])
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
