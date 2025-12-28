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
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">NPSN Sekolah</label>
      <input v-model="modelValue.npsn_sekolah" type="text" class="form-input" placeholder="NPSN sekolah asal" />
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
        <label
          :class="modelValue.jurusan_pilihan === 'IPA' ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-200 dark:border-gray-700'"
          class="flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition-all hover:border-green-400"
        >
          <input v-model="modelValue.jurusan_pilihan" type="radio" value="IPA" class="hidden" />
          <div :class="modelValue.jurusan_pilihan === 'IPA' ? 'bg-green-500' : 'bg-gray-300'" class="w-5 h-5 rounded-full flex items-center justify-center">
            <div v-if="modelValue.jurusan_pilihan === 'IPA'" class="w-2 h-2 bg-white rounded-full"></div>
          </div>
          <div>
            <p class="font-semibold text-gray-900 dark:text-white">IPA</p>
            <p class="text-xs text-gray-500">Ilmu Pengetahuan Alam</p>
          </div>
        </label>

        <label
          :class="modelValue.jurusan_pilihan === 'IPS' ? 'border-orange-500 bg-orange-50 dark:bg-orange-900/20' : 'border-gray-200 dark:border-gray-700'"
          class="flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition-all hover:border-orange-400"
        >
          <input v-model="modelValue.jurusan_pilihan" type="radio" value="IPS" class="hidden" />
          <div :class="modelValue.jurusan_pilihan === 'IPS' ? 'bg-orange-500' : 'bg-gray-300'" class="w-5 h-5 rounded-full flex items-center justify-center">
            <div v-if="modelValue.jurusan_pilihan === 'IPS'" class="w-2 h-2 bg-white rounded-full"></div>
          </div>
          <div>
            <p class="font-semibold text-gray-900 dark:text-white">IPS</p>
            <p class="text-xs text-gray-500">Ilmu Pengetahuan Sosial</p>
          </div>
        </label>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

defineProps({
  modelValue: { type: Object, required: true },
  errors: { type: Object, default: () => ({}) }
})

const currentYear = new Date().getFullYear()
const years = computed(() => [currentYear, currentYear + 1])
</script>

<style scoped>
.form-input {
  @apply w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all;
}
</style>
