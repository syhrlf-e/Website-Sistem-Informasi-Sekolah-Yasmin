/**
 * @component PrestasiListTable
 * @description Table untuk menampilkan data prestasi
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
      <p class="text-gray-600 dark:text-gray-400 font-poppins">Belum ada prestasi</p>
    </div>

    <!-- Data Table -->
    <div v-else class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
          <tr>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">#</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">Nama Prestasi</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">Kategori</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">Tingkat</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">Peserta</th>
            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">Tahun</th>
            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          <tr v-for="(prestasi, index) in items" :key="prestasi.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white font-poppins">{{ index + 1 }}</td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-yellow-100 to-orange-100 dark:from-yellow-900/20 dark:to-orange-900/20 overflow-hidden flex-shrink-0">
                  <img v-if="prestasi.image" :src="getImageUrl(prestasi.image)" :alt="prestasi.nama_prestasi" class="w-full h-full object-cover" />
                  <div v-else class="w-full h-full flex items-center justify-center">
                    <Trophy class="w-6 h-6 text-yellow-400" />
                  </div>
                </div>
                <p class="font-semibold text-gray-900 dark:text-white font-poppins line-clamp-1">{{ prestasi.nama_prestasi }}</p>
              </div>
            </td>
            <td class="px-6 py-4">
              <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium" :class="getCategoryClass(prestasi.kategori)">{{ prestasi.kategori }}</span>
            </td>
            <td class="px-6 py-4">
              <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400">{{ prestasi.tingkat }}</span>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                <User class="w-4 h-4" />
                <span class="font-poppins">{{ getPesertaText(prestasi.peserta) }}</span>
              </div>
            </td>
            <td class="px-6 py-4 text-center text-sm text-gray-600 dark:text-gray-400 font-poppins">{{ prestasi.tahun }}</td>
            <td class="px-6 py-4">
              <div class="flex items-center justify-center gap-2">
                <button @click="$emit('edit', prestasi)" class="p-2 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:bg-blue-100" title="Edit">
                  <Edit2 class="w-4 h-4" />
                </button>
                <button @click="$emit('delete', prestasi.id)" class="p-2 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100" title="Hapus">
                  <Trash2 class="w-4 h-4" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import LoadingSpinner from '@/components/ui/shared/LoadingSpinner.vue'
import { Edit2, Trash2, Trophy, User } from 'lucide-vue-next'

defineProps({
  items: Array,
  loading: Boolean,
  error: String
})

defineEmits(['edit', 'delete'])

const getImageUrl = (imagePath) => {
  if (!imagePath) return null
  if (imagePath.startsWith('http')) return imagePath
  if (imagePath.startsWith('/storage')) return imagePath
  // If path already includes folder (e.g., 'prestasi/xxx.webp')
  if (imagePath.includes('/')) {
    return `/storage/${imagePath}`
  }
  // Legacy format: just filename (old data)
  return `/storage/prestasi/${imagePath}`
}

const getCategoryClass = (category) => {
  const classes = {
    Akademik: 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400',
    Olahraga: 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400',
    Seni: 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400'
  }
  return classes[category] || 'bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300'
}

const getPesertaText = (peserta) => {
  if (!peserta || peserta.length === 0) return '-'
  if (typeof peserta === 'string') {
    try { peserta = JSON.parse(peserta) } catch { return peserta }
  }
  if (peserta.length === 1) return peserta[0]
  return `${peserta[0]} +${peserta.length - 1} lainnya`
}
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
