/**
 * @component BeritaListTable
 * @description Data table untuk menampilkan daftar berita
 */

<template>
  <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div v-if="items.length === 0" class="p-12 text-center">
      <p class="text-gray-600 dark:text-gray-400 font-poppins">Belum ada berita</p>
    </div>

    <div v-else class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
          <tr>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">#</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">Judul</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">Kategori</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">Tanggal</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">Status</th>
            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">Views</th>
            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          <tr
            v-for="(berita, index) in items"
            :key="berita.id"
            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150"
          >
            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white font-poppins">
              {{ startIndex + index + 1 }}
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <img
                  :src="berita.image || '/images/placeholder-news.jpg'"
                  :alt="berita.title"
                  class="w-12 h-12 rounded-lg object-cover"
                />
                <div>
                  <p class="font-semibold text-gray-900 dark:text-white font-poppins line-clamp-1">
                    {{ berita.title }}
                  </p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">{{ berita.slug }}</p>
                  <span
                    v-if="berita.is_featured"
                    class="inline-flex items-center gap-1 px-2 py-0.5 mt-1 rounded text-xs font-medium bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400"
                  >
                    ⭐ Featured
                  </span>
                </div>
              </div>
            </td>
            <td class="px-6 py-4">
              <span
                class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium"
                :class="getCategoryClass(berita.category)"
              >
                {{ getCategoryLabel(berita.category) }}
              </span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 font-poppins">
              {{ formatDate(berita.created_at) }}
            </td>
            <td class="px-6 py-4">
              <span
                v-if="berita.published"
                class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 font-poppins"
              >
                <div class="w-2 h-2 rounded-full bg-green-500"></div>
                Published
              </span>
              <span
                v-else
                class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-400 font-poppins"
              >
                <div class="w-2 h-2 rounded-full bg-yellow-500"></div>
                Draft
              </span>
            </td>
            <td class="px-6 py-4 text-center text-sm text-gray-600 dark:text-gray-400 font-poppins">
              {{ berita.views || 0 }}
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center justify-center gap-2">
                <button
                  @click="$emit('edit', berita.id)"
                  class="p-2 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors duration-200 group"
                  title="Edit"
                >
                  <Edit2 class="w-4 h-4 group-hover:scale-110 transition-transform" />
                </button>

                <button
                  @click="$emit('preview', berita)"
                  class="p-2 rounded-lg bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors duration-200 group"
                  title="Preview"
                >
                  <Eye class="w-4 h-4 group-hover:scale-110 transition-transform" />
                </button>

                <button
                  @click="$emit('delete', berita.id)"
                  class="p-2 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors duration-200 group"
                  title="Hapus"
                >
                  <Trash2 class="w-4 h-4 group-hover:scale-110 transition-transform" />
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
import { Edit2, Eye, Trash2 } from 'lucide-vue-next'

defineProps({
  items: {
    type: Array,
    required: true
  },
  startIndex: {
    type: Number,
    default: 0
  }
})

defineEmits(['edit', 'preview', 'delete'])

const getCategoryClass = (category) => {
  const classes = {
    event: 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400',
    achievement: 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400',
    announcement: 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400',
    other: 'bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300'
  }
  return classes[category] || classes.other
}

const getCategoryLabel = (category) => {
  const labels = {
    event: 'Event',
    achievement: 'Prestasi',
    announcement: 'Pengumuman',
    other: 'Lainnya'
  }
  return labels[category] || 'Lainnya'
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  })
}
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
