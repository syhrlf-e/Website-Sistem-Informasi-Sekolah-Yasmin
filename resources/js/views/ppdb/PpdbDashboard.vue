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
      <!-- Stats Cards -->
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <StatsCard
          v-for="stat in statsCards"
          :key="stat.label"
          :label="stat.label"
          :value="stat.value"
          :icon="stat.icon"
          :color="stat.color"
        />
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
    </template>
  </div>
</template>

<script setup>
import { CalendarDays, ChevronRight, Clock, CheckCircle, XCircle, Users, UserPlus, FileCheck } from 'lucide-vue-next'
import { computed, onMounted, ref } from 'vue'
import api from '@/services/api'
import StatsCard from './ppdbDashboard/StatsCard.vue'

const isLoading = ref(true)
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

const statsCards = computed(() => [
  { label: 'Total Pendaftar', value: stats.value.total, icon: Users, color: 'blue' },
  { label: 'Menunggu', value: stats.value.by_status.pending, icon: Clock, color: 'yellow' },
  { label: 'Terverifikasi', value: stats.value.by_status.verified, icon: FileCheck, color: 'cyan' },
  { label: 'Seleksi', value: stats.value.by_status.selection, icon: UserPlus, color: 'purple' },
  { label: 'Diterima', value: stats.value.by_status.accepted, icon: CheckCircle, color: 'green' },
  { label: 'Ditolak', value: stats.value.by_status.rejected, icon: XCircle, color: 'red' },
])

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
})
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
