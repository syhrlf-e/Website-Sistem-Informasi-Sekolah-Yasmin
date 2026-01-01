<!--
  @component SectionPendidikan
  @description Form section for education and major selection with school autocomplete
-->

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- Asal Sekolah with Autocomplete -->
    <div class="md:col-span-2 relative">
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        Asal Sekolah (SMP/MTs) <span class="text-red-500">*</span>
      </label>
      <div class="relative">
        <input 
          v-model="searchQuery" 
          @input="onSearchInput"
          @focus="showDropdown = true"
          @blur="hideDropdownDelayed"
          type="text" 
          class="form-input pr-10" 
          placeholder="Ketik nama sekolah..."
          autocomplete="off"
        />
        <div class="absolute right-3 top-1/2 -translate-y-1/2">
          <div v-if="isSearching" class="w-5 h-5 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
          <svg v-else class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
      </div>
      
      <!-- Dropdown Results -->
      <div 
        v-if="showDropdown && (searchResults.length > 0 || isSearching || searchError)"
        class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl max-h-64 overflow-y-auto"
      >
        <div v-if="isSearching" class="p-4 text-center text-gray-500">
          <div class="w-6 h-6 border-2 border-blue-500 border-t-transparent rounded-full animate-spin mx-auto mb-2"></div>
          Mencari sekolah...
        </div>
        
        <div v-else-if="searchError" class="p-4 text-center text-red-500 text-sm">
          {{ searchError }}
        </div>
        
        <div v-else-if="searchResults.length === 0 && searchQuery.length >= 3" class="p-4 text-center text-gray-500 text-sm">
          Sekolah tidak ditemukan
        </div>
        
        <div 
          v-for="school in searchResults" 
          :key="school.id"
          @mousedown.prevent="selectSchool(school)"
          class="p-3 hover:bg-blue-50 dark:hover:bg-gray-700 cursor-pointer border-b border-gray-100 dark:border-gray-700 last:border-0 transition-colors"
        >
          <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
              <span class="text-xs font-bold text-blue-600 dark:text-blue-400">{{ school.bentuk }}</span>
            </div>
            <div class="flex-1 min-w-0">
              <p class="font-medium text-gray-900 dark:text-white text-sm truncate">{{ school.sekolah }}</p>
              <p class="text-xs text-gray-500 truncate">{{ school.alamat_jalan }}</p>
              <p class="text-xs text-gray-400">{{ school.kecamatan }}, {{ school.kabupaten_kota }}</p>
              <p class="text-xs text-blue-500 font-mono">NPSN: {{ school.npsn }}</p>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Selected School Display -->
      <div v-if="selectedSchool" class="mt-2 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl">
        <div class="flex items-center justify-between">
          <div>
            <p class="font-medium text-green-800 dark:text-green-200 text-sm">{{ selectedSchool.sekolah }}</p>
            <p class="text-xs text-green-600 dark:text-green-400">NPSN: {{ selectedSchool.npsn }}</p>
          </div>
          <button @click="clearSelection" type="button" class="text-green-600 hover:text-green-800 dark:text-green-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- NPSN (readonly when auto-filled) -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        NPSN Sekolah <span class="text-red-500">*</span>
        <span v-if="selectedSchool" class="text-green-500 text-xs ml-1">(otomatis)</span>
      </label>
      <input 
        :value="modelValue.npsn_sekolah" 
        @input="handleNpsn" 
        @keypress="onlyNumbers"
        :readonly="!!selectedSchool"
        type="text" 
        inputmode="numeric"
        maxlength="8" 
        class="form-input" 
        :class="{ 'bg-gray-100 dark:bg-gray-800': selectedSchool }"
        placeholder="8 digit NPSN" 
      />
    </div>

    <!-- Tahun Lulus -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tahun Lulus <span class="text-red-500">*</span></label>
      <div class="relative">
        <select v-model.number="modelValue.tahun_lulus" class="form-select">
          <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
        </select>
        <ChevronDown class="select-icon" />
      </div>
    </div>

    <!-- Alamat Sekolah (readonly when auto-filled) -->
    <div class="md:col-span-2">
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        Alamat Sekolah Asal
        <span v-if="selectedSchool" class="text-green-500 text-xs ml-1">(otomatis)</span>
      </label>
      <textarea 
        v-model="modelValue.alamat_sekolah" 
        :readonly="!!selectedSchool"
        rows="2" 
        class="form-input" 
        :class="{ 'bg-gray-100 dark:bg-gray-800': selectedSchool }"
        placeholder="Alamat lengkap sekolah asal"
      ></textarea>
    </div>

    <!-- Jurusan Pilihan -->
    <div class="md:col-span-2">
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Jurusan Pilihan <span class="text-red-500">*</span></label>
      <div class="grid grid-cols-2 gap-4">
        <div
          @click="modelValue.jurusan_pilihan = 'IPA'"
          :class="modelValue.jurusan_pilihan === 'IPA' ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-green-400'"
          class="flex flex-col items-center justify-center p-4 border-2 rounded-xl cursor-pointer transition-all"
        >
          <p class="font-semibold text-gray-900 dark:text-white">IPA</p>
          <p class="text-xs text-gray-500 text-center">Ilmu Pengetahuan Alam</p>
        </div>

        <div
          @click="modelValue.jurusan_pilihan = 'IPS'"
          :class="modelValue.jurusan_pilihan === 'IPS' ? 'border-orange-500 bg-orange-50 dark:bg-orange-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-orange-400'"
          class="flex flex-col items-center justify-center p-4 border-2 rounded-xl cursor-pointer transition-all"
        >
          <p class="font-semibold text-gray-900 dark:text-white">IPS</p>
          <p class="text-xs text-gray-500 text-center">Ilmu Pengetahuan Sosial</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ChevronDown } from 'lucide-vue-next'
