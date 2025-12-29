<!--
  @component SectionWali
  @description Form section for guardian data (optional)
-->

<template>
  <div class="space-y-4">
    <p class="text-sm text-gray-500 dark:text-gray-400 italic">
      Isi bagian ini jika wali berbeda dari orang tua atau orang tua tidak dapat dihubungi
    </p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Wali</label>
        <input v-model="modelValue.nama_wali" type="text" class="form-input" placeholder="Nama lengkap wali" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">NIK Wali</label>
        <input 
          :value="modelValue.nik_wali" 
          @input="handleNik" 
          @keypress="onlyNumbers"
          type="text" 
          inputmode="numeric"
          maxlength="16" 
          class="form-input" 
          placeholder="16 digit NIK" 
        />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Hubungan dengan Siswa</label>
        <input v-model="modelValue.hubungan_wali" type="text" class="form-input" placeholder="Contoh: Paman, Kakek, dll" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pekerjaan</label>
        <input v-model="modelValue.pekerjaan_wali" type="text" class="form-input" placeholder="Pekerjaan wali" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pendidikan Terakhir</label>
        <select v-model="modelValue.pendidikan_wali" class="form-input">
          <option value="">Pilih</option>
          <option v-for="edu in pendidikanOptions" :key="edu" :value="edu">{{ edu }}</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">No. HP</label>
        <input 
          :value="modelValue.no_hp_wali" 
          @input="handlePhone" 
          @keypress="onlyNumbers"
          type="tel" 
          inputmode="numeric"
          maxlength="13" 
          class="form-input" 
          placeholder="08xxxxxxxxxx" 
        />
      </div>
    </div>

    <!-- Skip Button -->
    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
      <div class="flex items-center justify-between gap-3">
        <p class="text-sm md:text-sm text-gray-500 dark:text-gray-400">
          Tidak relevan? Klik lewati
        </p>
        <button
          @click="$emit('skip')"
          type="button"
          class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl transition-colors flex-shrink-0"
        >
          Lewati
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  modelValue: { type: Object, required: true },
  errors: { type: Object, default: () => ({}) }
})

defineEmits(['skip'])

const pendidikanOptions = ['SD', 'SMP', 'SMA/SMK', 'D1', 'D2', 'D3', 'D4/S1', 'S2', 'S3']

// Block non-numeric keypress
const onlyNumbers = (event) => {
  const char = String.fromCharCode(event.which || event.keyCode)
  if (!/[0-9]/.test(char)) {
    event.preventDefault()
  }
}

// Handle NIK - only allow numbers, 16 digits
const handleNik = (event) => {
  const value = event.target.value.replace(/\D/g, '').slice(0, 16)
  props.modelValue.nik_wali = value
}

// Handle phone - only numbers, max 13 digits
const handlePhone = (event) => {
  let value = event.target.value.replace(/\D/g, '')
  if (value.length > 0 && !value.startsWith('0')) {
    value = '08' + value
  }
  props.modelValue.no_hp_wali = value.slice(0, 13)
}
</script>

<style scoped>
.form-input {
  @apply w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all;
}
</style>
