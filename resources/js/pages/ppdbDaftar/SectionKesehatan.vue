<!--
  @component SectionKesehatan
  @description Form section for health information
-->

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- Golongan Darah -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Golongan Darah <span class="text-red-500">*</span></label>
      <select v-model="modelValue.golongan_darah" class="form-input">
        <option value="">Pilih</option>
        <option value="A">A</option>
        <option value="B">B</option>
        <option value="AB">AB</option>
        <option value="O">O</option>
      </select>
    </div>

    <!-- Tinggi Badan -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tinggi Badan (cm) <span class="text-red-500">*</span></label>
      <input v-model.number="modelValue.tinggi_badan" type="number" min="50" max="250" class="form-input" placeholder="165" />
    </div>

    <!-- Berat Badan -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Berat Badan (kg) <span class="text-red-500">*</span></label>
      <input v-model.number="modelValue.berat_badan" type="number" min="20" max="200" class="form-input" placeholder="55" />
    </div>

    <!-- Riwayat Penyakit Dropdown -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Riwayat Penyakit</label>
      <select v-model="hasRiwayatPenyakit" class="form-input">
        <option value="tidak">Tidak Ada</option>
        <option value="ada">Ada</option>
      </select>
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
import { ref, watch } from 'vue'

const props = defineProps({
  modelValue: { type: Object, required: true },
  errors: { type: Object, default: () => ({}) }
})

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
  @apply w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all;
}
</style>

