<!--
  @component EkskulRegistrationForm
  @description Complete registration form dengan fields, validation, dan submit logic
  @props {Number} ekskulId - ID ekstrakurikuler untuk pendaftaran
  @emits success - Ketika pendaftaran berhasil
  @emits error - Ketika terjadi error dengan message
  @endpoint POST /api/ekstrakurikuler/register (via ekskulRegistrationService)
  @validation nama (min:3), email, phone (ID format), kelas (min:2), alasan (min:20), token (min:8)
-->

<template>
  <form @submit.prevent="handleSubmit" class="space-y-4 md:space-y-5">
    <FormInput
      v-model="form.nama"
      label="Nama Lengkap"
      placeholder="Masukkan nama lengkap"
      :required="true"
      :error="errors.nama"
      :valid="validStates.nama"
      :allowed-pattern="/^[a-zA-Z '\\-]$/"
      @input="handleNamaInput"
      @blur="validateNama"
    />

    <FormInput
      v-model="form.email"
      label="Email"
      type="email"
      placeholder="contoh@email.com"
      :required="true"
      :error="errors.email"
      :valid="validStates.email"
      @blur="validateEmail"
    />

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <FormInput
        v-model="form.phone"
        label="No. Telepon"
        type="tel"
        placeholder="08xxxxxxxxxx"
        :maxlength="13"
        :required="true"
        :error="errors.phone"
        :valid="validStates.phone"
        @input="handlePhoneInput"
        @blur="validatePhone"
      />

      <FormInput
        v-model="form.kelas"
        label="Kelas"
        placeholder="Contoh: X IPA 1"
        :required="true"
        :error="errors.kelas"
        :valid="validStates.kelas"
        @blur="validateKelas"
      />
    </div>

    <FormTextarea
      v-model="form.alasan"
      label="Alasan Bergabung"
      placeholder="Ceritakan alasan kamu ingin bergabung dengan ekstrakurikuler ini..."
      :rows="4"
      :required="true"
      :error="errors.alasan"
      :valid="validStates.alasan"
      @blur="validateAlasan"
    />

    <EkskulTokenInput
      v-model="form.registration_token"
      :error="errors.registration_token"
      :valid="validStates.registration_token"
      @blur="validateToken"
    />

    <div
      v-if="errorMessage"
      class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl"
    >
      <p class="text-sm text-red-700 dark:text-red-300 font-poppins">
        {{ errorMessage }}
      </p>
    </div>

    <div class="pt-4">
      <button
        type="submit"
        class="w-full px-4 md:px-6 py-2 md:py-3 text-sm md:text-base bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 disabled:from-gray-400 disabled:to-gray-400 text-white rounded-xl font-semibold font-poppins transition-all shadow-lg hover:shadow-xl disabled:cursor-not-allowed flex items-center justify-center gap-2"
        :disabled="isSubmitting"
      >
        <Loader2 v-if="isSubmitting" class="w-4 md:w-5 h-4 md:h-5 animate-spin" />
        <span>{{ isSubmitting ? 'Mengirim...' : 'Daftar Sekarang' }}</span>
      </button>
    </div>
  </form>
</template>

<script setup>
import FormInput from '@/components/ui/FormInput.vue'
import FormTextarea from '@/components/ui/FormTextarea.vue'
import { registerUser } from '@/services/ekskulRegistrationService'
import { Loader2 } from 'lucide-vue-next'
import { reactive, ref, watch } from 'vue'
import EkskulTokenInput from './EkskulTokenInput.vue'

const props = defineProps({
  ekskulId: {
    type: Number,
    required: true
  },
  resetTrigger: {
    type: Number,
    default: 0
  }
})

const emit = defineEmits(['success', 'error'])

const form = reactive({
  nama: '',
  email: '',
  phone: '',
  kelas: '',
  alasan: '',
  registration_token: ''
})

const errors = reactive({})
const validStates = reactive({
  nama: false,
  email: false,
  phone: false,
  kelas: false,
  alasan: false,
  registration_token: false
})
const errorMessage = ref('')
const isSubmitting = ref(false)

/**
 * Reset form ketika resetTrigger berubah (modal closed)
 */
watch(
  () => props.resetTrigger,
  () => {
    resetForm()
  }
)

const resetForm = () => {
  form.nama = ''
  form.email = ''
  form.phone = ''
  form.kelas = ''
  form.alasan = ''
  form.registration_token = ''
  Object.keys(errors).forEach((key) => delete errors[key])
  Object.keys(validStates).forEach((key) => validStates[key] = false)
  errorMessage.value = ''
}

const validateNama = () => {
  const sanitized = sanitizeInput(form.nama)
  if (!sanitized || sanitized.length < 3) {
    errors.nama = 'Nama harus minimal 3 karakter'
    validStates.nama = false
  } else if (sanitized !== form.nama.trim()) {
    errors.nama = 'Nama mengandung karakter tidak valid'
    validStates.nama = false
  } else {
    delete errors.nama
    validStates.nama = true
  }
}

const validateEmail = () => {
  const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/
  if (!form.email || !emailRegex.test(form.email.trim())) {
    errors.email = 'Email tidak valid'
    validStates.email = false
  } else {
    delete errors.email
    validStates.email = true
  }
}

