/**
 * @component EkskulListFilters
 * @description Filter bar untuk pencarian, badge, dan status ekskul
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
          placeholder="Cari ekstrakurikuler..."
          class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white font-poppins"
        />
      </div>

      <select
        :value="badgeFilter"
        @change="$emit('update:badgeFilter', $event.target.value)"
        class="px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white font-poppins"
      >
        <option value="">Semua Kategori</option>
        <option value="Akademik">Akademik</option>
        <option value="Olahraga">Olahraga</option>
        <option value="Seni">Seni</option>
        <option value="Kepemimpinan">Kepemimpinan</option>
      </select>

      <select
        :value="statusFilter"
        @change="$emit('update:statusFilter', $event.target.value)"
        class="px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white font-poppins"
      >
        <option value="">Semua Status</option>
        <option value="1">Aktif</option>
        <option value="0">Nonaktif</option>
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
  badgeFilter: {
    type: String,
    default: ''
  },
  statusFilter: {
    type: String,
    default: ''
  }
})

defineEmits(['update:searchQuery', 'update:badgeFilter', 'update:statusFilter'])
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
