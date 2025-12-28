<template>
  <div class="space-y-6">
    <!-- Loading State -->
    <div v-if="initialLoading" class="flex flex-col items-center justify-center min-h-[400px]">
      <LoadingSpinner size="lg" color="blue" />
    </div>

    <!-- Content -->
    <template v-else>
      <!-- Header -->
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">
          Activity Logs
        </h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
          Pantau aktivitas admin di sistem
        </p>
      </div>

      <!-- Filters -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex flex-col md:flex-row gap-4">
          <!-- Search -->
          <div class="flex-1 relative">
            <Search class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Cari aktivitas..."
              class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white font-poppins"
            />
          </div>

          <!-- Action Filter -->
          <select
            v-model="actionFilter"
            class="px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white font-poppins"
          >
            <option value="all">Semua Aksi</option>
            <option v-for="action in actionList" :key="action" :value="action">
              {{ formatAction(action) }}
            </option>
          </select>

          <!-- User Filter -->
          <select
            v-model="userFilter"
            class="px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white font-poppins"
          >
            <option value="all">Semua User</option>
            <option v-for="user in userList" :key="user.user_id" :value="user.user_id">
              {{ user.user_name }}
            </option>
          </select>
        </div>
      </div>

      <!-- Table -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">
                  User
                </th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">
                  Aksi
                </th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">
                  Deskripsi
                </th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">
                  Waktu
                </th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">
                  IP
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
              <tr
                v-for="log in logs"
                :key="log.id"
                class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150 cursor-pointer"
                @click="showLogDetail(log)"
              >
                <!-- User -->
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white font-bold text-xs">
                      {{ log.user_name?.[0]?.toUpperCase() || 'S' }}
                    </div>
                    <span class="font-medium text-gray-900 dark:text-white font-poppins">
                      {{ log.user_name }}
                    </span>
                  </div>
                </td>

                <!-- Action -->
                <td class="px-6 py-4">
                  <span :class="getActionBadgeClass(log.action)" class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium font-poppins">
                    <component :is="getActionIcon(log.action)" class="w-3 h-3" />
                    {{ formatAction(log.action) }}
                  </span>
                </td>

                <!-- Description -->
                <td class="px-6 py-4 max-w-xs">
                  <p class="text-sm text-gray-900 dark:text-white font-poppins truncate">
                    {{ log.description }}
                  </p>
                  <p v-if="log.model_type" class="text-xs text-gray-500 dark:text-gray-400">
                    {{ log.model_type }} #{{ log.model_id }}
                  </p>
                </td>

                <!-- Time -->
                <td class="px-6 py-4">
                  <p class="text-sm text-gray-900 dark:text-white font-poppins">{{ log.time_ago }}</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">{{ log.created_at }}</p>
                </td>

                <!-- IP -->
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 font-mono">
                  {{ log.ip_address || '-' }}
                </td>
              </tr>

              <!-- Empty State -->
              <tr v-if="logs.length === 0">
                <td colspan="5" class="px-6 py-12 text-center">
                  <FileText class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" />
                  <p class="text-gray-500 dark:text-gray-400 font-poppins">Belum ada aktivitas tercatat</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="meta.total > 0" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
          <p class="text-sm text-gray-600 dark:text-gray-400 font-poppins">
            Menampilkan {{ logs.length }} dari {{ meta.total }} aktivitas
          </p>
          <div class="flex gap-2">
            <button
              @click="prevPage"
              :disabled="meta.current_page === 1"
              class="px-3 py-1 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed font-poppins text-sm"
            >
              Sebelumnya
            </button>
            <span class="px-3 py-1 text-sm text-gray-600 dark:text-gray-400 font-poppins">
              {{ meta.current_page }} / {{ meta.last_page }}
            </span>
            <button
              @click="nextPage"
              :disabled="meta.current_page === meta.last_page"
              class="px-3 py-1 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed font-poppins text-sm"
            >
              Selanjutnya
            </button>
          </div>
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
            v-if="selectedLog"
            class="fixed inset-0 z-[9999] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
            @click.self="selectedLog = null"
          >
            <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-2xl w-full max-h-[80vh] overflow-hidden border border-gray-200 dark:border-gray-700">
              <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white font-poppins">
                  Detail Aktivitas
                </h2>
                <button
                  @click="selectedLog = null"
                  class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                >
                  <X class="w-5 h-5 text-gray-500" />
                </button>
              </div>

              <div class="p-6 space-y-4 overflow-y-auto max-h-[60vh]">
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">User</p>
                    <p class="font-semibold text-gray-900 dark:text-white font-poppins">{{ selectedLog.user_name }}</p>
                  </div>
                  <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Aksi</p>
                    <span :class="getActionBadgeClass(selectedLog.action)" class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium font-poppins">
                      {{ formatAction(selectedLog.action) }}
                    </span>
                  </div>
                  <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Waktu</p>
                    <p class="font-semibold text-gray-900 dark:text-white font-poppins">{{ selectedLog.created_at }}</p>
                  </div>
                  <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">IP Address</p>
                    <p class="font-mono text-gray-900 dark:text-white">{{ selectedLog.ip_address || '-' }}</p>
                  </div>
                </div>

                <div>
                  <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Deskripsi</p>
                  <p class="text-gray-900 dark:text-white font-poppins">{{ selectedLog.description }}</p>
                </div>

                <!-- Changes (if any) -->
                <div v-if="selectedLog.has_changes && getChangedFields(selectedLog).length > 0" class="space-y-3">
                  <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase">Perubahan Data</p>
                  
                  <!-- Vertical column layout per field -->
                  <div class="space-y-3">
                    <div 
                      v-for="change in getChangedFields(selectedLog)" 
                      :key="change.field"
                      class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-3"
                    >
                      <!-- Field name -->
                      <p class="text-[10px] font-semibold text-gray-600 dark:text-gray-400 uppercase mb-2">
                        {{ formatFieldName(change.field) }}
                      </p>
                      
                      <!-- Stacked: Sebelum on top, Sesudah below -->
                      <div class="space-y-2">
                        <!-- Sebelum -->
                        <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-2">
                          <p class="text-[9px] font-semibold text-red-600 dark:text-red-400 mb-1">Sebelum</p>
                          <p class="text-[11px] text-red-800 dark:text-red-300 break-words whitespace-pre-wrap">{{ formatValue(change.old) }}</p>
                        </div>
                        
                        <!-- Sesudah -->
                        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-2">
                          <p class="text-[9px] font-semibold text-green-600 dark:text-green-400 mb-1">Sesudah</p>
                          <p class="text-[11px] text-green-800 dark:text-green-300 break-words whitespace-pre-wrap">{{ formatValue(change.new) }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- For create action, show summary instead of full data -->
                <div v-else-if="selectedLog.action === 'create' && selectedLog.new_values" class="space-y-3">
                  <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase">Data Dibuat</p>
                  <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-4">
                    <p class="text-sm text-green-700 dark:text-green-400">
                      Data baru berhasil dibuat dengan ID #{{ selectedLog.new_values.id || selectedLog.model_id }}
                    </p>
                  </div>
                </div>

                <!-- For delete action, show summary -->
                <div v-else-if="selectedLog.action === 'delete' && selectedLog.old_values" class="space-y-3">
                  <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase">Data Dihapus</p>
                  <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-4">
                    <p class="text-sm text-red-700 dark:text-red-400">
                      Data dengan ID #{{ selectedLog.old_values.id || selectedLog.model_id }} telah dihapus
                    </p>
                  </div>
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
import {
  FileText,
  LogIn,
  LogOut,
  Pencil,
  Plus,
  Search,
  Trash2,
  X
} from 'lucide-vue-next'
import { computed, onMounted, ref, watch } from 'vue'

const logs = ref([])
const actionList = ref([])
const userList = ref([])
const meta = ref({ current_page: 1, last_page: 1, total: 0 })
const initialLoading = ref(true)
const loading = ref(false)

const searchQuery = ref('')
const actionFilter = ref('all')
const userFilter = ref('all')
const currentPage = ref(1)

const selectedLog = ref(null)

// Fetch logs
const fetchLogs = async () => {
  try {
    loading.value = true
    const token = sessionStorage.getItem('admin_token')
    const params = new URLSearchParams({
      page: currentPage.value,
      per_page: 15,
      ...(actionFilter.value !== 'all' && { action: actionFilter.value }),
      ...(userFilter.value !== 'all' && { user_id: userFilter.value }),
      ...(searchQuery.value && { search: searchQuery.value })
    })

    const response = await fetch(`/api/yasmin-panel/activity-logs?${params}`, {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json'
      }
    })
    const data = await response.json()
    if (data.success) {
      logs.value = data.data
      meta.value = data.meta
    }
  } catch (error) {
    console.error('Error fetching logs:', error)
  } finally {
    loading.value = false
    initialLoading.value = false
  }
}

