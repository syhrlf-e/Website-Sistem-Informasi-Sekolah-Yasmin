/**
 * @component PpdbDashboard
 * @description Dashboard PPDB dengan statistik pendaftar dan gelombang
 * @route /yasmin-panel/ppdb
 */

<template>
  <div class="space-y-6">
    <!-- Loading State -->
    <div v-if="isLoading" class="flex items-center justify-center min-h-[400px]">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
    </div>

    <template v-else>
      <!-- Stats Cards (matching main dashboard style) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
        <!-- Total Pendaftar -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-300">
          <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
              <Users class="w-6 h-6 text-blue-600 dark:text-blue-400" />
            </div>
            <span class="text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 px-3 py-1 rounded-full">Terdaftar</span>
          </div>
          <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1 font-poppins">{{ stats.total }}</h3>
          <p class="text-sm text-gray-600 dark:text-gray-400 font-poppins">Total Pendaftar</p>
        </div>

        <!-- Menunggu Verifikasi -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-300">
          <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-yellow-50 dark:bg-yellow-900/20 flex items-center justify-center">
              <Clock class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
            </div>
            <span class="text-xs font-medium text-yellow-600 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-900/20 px-3 py-1 rounded-full">Pending</span>
          </div>
          <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1 font-poppins">{{ stats.by_status.pending }}</h3>
          <p class="text-sm text-gray-600 dark:text-gray-400 font-poppins">Menunggu Verifikasi</p>
        </div>

        <!-- Dalam Seleksi -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-300">
          <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center">
              <UserCheck class="w-6 h-6 text-purple-600 dark:text-purple-400" />
            </div>
            <span class="text-xs font-medium text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-900/20 px-3 py-1 rounded-full">Proses</span>
          </div>
          <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1 font-poppins">{{ stats.by_status.selection }}</h3>
          <p class="text-sm text-gray-600 dark:text-gray-400 font-poppins">Dalam Seleksi</p>
        </div>

        <!-- Diterima -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-300">
          <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 dark:bg-green-900/20 flex items-center justify-center">
              <CheckCircle class="w-6 h-6 text-green-600 dark:text-green-400" />
            </div>
            <span class="text-xs font-medium text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 px-3 py-1 rounded-full">Lulus</span>
          </div>
          <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1 font-poppins">{{ stats.by_status.accepted }}</h3>
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
          <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1 font-poppins">{{ stats.by_status.rejected }}</h3>
          <p class="text-sm text-gray-600 dark:text-gray-400 font-poppins">Ditolak</p>
        </div>
      </div>

      <!-- Wave Stats -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Active Waves -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 font-poppins">
            Gelombang Pendaftaran
          </h2>
          <div v-if="stats.wave_stats?.length" class="space-y-3">
            <div
              v-for="wave in stats.wave_stats"
              :key="wave.id"
              class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl"
            >
              <div>
                <p class="font-medium text-gray-900 dark:text-white">{{ wave.name }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ wave.academic_year }}</p>
              </div>
              <div class="text-right">
                <p class="text-2xl font-bold text-blue-600">{{ wave.registrations_count }}</p>
                <p class="text-xs text-gray-500">pendaftar</p>
              </div>
            </div>
          </div>
          <div v-else class="text-center py-8 text-gray-500">
            <CalendarDays class="w-12 h-12 mx-auto mb-2 opacity-50" />
            <p>Belum ada gelombang</p>
          </div>
          <router-link
            to="/yasmin-panel/ppdb/gelombang"
            class="mt-4 inline-flex items-center text-blue-600 hover:text-blue-700 text-sm font-medium"
          >
            Kelola Gelombang
            <ChevronRight class="w-4 h-4 ml-1" />
          </router-link>
        </div>

        <!-- Recent Registrations -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 font-poppins">
            Pendaftar Terbaru
          </h2>
          <div v-if="stats.recent?.length" class="space-y-3">
            <div
              v-for="reg in stats.recent"
              :key="reg.id"
              class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl"
            >
              <div>
                <p class="font-medium text-gray-900 dark:text-white">{{ reg.nama_lengkap }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ reg.registration_number }}</p>
              </div>
              <span
                :class="getStatusClass(reg.status)"
                class="px-3 py-1 rounded-full text-xs font-medium"
              >
                {{ getStatusLabel(reg.status) }}
              </span>
            </div>
          </div>
          <div v-else class="text-center py-8 text-gray-500">
            <UserPlus class="w-12 h-12 mx-auto mb-2 opacity-50" />
            <p>Belum ada pendaftar</p>
          </div>
          <router-link
            to="/yasmin-panel/ppdb/pendaftar"
            class="mt-4 inline-flex items-center text-blue-600 hover:text-blue-700 text-sm font-medium"
          >
            Lihat Semua Pendaftar
            <ChevronRight class="w-4 h-4 ml-1" />
          </router-link>
        </div>
      </div>

      <!-- Landing Page Settings Card -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
              <Settings class="w-5 h-5 text-blue-600 dark:text-blue-400" />
            </div>
            <div>
              <h2 class="text-lg font-semibold text-gray-900 dark:text-white font-poppins">
                Pengaturan Info Landing Page
              </h2>
              <p class="text-xs text-gray-500 dark:text-gray-400">Biaya & persyaratan yang ditampilkan di halaman PPDB</p>
            </div>
          </div>
          <button
            @click="saveLandingSettings"
            :disabled="isSaving"
            class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl font-medium text-sm transition-all disabled:opacity-50"
          >
            {{ isSaving ? 'Menyimpan...' : 'Simpan' }}
          </button>
        </div>

        <!-- Biaya Section -->
        <div class="mb-6">
          <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
            <Wallet class="w-4 h-4" /> Biaya
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Biaya Formulir</label>
              <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">Rp</span>
                <input
                  v-model="landingSettings.biaya_formulir"
                  type="number"
                  class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                />
              </div>
            </div>
            <div>
              <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">SPP Bulanan</label>
              <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">Rp</span>
                <input
                  v-model="landingSettings.spp_bulanan"
                  type="number"
                  class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                />
              </div>
            </div>
          </div>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
            * Uang Pangkal per gelombang diatur di halaman <router-link to="/yasmin-panel/ppdb/gelombang" class="text-blue-600 hover:underline">Kelola Gelombang</router-link>
          </p>
        </div>

        <!-- Persyaratan Section -->
        <div>
          <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
            <FileText class="w-4 h-4" /> Persyaratan
          </h3>
          <div class="space-y-2">
            <div
              v-for="(item, index) in landingSettings.persyaratan"
              :key="index"
              class="flex items-center gap-2"
            >
              <span class="text-gray-400 text-sm w-6">{{ index + 1 }}.</span>
              <input
                v-model="landingSettings.persyaratan[index]"
                type="text"
                class="flex-1 px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
              />
              <button
                @click="removePersyaratan(index)"
                class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                title="Hapus"
              >
                <Trash2 class="w-4 h-4" />
              </button>
            </div>
          </div>
          <button
            @click="addPersyaratan"
            class="mt-3 w-full px-4 py-2 border-2 border-dashed border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:border-blue-500 hover:text-blue-600 rounded-xl text-sm transition-colors flex items-center justify-center gap-2"
          >
            <Plus class="w-4 h-4" />
            Tambah Item Persyaratan
          </button>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { CalendarDays, ChevronRight, Clock, CheckCircle, UserCheck, XCircle, Users, UserPlus, Settings, Wallet, FileText, Trash2, Plus } from 'lucide-vue-next'
