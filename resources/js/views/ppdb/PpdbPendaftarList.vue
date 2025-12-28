/**
 * @component PpdbPendaftarList
 * @description Tabel data pendaftar PPDB dengan filter dan search
 * @route /yasmin-panel/ppdb/pendaftar
 */

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">Data Pendaftar PPDB</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Kelola data calon siswa baru</p>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Search -->
        <div class="md:col-span-2">
          <div class="relative">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
            <input
              v-model="filters.search"
              type="text"
              placeholder="Cari nama, no. registrasi, NIK..."
              class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500"
              @input="debouncedSearch"
            />
          </div>
        </div>

        <!-- Status Filter -->
        <select
          v-model="filters.status"
          class="px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm"
          @change="fetchData"
        >
          <option value="all">Semua Status</option>
          <option value="pending">Menunggu</option>
          <option value="verified">Terverifikasi</option>
          <option value="selection">Seleksi</option>
          <option value="accepted">Diterima</option>
          <option value="rejected">Ditolak</option>
        </select>

        <!-- Wave Filter -->
        <select
          v-model="filters.wave_id"
          class="px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm"
          @change="fetchData"
        >
          <option value="">Semua Gelombang</option>
          <option v-for="wave in waves" :key="wave.id" :value="wave.id">
            {{ wave.name }}
          </option>
        </select>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
      <!-- Loading -->
      <div v-if="isLoading" class="flex items-center justify-center py-12">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      </div>

      <!-- Empty State -->
      <div v-else-if="!registrations.length" class="text-center py-12">
        <UserX class="w-12 h-12 mx-auto text-gray-400 mb-3" />
        <p class="text-gray-500 dark:text-gray-400">Tidak ada data pendaftar</p>
      </div>

      <!-- Table Content -->
      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 dark:bg-gray-700/50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">No. Registrasi</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Nama Lengkap</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Asal Sekolah</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Jurusan</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Status</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Tanggal</th>
              <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            <tr
              v-for="reg in registrations"
              :key="reg.id"
              class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
            >
              <td class="px-4 py-3">
                <span class="font-mono text-sm text-blue-600 dark:text-blue-400">{{ reg.registration_number }}</span>
              </td>
              <td class="px-4 py-3">
                <div>
                  <p class="font-medium text-gray-900 dark:text-white">{{ reg.nama_lengkap }}</p>
                  <p class="text-xs text-gray-500">{{ reg.jenis_kelamin }}</p>
                </div>
              </td>
              <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ reg.asal_sekolah }}</td>
              <td class="px-4 py-3">
                <span
                  :class="reg.jurusan_pilihan === 'IPA' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400'"
                  class="px-2 py-1 rounded-lg text-xs font-medium"
                >
                  {{ reg.jurusan_pilihan }}
                </span>
              </td>
              <td class="px-4 py-3">
                <span
                  :class="getStatusClass(reg.status)"
                  class="px-3 py-1 rounded-full text-xs font-medium"
                >
                  {{ reg.status_label }}
                </span>
              </td>
              <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ reg.created_at }}</td>
              <td class="px-4 py-3 text-center">
                <router-link
                  :to="`/yasmin-panel/ppdb/pendaftar/${reg.id}`"
                  class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 text-blue-600 dark:text-blue-400 rounded-lg text-sm font-medium transition-colors"
                >
                  <Eye class="w-4 h-4" />
                  Detail
                </router-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
        <p class="text-sm text-gray-500">
          Menampilkan {{ pagination.from }} - {{ pagination.to }} dari {{ pagination.total }} data
        </p>
        <div class="flex gap-2">
          <button
            :disabled="pagination.current_page === 1"
            @click="goToPage(pagination.current_page - 1)"
            class="px-3 py-1.5 bg-gray-100 dark:bg-gray-700 rounded-lg text-sm disabled:opacity-50"
          >
            Prev
          </button>
          <button
            :disabled="pagination.current_page === pagination.last_page"
            @click="goToPage(pagination.current_page + 1)"
            class="px-3 py-1.5 bg-gray-100 dark:bg-gray-700 rounded-lg text-sm disabled:opacity-50"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Eye, Search, UserX } from 'lucide-vue-next'
import { onMounted, reactive, ref } from 'vue'
import api from '@/services/api'

const isLoading = ref(true)
const registrations = ref([])
const waves = ref([])
const pagination = reactive({
  current_page: 1,
  last_page: 1,
  from: 0,
  to: 0,
  total: 0
})

const filters = reactive({
  search: '',
  status: 'all',
  wave_id: ''
})

let searchTimeout = null

const debouncedSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    fetchData()
  }, 300)
}

const fetchData = async (page = 1) => {
  isLoading.value = true
  try {
    const params = new URLSearchParams()
    params.append('page', page)
    if (filters.search) params.append('search', filters.search)
    if (filters.status !== 'all') params.append('status', filters.status)
    if (filters.wave_id) params.append('wave_id', filters.wave_id)

    const response = await api.get(`/yasmin-panel/ppdb/registrations?${params.toString()}`)
    if (response.data.success) {
      registrations.value = response.data.data.data
      pagination.current_page = response.data.data.current_page
      pagination.last_page = response.data.data.last_page
      pagination.from = response.data.data.from || 0
      pagination.to = response.data.data.to || 0
      pagination.total = response.data.data.total
    }
  } catch (error) {
    console.error('Failed to fetch registrations:', error)
  } finally {
    isLoading.value = false
  }
}

const fetchWaves = async () => {
  try {
    const response = await api.get('/yasmin-panel/ppdb/waves?per_page=100')
    if (response.data.success) {
      waves.value = response.data.data.data
    }
  } catch (error) {
    console.error('Failed to fetch waves:', error)
  }
}

const goToPage = (page) => {
  fetchData(page)
}

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    verified: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
    selection: 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
    accepted: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    rejected: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

onMounted(() => {
  fetchWaves()
  fetchData()
})
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
