<template>
  <div class="space-y-6">
    <!-- Loading State -->
    <div v-if="initialLoading" class="flex flex-col items-center justify-center min-h-[400px]">
      <LoadingSpinner size="lg" color="blue" />
    </div>

    <!-- Content (shown when not loading) -->
    <template v-else>
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">
          Pendaftar Ekstrakurikuler
        </h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Kelola pendaftaran siswa Ekstrakurikuler</p>
      </div>
    </div>

    <!-- Stats (Dashboard Style) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
      <!-- Menunggu -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-300">
        <div class="flex items-center justify-between mb-4">
          <div class="w-12 h-12 rounded-xl bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center">
            <Clock class="w-6 h-6 text-orange-600 dark:text-orange-400" />
          </div>
          <span class="text-xs font-medium text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-900/20 px-3 py-1 rounded-full">Menunggu</span>
        </div>
        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1 font-poppins">{{ pendingCount }}</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 font-poppins">Perlu Persetujuan</p>
      </div>

      <!-- Disetujui -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-300">
        <div class="flex items-center justify-between mb-4">
          <div class="w-12 h-12 rounded-xl bg-green-50 dark:bg-green-900/20 flex items-center justify-center">
            <CheckCircle class="w-6 h-6 text-green-600 dark:text-green-400" />
          </div>
          <span class="text-xs font-medium text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 px-3 py-1 rounded-full">Disetujui</span>
        </div>
        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1 font-poppins">{{ approvedCount }}</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 font-poppins">Pendaftar Diterima</p>
      </div>

      <!-- Ditolak -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-300">
        <div class="flex items-center justify-between mb-4">
          <div class="w-12 h-12 rounded-xl bg-red-50 dark:bg-red-900/20 flex items-center justify-center">
            <XCircle class="w-6 h-6 text-red-600 dark:text-red-400" />
          </div>
          <span class="text-xs font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 px-3 py-1 rounded-full">Ditolak</span>
        </div>
        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1 font-poppins">{{ rejectedCount }}</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 font-poppins">Pendaftar Ditolak</p>
      </div>
    </div>

    <!-- Filters -->
    <div
      class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700"
    >
      <div class="flex flex-col md:flex-row gap-4">
        <!-- Search -->
        <div class="flex-1 relative">
          <Search class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Cari nama siswa..."
            class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 text-gray-900 dark:text-white font-poppins"
          />
        </div>

        <!-- Ekskul Filter -->
        <select
          v-model="ekskulFilter"
          class="px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 text-gray-900 dark:text-white font-poppins"
        >
          <option value="">Semua Ekskul</option>
          <option v-for="ekskul in ekskulList" :key="ekskul.id" :value="ekskul.nama">
            {{ ekskul.nama }}
          </option>
        </select>

        <!-- Status Filter -->
        <select
          v-model="statusFilter"
          class="px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 text-gray-900 dark:text-white font-poppins"
        >
          <option value="">Semua Status</option>
          <option value="pending">Pending</option>
          <option value="approved">Approved</option>
          <option value="rejected">Rejected</option>
        </select>
      </div>
    </div>

    <!-- Table -->
    <div
      class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden"
    >
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
            <tr>
              <th
                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins"
              >
                Nama Siswa
              </th>
              <th
                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins"
              >
                Kelas
              </th>
              <th
                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins"
              >
                Ekskul
              </th>
              <th
                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins"
              >
                Tanggal
              </th>
              <th
                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins"
              >
                Status
              </th>
              <th
                class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins"
              >
                Action
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            <tr
              v-for="pendaftar in filteredPendaftar"
              :key="pendaftar.id"
              class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150"
            >
              <!-- Nama -->
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div
                    class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white font-bold text-sm"
                  >
                    {{ pendaftar.nama[0].toUpperCase() }}
                  </div>
                  <div>
                    <p class="font-semibold text-gray-900 dark:text-white font-poppins">
                      {{ pendaftar.nama }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ pendaftar.email }}</p>
                  </div>
                </div>
              </td>

              <!-- Kelas -->
              <td class="px-6 py-4 text-sm text-gray-900 dark:text-white font-poppins">
                {{ pendaftar.kelas }}
              </td>

              <!-- Ekskul -->
              <td class="px-6 py-4 text-sm text-gray-900 dark:text-white font-poppins">
                {{ pendaftar.ekskul }}
              </td>

              <!-- Tanggal -->
              <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 font-poppins">
                {{ pendaftar.tanggal }}
              </td>

              <!-- Status -->
              <td class="px-6 py-4">
                <span
                  v-if="pendaftar.status === 'pending'"
                  class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-400 font-poppins"
                >
                  <div class="w-2 h-2 rounded-full bg-orange-500"></div>
                  Pending
                </span>
                <span
                  v-else-if="pendaftar.status === 'approved'"
                  class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 font-poppins"
                >
                  <div class="w-2 h-2 rounded-full bg-green-500"></div>
                  Approved
                </span>
                <span
                  v-else
                  class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 font-poppins"
                >
                  <div class="w-2 h-2 rounded-full bg-red-500"></div>
                  Rejected
                </span>
              </td>

              <!-- Actions -->
              <td class="px-6 py-4">
                <div class="flex items-center justify-center">
                  <button
                    @click="handleDetail(pendaftar)"
                    class="flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors duration-200 font-medium font-poppins text-sm"
                  >
                    <Eye class="w-4 h-4" />
                    Lihat Detail
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Detail Modal -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition-opacity duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-if="showDetail"
          class="fixed inset-0 z-[9999] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
        >
          <div
            class="bg-white dark:bg-gray-800 rounded-2xl max-w-lg w-full p-6 border border-gray-200 dark:border-gray-700"
          >
            <div class="flex items-center justify-between mb-6">
              <h2 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">
                Detail Pendaftar
              </h2>
              <button
                @click="closeDetail"
                class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
              >
                <X class="w-5 h-5 text-gray-500" />
              </button>
            </div>

            <div v-if="selectedPendaftar" class="space-y-4">
              <div
                class="flex items-center gap-4 pb-4 border-b border-gray-200 dark:border-gray-700"
              >
                <div
                  class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white font-bold text-2xl"
                >
                  {{ selectedPendaftar.nama[0].toUpperCase() }}
                </div>
                <div>
                  <p class="text-xl font-bold text-gray-900 dark:text-white font-poppins">
                    {{ selectedPendaftar.nama }}
                  </p>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ selectedPendaftar.kelas }}
                  </p>
                </div>
              </div>

              <div class="space-y-3">
                <div class="flex items-center gap-3">
                  <Target class="w-5 h-5 text-gray-400" />
                  <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Ekskul</p>
                    <p class="font-semibold text-gray-900 dark:text-white font-poppins">
                      {{ selectedPendaftar.ekskul }}
                    </p>
                  </div>
                </div>

                <div class="flex items-center gap-3">
                  <Phone class="w-5 h-5 text-gray-400" />
                  <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">No. HP</p>
                    <p class="font-semibold text-gray-900 dark:text-white font-poppins">
                      {{ selectedPendaftar.noHp }}
                    </p>
                  </div>
                </div>

                <div class="flex items-center gap-3">
                  <Mail class="w-5 h-5 text-gray-400" />
                  <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Email</p>
                    <p class="font-semibold text-gray-900 dark:text-white font-poppins">
                      {{ selectedPendaftar.email }}
                    </p>
                  </div>
                </div>

                <div class="flex items-start gap-3">
                  <MessageSquare class="w-5 h-5 text-gray-400 mt-1 flex-shrink-0" />
                  <div class="min-w-0 flex-1">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Alasan Bergabung</p>
                    <p class="text-sm text-gray-900 dark:text-white font-poppins break-words whitespace-pre-wrap">
                      {{ selectedPendaftar.alasan }}
                    </p>
                  </div>
                </div>

                <div class="flex items-center gap-3">
                  <Calendar class="w-5 h-5 text-gray-400" />
                  <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Tanggal Daftar</p>
                    <p class="font-semibold text-gray-900 dark:text-white font-poppins">
                      {{ selectedPendaftar.tanggal }}
                    </p>
                  </div>
                </div>
              </div>

              <div
                v-if="selectedPendaftar.status === 'pending'"
                class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-700"
              >
                <button
                  @click="handleApprove(selectedPendaftar.id)"
                  class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold transition-colors duration-200 font-poppins"
                >
                  <Check class="w-5 h-5" />
                  Terima
                </button>
                <button
                  @click="handleReject(selectedPendaftar.id)"
                  class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-semibold transition-colors duration-200 font-poppins"
                >
                  <X class="w-5 h-5" />
                  Tolak
                </button>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
    </template>
  </div>
