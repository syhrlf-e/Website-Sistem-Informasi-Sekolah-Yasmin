<!--
  @component PpdbDaftar
  @description Halaman pendaftaran PPDB dengan multi-step form accordion
  @route /ppdb/daftar
-->

<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-12 px-4">
    <div class="max-w-4xl mx-auto">

      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-2 font-poppins">
          Formulir Pendaftaran
        </h1>
        <p class="text-sm md:text-base text-gray-600 dark:text-gray-300 font-poppins">
          SMA Mutiara Insan Nusantara - Tahun Ajaran {{ activeWave?.academic_year || '2025/2026' }}
        </p>
      </div>

      <!-- No Active Wave -->
      <div v-if="!isLoading && !activeWave" class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-2xl p-8 text-center">
        <AlertTriangle class="w-16 h-16 mx-auto mb-4 text-yellow-500" />
        <h2 class="text-xl font-bold text-yellow-800 dark:text-yellow-200 mb-2">Pendaftaran Belum Dibuka</h2>
        <p class="text-yellow-700 dark:text-yellow-300">Silakan cek kembali nanti atau hubungi sekolah untuk informasi lebih lanjut.</p>
      </div>

      <!-- Loading -->
      <div v-else-if="isLoading" class="py-20">
        <LoadingSpinner size="lg" color="blue" text="Memuat formulir..." />
      </div>

      <!-- Registration Form -->
      <div v-else class="space-y-4">
        <!-- Progress Indicator -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 border border-gray-200 dark:border-gray-700 mb-6">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Progress Pengisian</span>
            <span class="text-sm font-bold text-blue-600">{{ completedSections }}/{{ totalSections }} Bagian</span>
          </div>
          <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
            <div class="h-full bg-gradient-to-r from-blue-500 to-purple-500 transition-all duration-500" :style="`width: ${progressPercent}%`"></div>
          </div>
        </div>

        <!-- Accordion Sections -->
        <FormSection
          v-for="(section, index) in sections"
          :key="section.id"
          :title="section.title"
          :icon="section.icon"
          :is-open="openSection === section.id"
          :is-complete="isSectionComplete(section.id) || skippedSections.includes(section.id)"
          :is-locked="!isSectionUnlocked(section.id)"
          :number="index + 1"
          @toggle="toggleSection(section.id)"
        >
          <component 
            :is="section.component" 
            v-model="formData" 
            :errors="errors"
            @skip="skipSection(section.id)"
          />
        </FormSection>

        <!-- Submit Button -->
        <div class="pt-6">
          <p class="text-center text-sm text-gray-500 dark:text-gray-400 mb-3">
            Pastikan semua data sudah benar sebelum mengirim
          </p>
          <button
            @click="submitForm"
            :disabled="isSubmitting || !isFormValid"
            class="w-full py-4 px-6 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 disabled:from-gray-400 disabled:to-gray-500 text-white rounded-2xl font-bold text-lg transition-all shadow-lg hover:shadow-xl disabled:cursor-not-allowed font-poppins flex items-center justify-center gap-2"
          >
            <span v-if="isSubmitting">
              <div class="animate-spin w-5 h-5 border-2 border-white border-t-transparent rounded-full"></div>
            </span>
            <span v-else>
              <Send class="w-5 h-5" />
            </span>
            {{ isSubmitting ? 'Mengirim...' : 'Kirim Pendaftaran' }}
          </button>
          <div class="text-center mt-4">
            <a href="/ppdb/landing" class="inline-flex items-center gap-2 text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 text-sm transition-colors">
              ← Kembali ke halaman PPDB
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import BackButton from '@/components/ui/BackButton.vue'
import LoadingSpinner from '@/components/ui/shared/LoadingSpinner.vue'
import { useHead } from '@vueuse/head'
import { AlertTriangle, Send } from 'lucide-vue-next'
import { computed, onMounted, ref, shallowRef, watch } from 'vue'
// Note: This is an Inertia page, uses window.location for navigation
import FormSection from './ppdbDaftar/FormSection.vue'
import SectionIdentitas from './ppdbDaftar/SectionIdentitas.vue'
import SectionAlamat from './ppdbDaftar/SectionAlamat.vue'
import SectionPendidikan from './ppdbDaftar/SectionPendidikan.vue'
import SectionKesehatan from './ppdbDaftar/SectionKesehatan.vue'
import SectionOrangTua from './ppdbDaftar/SectionOrangTua.vue'
import SectionWali from './ppdbDaftar/SectionWali.vue'



