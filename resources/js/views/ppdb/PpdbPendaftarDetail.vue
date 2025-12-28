/**
 * @component PpdbPendaftarDetail
 * @description Detail pendaftar dengan update status
 * @route /yasmin-panel/ppdb/pendaftar/:id
 */

<template>
  <div class="space-y-6">
    <!-- Back Button & Header -->
    <div class="flex items-center gap-4">
      <button
        @click="$router.back()"
        class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
      >
        <ArrowLeft class="w-5 h-5 text-gray-600 dark:text-gray-400" />
      </button>
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">Detail Pendaftar</h1>
        <p class="text-gray-500 text-sm font-mono">{{ registration?.registration_number }}</p>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="isLoading" class="flex items-center justify-center py-12">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
    </div>

    <template v-else-if="registration">
      <!-- Status Card -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ registration.nama_lengkap }}</h2>
            <p class="text-gray-500">{{ registration.asal_sekolah }} - {{ registration.jurusan_pilihan }}</p>
          </div>
          <div class="flex items-center gap-3">
            <span
              :class="getStatusClass(registration.status)"
              class="px-4 py-2 rounded-full text-sm font-semibold"
            >
              {{ getStatusLabel(registration.status) }}
            </span>
            <button
              @click="showStatusModal = true"
              class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-medium transition-colors"
            >
              Update Status
            </button>
          </div>
        </div>
      </div>

      <!-- Data Sections -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Identitas Diri -->
        <DataSection title="Identitas Diri">
          <DataRow label="NIK" :value="registration.nik" />
          <DataRow label="NISN" :value="registration.nisn" />
          <DataRow label="Tempat, Tanggal Lahir" :value="`${registration.tempat_lahir}, ${formatDate(registration.tanggal_lahir)}`" />
          <DataRow label="Jenis Kelamin" :value="registration.jenis_kelamin" />
          <DataRow label="Agama" :value="registration.agama" />
          <DataRow label="Anak ke / Jumlah Saudara" :value="`${registration.anak_ke} dari ${registration.jumlah_saudara} bersaudara`" />
          <DataRow label="No. HP" :value="registration.no_hp" />
          <DataRow label="Email" :value="registration.email" />
        </DataSection>

        <!-- Alamat -->
        <DataSection title="Alamat">
          <DataRow label="Alamat Lengkap" :value="registration.alamat_lengkap" />
          <DataRow label="RT/RW" :value="`${registration.rt}/${registration.rw}`" />
          <DataRow label="Kelurahan" :value="registration.kelurahan" />
          <DataRow label="Kecamatan" :value="registration.kecamatan" />
          <DataRow label="Kota/Kabupaten" :value="registration.kota_kabupaten" />
          <DataRow label="Provinsi" :value="registration.provinsi" />
          <DataRow label="Kode Pos" :value="registration.kode_pos" />
        </DataSection>

        <!-- Pendidikan -->
        <DataSection title="Pendidikan">
          <DataRow label="Asal Sekolah" :value="registration.asal_sekolah" />
          <DataRow label="NPSN" :value="registration.npsn_sekolah" />
          <DataRow label="Tahun Lulus" :value="registration.tahun_lulus" />
          <DataRow label="Jurusan Pilihan" :value="registration.jurusan_pilihan" />
        </DataSection>

        <!-- Kesehatan -->
        <DataSection title="Kesehatan">
          <DataRow label="Golongan Darah" :value="registration.golongan_darah" />
          <DataRow label="Tinggi Badan" :value="registration.tinggi_badan ? `${registration.tinggi_badan} cm` : '-'" />
          <DataRow label="Berat Badan" :value="registration.berat_badan ? `${registration.berat_badan} kg` : '-'" />
          <DataRow label="Riwayat Penyakit" :value="registration.riwayat_penyakit" />
          <DataRow label="Alergi" :value="registration.alergi ? registration.keterangan_alergi : 'Tidak ada'" />
        </DataSection>

        <!-- Data Ayah -->
        <DataSection title="Data Ayah">
          <DataRow label="Nama" :value="registration.nama_ayah" />
          <DataRow label="NIK" :value="registration.nik_ayah" />
          <DataRow label="Pekerjaan" :value="registration.pekerjaan_ayah" />
          <DataRow label="Pendidikan" :value="registration.pendidikan_ayah" />
          <DataRow label="Penghasilan" :value="registration.penghasilan_ayah" />
          <DataRow label="No. HP" :value="registration.no_hp_ayah" />
        </DataSection>

        <!-- Data Ibu -->
        <DataSection title="Data Ibu">
          <DataRow label="Nama" :value="registration.nama_ibu" />
          <DataRow label="NIK" :value="registration.nik_ibu" />
          <DataRow label="Pekerjaan" :value="registration.pekerjaan_ibu" />
          <DataRow label="Pendidikan" :value="registration.pendidikan_ibu" />
          <DataRow label="Penghasilan" :value="registration.penghasilan_ibu" />
          <DataRow label="No. HP" :value="registration.no_hp_ibu" />
        </DataSection>

        <!-- Data Wali (if exists) -->
        <DataSection v-if="registration.nama_wali" title="Data Wali">
          <DataRow label="Nama" :value="registration.nama_wali" />
          <DataRow label="Hubungan" :value="registration.hubungan_wali" />
          <DataRow label="Pekerjaan" :value="registration.pekerjaan_wali" />
          <DataRow label="No. HP" :value="registration.no_hp_wali" />
        </DataSection>

        <!-- Catatan Admin -->
        <DataSection title="Catatan Admin" class="lg:col-span-2">
          <textarea
            v-model="adminNotes"
            rows="3"
            placeholder="Tambahkan catatan..."
            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
          ></textarea>
          <button
            @click="saveNotes"
            :disabled="isSaving"
            class="mt-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm font-medium disabled:opacity-50"
          >
            {{ isSaving ? 'Menyimpan...' : 'Simpan Catatan' }}
          </button>
        </DataSection>
      </div>
    </template>

    <!-- Status Update Modal -->
    <Teleport to="body">
      <div v-if="showStatusModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 max-w-md w-full mx-4 shadow-xl">
          <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Update Status</h3>
          <select
            v-model="newStatus"
            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl mb-4 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
          >
            <option value="pending">Menunggu Verifikasi</option>
            <option value="verified">Terverifikasi</option>
            <option value="selection">Tahap Seleksi</option>
            <option value="accepted">Diterima</option>
            <option value="rejected">Ditolak</option>
          </select>
          <div class="flex gap-3">
            <button
              @click="showStatusModal = false"
              class="flex-1 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-medium"
            >
              Batal
            </button>
            <button
              @click="updateStatus"
              :disabled="isUpdating"
              class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium disabled:opacity-50"
            >
              {{ isUpdating ? 'Updating...' : 'Update' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ArrowLeft } from 'lucide-vue-next'
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/api'
import { usePopup } from '@/composables/usePopup'
import DataSection from './ppdbPendaftarDetail/DataSection.vue'
import DataRow from './ppdbPendaftarDetail/DataRow.vue'

const route = useRoute()
const { showSuccess, showError } = usePopup()

const isLoading = ref(true)
const registration = ref(null)
const adminNotes = ref('')
const isSaving = ref(false)
const showStatusModal = ref(false)
const newStatus = ref('')
const isUpdating = ref(false)

const fetchData = async () => {
  try {
    const response = await api.get(`/yasmin-panel/ppdb/registrations/${route.params.id}`)
    if (response.data.success) {
      registration.value = response.data.data
      adminNotes.value = registration.value.catatan_admin || ''
      newStatus.value = registration.value.status
    }
  } catch (error) {
    console.error('Failed to fetch registration:', error)
    showError('Error', 'Gagal memuat data pendaftar')
  } finally {
    isLoading.value = false
  }
}

const updateStatus = async () => {
  isUpdating.value = true
  try {
    await api.put(`/yasmin-panel/ppdb/registrations/${route.params.id}/status`, {
      status: newStatus.value,
      catatan_admin: adminNotes.value
    })
    registration.value.status = newStatus.value
    showStatusModal.value = false
    showSuccess('Berhasil!', 'Status berhasil diupdate')
  } catch (error) {
    showError('Gagal!', error.response?.data?.message || 'Gagal update status')
  } finally {
    isUpdating.value = false
  }
}

const saveNotes = async () => {
  isSaving.value = true
  try {
    await api.put(`/yasmin-panel/ppdb/registrations/${route.params.id}/status`, {
      status: registration.value.status,
      catatan_admin: adminNotes.value
    })
    showSuccess('Berhasil!', 'Catatan disimpan')
  } catch (error) {
    showError('Gagal!', 'Gagal menyimpan catatan')
  } finally {
    isSaving.value = false
  }
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

const getStatusLabel = (status) => {
  const labels = { pending: 'Menunggu', verified: 'Terverifikasi', selection: 'Seleksi', accepted: 'Diterima', rejected: 'Ditolak' }
  return labels[status] || status
}

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    verified: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
    selection: 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
    accepted: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    rejected: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

onMounted(() => {
  fetchData()
})
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
