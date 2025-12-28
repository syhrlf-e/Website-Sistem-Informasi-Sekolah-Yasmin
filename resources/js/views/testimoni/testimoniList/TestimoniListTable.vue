/**
 * @component TestimoniListTable
 * @description Table untuk menampilkan data testimoni dengan drag and drop
 */

<template>
  <!-- Loading State -->
  <div v-if="loading" class="py-20">
    <LoadingSpinner size="lg" color="blue" />
  </div>

  <!-- Error State -->
  <div v-else-if="error" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-6 text-center">
    <p class="text-red-600 dark:text-red-400 font-poppins">{{ error }}</p>
  </div>

  <!-- Table -->
  <div v-else-if="items.length > 0" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border-b border-blue-100 dark:border-blue-800">
      <p class="text-sm text-blue-700 dark:text-blue-300 font-poppins">Drag & drop untuk mengubah urutan testimoni</p>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
          <tr>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase font-poppins w-16">Order</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase font-poppins">Nama</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase font-poppins">Role</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase font-poppins">Testimoni</th>
            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase font-poppins">Status</th>
            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase font-poppins">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          <tr v-for="(testimonial, index) in items" :key="testimonial.id" draggable="true"
            @dragstart="$emit('drag-start', index)" @dragover.prevent @drop="$emit('drop', index)"
            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors cursor-move">
            <td class="px-6 py-4">
              <div class="flex items-center gap-2">
                <GripVertical class="w-4 h-4 text-gray-400" />
                <span class="text-sm font-semibold text-gray-900 dark:text-white font-poppins">{{ testimonial.order + 1 }}</span>
              </div>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white font-bold text-sm">
                  {{ getInitials(testimonial.author) }}
                </div>
                <p class="font-semibold text-gray-900 dark:text-white font-poppins">{{ testimonial.author }}</p>
              </div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 font-poppins">{{ testimonial.role }}</td>
            <td class="px-6 py-4">
              <p class="text-sm text-gray-900 dark:text-white font-poppins line-clamp-2 max-w-md">"{{ testimonial.text }}"</p>
            </td>
            <td class="px-6 py-4 text-center">
              <button @click="$emit('toggle', testimonial)"
                :class="['px-3 py-1.5 rounded-lg text-xs font-semibold', testimonial.is_active
                  ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 hover:bg-green-200'
                  : 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 hover:bg-orange-200']">
                {{ testimonial.is_active ? 'Aktif' : 'Pending' }}
              </button>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center justify-center gap-2">
                <button @click="$emit('edit', testimonial)" class="p-2 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:bg-blue-100" title="Edit">
                  <Edit2 class="w-4 h-4" />
                </button>
                <button @click="$emit('delete', testimonial.id)" class="p-2 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100" title="Hapus">
                  <Trash2 class="w-4 h-4" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Empty State -->
  <div v-else class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-12 text-center">
    <MessageSquareQuote class="w-16 h-16 mx-auto mb-4 text-gray-400" />
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 font-poppins">Belum Ada Testimoni</h3>
    <p class="text-gray-600 dark:text-gray-400 mb-6 font-poppins">Tambahkan testimoni pertama</p>
    <button @click="$emit('add')" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-semibold font-poppins">
      <Plus class="w-5 h-5" />
      Tambah Testimoni
    </button>
  </div>
</template>

<script setup>
import LoadingSpinner from '@/components/ui/shared/LoadingSpinner.vue'
import { Edit2, GripVertical, MessageSquareQuote, Plus, Trash2 } from 'lucide-vue-next'

defineProps({
  items: Array,
  loading: Boolean,
  error: String
})

defineEmits(['add', 'edit', 'delete', 'toggle', 'drag-start', 'drop'])

const getInitials = (name) => name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
.line-clamp-2 { display: -webkit-box; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>