const validatePhone = () => {
  const phoneRegex = /^08[0-9]{8,11}$/
  if (!form.phone || !phoneRegex.test(form.phone)) {
    errors.phone = 'Nomor telepon harus diawali 08 dan maksimal 13 digit'
    validStates.phone = false
  } else {
    delete errors.phone
    validStates.phone = true
  }
}

const validateKelas = () => {
  const sanitized = sanitizeInput(form.kelas)
  if (!sanitized || sanitized.length < 2) {
    errors.kelas = 'Kelas harus diisi'
    validStates.kelas = false
  } else {
    delete errors.kelas
    validStates.kelas = true
  }
}

const validateAlasan = () => {
  const sanitized = sanitizeInput(form.alasan)
  if (!sanitized || sanitized.length < 20) {
    errors.alasan = 'Alasan minimal 20 karakter'
    validStates.alasan = false
  } else if (sanitized !== form.alasan.trim()) {
    errors.alasan = 'Alasan mengandung karakter tidak valid'
    validStates.alasan = false
  } else {
    delete errors.alasan
    validStates.alasan = true
  }
}

const validateToken = () => {
  if (!form.registration_token || form.registration_token.trim().length < 8) {
    errors.registration_token = 'Token registrasi harus diisi (minimal 8 karakter)'
    validStates.registration_token = false
  } else {
    delete errors.registration_token
    validStates.registration_token = true
  }
}

const sanitizeInput = (input) => {
  if (!input) return ''
  return input
    .replace(/[<>]/g, '')
    .replace(/javascript:/gi, '')
    .replace(/on\w+=/gi, '')
    .replace(/[\x00-\x1F\x7F]/g, '')
    .trim()
}

const handlePhoneInput = (event) => {
  let value = event.target.value.replace(/\D/g, '')
  if (value.length > 13) value = value.slice(0, 13)
  if (value.length > 0 && !value.startsWith('0')) value = '0' + value
  if (value.length > 1 && value[1] !== '8') value = '08' + value.slice(2)
  form.phone = value
}

const handleNamaInput = (event) => {
  let value = event.target.value.replace(/[^a-zA-Z\s'\-]/g, '')
  value = value.replace(/\s+/g, ' ')
  form.nama = value
}
const validateForm = () => {
  Object.keys(errors).forEach((key) => delete errors[key])
  errorMessage.value = ''

  let isValid = true

  const sanitizedNama = sanitizeInput(form.nama)
  if (!sanitizedNama || sanitizedNama.length < 3) {
    errors.nama = 'Nama harus minimal 3 karakter'
    isValid = false
  } else if (sanitizedNama !== form.nama.trim()) {
    errors.nama = 'Nama mengandung karakter tidak valid'
    isValid = false
  }

  const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/
  if (!form.email || !emailRegex.test(form.email.trim())) {
    errors.email = 'Email tidak valid'
    isValid = false
  }

  const phoneRegex = /^08[0-9]{8,11}$/
  if (!form.phone || !phoneRegex.test(form.phone)) {
    errors.phone = 'Nomor telepon harus diawali 08 dan maksimal 13 digit'
    isValid = false
  }

  const sanitizedKelas = sanitizeInput(form.kelas)
  if (!sanitizedKelas || sanitizedKelas.length < 2) {
    errors.kelas = 'Kelas harus diisi'
    isValid = false
  }

  const sanitizedAlasan = sanitizeInput(form.alasan)
  if (!sanitizedAlasan || sanitizedAlasan.length < 20) {
    errors.alasan = 'Alasan minimal 20 karakter'
    isValid = false
  } else if (sanitizedAlasan !== form.alasan.trim()) {
    errors.alasan = 'Alasan mengandung karakter tidak valid'
    isValid = false
  }

  if (!form.registration_token || form.registration_token.trim().length < 8) {
    errors.registration_token = 'Token registrasi harus diisi (minimal 8 karakter)'
    isValid = false
  }

  return isValid
}

const handleSubmit = async () => {
  if (!validateForm()) {
    return
  }

  isSubmitting.value = true
  errorMessage.value = ''

  try {
    const payload = {
      ekstrakurikuler_id: props.ekskulId,
      nama_lengkap: sanitizeInput(form.nama),
      email: form.email.trim().toLowerCase(),
      no_whatsapp: form.phone.trim(),
      kelas: sanitizeInput(form.kelas),
      alasan_bergabung: sanitizeInput(form.alasan),
      registration_token: form.registration_token.trim().toUpperCase()
    }

    const response = await registerUser(payload)
    console.log('Registration success:', response)
    emit('success')
  } catch (error) {
    console.error('❌ Registration failed:', error)

    if (error.response?.data?.errors) {
      const serverErrors = error.response.data.errors
      Object.keys(serverErrors).forEach((key) => {
        errors[key] = serverErrors[key][0]
      })
    } else if (error.response?.data?.message) {
      errorMessage.value = error.response.data.message
    } else {
      errorMessage.value = 'Terjadi kesalahan. Silakan coba lagi.'
    }
  } finally {
    isSubmitting.value = false
  }
}

defineExpose({ isSubmitting })
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
