/**
 * @component PpdbPengumumanList  
 * @description Halaman pengumuman PPDB (menampilkan hasil seleksi)
 * @route /yasmin-panel/ppdb/pengumuman
 */

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">Pengumuman PPDB</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Hasil seleksi calon siswa baru</p>
      </div>
    </div>

    <!-- Summary Cards (matching dashboard style) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
      <!-- Diterima -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-300">
        <div class="flex items-center justify-between mb-4">
          <div class="w-12 h-12 rounded-xl bg-green-50 dark:bg-green-900/20 flex items-center justify-center">
            <CheckCircle class="w-6 h-6 text-green-600 dark:text-green-400" />
          </div>
          <span class="text-xs font-medium text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 px-3 py-1 rounded-full">Lulus</span>
        </div>
        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1 font-poppins">{{ counts.accepted || 0 }}</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 font-poppins">Diterima</p>
      </div>
      
      <!-- Ditolak -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-300">
        <div class="flex items-center justify-between mb-4">
          <div class="w-12 h-12 rounded-xl bg-red-50 dark:bg-red-900/20 flex items-center justify-center">
            <XCircle class="w-6 h-6 text-red-600 dark:text-red-400" />
          </div>
          <span class="text-xs font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 px-3 py-1 rounded-full">Tidak Lolos</span>
        </div>
        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1 font-poppins">{{ counts.rejected || 0 }}</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 font-poppins">Ditolak</p>
      </div>
      
      <!-- Menunggu -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-300">
        <div class="flex items-center justify-between mb-4">
          <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center">
            <Clock class="w-6 h-6 text-purple-600 dark:text-purple-400" />
          </div>
          <span class="text-xs font-medium text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-900/20 px-3 py-1 rounded-full">Proses</span>
        </div>
        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1 font-poppins">{{ counts.selection || 0 }}</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 font-poppins">Menunggu</p>
      </div>
    </div>

    <!-- Filter -->
    <div class="flex gap-3">
      <select
        v-model="filter"
        @change="fetchData"
        class="px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm"
      >
        <option value="all">Semua Hasil</option>
        <option value="accepted">Diterima</option>
        <option value="rejected">Ditolak</option>
      </select>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
      <div v-if="isLoading" class="flex items-center justify-center py-12">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      </div>

      <div v-else-if="!registrations.length" class="text-center py-12">
        <FileText class="w-12 h-12 mx-auto text-gray-400 mb-3" />
        <p class="text-gray-500">Belum ada hasil pengumuman</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 dark:bg-gray-700/50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">No. Registrasi</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Asal Sekolah</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Jurusan</th>
              <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Hasil</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="reg in registrations" :key="reg.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
              <td class="px-4 py-3 font-mono text-sm text-blue-600">{{ reg.registration_number }}</td>
              <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ reg.nama_lengkap }}</td>
              <td class="px-4 py-3 text-sm text-gray-600">{{ reg.asal_sekolah }}</td>
              <td class="px-4 py-3">
                <span :class="reg.jurusan_pilihan === 'IPA' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800'" class="px-2 py-1 rounded-lg text-xs font-medium">
                  {{ reg.jurusan_pilihan }}
                </span>
              </td>
              <td class="px-4 py-3 text-center">
                <span 
                  :class="reg.status === 'accepted' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" 
                  class="px-4 py-1.5 rounded-full text-sm font-semibold"
                >
                  {{ reg.status === 'accepted' ? '✓ DITERIMA' : '✗ DITOLAK' }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { CheckCircle, Clock, FileText, XCircle } from 'lucide-vue-next'
import { onMounted, ref } from 'vue'
import api from '@/services/api'

const isLoading = ref(true)
const registrations = ref([])
const filter = ref('all')
const counts = ref({})

const fetchData = async () => {
  isLoading.value = true
  try {
    let status = filter.value === 'all' ? 'accepted,rejected' : filter.value
    // For 'all', we need to fetch both accepted and rejected
    if (filter.value === 'all') {
      const [accepted, rejected] = await Promise.all([
        api.get('/yasmin-panel/ppdb/registrations?status=accepted&per_page=100'),
        api.get('/yasmin-panel/ppdb/registrations?status=rejected&per_page=100')
      ])
      registrations.value = [
        ...(accepted.data.data?.data || []),
        ...(rejected.data.data?.data || [])
      ]
    } else {
      const response = await api.get(`/yasmin-panel/ppdb/registrations?status=${status}&per_page=100`)
      registrations.value = response.data.data?.data || []
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

onMounted(() => {
  fetchData()
  fetchCounts()
})
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