import { computed, ref, watch } from 'vue'

const props = defineProps({
  modelValue: { type: Object, required: true },
  errors: { type: Object, default: () => ({}) }
})

const currentYear = new Date().getFullYear()
const years = computed(() => [currentYear, currentYear - 1, currentYear - 2, currentYear - 3])

// Search state
const searchQuery = ref('')
const searchResults = ref([])
const isSearching = ref(false)
const showDropdown = ref(false)
const searchError = ref('')
const selectedSchool = ref(null)
let debounceTimer = null

// API Endpoint (Laravel proxy to Kemendikdasmen)
const API_URL = '/api/sekolah/search'

// Search schools with debounce
const onSearchInput = () => {
  clearTimeout(debounceTimer)
  searchError.value = ''
  
  if (searchQuery.value.length < 3) {
    searchResults.value = []
    return
  }
  
  isSearching.value = true
  
  debounceTimer = setTimeout(async () => {
    try {
      const response = await fetch(`${API_URL}?keyword=${encodeURIComponent(searchQuery.value)}`)
      const data = await response.json()
      
      if (data.success) {
        // Map Kemendikdasmen fields to our format (API uses snake_case)
        searchResults.value = (data.data || []).map(school => ({
          id: school.sekolah_id || school.npsn,
          npsn: school.npsn,
          sekolah: school.nama,
          bentuk: school.bentuk_pendidikan,
          alamat_jalan: school.alamat_jalan || '-',
          kecamatan: school.kecamatan || '',
          kabupaten_kota: school.kabupaten || '',
          propinsi: school.provinsi || ''
        }))
      } else {
        searchResults.value = []
        if (data.message) {
          searchError.value = data.message
        }
      }
    } catch (error) {
      console.error('Search error:', error)
      searchError.value = 'Gagal mencari sekolah. Coba lagi.'
    } finally {
      isSearching.value = false
    }
  }, 500)
}

// Select a school from dropdown
const selectSchool = (school) => {
  selectedSchool.value = school
  searchQuery.value = school.sekolah
  showDropdown.value = false
  searchResults.value = []
  
  // Auto-fill form fields
  props.modelValue.asal_sekolah = school.sekolah
  props.modelValue.npsn_sekolah = school.npsn
  props.modelValue.alamat_sekolah = `${school.alamat_jalan}, ${school.kecamatan}, ${school.kabupaten_kota}, ${school.propinsi}`
}

// Clear selection
const clearSelection = () => {
  selectedSchool.value = null
  searchQuery.value = ''
  props.modelValue.asal_sekolah = ''
  props.modelValue.npsn_sekolah = ''
  props.modelValue.alamat_sekolah = ''
}

// Hide dropdown with delay (allow click on items)
const hideDropdownDelayed = () => {
  setTimeout(() => {
    showDropdown.value = false
  }, 200)
}

// Sync searchQuery with modelValue when component mounts
watch(() => props.modelValue.asal_sekolah, (newVal) => {
  if (newVal && !selectedSchool.value) {
    searchQuery.value = newVal
  }
}, { immediate: true })

// Block non-numeric keypress
const onlyNumbers = (event) => {
  const char = String.fromCharCode(event.which || event.keyCode)
  if (!/[0-9]/.test(char)) {
    event.preventDefault()
  }
}

// Handle NPSN - only allow numbers, max 8 digits
const handleNpsn = (event) => {
  if (selectedSchool.value) return // Don't allow manual edit when auto-filled
  const value = event.target.value.replace(/\D/g, '').slice(0, 8)
  props.modelValue.npsn_sekolah = value
}
</script>

<style scoped>
.form-input {
  @apply w-full px-4 py-2.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all;
}
.form-input:-webkit-autofill,
.form-input:-webkit-autofill:hover,
.form-input:-webkit-autofill:focus {
  -webkit-box-shadow: 0 0 0 30px white inset !important;
  -webkit-text-fill-color: #111827 !important;
}

/* Custom select dropdown styling */
.form-select {
  @apply w-full px-4 py-2.5 pr-10 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all cursor-pointer;
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
}
.form-select:-webkit-autofill,
.form-select:-webkit-autofill:hover,
.form-select:-webkit-autofill:focus {
  -webkit-box-shadow: 0 0 0 30px white inset !important;
  -webkit-text-fill-color: #111827 !important;
}

/* Chevron icon for select */
.select-icon {
  @apply absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none;
}
</style>