</template>

<script setup>
import LoadingSpinner from '@/components/ui/shared/LoadingSpinner.vue'
import { usePopup } from '@/composables/usePopup'
import {
  Calendar,
  Check,
  CheckCircle,
  Clock,
  Eye,
  Mail,
  MessageSquare,
  Phone,
  Search,
  Target,
  X,
  XCircle
} from 'lucide-vue-next'
import { computed, onMounted, onUnmounted, ref } from 'vue'

const { confirm, showSuccess, showError, showPrompt } = usePopup()

const searchQuery = ref('')
const ekskulFilter = ref('')
const statusFilter = ref('pending') // Default filter to pending for better UX
const showDetail = ref(false)
const selectedPendaftar = ref(null)
const pendaftarList = ref([])
const ekskulList = ref([])
const loading = ref(true)
const initialLoading = ref(true) // For showing loading spinner only on first load

// Fetch registrations from API
const fetchRegistrations = async () => {
  try {
    loading.value = true
    const token = sessionStorage.getItem('admin_token')
    const response = await fetch('/api/yasmin-panel/ekstrakurikuler/registrations', {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json'
      }
    })
    const data = await response.json()
    if (data.success) {
      pendaftarList.value = data.data.map((item) => ({
        id: item.id,
        nama: item.nama_lengkap,
        kelas: item.kelas,
        email: item.email,
        noHp: item.no_whatsapp || '-',
        ekskul: item.ekstrakurikuler?.nama || 'N/A',
        alasan: item.alasan_bergabung || '-',
        tanggal: new Date(item.created_at).toLocaleDateString('id-ID', {
          day: 'numeric',
          month: 'short',
          year: 'numeric'
        }),
        status: item.status
      }))
    }
  } catch (error) {
    console.error('Error fetching registrations:', error)
  } finally {
    loading.value = false
    initialLoading.value = false // Disable after first load
  }
}

