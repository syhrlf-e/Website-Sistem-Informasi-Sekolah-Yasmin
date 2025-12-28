/**
 * @component PendaftarListFilters
 * @description Filter dan search untuk halaman pendaftar ekstrakurikuler
 */

<template>
  <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <!-- Search -->
      <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">Cari</label>
        <div class="relative">
          <input
            :value="search"
            @input="$emit('update:search', $event.target.value)"
            type="text"
            placeholder="Cari nama, email, atau telepon..."
            class="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-900 dark:text-white font-poppins"
          />
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
        </div>
      </div>

      <!-- Status Filter -->
      <div>
        <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">Status</label>
        <select
          :value="status"
          @change="$emit('update:status', $event.target.value)"
          class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-900 dark:text-white font-poppins"
        >
          <option value="">Semua Status</option>
          <option value="pending">Menunggu</option>
          <option value="approved">Diterima</option>
          <option value="rejected">Ditolak</option>
        </select>
      </div>

      <!-- Ekstrakurikuler Filter -->
      <div>
        <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">Ekstrakurikuler</label>
        <select
          :value="ekskulId"
          @change="$emit('update:ekskulId', $event.target.value)"
          class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-900 dark:text-white font-poppins"
        >
          <option value="">Semua Ekskul</option>
          <option v-for="ekskul in ekskulList" :key="ekskul.id" :value="ekskul.id">
            {{ ekskul.name }}
          </option>
        </select>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Search } from 'lucide-vue-next'

defineProps({
  search: String,
  status: String,
  ekskulId: String,
  ekskulList: Array
})

defineEmits(['update:search', 'update:status', 'update:ekskulId'])
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
