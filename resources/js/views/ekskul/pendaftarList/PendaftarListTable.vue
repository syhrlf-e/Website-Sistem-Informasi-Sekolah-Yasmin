/**
 * @component PendaftarListTable
 * @description Table untuk menampilkan data pendaftar ekstrakurikuler
 */

<template>
  <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <!-- Loading State -->
    <div v-if="loading" class="p-12">
      <LoadingSpinner size="md" color="emerald" />
    </div>

    <!-- Empty State -->
    <div v-else-if="items.length === 0" class="p-12 text-center">
      <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
        <Users class="w-8 h-8 text-gray-400" />
      </div>
      <p class="text-gray-600 dark:text-gray-400 font-poppins">Belum ada pendaftar</p>
    </div>

    <!-- Table Content -->
    <div v-else class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50 dark:bg-gray-900/50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">Nama</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">Ekstrakurikuler</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">Kelas</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">Status</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">Tanggal</th>
            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider font-poppins">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          <tr v-for="registration in items" :key="registration.id" class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors">
            <td class="px-6 py-4">
              <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white font-poppins">{{ registration.nama }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-poppins">{{ registration.email }}</p>
              </div>
            </td>
            <td class="px-6 py-4">
              <p class="text-sm text-gray-900 dark:text-white font-poppins">{{ registration.ekstrakurikuler?.name || '-' }}</p>
            </td>
            <td class="px-6 py-4">
              <p class="text-sm text-gray-900 dark:text-white font-poppins">{{ registration.kelas }}</p>
            </td>
            <td class="px-6 py-4">
              <span :class="['px-3 py-1 rounded-full text-xs font-semibold font-poppins', getStatusClass(registration.status)]">
                {{ getStatusLabel(registration.status) }}
              </span>
            </td>
            <td class="px-6 py-4">
              <p class="text-sm text-gray-900 dark:text-white font-poppins">{{ formatDate(registration.created_at) }}</p>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center justify-end gap-2">
                <button @click="$emit('view', registration)" class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg" title="Lihat Detail">
                  <Eye class="w-4 h-4" />
                </button>
                <button v-if="registration.status === 'pending'" @click="$emit('approve', registration)" class="p-2 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/30 rounded-lg" title="Terima">
                  <CheckCircle class="w-4 h-4" />
                </button>
                <button v-if="registration.status === 'pending'" @click="$emit('reject', registration)" class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg" title="Tolak">
                  <XCircle class="w-4 h-4" />
                </button>
                <button @click="$emit('delete', registration)" class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg" title="Hapus">
                  <Trash2 class="w-4 h-4" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import LoadingSpinner from '@/components/ui/shared/LoadingSpinner.vue'
import { formatStatus } from '@/services/ekskulRegistrationService'
import { CheckCircle, Eye, Trash2, Users, XCircle } from 'lucide-vue-next'

defineProps({
  items: { type: Array, required: true },
  loading: { type: Boolean, default: false }
})

defineEmits(['view', 'approve', 'reject', 'delete'])

const getStatusClass = (status) => {
  const statusFormat = formatStatus(status)
  return `${statusFormat.bgClass} ${statusFormat.textClass}`
}

const getStatusLabel = (status) => formatStatus(status).label

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
