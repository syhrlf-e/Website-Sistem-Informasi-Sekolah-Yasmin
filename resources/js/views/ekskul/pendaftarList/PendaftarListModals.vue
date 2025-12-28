/**
 * @component PendaftarListModals
 * @description Modal detail dan reject untuk pendaftar ekstrakurikuler
 */

<template>
  <!-- Detail Modal -->
  <Transition enter-active-class="transition-opacity duration-300" enter-from-class="opacity-0" leave-active-class="transition-opacity duration-200" leave-to-class="opacity-0">
    <div v-if="showDetail" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" @click.self="$emit('close-detail')">
      <div class="bg-white dark:bg-gray-900 rounded-3xl p-8 max-w-2xl w-full shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">Detail Pendaftar</h3>
          <button @click="$emit('close-detail')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg">
            <X class="w-5 h-5 text-gray-500" />
          </button>
        </div>

        <div v-if="selectedRegistration" class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400 font-poppins">Nama</p>
              <p class="text-base font-semibold text-gray-900 dark:text-white font-poppins">{{ selectedRegistration.nama }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400 font-poppins">Kelas</p>
              <p class="text-base font-semibold text-gray-900 dark:text-white font-poppins">{{ selectedRegistration.kelas }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400 font-poppins">Email</p>
              <p class="text-base font-semibold text-gray-900 dark:text-white font-poppins">{{ selectedRegistration.email }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400 font-poppins">Telepon</p>
              <p class="text-base font-semibold text-gray-900 dark:text-white font-poppins">{{ selectedRegistration.phone }}</p>
            </div>
          </div>

          <div>
            <p class="text-sm text-gray-600 dark:text-gray-400 font-poppins mb-2">Ekstrakurikuler</p>
            <p class="text-base font-semibold text-gray-900 dark:text-white font-poppins">{{ selectedRegistration.ekstrakurikuler?.name || '-' }}</p>
          </div>

          <div>
            <p class="text-sm text-gray-600 dark:text-gray-400 font-poppins mb-2">Alasan Bergabung</p>
            <p class="text-base text-gray-900 dark:text-white font-poppins leading-relaxed">{{ selectedRegistration.alasan }}</p>
          </div>

          <div>
            <p class="text-sm text-gray-600 dark:text-gray-400 font-poppins mb-2">Status</p>
            <span :class="['px-3 py-1 rounded-full text-sm font-semibold', getStatusClass(selectedRegistration.status)]">
              {{ getStatusLabel(selectedRegistration.status) }}
            </span>
          </div>

          <div v-if="selectedRegistration.rejection_reason">
            <p class="text-sm text-gray-600 dark:text-gray-400 font-poppins mb-2">Alasan Penolakan</p>
            <p class="text-base text-red-600 dark:text-red-400 font-poppins">{{ selectedRegistration.rejection_reason }}</p>
          </div>

          <div class="flex gap-3 pt-4">
            <button v-if="selectedRegistration.status === 'pending'" @click="$emit('approve', selectedRegistration)"
              class="flex-1 px-6 py-3 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700">Terima</button>
            <button v-if="selectedRegistration.status === 'pending'" @click="$emit('reject', selectedRegistration)"
              class="flex-1 px-6 py-3 bg-red-600 text-white rounded-xl font-semibold hover:bg-red-700">Tolak</button>
            <button @click="$emit('close-detail')"
              class="flex-1 px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl font-semibold">Tutup</button>
          </div>
        </div>
      </div>
    </div>
  </Transition>

  <!-- Reject Modal -->
  <Transition enter-active-class="transition-opacity duration-300" enter-from-class="opacity-0" leave-active-class="transition-opacity duration-200" leave-to-class="opacity-0">
    <div v-if="showReject" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" @click.self="$emit('close-reject')">
      <div class="bg-white dark:bg-gray-900 rounded-3xl p-8 max-w-md w-full shadow-2xl">
        <h3 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins mb-4">Tolak Pendaftaran</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 font-poppins mb-4">Berikan alasan penolakan (opsional):</p>
        <textarea
          v-model="localRejectReason"
          rows="4"
          placeholder="Contoh: Kuota sudah penuh"
          class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl resize-none"
        ></textarea>
        <div class="flex gap-3 mt-6">
          <button @click="$emit('close-reject')" class="flex-1 px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl font-semibold">Batal</button>
          <button @click="$emit('confirm-reject', localRejectReason)" :disabled="processing"
            class="flex-1 px-6 py-3 bg-red-600 text-white rounded-xl font-semibold disabled:opacity-50 flex items-center justify-center gap-2">
            <Loader2 v-if="processing" class="w-5 h-5 animate-spin" />
            {{ processing ? 'Memproses...' : 'Tolak' }}
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { formatStatus } from '@/services/ekskulRegistrationService'
import { Loader2, X } from 'lucide-vue-next'
import { ref, watch } from 'vue'

const props = defineProps({
  showDetail: Boolean,
  showReject: Boolean,
  selectedRegistration: Object,
  processing: Boolean
})

defineEmits(['close-detail', 'close-reject', 'approve', 'reject', 'confirm-reject'])

const localRejectReason = ref('')

watch(() => props.showReject, (val) => { if (!val) localRejectReason.value = '' })

const getStatusClass = (status) => {
  const statusFormat = formatStatus(status)
  return `${statusFormat.bgClass} ${statusFormat.textClass}`
}
const getStatusLabel = (status) => formatStatus(status).label
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
