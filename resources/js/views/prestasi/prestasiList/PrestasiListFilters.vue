/**
 * @component PrestasiListFilters
 * @description Filter search, kategori, dan tingkat untuk prestasi
 */

<template>
  <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
    <div class="flex flex-col md:flex-row gap-4">
      <div class="flex-1 relative">
        <Search class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
        <input
          :value="search"
          @input="$emit('update:search', $event.target.value)"
          type="text"
          placeholder="Cari prestasi..."
          class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white font-poppins"
        />
      </div>
      <select
        :value="category"
        @change="$emit('update:category', $event.target.value)"
        class="px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white font-poppins"
      >
        <option value="">Semua Kategori</option>
        <option value="Akademik">Akademik</option>
        <option value="Olahraga">Olahraga</option>
        <option value="Seni">Seni</option>
      </select>
      <select
        :value="tingkat"
        @change="$emit('update:tingkat', $event.target.value)"
        class="px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white font-poppins"
      >
        <option value="">Semua Tingkat</option>
        <option value="Sekolah">Sekolah</option>
        <option value="Kota">Kota</option>
        <option value="Provinsi">Provinsi</option>
        <option value="Nasional">Nasional</option>
        <option value="Internasional">Internasional</option>
      </select>
    </div>
  </div>
</template>

<script setup>
import { Search } from 'lucide-vue-next'

defineProps({
  search: String,
  category: String,
  tingkat: String
})

defineEmits(['update:search', 'update:category', 'update:tingkat'])
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
