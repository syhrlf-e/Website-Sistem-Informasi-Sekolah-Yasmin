<!--
  @component SectionIdentitas
  @description Form section for personal identity data with validations
-->

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- Nama Lengkap -->
    <div class="md:col-span-2">
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
      <input v-model="modelValue.nama_lengkap" type="text" class="form-input" placeholder="Nama sesuai akta kelahiran" />
      <p v-if="errors?.nama_lengkap" class="text-red-500 text-xs mt-1">{{ errors.nama_lengkap[0] }}</p>
    </div>

    <!-- NIK -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">NIK <span class="text-red-500">*</span></label>
      <input 
        :value="modelValue.nik" 
        @input="handleNik($event, 'nik', 16)" 
        @keypress="onlyNumbers"
        type="text" 
        inputmode="numeric"
        maxlength="16" 
        class="form-input" 
        placeholder="16 digit NIK" 
      />
      <p v-if="errors?.nik" class="text-red-500 text-xs mt-1">{{ errors.nik[0] }}</p>
    </div>

    <!-- NISN -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">NISN <span class="text-red-500">*</span></label>
      <input 
        :value="modelValue.nisn" 
        @input="handleNik($event, 'nisn', 10)" 
        @keypress="onlyNumbers"
        type="text" 
        inputmode="numeric"
        maxlength="10" 
        class="form-input" 
        placeholder="10 digit NISN" 
      />
    </div>

    <!-- Tempat Lahir -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tempat Lahir <span class="text-red-500">*</span></label>
      <input 
        v-model="modelValue.tempat_lahir" 
        @keypress="onlyLetters"
        @input="handleTempatLahir"
        type="text" 
        class="form-input" 
        placeholder="Kota/Kabupaten" 
      />
    </div>

    <!-- Tanggal Lahir -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Lahir <span class="text-red-500">*</span></label>
      <DateWheelPicker v-model="modelValue.tanggal_lahir" />
      <p v-if="age" class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">
        Umur kamu {{ age.years }} tahun {{ age.months }} bulan
      </p>
    </div>

    <!-- Jenis Kelamin -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
      <div class="relative">
        <select v-model="modelValue.jenis_kelamin" class="form-select">
          <option value="">Pilih</option>
          <option value="Laki-laki">Laki-laki</option>
          <option value="Perempuan">Perempuan</option>
        </select>
        <ChevronDown class="select-icon" />
      </div>
    </div>

    <!-- Agama -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Agama <span class="text-red-500">*</span></label>
      <div class="relative">
        <select v-model="modelValue.agama" class="form-select">
          <option value="">Pilih</option>
          <option value="Islam">Islam</option>
          <option value="Kristen">Kristen</option>
          <option value="Katolik">Katolik</option>
          <option value="Hindu">Hindu</option>
          <option value="Buddha">Buddha</option>
          <option value="Konghucu">Konghucu</option>
        </select>
        <ChevronDown class="select-icon" />
      </div>
    </div>

    <!-- Anak Ke -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Anak Ke <span class="text-red-500">*</span></label>
      <div class="relative">
        <select v-model.number="modelValue.anak_ke" class="form-select">
          <option value="">Pilih</option>
          <option v-for="n in 10" :key="n" :value="n">{{ n }}</option>
        </select>
        <ChevronDown class="select-icon" />
      </div>
    </div>

    <!-- Jumlah Saudara -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jumlah Saudara <span class="text-red-500">*</span></label>
      <div class="relative">
        <select v-model.number="modelValue.jumlah_saudara" class="form-select">
          <option value="">Pilih</option>
          <option v-for="n in 11" :key="n - 1" :value="n - 1">{{ n - 1 }}</option>
        </select>
        <ChevronDown class="select-icon" />
      </div>
    </div>

    <!-- No HP -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">No. HP/WhatsApp <span class="text-red-500">*</span></label>
      <input 
        :value="modelValue.no_hp" 
        @input="handlePhone" 
        @keypress="onlyNumbers"
        type="tel" 
        inputmode="numeric"
        maxlength="13" 
        class="form-input" 
        placeholder="08xxxxxxxxxx" 
      />
    </div>

    <!-- Email -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email <span class="text-red-500">*</span></label>
      <input 
        v-model="modelValue.email" 
        type="email" 
        class="form-input" 
        :class="{ 'border-red-500': emailError }"
        placeholder="email@gmail.com" 
        @blur="validateEmail"
      />
      <p v-if="emailError" class="text-red-500 text-xs mt-1">{{ emailError }}</p>
    </div>

    <!-- Hobi -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Hobi</label>
      <input v-model="modelValue.hobi" type="text" class="form-input" placeholder="Hobi utama" />
    </div>

    <!-- Cita-cita -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cita-cita</label>
      <input v-model="modelValue.cita_cita" type="text" class="form-input" placeholder="Cita-cita masa depan" />
    </div>
  </div>
</template>

<script setup>
import DateWheelPicker from '@/components/ui/DateWheelPicker.vue'
import { ChevronDown } from 'lucide-vue-next'
import { computed, ref } from 'vue'

const props = defineProps({
  modelValue: { type: Object, required: true },
  errors: { type: Object, default: () => ({}) }
})

const emit = defineEmits(['update:modelValue'])

const emailError = ref('')

// Calculate age from birth date
const age = computed(() => {
  if (!props.modelValue.tanggal_lahir) return null
  const birth = new Date(props.modelValue.tanggal_lahir)
  const today = new Date()
  let years = today.getFullYear() - birth.getFullYear()
  let months = today.getMonth() - birth.getMonth()
  if (months < 0) {
    years--
    months += 12
  }
  if (today.getDate() < birth.getDate()) {
    months--
    if (months < 0) {
      years--
      months += 12
    }
  }
  return { years, months }
})

// Max birth date (must be at least 12 years old)
const maxBirthDate = computed(() => {
  const d = new Date()
  d.setFullYear(d.getFullYear() - 12)
  return d.toISOString().split('T')[0]
})

// Block non-numeric keypress
const onlyNumbers = (event) => {
  const char = String.fromCharCode(event.which || event.keyCode)
  if (!/[0-9]/.test(char)) {
    event.preventDefault()
  }
}

// Block non-letter keypress (allow letters and space only)
const onlyLetters = (event) => {
  const char = String.fromCharCode(event.which || event.keyCode)
  if (!/[a-zA-Z\s]/.test(char)) {
    event.preventDefault()
  }
}

// Handle Tempat Lahir - only allow letters and spaces
const handleTempatLahir = (event) => {
  const value = event.target.value.replace(/[^a-zA-Z\s]/g, '')
  props.modelValue.tempat_lahir = value
}

// Handle NIK/NISN - only allow numbers
const handleNik = (event, field, maxLen) => {
  const value = event.target.value.replace(/\D/g, '').slice(0, maxLen)
  props.modelValue[field] = value
}

// Handle phone - auto prefix 08, only numbers
const handlePhone = (event) => {
  let value = event.target.value.replace(/\D/g, '')
  
  // Auto prefix with 08 if user starts with different digit
  if (value.length > 0 && !value.startsWith('0')) {
    value = '08' + value
  }
  
  // Limit to 13 digits
  value = value.slice(0, 13)
  
  props.modelValue.no_hp = value
}

// Validate email (Gmail format)
const validateEmail = () => {
  const email = props.modelValue.email
  if (!email) {
    emailError.value = 'Email wajib diisi'
    return
  }
  
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!emailRegex.test(email)) {
    emailError.value = 'Format email tidak valid'
    return
  }
  
  emailError.value = ''
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