// Fetch filter options
const fetchFilters = async () => {
  try {
    const token = sessionStorage.getItem('admin_token')
    const headers = { Authorization: `Bearer ${token}`, Accept: 'application/json' }

    const [actionsRes, usersRes] = await Promise.all([
      fetch('/api/yasmin-panel/activity-logs/actions', { headers }),
      fetch('/api/yasmin-panel/activity-logs/users', { headers })
    ])

    const actionsData = await actionsRes.json()
    const usersData = await usersRes.json()

    if (actionsData.success) actionList.value = actionsData.data
    if (usersData.success) userList.value = usersData.data
  } catch (error) {
    console.error('Error fetching filters:', error)
  }
}

// Helpers
const formatAction = (action) => {
  const actionMap = {
    create: 'Buat',
    update: 'Update',
    delete: 'Hapus',
    login: 'Login',
    logout: 'Logout'
  }
  return actionMap[action] || action
}

const getActionBadgeClass = (action) => {
  const classes = {
    create: 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400',
    update: 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-400',
    delete: 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400',
    login: 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400',
    logout: 'bg-gray-50 dark:bg-gray-900/20 text-gray-700 dark:text-gray-400'
  }
  return classes[action] || 'bg-gray-50 dark:bg-gray-900/20 text-gray-700 dark:text-gray-400'
}

