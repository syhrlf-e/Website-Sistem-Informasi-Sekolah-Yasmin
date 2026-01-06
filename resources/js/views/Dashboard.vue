/**
 * @component Dashboard
 * @description Parent component untuk halaman dashboard admin
 * @route /yasmin-panel/dashboard
 * Uses Pinia store for cached data - shows loading on first login, cached data on menu navigation
 */

<template>
  <div class="space-y-6">
    <!-- Loading State (First Login Only) -->
    <div v-if="store.isInitialLoading" class="flex flex-col items-center justify-center min-h-[400px]">
      <LoadingSpinner size="lg" color="blue" />
    </div>

    <!-- Content (shown after initial load) -->
    <template v-else>
      <!-- Welcome Greeting with Date/Time -->
      <div class="flex items-start justify-between mb-2">
        <div>
          <h1 class="text-base font-semibold text-gray-900 dark:text-white font-poppins">
            {{ greeting }}, {{ userName }}! 👋
          </h1>
          <p class="text-sm text-gray-500 dark:text-gray-400 font-poppins">sebagai {{ userRole }}</p>
        </div>
        <div class="text-right cursor-pointer hover:opacity-75 transition-opacity" @click="showCalendar = true" title="Buka Kalender">
          <p class="text-base font-semibold text-gray-900 dark:text-white font-poppins">{{ currentDate }}</p>
          <p class="text-sm text-gray-500 dark:text-gray-400 font-poppins">{{ currentTime }}</p>
        </div>
      </div>

      <!-- All Dashboard Cards with consistent spacing -->
      <div class="space-y-4">
        <!-- Stats Card - full width for Admin, 3/5 for Super Admin -->
        <div :class="isRegularAdmin ? '' : 'grid grid-cols-1 lg:grid-cols-5 gap-4'">
          <div :class="isRegularAdmin ? '' : 'lg:col-span-3'">
            <DashboardStats :stats="displayStats" />
          </div>
        </div>

        <!-- Two Column Layout: PPDB Overview (Left) + Pendaftar Ekskul (Right) -->
        <!-- Super Admin: show both PPDB Overview and Pendaftar -->
        <div v-if="!isRegularAdmin" class="grid grid-cols-1 lg:grid-cols-5 gap-4">
          <!-- PPDB Overview - Left (wider) -->
          <div class="lg:col-span-3">
            <DashboardPpdbOverview />
          </div>

          <!-- Pendaftar Ekskul - Right (narrower) -->
          <div class="lg:col-span-2">
            <DashboardPendaftar
              :items="store.recentPendaftar"
              :pending-count="displayStats.pendaftar_pending"
              :approved-count="displayStats.pendaftar_approved || 0"
              :rejected-count="displayStats.pendaftar_rejected || 0"
            />
          </div>
        </div>

        <!-- Regular Admin: show only Pendaftar Ekskul (full width) -->
        <div v-else>
          <DashboardPendaftar
            :items="store.recentPendaftar"
            :pending-count="displayStats.pendaftar_pending"
            :approved-count="displayStats.pendaftar_approved || 0"
            :rejected-count="displayStats.pendaftar_rejected || 0"
          />
        </div>
      </div>

      <DashboardModals
        :show-reject="showRejectModal"
        :show-detail="showDetailModal"
        :selected-pendaftar="selectedPendaftar"
        @close-reject="closeRejectModal"
        @close-detail="showDetailModal = false"
        @confirm-reject="confirmReject"
        @approve-from-modal="handleApproveFromModal"
        @reject-from-modal="handleRejectFromModal"
      />

      <!-- Calendar Sidebar -->
      <CalendarSidebar :is-open="showCalendar" @close="showCalendar = false" />
    </template>
  </div>
</template>

<script setup>
import LoadingSpinner from '@/components/ui/shared/LoadingSpinner.vue'
import { useAuth } from '@/composables/useAuth'
import { usePopup } from '@/composables/usePopup'
import api from '@/services/api'
import { usePendaftarStore } from '@/stores/pendaftar'
import { computed, onMounted, onUnmounted, ref, watch } from 'vue'
import DashboardModals from './dashboard/DashboardModals.vue'
import DashboardPendaftar from './dashboard/DashboardPendaftar.vue'
import DashboardPpdbOverview from './dashboard/DashboardPpdbOverview.vue'
import DashboardStats from './dashboard/DashboardStats.vue'
import CalendarSidebar from './dashboard/CalendarSidebar.vue'

const { isRegularAdmin } = useAuth()
const { showSuccess, showError, showWarning, confirm } = usePopup()
const store = usePendaftarStore()
const showCalendar = ref(false)

