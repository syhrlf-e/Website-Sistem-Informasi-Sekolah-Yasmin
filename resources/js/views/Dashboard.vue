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
      <DashboardStats :stats="displayStats" />

      <DashboardPendaftar
        :items="store.recentPendaftar"
        :pending-count="displayStats.pendaftar_pending"
        :is-loading="store.isInitialLoading"
      />

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
    </template>
  </div>
</template>

<script setup>
import LoadingSpinner from '@/components/ui/shared/LoadingSpinner.vue'
import { usePopup } from '@/composables/usePopup'
import api from '@/services/api'
import { usePendaftarStore } from '@/stores/pendaftar'
import { onMounted, onUnmounted, ref, watch } from 'vue'
import DashboardModals from './dashboard/DashboardModals.vue'
import DashboardPendaftar from './dashboard/DashboardPendaftar.vue'
import DashboardStats from './dashboard/DashboardStats.vue'

const { showSuccess, showError, showWarning, confirm } = usePopup()
const store = usePendaftarStore()

// Display stats with animation
const displayStats = ref({ berita: 0, ekskul: 0, galeri: 0, prestasi: 0, pendaftar_pending: 0 })

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
})

onUnmounted(() => {
  // Stop polling when leaving dashboard
  store.stopPolling()
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
