/**
 * @component DashboardPpdbOverview
 * @description PPDB overview widget untuk unified dashboard (Super Admin & Admin only)
 * Shows active wave stats and recent registrants
 */

<template>
  <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden h-full">
    <!-- Header -->
    <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
          <UserPlus class="w-5 h-5 text-blue-600 dark:text-blue-400" />
        </div>
        <div>
          <h3 class="text-base font-bold text-gray-900 dark:text-white font-poppins">PPDB Overview</h3>
          <p class="text-xs text-gray-500 dark:text-gray-400 font-poppins">{{ activeWave?.name || 'Tidak ada gelombang aktif' }}</p>
        </div>
      </div>
      <router-link 
        to="/yasmin-panel/ppdb" 
        class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline font-poppins flex items-center gap-1"
      >
        Detail
        <ArrowRight class="w-4 h-4" />
      </router-link>
    </div>

    <!-- Loading -->
    <div v-if="isLoading" class="p-8 text-center">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
    </div>

    <!-- Content -->
    <div v-else class="p-5">
      <!-- Mini Stats (matching main dashboard style) -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
        <!-- Pending -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
          <div class="flex items-center justify-between mb-2">
            <div class="w-8 h-8 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 flex items-center justify-center">
              <Clock class="w-4 h-4 text-yellow-600 dark:text-yellow-400" />
            </div>
            <span class="text-xs font-medium text-yellow-600 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-900/20 px-2 py-0.5 rounded-full">Pending</span>
          </div>
          <h3 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">{{ stats.pending }}</h3>
        </div>
        <!-- Proses -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
          <div class="flex items-center justify-between mb-2">
            <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
              <Users class="w-4 h-4 text-blue-600 dark:text-blue-400" />
            </div>
            <span class="text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 px-2 py-0.5 rounded-full">Proses</span>
          </div>
          <h3 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">{{ stats.inSelection }}</h3>
        </div>
        <!-- Diterima -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
          <div class="flex items-center justify-between mb-2">
            <div class="w-8 h-8 rounded-lg bg-green-50 dark:bg-green-900/20 flex items-center justify-center">
              <CheckCircle class="w-4 h-4 text-green-600 dark:text-green-400" />
            </div>
            <span class="text-xs font-medium text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 px-2 py-0.5 rounded-full">Diterima</span>
          </div>
          <h3 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">{{ stats.accepted }}</h3>
        </div>
        <!-- Ditolak -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
          <div class="flex items-center justify-between mb-2">
            <div class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/20 flex items-center justify-center">
              <XCircle class="w-4 h-4 text-red-600 dark:text-red-400" />
            </div>
            <span class="text-xs font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 px-2 py-0.5 rounded-full">Ditolak</span>
          </div>
          <h3 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">{{ stats.rejected }}</h3>
        </div>
      </div>

      <!-- Recent Registrants -->
      <div>
        <h4 class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-3">Pendaftar Terbaru</h4>
        <div v-if="recentRegistrants.length" class="space-y-2">
          <div 
            v-for="registrant in recentRegistrants" 
            :key="registrant.id"
            class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg"
          >
            <div>
              <p class="text-sm font-medium text-gray-900 dark:text-white">{{ registrant.nama_lengkap }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400">{{ registrant.registration_number }}</p>
            </div>
            <span 
              :class="statusBadgeClass(registrant.status)"
              class="px-2 py-1 text-xs font-medium rounded-full"
            >
              {{ statusLabel(registrant.status) }}
            </span>
          </div>
          <!-- More registrants count -->
          <p v-if="remainingCount > 0" class="text-xs text-center text-gray-500 dark:text-gray-400 pt-2">
            dan {{ remainingCount }} pendaftar lainnya
          </p>
        </div>
        <div v-else class="text-center py-6">
          <ClipboardList class="w-8 h-8 text-gray-300 dark:text-gray-600 mx-auto mb-2" />
          <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada pendaftar</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import api from '@/services/api'
import { ArrowRight, CheckCircle, ClipboardList, Clock, UserPlus, Users, XCircle } from 'lucide-vue-next'
import { computed, onMounted, ref } from 'vue'

const isLoading = ref(true)
const activeWave = ref(null)
const stats = ref({
  pending: 0,
  inSelection: 0,
  accepted: 0,
  rejected: 0
})
const recentRegistrants = ref([])
const totalRegistrants = ref(0)
const remainingCount = computed(() => Math.max(0, totalRegistrants.value - 3))

const fetchData = async () => {
  try {
    isLoading.value = true
    const token = sessionStorage.getItem('admin_token')
    const response = await fetch('/api/yasmin-panel/ppdb/dashboard', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })
    const data = await response.json()
    
    if (data.success) {
      // Get active wave from wave_stats (check is_active field, get the one with lowest order/id)
      const activeWaves = data.data.wave_stats?.filter(w => w.is_active === true || w.is_active === 1) || []
      const activeWaveData = activeWaves.sort((a, b) => (a.order || a.id || 0) - (b.order || b.id || 0))[0]
      activeWave.value = activeWaveData ? { name: `${activeWaveData.name} • ${activeWaveData.academic_year}` } : null
      
      stats.value = {
        pending: data.data.by_status?.pending || 0,
        inSelection: data.data.by_status?.selection || 0,
        accepted: data.data.by_status?.accepted || 0,
        rejected: data.data.by_status?.rejected || 0
      }
      recentRegistrants.value = data.data.recent?.slice(0, 3) || []
      totalRegistrants.value = data.data.total || data.data.recent?.length || 0
    }
  } catch (error) {
    console.error('Failed to fetch PPDB overview:', error)
  } finally {
    isLoading.value = false
  }
}

const statusBadgeClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
    verified: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
    in_selection: 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
    accepted: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
    rejected: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'
  }
  return classes[status] || classes.pending
}

const statusLabel = (status) => {
  const labels = {
    pending: 'Menunggu',
    verified: 'Terverifikasi',
    in_selection: 'Seleksi',
    accepted: 'Diterima',
    rejected: 'Ditolak'
  }
  return labels[status] || 'Menunggu'
}

onMounted(fetchData)
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