import { onMounted, ref } from 'vue'
import api from '@/services/api'
import { usePopup } from '@/composables/usePopup'

const { showSuccess, showError } = usePopup()

const isLoading = ref(true)
const isSaving = ref(false)

const stats = ref({
  total: 0,
  by_status: {
    pending: 0,
    verified: 0,
    selection: 0,
    accepted: 0,
    rejected: 0
  },
  waves: { active: 0, total: 0 },
  wave_stats: [],
  recent: []
})

const landingSettings = ref({
  biaya_formulir: 250000,
  spp_bulanan: 850000,
  persyaratan: []
})

const fetchDashboard = async () => {
  try {
    const response = await api.get('/yasmin-panel/ppdb/dashboard')
    if (response.data.success) {
      stats.value = response.data.data
    }
  } catch (error) {
    console.error('Failed to fetch PPDB dashboard:', error)
  } finally {
    isLoading.value = false
  }
}

const fetchLandingSettings = async () => {
  try {
    const response = await api.get('/yasmin-panel/ppdb/landing-settings')
    if (response.data.success) {
      landingSettings.value = response.data.data
    }
  } catch (error) {
    console.error('Failed to fetch landing settings:', error)
  }
}

const saveLandingSettings = async () => {
  isSaving.value = true
  try {
    const response = await api.post('/yasmin-panel/ppdb/landing-settings', landingSettings.value)
    if (response.data.success) {
      showSuccess('Berhasil!', 'Pengaturan berhasil disimpan')
    }
  } catch (error) {
    console.error('Failed to save landing settings:', error)
    showError('Gagal!', 'Gagal menyimpan pengaturan')
  } finally {
    isSaving.value = false
  }
}

const addPersyaratan = () => {
  landingSettings.value.persyaratan.push('')
}

const removePersyaratan = (index) => {
  landingSettings.value.persyaratan.splice(index, 1)
}

const getStatusLabel = (status) => {
  const labels = {
    pending: 'Menunggu',
    verified: 'Terverifikasi',
    selection: 'Seleksi',
    accepted: 'Diterima',
    rejected: 'Ditolak'
  }
  return labels[status] || status
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
  fetchDashboard()
  fetchLandingSettings()
})
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
