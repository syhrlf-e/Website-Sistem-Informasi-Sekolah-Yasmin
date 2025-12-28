/**
 * @component DashboardModals
 * @description Modals untuk dashboard (reject dan detail)
 */

<template>
  <!-- Reject Modal -->
  <Teleport to="body">
    <Transition enter-active-class="transition-opacity duration-300" enter-from-class="opacity-0" leave-active-class="transition-opacity duration-200" leave-to-class="opacity-0">
      <div v-if="showReject" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4" @click.self="$emit('close-reject')">
        <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 shadow-2xl">
          <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 font-poppins">Tolak Pendaftaran</h3>
          <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 font-poppins">
            Berikan alasan penolakan untuk <strong>{{ selectedPendaftar?.nama }}</strong>
          </p>
          <textarea v-model="localRejectNotes" rows="4" placeholder="Masukkan alasan penolakan..."
            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl resize-none text-gray-900 dark:text-white font-poppins"></textarea>
          <div class="flex gap-3 mt-4">
            <button @click="$emit('close-reject')" class="flex-1 px-4 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-semibold font-poppins">Batal</button>
            <button @click="$emit('confirm-reject', localRejectNotes)" class="flex-1 px-4 py-3 bg-red-600 text-white rounded-xl font-semibold font-poppins">Tolak</button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>

  <!-- Detail Modal -->
  <Teleport to="body">
    <Transition enter-active-class="transition-opacity duration-300" enter-from-class="opacity-0" leave-active-class="transition-opacity duration-200" leave-to-class="opacity-0">
      <div v-if="showDetail" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4" @click.self="$emit('close-detail')">
        <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto shadow-2xl">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">Detail Pendaftar</h3>
            <button @click="$emit('close-detail')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
              <X class="w-5 h-5 text-gray-500" />
            </button>
          </div>

          <div v-if="selectedPendaftar" class="space-y-4">
            <div>
              <label class="text-sm text-gray-600 dark:text-gray-400 font-poppins">Nama Lengkap</label>
              <p class="font-semibold text-gray-900 dark:text-white font-poppins">{{ selectedPendaftar.nama_lengkap }}</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="text-sm text-gray-600 dark:text-gray-400 font-poppins">Kelas</label>
                <p class="font-semibold text-gray-900 dark:text-white font-poppins">{{ selectedPendaftar.kelas }}</p>
              </div>
              <div>
                <label class="text-sm text-gray-600 dark:text-gray-400 font-poppins">Email</label>
                <p class="font-semibold text-gray-900 dark:text-white font-poppins">{{ selectedPendaftar.email }}</p>
              </div>
            </div>
            <div>
              <label class="text-sm text-gray-600 dark:text-gray-400 font-poppins">No. WhatsApp</label>
              <p class="font-semibold text-gray-900 dark:text-white font-poppins">{{ selectedPendaftar.no_whatsapp }}</p>
            </div>
            <div>
              <label class="text-sm text-gray-600 dark:text-gray-400 font-poppins">Ekstrakurikuler</label>
              <p class="font-semibold text-gray-900 dark:text-white font-poppins">{{ selectedPendaftar.ekstrakurikuler?.nama }}</p>
            </div>
            <div>
              <label class="text-sm text-gray-600 dark:text-gray-400 font-poppins">Alasan Bergabung</label>
              <p class="text-gray-900 dark:text-white font-poppins leading-relaxed">{{ selectedPendaftar.alasan_bergabung }}</p>
            </div>
            <div>
              <label class="text-sm text-gray-600 dark:text-gray-400 font-poppins">Waktu Pendaftaran</label>
              <p class="font-semibold text-gray-900 dark:text-white font-poppins">{{ selectedPendaftar.created_at }}</p>
            </div>
          </div>

          <div class="flex gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <button @click="$emit('approve-from-modal')" class="flex-1 px-4 py-3 bg-green-600 text-white rounded-xl font-semibold font-poppins flex items-center justify-center gap-2">
              <Check class="w-5 h-5" />
              Approve
            </button>
            <button @click="$emit('reject-from-modal')" class="flex-1 px-4 py-3 bg-red-600 text-white rounded-xl font-semibold font-poppins flex items-center justify-center gap-2">
              <X class="w-5 h-5" />
              Reject
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { Check, X } from 'lucide-vue-next'
import { ref, watch } from 'vue'

const props = defineProps({
  showReject: Boolean,
  showDetail: Boolean,
  selectedPendaftar: Object
})

defineEmits(['close-reject', 'close-detail', 'confirm-reject', 'approve-from-modal', 'reject-from-modal'])

const localRejectNotes = ref('')

watch(() => props.showReject, (val) => { if (!val) localRejectNotes.value = '' })
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
