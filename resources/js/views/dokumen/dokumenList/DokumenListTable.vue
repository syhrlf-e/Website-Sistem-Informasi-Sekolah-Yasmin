/**
 * @component DokumenListTable
 * @description Data table untuk menampilkan daftar dokumen PPDB
 */

<template>
  <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div v-if="items.length === 0" class="p-12 text-center">
      <FileText class="w-16 h-16 mx-auto mb-4 text-gray-400" />
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Belum Ada Dokumen</h3>
      <p class="text-gray-600 dark:text-gray-400 mb-6">Upload dokumen PPDB pertama</p>
      <button
        @click="$emit('upload')"
        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-semibold"
      >
        <Plus class="w-5 h-5" />
        Upload Dokumen
      </button>
    </div>

    <table v-else class="w-full">
      <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
        <tr>
          <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Judul</th>
          <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Tipe</th>
          <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Ukuran</th>
          <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Status</th>
          <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Action</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
        <tr
          v-for="doc in items"
          :key="doc.id"
          class="hover:bg-gray-50 dark:hover:bg-gray-700/50"
        >
          <td class="px-6 py-4">
            <p class="font-semibold text-gray-900 dark:text-white font-poppins">{{ doc.title }}</p>
            <p v-if="doc.description" class="text-sm text-gray-600 dark:text-gray-400 line-clamp-1">
              {{ doc.description }}
            </p>
          </td>
          <td class="px-6 py-4">
            <span class="px-2 py-1 rounded text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
              {{ doc.file_type.toUpperCase() }}
            </span>
          </td>
          <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
            {{ formatFileSize(doc.file_size) }}
          </td>
          <td class="px-6 py-4">
            <div class="flex justify-center">
              <button
                @click="$emit('toggle-active', doc)"
                class="px-3 py-1.5 rounded-lg text-xs font-semibold"
                :class="doc.is_active
                  ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400'
                  : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400'"
              >
                {{ doc.is_active ? 'Aktif' : 'Nonaktif' }}
              </button>
            </div>
          </td>
          <td class="px-6 py-4">
            <div class="flex items-center justify-center gap-2">
              <button
                @click="$emit('edit', doc)"
                class="p-2 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:bg-blue-100"
              >
                <Edit2 class="w-4 h-4" />
              </button>
              <button
                @click="$emit('delete', doc.id)"
                class="p-2 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100"
              >
                <Trash2 class="w-4 h-4" />
              </button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { Edit2, FileText, Plus, Trash2 } from 'lucide-vue-next'

defineProps({
  items: {
    type: Array,
    required: true
  }
})

defineEmits(['upload', 'edit', 'delete', 'toggle-active'])



const formatFileSize = (bytes) => {
  if (bytes >= 1048576) return (bytes / 1048576).toFixed(2) + ' MB'
  if (bytes >= 1024) return (bytes / 1024).toFixed(2) + ' KB'
  return bytes + ' B'
}
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
.line-clamp-1 {
  display: -webkit-box;
  line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