useHead({
  title: 'Daftar PPDB - SMA Mutiara Insan Nusantara',
  meta: [
    { name: 'description', content: 'Formulir pendaftaran PPDB SMA Mutiara Insan Nusantara' }
  ]
})

const isLoading = ref(true)
const isSubmitting = ref(false)
const activeWave = ref(null)
const openSection = ref('identitas')
const errors = ref({})
const skippedSections = ref([]) // Track skipped optional sections (e.g., wali)

const formData = ref({
  // Identitas
  nama_lengkap: '', nik: '', nisn: '', tempat_lahir: '', tanggal_lahir: '',
  jenis_kelamin: '', agama: '', anak_ke: 1, jumlah_saudara: 0, no_hp: '', email: '',
  hobi: '', cita_cita: '', prestasi_akademik: '', prestasi_non_akademik: '',
  // Alamat
  alamat_lengkap: '', rt: '', rw: '', kelurahan: '', kecamatan: '',
  kota_kabupaten: '', provinsi: '', kode_pos: '',
  // Pendidikan
  asal_sekolah: '', npsn_sekolah: '', alamat_sekolah: '', tahun_lulus: new Date().getFullYear(),
  jurusan_pilihan: '',
  // Kesehatan
  golongan_darah: '', tinggi_badan: null, berat_badan: null,
  riwayat_penyakit: '', alergi: false, keterangan_alergi: '',
  // Orang Tua
  nama_ayah: '', nik_ayah: '', pekerjaan_ayah: '', pendidikan_ayah: '',
  penghasilan_ayah: '', no_hp_ayah: '', ayah_masih_hidup: true,
  nama_ibu: '', nik_ibu: '', pekerjaan_ibu: '', pendidikan_ibu: '',
  penghasilan_ibu: '', no_hp_ibu: '', ibu_masih_hidup: true,
  // Wali
  nama_wali: '', nik_wali: '', hubungan_wali: '', pekerjaan_wali: '',
  pendidikan_wali: '', penghasilan_wali: '', no_hp_wali: ''
})

const sections = shallowRef([
  { id: 'identitas', title: 'Identitas Diri', icon: 'User', component: SectionIdentitas },
  { id: 'alamat', title: 'Alamat Tempat Tinggal', icon: 'MapPin', component: SectionAlamat },
  { id: 'pendidikan', title: 'Pendidikan & Jurusan', icon: 'GraduationCap', component: SectionPendidikan },
  { id: 'kesehatan', title: 'Informasi Kesehatan', icon: 'Heart', component: SectionKesehatan },
  { id: 'orangtua', title: 'Data Orang Tua', icon: 'Users', component: SectionOrangTua },
  { id: 'wali', title: 'Data Wali', icon: 'UserPlus', component: SectionWali },
])

const totalSections = computed(() => sections.value.length)