// User greeting
const userName = computed(() => {
  const user = JSON.parse(sessionStorage.getItem('admin_user') || '{}')
  return user.name || 'Admin'
})

const userRole = computed(() => {
  const user = JSON.parse(sessionStorage.getItem('admin_user') || '{}')
  const roles = { super_admin: 'Super Admin', admin: 'Admin', admin_ppdb: 'Admin PPDB' }
  return roles[user.role] || 'Admin'
})

const greeting = computed(() => {
  const hour = new Date().getHours()
  if (hour < 11) return 'Selamat pagi'
  if (hour < 15) return 'Selamat siang'
  if (hour < 18) return 'Selamat sore'
  return 'Selamat malam'
})

// Date/Time
const clockTime = ref(new Date())
let clockInterval = null

const currentDate = computed(() => {
  const options = { day: 'numeric', month: 'long', year: 'numeric' }
  return clockTime.value.toLocaleDateString('id-ID', options)
})

const currentTime = computed(() => {
  const hours = clockTime.value.getHours().toString().padStart(2, '0')
  const minutes = clockTime.value.getMinutes().toString().padStart(2, '0')
  const seconds = clockTime.value.getSeconds().toString().padStart(2, '0')
  return `${hours}.${minutes}.${seconds}`
})

// Display stats with animation
const displayStats = ref({ berita: 0, ekskul: 0, galeri: 0, prestasi: 0, pendaftar_pending: 0, pendaftar_approved: 0, pendaftar_rejected: 0 })

const showDetailModal = ref(false)
const showRejectModal = ref(false)
const selectedPendaftar = ref(null)
const rejectNotes = ref('')

// Animate number changes
const animateNumber = (key, target) => {
  const start = displayStats.value[key] || 0
  const diff = Math.abs(target - start)
  
  // Instant update for small changes
  if (diff <= 2) {
    displayStats.value[key] = target
    return
  }
  
  const duration = 500
  const steps = Math.min(diff, 10)
  const increment = (target - start) / steps
  let current = start
  let step = 0
  const interval = setInterval(() => {
    step++
    current += increment
    if (step >= steps) { 
      displayStats.value[key] = target
      clearInterval(interval) 
    } else { 
      displayStats.value[key] = Math.round(current) 
    }
  }, duration / steps)
}

// Watch store stats changes for animation
watch(() => store.stats, (newStats) => {
  Object.keys(newStats).forEach((key) => { 
    if (typeof newStats[key] === 'number') animateNumber(key, newStats[key]) 
  })
}, { deep: true, immediate: true })

onMounted(() => {
  // Start polling - store handles caching and initial load
  store.startPolling()
  // Update clock every second
  clockInterval = setInterval(() => {
    clockTime.value = new Date()
  }, 1000)
})

onUnmounted(() => {
  // Stop polling when leaving dashboard
  store.stopPolling()
  if (clockInterval) clearInterval(clockInterval)
})

const handleApprove = async (id) => {
  const result = await confirm('Approve Pendaftaran?', 'Pendaftar akan diterima di ekstrakurikuler ini', 'Ya, Approve')
  if (!result.isConfirmed) return
  try {
    await api.put(`/yasmin-panel/ekstrakurikuler/registrations/${id}/approve`, { admin_notes: 'Approved from dashboard' })
    await showSuccess('Berhasil!', 'Pendaftaran telah diapprove')
    store.fetchAll() // Refresh store data
  } catch (err) {
    await showError('Gagal!', err.response?.data?.message || 'Gagal approve pendaftaran')
  }
}

const handleReject = async (id) => {
  selectedPendaftar.value = store.recentPendaftar.find((p) => p.id === id)
  showRejectModal.value = true
}

const confirmReject = async (notes) => {
  if (!notes.trim()) { await showWarning('Perhatian!', 'Alasan penolakan harus diisi'); return }
  try {
    await api.put(`/yasmin-panel/ekstrakurikuler/registrations/${selectedPendaftar.value.id}/reject`, { admin_notes: notes.trim() })
    closeRejectModal()
    await showSuccess('Berhasil!', 'Pendaftaran telah ditolak')
    store.fetchAll() // Refresh store data
  } catch (err) {
    await showError('Gagal!', err.response?.data?.message || 'Gagal reject pendaftaran')
  }
}

const closeRejectModal = () => { showRejectModal.value = false; rejectNotes.value = ''; selectedPendaftar.value = null }

const handleApproveFromModal = () => { showDetailModal.value = false; handleApprove(selectedPendaftar.value.id) }
const handleRejectFromModal = () => { showDetailModal.value = false; handleReject(selectedPendaftar.value.id) }
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
