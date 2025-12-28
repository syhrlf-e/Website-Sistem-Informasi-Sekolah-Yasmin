/**
 * @component PengumumanList
 * @description Halaman daftar pengumuman dengan tampilan tabel seperti BeritaList
 * @route /yasmin-panel/pengumuman
 */

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">
          Pengumuman
        </h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
          Kelola pengumuman popup yang tampil di homepage
        </p>
      </div>
      <router-link
        to="/yasmin-panel/pengumuman/create"
        class="flex items-center gap-2 px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition-colors duration-200 shadow-lg hover:shadow-xl font-poppins"
      >
        <Plus class="w-5 h-5" />
        Tambah Pengumuman
      </router-link>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 py-12">
      <LoadingSpinner size="md" color="blue" />
    </div>

    <!-- Error State -->
    <div
      v-else-if="error"
      class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4"
    >
      <p class="text-sm text-red-600 dark:text-red-400 font-poppins">{{ error }}</p>
    </div>

    <!-- Table -->
    <div v-else class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
      <!-- Empty State -->
      <div v-if="announcements.length === 0" class="p-12 text-center">
        <Megaphone class="w-16 h-16 text-gray-400 mx-auto mb-4" />
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 font-poppins">
          Belum Ada Pengumuman
        </h3>
        <p class="text-gray-600 dark:text-gray-400 mb-6">
          Buat pengumuman pertama untuk ditampilkan di homepage
        </p>
      </div>

      <!-- Table Content -->
      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">#</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">Judul</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">Status</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">Dibuat</th>
              <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            <tr
              v-for="(announcement, index) in announcements"
              :key="announcement.id"
              class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150"
            >
              <!-- No -->
              <td class="px-6 py-4 text-sm text-gray-900 dark:text-white font-poppins">
                {{ index + 1 }}
              </td>

              <!-- Judul + Thumbnail -->
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <img
                    :src="announcement.image || '/images/placeholder.jpg'"
                    :alt="announcement.title"
                    class="w-12 h-12 rounded-lg object-cover"
                  />
                  <div>
                    <p class="font-semibold text-gray-900 dark:text-white font-poppins line-clamp-1">
                      {{ announcement.title }}
                    </p>
                  </div>
                </div>
              </td>

              <!-- Status -->
              <td class="px-6 py-4">
                <span
                  :class="[
                    'inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium font-poppins',
                    announcement.is_active && isInDateRange(announcement)
                      ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400'
                      : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400'
                  ]"
                >
                  <div
                    :class="[
                      'w-2 h-2 rounded-full',
                      announcement.is_active && isInDateRange(announcement)
                        ? 'bg-green-500'
                        : 'bg-gray-400'
                    ]"
                  ></div>
                  {{ announcement.is_active && isInDateRange(announcement) ? 'Aktif' : 'Nonaktif' }}
                </span>
              </td>

              <!-- Tanggal Dibuat -->
              <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 font-poppins">
                {{ formatDate(announcement.created_at) }}
              </td>

              <!-- Actions -->
              <td class="px-6 py-4">
                <div class="flex items-center justify-center gap-2">
                  <router-link
                    :to="`/yasmin-panel/pengumuman/edit/${announcement.id}`"
                    class="p-2 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors duration-200"
                    title="Edit"
                  >
                    <Edit class="w-4 h-4" />
                  </router-link>
                  <button
                    @click="handleDelete(announcement.id)"
                    class="p-2 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors duration-200"
                    title="Hapus"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import LoadingSpinner from '@/components/ui/shared/LoadingSpinner.vue'
import api from '@/services/api'
import { usePopup } from '@/composables/usePopup'
import { Edit, Megaphone, Plus, Trash2 } from 'lucide-vue-next'
import { onMounted, ref } from 'vue'

const { showSuccess, showError, confirmDelete } = usePopup()
const announcements = ref([])
const loading = ref(false)
const error = ref(null)

onMounted(() => {
  fetchAnnouncements()
})

const fetchAnnouncements = async () => {
  loading.value = true
  error.value = null
  try {
    const response = await api.get('/yasmin-panel/announcements')
    if (response.data.success) {
      announcements.value = response.data.data
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Gagal memuat data pengumuman'
  } finally {
    loading.value = false
  }
}

const isInDateRange = (announcement) => {
  const now = new Date()
  const start = new Date(announcement.start_date)
  const end = new Date(announcement.end_date)
  return now >= start && now <= end
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

const handleDelete = async (id) => {
  const result = await confirmDelete(
    'Hapus Pengumuman?',
    'Pengumuman yang dihapus tidak dapat dikembalikan!'
  )
  
  if (!result.isConfirmed) return
  
  try {
    const response = await api.delete(`/yasmin-panel/announcements/${id}`)
    if (response.data.success) {
      await showSuccess('Berhasil!', 'Pengumuman berhasil dihapus')
      fetchAnnouncements()
    }
  } catch (err) {
    await showError(
      'Gagal Menghapus',
      err.response?.data?.message || 'Terjadi kesalahan saat menghapus pengumuman'
    )
  }
}
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
.line-clamp-1 {
  display: -webkit-box;
  line-clamp: 1;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
