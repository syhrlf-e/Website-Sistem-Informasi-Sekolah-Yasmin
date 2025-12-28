/**
 * @component PendaftarEkskulList
 * @description Parent component untuk halaman pendaftar ekstrakurikuler
 * @route /yasmin-panel/ekskul/pendaftar
 */

<template>
  <div class="space-y-6">
    <PendaftarListHeader />

    <PendaftarListStats :stats="stats" />

    <PendaftarListFilters
      v-model:search="filters.search"
      v-model:status="filters.status"
      v-model:ekskul-id="filters.ekstrakurikuler_id"
      :ekskul-list="ekskulList"
      @update:search="debouncedFetch"
      @update:status="fetchRegistrations"
      @update:ekskul-id="fetchRegistrations"
    />

    <PendaftarListTable
      :items="registrations"
      :loading="isLoading"
      @view="viewDetail"
      @approve="handleApprove"
      @reject="handleReject"
      @delete="handleDelete"
    />

    <PendaftarListModals
      :show-detail="showDetailModal"
      :show-reject="showRejectModal"
      :selected-registration="selectedRegistration"
      :processing="isProcessing"
      @close-detail="closeDetailModal"
      @close-reject="closeRejectModal"
      @approve="handleApprove"
      @reject="handleReject"
      @confirm-reject="confirmReject"
    />
  </div>
</template>

<script setup>
import api from '@/services/api'
import { usePopup } from '@/composables/usePopup'
import {
  approveRegistration,
  deleteRegistration,
  getDashboardStats,
  getRegistrations,
  rejectRegistration
} from '@/services/ekskulRegistrationService'
import { onMounted, reactive, ref } from 'vue'
import PendaftarListFilters from './pendaftarList/PendaftarListFilters.vue'
import PendaftarListHeader from './pendaftarList/PendaftarListHeader.vue'
import PendaftarListModals from './pendaftarList/PendaftarListModals.vue'
import PendaftarListStats from './pendaftarList/PendaftarListStats.vue'
import PendaftarListTable from './pendaftarList/PendaftarListTable.vue'

const { showSuccess, showError, confirm } = usePopup()

const registrations = ref([])
const ekskulList = ref([])
const stats = reactive({ pending: 0, approved: 0, rejected: 0 })
const filters = reactive({ search: '', status: '', ekstrakurikuler_id: '' })
const isLoading = ref(false)
const isProcessing = ref(false)

const showDetailModal = ref(false)
const showRejectModal = ref(false)
const selectedRegistration = ref(null)

let debounceTimer = null

const fetchRegistrations = async () => {
  isLoading.value = true
  try {
    const response = await getRegistrations(filters)
    registrations.value = response.data || []
  } catch (error) {
    await showError('Gagal Memuat Data', 'Gagal memuat data pendaftar')
  } finally {
    isLoading.value = false
  }
}

const fetchStats = async () => {
  try {
    const response = await getDashboardStats()
    Object.assign(stats, response.data)
  } catch (error) { /* silent */ }
}

const fetchEkskulList = async () => {
  try {
    const response = await api.get('/admin/ekstrakurikuler')
    ekskulList.value = response.data.data || []
  } catch (error) { /* silent */ }
}

const debouncedFetch = () => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(fetchRegistrations, 500)
}

const viewDetail = (reg) => { selectedRegistration.value = reg; showDetailModal.value = true }
const closeDetailModal = () => { showDetailModal.value = false; selectedRegistration.value = null }
const closeRejectModal = () => { showRejectModal.value = false; selectedRegistration.value = null }

const handleApprove = async (reg) => {
  const result = await confirm(`Terima pendaftaran dari ${reg.nama}?`, 'Pendaftar akan menerima email konfirmasi', 'Ya, Terima')
  if (!result.isConfirmed) return

  isProcessing.value = true
  try {
    await approveRegistration(reg.id)
    await showSuccess('Berhasil!', 'Pendaftaran berhasil diterima!')
    await fetchRegistrations()
    await fetchStats()
    closeDetailModal()
  } catch (error) {
    await showError('Gagal Menerima', error.response?.data?.message || 'Gagal menerima pendaftaran')
  } finally {
    isProcessing.value = false
  }
}

const handleReject = (reg) => { selectedRegistration.value = reg; showRejectModal.value = true }

const confirmReject = async (reason) => {
  if (!selectedRegistration.value) return
  isProcessing.value = true
  try {
    await rejectRegistration(selectedRegistration.value.id, reason)
    await showSuccess('Berhasil!', 'Pendaftaran berhasil ditolak!')
    await fetchRegistrations()
    await fetchStats()
    closeRejectModal()
    closeDetailModal()
  } catch (error) {
    await showError('Gagal Menolak', error.response?.data?.message || 'Gagal menolak pendaftaran')
  } finally {
    isProcessing.value = false
  }
}

const handleDelete = async (reg) => {
  const result = await confirm(`Hapus pendaftaran dari ${reg.nama}?`, 'Tindakan ini tidak dapat dibatalkan', 'Ya, Hapus')
  if (!result.isConfirmed) return

  try {
    await deleteRegistration(reg.id)
    await showSuccess('Berhasil!', 'Pendaftaran berhasil dihapus!')
    await fetchRegistrations()
    await fetchStats()
  } catch (error) {
    await showError('Gagal Menghapus', error.response?.data?.message || 'Gagal menghapus pendaftaran')
  }
}

onMounted(() => { fetchRegistrations(); fetchStats(); fetchEkskulList() })
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
