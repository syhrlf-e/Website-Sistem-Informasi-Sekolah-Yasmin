<!--
  @component SectionKesehatan
  @description Form section for health information
-->

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- Golongan Darah -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Golongan Darah <span class="text-red-500">*</span></label>
      <div class="relative">
        <select v-model="modelValue.golongan_darah" class="form-select">
          <option value="">Pilih</option>
          <option value="A">A</option>
          <option value="B">B</option>
          <option value="AB">AB</option>
          <option value="O">O</option>
        </select>
        <ChevronDown class="select-icon" />
      </div>
    </div>

    <!-- Tinggi Badan -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tinggi Badan (cm) <span class="text-red-500">*</span></label>
      <input 
        :value="modelValue.tinggi_badan" 
        @input="handleNumeric($event, 'tinggi_badan', 3)" 
        @keypress="onlyNumbers"
        type="text" 
        inputmode="numeric"
        maxlength="3" 
        class="form-input" 
        placeholder="165" 
      />
    </div>

    <!-- Berat Badan -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Berat Badan (kg) <span class="text-red-500">*</span></label>
      <input 
        :value="modelValue.berat_badan" 
        @input="handleNumeric($event, 'berat_badan', 3)" 
        @keypress="onlyNumbers"
        type="text" 
        inputmode="numeric"
        maxlength="3" 
        class="form-input" 
        placeholder="55" 
      />
    </div>

    <!-- Riwayat Penyakit Dropdown -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Riwayat Penyakit</label>
      <div class="relative">
        <select v-model="hasRiwayatPenyakit" class="form-select">
          <option value="tidak">Tidak Ada</option>
          <option value="ada">Ada</option>
        </select>
        <ChevronDown class="select-icon" />
      </div>
    </div>

    <!-- Riwayat Penyakit Detail (shown when "Ada" selected) -->
    <div v-if="hasRiwayatPenyakit === 'ada'" class="md:col-span-2">
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Keterangan Riwayat Penyakit</label>
      <textarea v-model="modelValue.riwayat_penyakit" rows="2" class="form-input" placeholder="Jelaskan riwayat penyakit yang dimiliki"></textarea>
    </div>

    <!-- Alergi -->
    <div class="md:col-span-2">
      <label class="flex items-center gap-2 cursor-pointer">
        <input v-model="modelValue.alergi" type="checkbox" class="w-4 h-4 rounded border-gray-300" />
        <span class="text-sm text-gray-700 dark:text-gray-300">Memiliki alergi</span>
      </label>
    </div>

    <!-- Keterangan Alergi -->
    <div v-if="modelValue.alergi" class="md:col-span-2">
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Keterangan Alergi</label>
      <textarea v-model="modelValue.keterangan_alergi" rows="2" class="form-input" placeholder="Jelaskan alergi yang dimiliki"></textarea>
    </div>
  </div>
</template>

<script setup>
import { ChevronDown } from 'lucide-vue-next'
import { ref, watch } from 'vue'

const props = defineProps({
  modelValue: { type: Object, required: true },
  errors: { type: Object, default: () => ({}) }
})

// Block non-numeric keypress
const onlyNumbers = (event) => {
  const char = String.fromCharCode(event.which || event.keyCode)
  if (!/[0-9]/.test(char)) {
    event.preventDefault()
  }
}

// Handle numeric input with max length
const handleNumeric = (event, field, maxLen) => {
  const value = event.target.value.replace(/\D/g, '').slice(0, maxLen)
  props.modelValue[field] = value ? Number(value) : null
}

// Track if user has riwayat penyakit
const hasRiwayatPenyakit = ref(props.modelValue.riwayat_penyakit ? 'ada' : 'tidak')

// Clear riwayat_penyakit when user selects "Tidak Ada"
watch(hasRiwayatPenyakit, (newVal) => {
  if (newVal === 'tidak') {
    props.modelValue.riwayat_penyakit = ''
  }
})

// Sync initial value
watch(() => props.modelValue.riwayat_penyakit, (newVal) => {
  if (newVal && hasRiwayatPenyakit.value === 'tidak') {
    hasRiwayatPenyakit.value = 'ada'
  }
}, { immediate: true })
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