const requiredFields = {
  identitas: ['nama_lengkap', 'nik', 'nisn', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama', 'anak_ke', 'jumlah_saudara', 'no_hp', 'email'],
  alamat: ['alamat_lengkap', 'rt', 'rw', 'kelurahan', 'kecamatan', 'kota_kabupaten', 'provinsi', 'kode_pos'],
  pendidikan: ['asal_sekolah', 'npsn_sekolah', 'tahun_lulus', 'jurusan_pilihan'],
  kesehatan: ['golongan_darah', 'tinggi_badan', 'berat_badan'],
  orangtua: ['nama_ayah', 'nik_ayah', 'pekerjaan_ayah', 'pendidikan_ayah', 'no_hp_ayah', 'nama_ibu', 'nik_ibu', 'pekerjaan_ibu', 'pendidikan_ibu', 'no_hp_ibu'],
  wali: []
}

const isSectionComplete = (sectionId) => {
  // If section is skipped, it counts as complete
  if (skippedSections.value.includes(sectionId)) return true
  
  const fields = requiredFields[sectionId] || []
  // Sections with no required fields are complete only if skipped (handled above)
  if (fields.length === 0) return false
  return fields.every(field => {
    const value = formData.value[field]
    return value !== '' && value !== null && value !== undefined
  })
}

const completedSections = computed(() => {
  return Object.keys(requiredFields).filter(id => isSectionComplete(id)).length
})

const progressPercent = computed(() => {
  return Math.round((completedSections.value / totalSections.value) * 100)
})

const isFormValid = computed(() => {
  return ['identitas', 'alamat', 'pendidikan', 'kesehatan', 'orangtua'].every(id => isSectionComplete(id))
})

/**
 * Check if a section is unlocked (all previous required sections are complete)
 * First section is always unlocked
 */
const isSectionUnlocked = (sectionId) => {
  const sectionIndex = sections.value.findIndex(s => s.id === sectionId)
  
  // First section is always unlocked
  if (sectionIndex === 0) return true
  
  // Check all previous sections with required fields
  for (let i = 0; i < sectionIndex; i++) {
    const prevSectionId = sections.value[i].id
    const prevRequiredFields = requiredFields[prevSectionId] || []
    
    // Only check sections that have required fields
    if (prevRequiredFields.length > 0 && !isSectionComplete(prevSectionId)) {
      return false
    }
  }
  
  return true
}

/**
 * Toggle section with sequential validation
 * Can only open if section is unlocked
 */
const toggleSection = (id) => {
  // Always allow closing current section
  if (openSection.value === id) {
    openSection.value = null
    return
  }
  
  // Check if section is unlocked before opening
  if (isSectionUnlocked(id)) {
    openSection.value = id
  }
}

/**
 * Skip an optional section (e.g., Wali)
 * Marks it as complete and closes it
 */
const skipSection = (id) => {
  if (!skippedSections.value.includes(id)) {
    skippedSections.value.push(id)
  }
  openSection.value = null
}

const fetchActiveWave = async () => {
  try {
    const response = await fetch('/api/ppdb/wave/active')
    const data = await response.json()
    if (data.success) {
      activeWave.value = data.data
    }
  } catch (error) {
    console.error('Error fetching wave:', error)
  } finally {
    isLoading.value = false
  }
}

const submitForm = async () => {
  if (!isFormValid.value) return
  
  isSubmitting.value = true
  errors.value = {}
  
  // Clean up data - convert empty strings to null for numeric fields
  const cleanData = { ...formData.value }
  const numericFields = ['tinggi_badan', 'berat_badan', 'anak_ke', 'jumlah_saudara', 'tahun_lulus']
  numericFields.forEach(field => {
    if (cleanData[field] === '' || cleanData[field] === null) {
      cleanData[field] = null
    } else {
      cleanData[field] = Number(cleanData[field])
    }
  })
  
  try {
    const response = await fetch('/api/ppdb/register', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(cleanData)
    })
    
    const data = await response.json()
    
    if (data.success) {
      // Clear form storage before navigating away
      clearFormStorage()
      // Navigate to success page with registration data
      const params = new URLSearchParams({
        reg: data.data.registration_number,
        token: data.data.token,
        nama: data.data.nama
      })
      window.location.href = `/ppdb/sukses?${params.toString()}`
    } else {
      console.error('Validation errors:', JSON.stringify(data.errors, null, 2))
      errors.value = data.errors || {}
      
      // Build detailed error message
      let errorMsg = data.message || 'Data tidak valid'
      if (data.errors) {
        const fields = Object.keys(data.errors)
        errorMsg += '\n\nField bermasalah: ' + fields.join(', ')
      }
      alert(errorMsg)
      
      // Find first section with error and open it
      for (const section of sections.value) {
        const sectionFields = requiredFields[section.id] || []
        if (sectionFields.some(f => errors.value[f])) {
          openSection.value = section.id
          break
        }
      }
    }
  } catch (error) {
    console.error('Error submitting form:', error)
    alert('Terjadi kesalahan saat mengirim pendaftaran. Silakan coba lagi.')
  } finally {
    isSubmitting.value = false
  }
}

// LocalStorage key
const STORAGE_KEY = 'ppdb_form_data'

// Save form data to localStorage when it changes
watch(formData, (newData) => {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(newData))
}, { deep: true })

// Save skipped sections to localStorage
watch(skippedSections, (newData) => {
  localStorage.setItem(STORAGE_KEY + '_skipped', JSON.stringify(newData))
}, { deep: true })

// Clear localStorage after successful submit
const clearFormStorage = () => {
  localStorage.removeItem(STORAGE_KEY)
  localStorage.removeItem(STORAGE_KEY + '_skipped')
}

onMounted(() => {
  // Restore form data from localStorage
  const savedData = localStorage.getItem(STORAGE_KEY)
  if (savedData) {
    try {
      const parsed = JSON.parse(savedData)
      Object.assign(formData.value, parsed)
    } catch (e) {
      console.error('Failed to restore form data:', e)
    }
  }
  
  // Restore skipped sections
  const savedSkipped = localStorage.getItem(STORAGE_KEY + '_skipped')
  if (savedSkipped) {
    try {
      skippedSections.value = JSON.parse(savedSkipped)
    } catch (e) {
      console.error('Failed to restore skipped sections:', e)
    }
  }
  
  fetchActiveWave()
})
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
