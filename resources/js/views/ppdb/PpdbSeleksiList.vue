/**
 * @component PpdbSeleksiList
 * @description Halaman seleksi pendaftar PPDB (filter status selection/accepted/rejected)
 * @route /yasmin-panel/ppdb/seleksi
 */

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">Seleksi Pendaftar</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Kelola proses seleksi calon siswa</p>
      </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex gap-2 flex-wrap">
      <button
        v-for="tab in tabs"
        :key="tab.value"
        @click="activeTab = tab.value; fetchData()"
        :class="activeTab === tab.value 
          ? 'bg-blue-600 text-white' 
          : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'"
        class="px-4 py-2 rounded-xl font-medium text-sm border border-gray-200 dark:border-gray-700 transition-colors"
      >
        {{ tab.label }}
        <span v-if="counts[tab.value]" class="ml-2 px-2 py-0.5 rounded-full text-xs" :class="activeTab === tab.value ? 'bg-white/20' : 'bg-gray-100 dark:bg-gray-700'">
          {{ counts[tab.value] }}
        </span>
      </button>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
      <div v-if="isLoading" class="flex items-center justify-center py-12">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      </div>

      <div v-else-if="!registrations.length" class="text-center py-12">
        <UserX class="w-12 h-12 mx-auto text-gray-400 mb-3" />
        <p class="text-gray-500 dark:text-gray-400">Tidak ada data</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 dark:bg-gray-700/50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">No. Registrasi</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Nama</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Asal Sekolah</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Jurusan</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Status</th>
              <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            <tr
              v-for="reg in registrations"
              :key="reg.id"
              class="hover:bg-gray-50 dark:hover:bg-gray-700/50"
            >
              <td class="px-4 py-3 font-mono text-sm text-blue-600">{{ reg.registration_number }}</td>
              <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ reg.nama_lengkap }}</td>
              <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ reg.asal_sekolah }}</td>
              <td class="px-4 py-3">
                <span :class="reg.jurusan_pilihan === 'IPA' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800'" class="px-2 py-1 rounded-lg text-xs font-medium">
                  {{ reg.jurusan_pilihan }}
                </span>
              </td>
              <td class="px-4 py-3">
                <span :class="getStatusClass(reg.status)" class="px-3 py-1 rounded-full text-xs font-medium">
                  {{ reg.status_label }}
                </span>
              </td>
              <td class="px-4 py-3">
                <div class="flex items-center justify-center gap-2">
                  <button
                    v-if="reg.status === 'selection'"
                    @click="updateStatus(reg.id, 'accepted')"
                    class="px-3 py-1.5 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg text-sm font-medium"
                  >
                    Terima
                  </button>
                  <button
                    v-if="reg.status === 'selection'"
                    @click="updateStatus(reg.id, 'rejected')"
                    class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-sm font-medium"
                  >
                    Tolak
                  </button>
                  <router-link
                    :to="`/yasmin-panel/ppdb/pendaftar/${reg.id}`"
                    class="px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg text-sm font-medium"
                  >
                    Detail
                  </router-link>
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
import { UserX } from 'lucide-vue-next'
import { onMounted, ref } from 'vue'
import api from '@/services/api'
import { usePopup } from '@/composables/usePopup'

const { showSuccess, showError, confirm } = usePopup()

const isLoading = ref(true)
const registrations = ref([])
const activeTab = ref('selection')
const counts = ref({})

const tabs = [
  { value: 'selection', label: 'Tahap Seleksi' },
  { value: 'accepted', label: 'Diterima' },
  { value: 'rejected', label: 'Ditolak' }
]

const fetchData = async () => {
  isLoading.value = true
  try {
    const response = await api.get(`/yasmin-panel/ppdb/registrations?status=${activeTab.value}&per_page=50`)
    if (response.data.success) {
      registrations.value = response.data.data.data
    }
  } catch (error) {
    console.error('Failed to fetch:', error)
  } finally {
    isLoading.value = false
  }
}

const fetchCounts = async () => {
  try {
    const response = await api.get('/yasmin-panel/ppdb/dashboard')
    if (response.data.success) {
      counts.value = response.data.data.by_status
    }
  } catch (error) {
    console.error('Failed to fetch counts:', error)
  }
}

const updateStatus = async (id, newStatus) => {
  const label = newStatus === 'accepted' ? 'menerima' : 'menolak'
  const result = await confirm(`Konfirmasi`, `Yakin ingin ${label} pendaftar ini?`, 'Ya, Lanjutkan')
  if (!result.isConfirmed) return

  try {
    await api.put(`/yasmin-panel/ppdb/registrations/${id}/status`, { status: newStatus })
    showSuccess('Berhasil!', `Status berhasil diupdate`)
    fetchData()
    fetchCounts()
  } catch (error) {
    showError('Gagal!', error.response?.data?.message || 'Gagal update status')
  }
}

const getStatusClass = (status) => {
  const classes = {
    selection: 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
    accepted: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    rejected: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

onMounted(() => {
  fetchData()
  fetchCounts()
})
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