const getActionIcon = (action) => {
  const icons = { create: Plus, update: Pencil, delete: Trash2, login: LogIn, logout: LogOut }
  return icons[action] || FileText
}

// Get only the fields that changed between old and new values
const getChangedFields = (log) => {
  if (!log.old_values || !log.new_values) return []
  
  const changes = []
  const skipFields = ['updated_at', 'created_at', 'deleted_at', 'remember_token', 'email_verified_at']
  
  for (const key of Object.keys(log.new_values)) {
    if (skipFields.includes(key)) continue
    
    const oldVal = log.old_values[key]
    const newVal = log.new_values[key]
    
    // Compare values (stringify for objects/arrays)
    const oldStr = typeof oldVal === 'object' ? JSON.stringify(oldVal) : String(oldVal ?? '')
    const newStr = typeof newVal === 'object' ? JSON.stringify(newVal) : String(newVal ?? '')
    
    if (oldStr !== newStr) {
      changes.push({
        field: key,
        old: oldVal,
        new: newVal
      })
    }
  }
  
  return changes
}

// Format field name to human readable
const formatFieldName = (field) => {
  const fieldMap = {
    nama: 'Nama',
    title: 'Judul',
    content: 'Konten',
    excerpt: 'Ringkasan',
    image: 'Gambar',
    is_active: 'Status Aktif',
    is_featured: 'Unggulan',
    published: 'Dipublikasi',
    category: 'Kategori',
    deskripsi: 'Deskripsi',
    jadwal: 'Jadwal',
    pembina: 'Pembina',
    lokasi: 'Lokasi',
    max_peserta: 'Maks Peserta',
    role: 'Peran',
    email: 'Email',
    name: 'Nama',
    author: 'Penulis',
    location: 'Lokasi',
    views: 'Views',
    slug: 'Slug',
    priority: 'Prioritas',
    order: 'Urutan'
  }
  return fieldMap[field] || field.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
}

// Truncate long values for display (legacy, not used anymore)
const truncateValue = (value) => {
  if (value === null || value === undefined) return '-'
  if (typeof value === 'boolean') return value ? 'Ya' : 'Tidak'
  if (typeof value === 'object') return '[Data Kompleks]'
  
  const str = String(value)
  if (str.length > 50) {
    return str.substring(0, 50) + '...'
  }
  return str
}

// Format value for full display (no truncation)
const formatValue = (value) => {
  if (value === null || value === undefined) return '-'
  if (typeof value === 'boolean') return value ? 'Ya' : 'Tidak'
  if (Array.isArray(value)) return value.join(', ')
  if (typeof value === 'object') return JSON.stringify(value, null, 2)
  return String(value)
}

const showLogDetail = (log) => {
  selectedLog.value = log
}

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
    fetchLogs()
  }
}

const nextPage = () => {
  if (currentPage.value < meta.value.last_page) {
    currentPage.value++
    fetchLogs()
  }
}

// Watch filters
watch([actionFilter, userFilter], () => {
  currentPage.value = 1
  fetchLogs()
})

// Debounced search
let searchTimeout
watch(searchQuery, () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    currentPage.value = 1
    fetchLogs()
  }, 500)
})

onMounted(() => {
  fetchFilters()
  fetchLogs()
})
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}

/* Modern scrollbar styling */
.overflow-x-auto,
.overflow-y-auto {
  scrollbar-width: thin;
  scrollbar-color: rgba(156, 163, 175, 0.3) transparent;
}

.overflow-x-auto::-webkit-scrollbar,
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track,
.overflow-y-auto::-webkit-scrollbar-track {
  background: transparent;
}

.overflow-x-auto::-webkit-scrollbar-thumb,
.overflow-y-auto::-webkit-scrollbar-thumb {
  background-color: rgba(156, 163, 175, 0.3);
  border-radius: 10px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover,
.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background-color: rgba(156, 163, 175, 0.5);
}

/* Dark mode scrollbar */
.dark .overflow-x-auto,
.dark .overflow-y-auto {
  scrollbar-color: rgba(75, 85, 99, 0.5) transparent;
}

.dark .overflow-x-auto::-webkit-scrollbar-thumb,
.dark .overflow-y-auto::-webkit-scrollbar-thumb {
  background-color: rgba(75, 85, 99, 0.5);
}

.dark .overflow-x-auto::-webkit-scrollbar-thumb:hover,
.dark .overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background-color: rgba(75, 85, 99, 0.7);
}

/* Pre element scrollbar for JSON display */
pre {
  scrollbar-width: thin;
  scrollbar-color: rgba(156, 163, 175, 0.3) transparent;
}

pre::-webkit-scrollbar {
  height: 4px;
}

pre::-webkit-scrollbar-track {
  background: transparent;
}

pre::-webkit-scrollbar-thumb {
  background-color: rgba(156, 163, 175, 0.3);
  border-radius: 10px;
}

pre::-webkit-scrollbar-thumb:hover {
  background-color: rgba(156, 163, 175, 0.5);
}
</style>

