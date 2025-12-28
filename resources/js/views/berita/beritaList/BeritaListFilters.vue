/**
 * @component BeritaListFilters
 * @description Filter bar untuk pencarian, kategori, dan status berita
 */

<template>
  <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
    <div class="flex flex-col md:flex-row gap-4">
      <div class="flex-1 relative">
        <Search class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
        <input
          :value="searchQuery"
          @input="$emit('update:searchQuery', $event.target.value)"
          type="text"
          placeholder="Cari berita..."
          class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white font-poppins"
        />
      </div>

      <select
        :value="filterCategory"
        @change="$emit('update:filterCategory', $event.target.value)"
        class="px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white font-poppins"
      >
        <option value="">Semua Kategori</option>
        <option value="event">Event</option>
        <option value="achievement">Prestasi</option>
        <option value="announcement">Pengumuman</option>
        <option value="other">Lainnya</option>
      </select>

      <select
        :value="filterStatus"
        @change="$emit('update:filterStatus', $event.target.value)"
        class="px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white font-poppins"
      >
        <option value="">Semua Status</option>
        <option value="published">Terpublikasi</option>
        <option value="draft">Draf</option>
      </select>
    </div>
  </div>
</template>

<script setup>
import { Search } from 'lucide-vue-next'

defineProps({
  searchQuery: {
    type: String,
    default: ''
  },
  filterCategory: {
    type: String,
    default: ''
  },
  filterStatus: {
    type: String,
    default: ''
  }
})

defineEmits(['update:searchQuery', 'update:filterCategory', 'update:filterStatus'])
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
