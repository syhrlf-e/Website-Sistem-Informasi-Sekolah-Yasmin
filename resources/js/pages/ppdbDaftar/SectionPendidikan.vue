<!--
  @component SectionPendidikan
  @description Form section for education and major selection
-->

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- Asal Sekolah -->
    <div class="md:col-span-2">
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Asal Sekolah (SMP/MTs) <span class="text-red-500">*</span></label>
      <input v-model="modelValue.asal_sekolah" type="text" class="form-input" placeholder="Nama sekolah asal" />
    </div>

    <!-- NPSN -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">NPSN Sekolah <span class="text-red-500">*</span></label>
      <input 
        :value="modelValue.npsn_sekolah" 
        @input="handleNpsn" 
        type="text" 
        inputmode="numeric"
        maxlength="8" 
        class="form-input" 
        placeholder="8 digit NPSN" 
      />
    </div>

    <!-- Tahun Lulus -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tahun Lulus <span class="text-red-500">*</span></label>
      <select v-model.number="modelValue.tahun_lulus" class="form-input">
        <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
      </select>
    </div>

    <!-- Alamat Sekolah -->
    <div class="md:col-span-2">
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alamat Sekolah Asal</label>
      <textarea v-model="modelValue.alamat_sekolah" rows="2" class="form-input" placeholder="Alamat lengkap sekolah asal"></textarea>
    </div>

    <!-- Jurusan Pilihan -->
    <div class="md:col-span-2">
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Jurusan Pilihan <span class="text-red-500">*</span></label>
      <div class="grid grid-cols-2 gap-4">
        <div
          @click="modelValue.jurusan_pilihan = 'IPA'"
          :class="modelValue.jurusan_pilihan === 'IPA' ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-green-400'"
          class="flex flex-col items-center justify-center p-6 border-2 rounded-xl cursor-pointer transition-all"
        >
          <div 
            :class="modelValue.jurusan_pilihan === 'IPA' ? 'bg-green-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-500'" 
            class="w-12 h-12 rounded-xl flex items-center justify-center mb-3 transition-all"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
            </svg>
          </div>
          <p class="font-semibold text-gray-900 dark:text-white">IPA</p>
          <p class="text-xs text-gray-500 text-center">Ilmu Pengetahuan Alam</p>
        </div>

        <div
          @click="modelValue.jurusan_pilihan = 'IPS'"
          :class="modelValue.jurusan_pilihan === 'IPS' ? 'border-orange-500 bg-orange-50 dark:bg-orange-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-orange-400'"
          class="flex flex-col items-center justify-center p-6 border-2 rounded-xl cursor-pointer transition-all"
        >
          <div 
            :class="modelValue.jurusan_pilihan === 'IPS' ? 'bg-orange-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-500'" 
            class="w-12 h-12 rounded-xl flex items-center justify-center mb-3 transition-all"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <p class="font-semibold text-gray-900 dark:text-white">IPS</p>
          <p class="text-xs text-gray-500 text-center">Ilmu Pengetahuan Sosial</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: { type: Object, required: true },
  errors: { type: Object, default: () => ({}) }
})

const currentYear = new Date().getFullYear()
const years = computed(() => [currentYear, currentYear + 1])

// Handle NPSN - only allow numbers, max 8 digits
const handleNpsn = (event) => {
  const value = event.target.value.replace(/\D/g, '').slice(0, 8)
  props.modelValue.npsn_sekolah = value
}
</script>

<style scoped>
.form-input {
  @apply w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all;
}
</style>