// Fetch ekskul list for filter dropdown
const fetchEkskulList = async () => {
  try {
    const token = sessionStorage.getItem('admin_token')
    const response = await fetch('/api/yasmin-panel/ekstrakurikuler', {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json'
      }
    })
    const data = await response.json()
    if (data.success && Array.isArray(data.data)) {
      ekskulList.value = data.data.map((item) => ({
        id: item.id,
        nama: item.nama
      }))
    }
  } catch (error) {
    console.error('Error fetching ekskul list:', error)
  }
}

// Handle visibility change - refresh when tab becomes visible
const handleVisibilityChange = () => {
  if (!document.hidden) {
    fetchRegistrations()
  }
}

let refreshInterval = null

const filteredPendaftar = computed(() => {
  return pendaftarList.value.filter((p) => {
    const matchSearch = p.nama.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchEkskul = ekskulFilter.value === '' || p.ekskul === ekskulFilter.value
    const matchStatus = statusFilter.value === '' || p.status === statusFilter.value
    return matchSearch && matchEkskul && matchStatus
  })
})

const pendingCount = computed(
  () => pendaftarList.value.filter((p) => p.status === 'pending').length
)
const approvedCount = computed(
  () => pendaftarList.value.filter((p) => p.status === 'approved').length
)
const rejectedCount = computed(
  () => pendaftarList.value.filter((p) => p.status === 'rejected').length
)

const handleApprove = async (id) => {
  const result = await confirm(
    'Terima Pendaftar?',
    'Pendaftar akan disetujui untuk bergabung',
    'Ya, Terima!'
  )

  if (!result.isConfirmed) return

  try {
    const token = sessionStorage.getItem('admin_token')
    const response = await fetch(`/api/yasmin-panel/ekstrakurikuler/registrations/${id}/approve`, {
      method: 'PUT',
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json'
      }
    })

    if (!response.ok) throw new Error('Failed to approve')

    await showSuccess('Berhasil!', 'Pendaftar berhasil diterima')

    if (showDetail.value) closeDetail()
    await fetchRegistrations()
  } catch (error) {
    console.error('Error approving:', error)
    await showError('Gagal!', 'Gagal menerima pendaftar')
  }
}


const handleReject = async (id) => {
  const result = await confirm('Tolak Pendaftar?', 'Pendaftar akan ditolak', 'Ya, Tolak!')

  if (!result.isConfirmed) return

  // Prompt for rejection reason using custom popup
  const promptResult = await showPrompt('Alasan Penolakan', {
    inputLabel: 'Masukkan alasan penolakan (minimal 10 karakter)',
    inputPlaceholder: 'Contoh: Tidak memenuhi kriteria usia minimum',
    confirmText: 'Tolak',
    cancelText: 'Batal',
    inputValidator: (value) => {
      if (!value) {
        return 'Alasan penolakan harus diisi!'
      }
      if (value.length < 10) {
        return 'Alasan penolakan minimal 10 karakter!'
      }
      if (value.length > 1000) {
        return 'Alasan penolakan maksimal 1000 karakter!'
      }
      return null
    }
  })

  if (!promptResult.isConfirmed || !promptResult.value) return

  try {
    const token = sessionStorage.getItem('admin_token')
    const response = await fetch(`/api/yasmin-panel/ekstrakurikuler/registrations/${id}/reject`, {
      method: 'PUT',
      headers: {
        Authorization: `Bearer ${token}`,
        'Content-Type': 'application/json',
        Accept: 'application/json'
      },
      body: JSON.stringify({
        admin_notes: promptResult.value
      })
    })

    if (!response.ok) throw new Error('Failed to reject')

    await showSuccess('Berhasil!', 'Pendaftar berhasil ditolak')

    if (showDetail.value) closeDetail()
    await fetchRegistrations()
  } catch (error) {
    console.error('Error rejecting:', error)
    await showError('Gagal!', 'Gagal menolak pendaftar')
  }
}

const handleDetail = (pendaftar) => {
  selectedPendaftar.value = pendaftar
  showDetail.value = true
}

const closeDetail = () => {
  showDetail.value = false
  selectedPendaftar.value = null
}

// Lifecycle
onMounted(() => {
  fetchEkskulList()
  fetchRegistrations()
  document.addEventListener('visibilitychange', handleVisibilityChange)
  // Super fast polling (3s) for near-instant updates
  refreshInterval = setInterval(fetchRegistrations, 3000)
})

onUnmounted(() => {
  document.removeEventListener('visibilitychange', handleVisibilityChange)
  if (refreshInterval) {
    clearInterval(refreshInterval)
  }
})
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
