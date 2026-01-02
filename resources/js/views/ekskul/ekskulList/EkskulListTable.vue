/**
 * @component EkskulListTable
 * @description Data table untuk menampilkan daftar ekstrakurikuler
 */

<template>
  <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div v-if="items.length === 0" class="p-12 text-center">
      <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
        <Target class="w-8 h-8 text-gray-400" />
      </div>
      <p class="text-gray-600 dark:text-gray-400 font-poppins">Tidak ada ekstrakurikuler</p>
    </div>

    <div v-else class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
          <tr>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins w-12">#</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">Judul</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">Kategori</th>
            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">Status Pendaftaran</th>
            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">Batas Pendaftaran</th>
            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">Maksimal Peserta</th>
            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          <tr
            v-for="(ekskul, index) in items"
            :key="ekskul.id"
            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150"
          >
            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 font-poppins w-12">{{ index + 1 }}</td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                  <img
                    v-if="ekskul.gambar"
                    :src="getImageUrl(ekskul.gambar)"
                    :alt="ekskul.nama"
                    class="w-full h-full object-cover"
                  />
                  <ImageIcon v-else class="w-5 h-5 text-gray-400" />
                </div>
                <div class="min-w-0">
                  <p class="font-semibold text-gray-900 dark:text-white font-poppins">{{ ekskul.nama }}</p>
                  <p v-if="ekskul.tagline" class="text-xs text-gray-500 dark:text-gray-400 font-poppins truncate">{{ ekskul.tagline }}</p>
                </div>
              </div>
            </td>
            <td class="px-6 py-4">
              <BaseBadge
                :label="ekskul.badge"
                :variant="getBadgeVariant(ekskul.badge)"
                size="sm"
              />
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center justify-center">
                <button
                  @click="handleToggleStatus(ekskul)"
                  class="px-3 py-2 rounded-lg transition-colors duration-200 group flex items-center gap-2"
                  :class="[
                    getRegistrationStatus(ekskul)
                      ? 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 hover:bg-green-100'
                      : 'bg-gray-50 dark:bg-gray-900/20 text-gray-600 dark:text-gray-400 hover:bg-gray-100'
                  ]"
                >
                  <Power class="w-4 h-4 group-hover:scale-110 transition-transform" />
                  <span class="text-xs font-semibold font-poppins">
                    {{ getRegistrationStatus(ekskul) ? 'BUKA' : 'TUTUP' }}
                  </span>
                </button>
              </div>
            </td>
            <td class="px-6 py-4">
              <div class="text-center">
                <div v-if="ekskul.registration_deadline">
                  <p
                    class="text-sm font-poppins"
                    :class="isDeadlinePassed(ekskul) ? 'text-red-600 dark:text-red-400 font-semibold' : 'text-gray-900 dark:text-white'"
                  >
                    {{ formatDate(ekskul.registration_deadline) }}
                  </p>
                  <span
                    v-if="isDeadlinePassed(ekskul)"
                    class="inline-block mt-1 px-2 py-0.5 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-xs font-semibold rounded-full"
                  >
                    Sudah Lewat
                  </span>
                </div>
                <p v-else class="text-sm text-gray-400 dark:text-gray-500 font-poppins">-</p>
              </div>
            </td>
            <td class="px-6 py-4">
              <div class="text-center">
                <p v-if="ekskul.max_participants" class="text-sm text-gray-900 dark:text-white font-poppins font-semibold">
                  {{ ekskul.max_participants }} orang
                </p>
                <p v-else class="text-sm text-gray-400 dark:text-gray-500 font-poppins">-</p>
              </div>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center justify-center gap-2">
                <button
                  @click="$emit('edit', ekskul)"
                  class="p-2 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:bg-blue-100 transition-colors duration-200 group"
                  title="Edit"
                >
                  <Edit2 class="w-4 h-4 group-hover:scale-110 transition-transform" />
                </button>
                <button
                  @click="$emit('delete', ekskul.id)"
                  class="p-2 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 transition-colors duration-200 group"
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
import { Edit2, ImageIcon, Power, Target, Trash2 } from 'lucide-vue-next'
import BaseBadge from '@/components/ui/shared/BaseBadge.vue'
import { usePopup } from '@/composables/usePopup'

const { showWarning } = usePopup()

defineProps({
  items: {
    type: Array,
    required: true
  }
})

const emit = defineEmits(['edit', 'delete', 'toggle-status'])

const getImageUrl = (path) => {
  if (!path) return null
  if (path.startsWith('http')) return path
  return `/storage/${path}`
}

const getBadgeVariant = (badge) => {
  const variants = {
    Akademik: 'blue',
    Olahraga: 'green',
    Seni: 'purple',
    Kepemimpinan: 'orange'
  }
  return variants[badge] || 'gray'
}

const isDeadlinePassed = (ekskul) => {
  if (!ekskul.registration_deadline) return false
  return new Date(ekskul.registration_deadline) < new Date()
}

// Registration status: BUKA if enabled AND not expired
const getRegistrationStatus = (ekskul) => {
  if (isDeadlinePassed(ekskul)) return false
  return ekskul.enable_registration
}

// Handle toggle with deadline validation
const handleToggleStatus = (ekskul) => {
  // If currently closed and trying to open
  if (!ekskul.enable_registration || isDeadlinePassed(ekskul)) {
    // Check if deadline has passed
    if (isDeadlinePassed(ekskul)) {
      showWarning('Batas pendaftaran sudah lewat', 'Silakan ubah batas pendaftaran terlebih dahulu untuk membuka pendaftaran.')
      return
    }
  }
  // Emit toggle event to parent
  emit('toggle-status', ekskul)
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID', {
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
