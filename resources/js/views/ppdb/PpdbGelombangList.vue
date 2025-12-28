/**
 * @component PpdbGelombangList
 * @description Kelola gelombang pendaftaran PPDB
 * @route /yasmin-panel/ppdb/gelombang
 */

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">Gelombang Pendaftaran</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Kelola periode pendaftaran PPDB</p>
      </div>
      <button
        @click="openModal()"
        class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition-colors"
      >
        <Plus class="w-5 h-5" />
        Tambah Gelombang
      </button>
    </div>

    <!-- Waves List -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
      <div v-if="isLoading" class="flex items-center justify-center py-12">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      </div>

      <div v-else-if="!waves.length" class="text-center py-12">
        <CalendarX class="w-12 h-12 mx-auto text-gray-400 mb-3" />
        <p class="text-gray-500 dark:text-gray-400">Belum ada gelombang pendaftaran</p>
      </div>

      <div v-else class="divide-y divide-gray-200 dark:divide-gray-700">
        <div
          v-for="wave in waves"
          :key="wave.id"
          class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
        >
          <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex-1">
              <div class="flex items-center gap-3">
                <h3 class="font-semibold text-gray-900 dark:text-white">{{ wave.name }}</h3>
                <span
                  :class="wave.is_open ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'"
                  class="px-2 py-0.5 rounded-full text-xs font-medium"
                >
                  {{ wave.is_open ? 'Dibuka' : 'Ditutup' }}
                </span>
              </div>
              <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                {{ wave.academic_year }} • {{ formatDate(wave.start_date) }} - {{ formatDate(wave.end_date) }}
              </p>
              <div class="flex items-center gap-4 mt-2 text-sm">
                <span class="text-gray-600 dark:text-gray-300">
                  <strong>{{ wave.registrations_count }}</strong> pendaftar
                </span>
                <span v-if="wave.quota" class="text-gray-500">
                  Kuota: {{ wave.available_slots }}/{{ wave.quota }}
                </span>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <button
                @click="toggleActive(wave)"
                :class="wave.is_active ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-green-100 text-green-700 hover:bg-green-200'"
                class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors"
              >
                {{ wave.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
              </button>
              <button
                @click="openModal(wave)"
                class="px-3 py-1.5 bg-blue-100 text-blue-700 hover:bg-blue-200 rounded-lg text-sm font-medium transition-colors"
              >
                Edit
              </button>
              <button
                @click="deleteWave(wave)"
                :disabled="wave.registrations_count > 0"
                class="px-3 py-1.5 bg-red-100 text-red-700 hover:bg-red-200 rounded-lg text-sm font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Hapus
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <Teleport to="body">
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 max-w-lg w-full shadow-xl max-h-[90vh] overflow-y-auto">
          <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
            {{ editingWave ? 'Edit Gelombang' : 'Tambah Gelombang' }}
          </h3>
          
          <form @submit.prevent="saveWave" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Gelombang</label>
              <input v-model="form.name" type="text" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="Gelombang 1" />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tahun Ajaran</label>
              <input v-model="form.academic_year" type="text" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="2025/2026" />
            </div>
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Mulai</label>
                <input v-model="form.start_date" type="date" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Selesai</label>
                <input v-model="form.end_date" type="date" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" />
              </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kuota (opsional)</label>
                <input v-model="form.quota" type="number" min="1" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="100" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Biaya (opsional)</label>
                <input v-model="form.fee" type="number" min="0" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="500000" />
              </div>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi</label>
              <textarea v-model="form.description" rows="2" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="Info tambahan..."></textarea>
            </div>

            <div class="flex items-center gap-2">
              <input v-model="form.is_active" type="checkbox" id="is_active" class="rounded" />
              <label for="is_active" class="text-sm text-gray-700 dark:text-gray-300">Aktif</label>
            </div>
            
            <div class="flex gap-3 pt-2">
              <button type="button" @click="showModal = false" class="flex-1 px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-medium">
                Batal
              </button>
              <button type="submit" :disabled="isSaving" class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium disabled:opacity-50">
                {{ isSaving ? 'Menyimpan...' : 'Simpan' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { CalendarX, Plus } from 'lucide-vue-next'
import { onMounted, reactive, ref } from 'vue'
import api from '@/services/api'
import { usePopup } from '@/composables/usePopup'

const { showSuccess, showError, confirm } = usePopup()

const isLoading = ref(true)
const waves = ref([])
const showModal = ref(false)
const editingWave = ref(null)
const isSaving = ref(false)

const form = reactive({
  name: '',
  academic_year: '',
  start_date: '',
  end_date: '',
  quota: null,
  fee: null,
  description: '',
  is_active: true
})

const fetchWaves = async () => {
  try {
    const response = await api.get('/yasmin-panel/ppdb/waves')
    if (response.data.success) {
      waves.value = response.data.data.data
    }
  } catch (error) {
    console.error('Failed to fetch waves:', error)
  } finally {
    isLoading.value = false
  }
}

const openModal = (wave = null) => {
  editingWave.value = wave
  if (wave) {
    // Format dates to YYYY-MM-DD for HTML date input
    const formatDateForInput = (dateStr) => {
      if (!dateStr) return ''
      const date = new Date(dateStr)
      return date.toISOString().split('T')[0]
    }
    Object.assign(form, {
      name: wave.name,
      academic_year: wave.academic_year,
      start_date: formatDateForInput(wave.start_date),
      end_date: formatDateForInput(wave.end_date),
      quota: wave.quota,
      fee: wave.fee,
      description: wave.description || '',
      is_active: wave.is_active
    })
  } else {
    Object.assign(form, {
      name: '',
      academic_year: new Date().getFullYear() + '/' + (new Date().getFullYear() + 1),
      start_date: '',
      end_date: '',
      quota: null,
      fee: null,
      description: '',
      is_active: true
    })
  }
  showModal.value = true
}

const saveWave = async () => {
  isSaving.value = true
  try {
    if (editingWave.value) {
      await api.put(`/yasmin-panel/ppdb/waves/${editingWave.value.id}`, form)
      showSuccess('Berhasil!', 'Gelombang berhasil diupdate')
    } else {
      await api.post('/yasmin-panel/ppdb/waves', form)
      showSuccess('Berhasil!', 'Gelombang berhasil ditambahkan')
    }
    showModal.value = false
    fetchWaves()
  } catch (error) {
    showError('Gagal!', error.response?.data?.message || 'Gagal menyimpan gelombang')
  } finally {
    isSaving.value = false
  }
}

const toggleActive = async (wave) => {
  try {
    const response = await api.put(`/yasmin-panel/ppdb/waves/${wave.id}/toggle`)
    if (response.data.success) {
      showSuccess('Berhasil!', response.data.data.is_active ? 'Gelombang diaktifkan' : 'Gelombang dinonaktifkan')
      fetchWaves() // Refresh from server
    }
  } catch (error) {
    showError('Gagal!', error.response?.data?.message || 'Gagal mengubah status gelombang')
  }
}

const deleteWave = async (wave) => {
  const result = await confirm('Hapus Gelombang?', `Gelombang "${wave.name}" akan dihapus permanen`, 'Ya, Hapus')
  if (!result.isConfirmed) return
  
  try {
    await api.delete(`/yasmin-panel/ppdb/waves/${wave.id}`)
    showSuccess('Berhasil!', 'Gelombang berhasil dihapus')
    fetchWaves()
  } catch (error) {
    showError('Gagal!', error.response?.data?.message || 'Gagal menghapus gelombang')
  }
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

onMounted(() => {
  fetchWaves()
})
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
