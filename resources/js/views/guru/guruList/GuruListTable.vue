/**
 * @component GuruListTable
 * @description Table untuk menampilkan data guru
 */

<template>
  <!-- Loading State -->
  <div v-if="loading" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 py-12">
    <LoadingSpinner size="md" color="blue" />
  </div>

  <!-- Error State -->
  <div v-else-if="error" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
    <p class="text-sm text-red-600 dark:text-red-400 font-poppins">{{ error }}</p>
  </div>

  <!-- Table -->
  <div v-else class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <!-- Empty State -->
    <div v-if="items.length === 0" class="p-12 text-center">
      <p class="text-gray-600 dark:text-gray-400 font-poppins">Belum ada data guru</p>
    </div>

    <!-- Data Table -->
    <div v-else class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
          <tr>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">#</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">Foto</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">Nama</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">Mata Pelajaran</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">Tanggal Dibuat</th>
            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          <tr v-for="(guru, index) in items" :key="guru.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white font-poppins">
              {{ (pagination.current_page - 1) * pagination.per_page + index + 1 }}
            </td>
            <td class="px-6 py-4">
              <img :src="guru.foto || '/images/placeholder-avatar.jpg'" :alt="guru.nama" class="w-12 h-12 rounded-lg object-cover" />
            </td>
            <td class="px-6 py-4">
              <p class="font-semibold text-gray-900 dark:text-white font-poppins">{{ guru.nama }}</p>
            </td>
            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 font-poppins">{{ guru.pelajaran }}</td>
            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 font-poppins">{{ formatDate(guru.created_at) }}</td>
            <td class="px-6 py-4">
              <div class="flex items-center justify-center gap-2">
                <button @click="$emit('edit', guru)" class="p-2 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/30" title="Edit">
                  <Edit2 class="w-4 h-4" />
                </button>
                <button @click="$emit('delete', guru.id)" class="p-2 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30" title="Hapus">
                  <Trash2 class="w-4 h-4" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="items.length > 0" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
      <p class="text-sm text-gray-600 dark:text-gray-400 font-poppins">
        Showing {{ pagination.from || 0 }} to {{ pagination.to || 0 }} of {{ pagination.total || 0 }} entries
      </p>
      <div class="flex items-center gap-2">
        <button @click="$emit('page-change', pagination.current_page - 1)" :disabled="pagination.current_page === 1"
          class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 font-poppins disabled:opacity-50 disabled:cursor-not-allowed">Previous</button>
        <template v-for="page in visiblePages" :key="page">
          <button @click="$emit('page-change', page)"
            :class="['px-4 py-2 rounded-lg font-semibold font-poppins', page === pagination.current_page
              ? 'bg-gradient-to-r from-blue-600 to-purple-600 text-white'
              : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200']">{{ page }}</button>
        </template>
        <button @click="$emit('page-change', pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page"
          class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 font-poppins disabled:opacity-50 disabled:cursor-not-allowed">Next</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import LoadingSpinner from '@/components/ui/shared/LoadingSpinner.vue'
import { Edit2, Trash2 } from 'lucide-vue-next'
import { computed } from 'vue'

const props = defineProps({
  items: Array,
  loading: Boolean,
  error: String,
  pagination: Object
})

defineEmits(['edit', 'delete', 'page-change'])

const visiblePages = computed(() => {
  const pages = []
  const current = props.pagination.current_page
  const last = props.pagination.last_page
  let start = Math.max(1, current - 2)
  let end = Math.min(last, start + 4)
  if (end - start < 4) start = Math.max(1, end - 4)
  for (let i = start; i <= end; i++) pages.push(i)
  return pages
})

const formatDate = (dateString) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
