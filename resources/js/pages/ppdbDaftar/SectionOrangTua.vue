<!--
  @component SectionOrangTua
  @description Form section for parents data (Ayah & Ibu)
-->

<template>
  <div class="space-y-6">
    <!-- Data Ayah -->
    <div>
      <h4 class="text-sm font-semibold text-blue-600 dark:text-blue-400 mb-3 uppercase tracking-wide">Data Ayah</h4>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Ayah <span class="text-red-500">*</span></label>
          <input v-model="modelValue.nama_ayah" type="text" class="form-input" placeholder="Nama lengkap ayah" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">NIK Ayah</label>
          <input v-model="modelValue.nik_ayah" type="text" maxlength="16" class="form-input" placeholder="16 digit NIK" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pekerjaan <span class="text-red-500">*</span></label>
          <input v-model="modelValue.pekerjaan_ayah" type="text" class="form-input" placeholder="Pekerjaan ayah" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pendidikan Terakhir <span class="text-red-500">*</span></label>
          <select v-model="modelValue.pendidikan_ayah" class="form-input">
            <option value="">Pilih</option>
            <option v-for="edu in pendidikanOptions" :key="edu" :value="edu">{{ edu }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Penghasilan/Bulan</label>
          <select v-model="modelValue.penghasilan_ayah" class="form-input">
            <option value="">Pilih</option>
            <option v-for="income in penghasilanOptions" :key="income" :value="income">{{ income }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">No. HP</label>
          <input v-model="modelValue.no_hp_ayah" type="tel" class="form-input" placeholder="08xxxxxxxxxx" />
        </div>
      </div>
    </div>

    <hr class="border-gray-200 dark:border-gray-700" />

    <!-- Data Ibu -->
    <div>
      <h4 class="text-sm font-semibold text-pink-600 dark:text-pink-400 mb-3 uppercase tracking-wide">Data Ibu</h4>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Ibu <span class="text-red-500">*</span></label>
          <input v-model="modelValue.nama_ibu" type="text" class="form-input" placeholder="Nama lengkap ibu" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">NIK Ibu</label>
          <input v-model="modelValue.nik_ibu" type="text" maxlength="16" class="form-input" placeholder="16 digit NIK" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pekerjaan <span class="text-red-500">*</span></label>
          <input v-model="modelValue.pekerjaan_ibu" type="text" class="form-input" placeholder="Pekerjaan ibu" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pendidikan Terakhir <span class="text-red-500">*</span></label>
          <select v-model="modelValue.pendidikan_ibu" class="form-input">
            <option value="">Pilih</option>
            <option v-for="edu in pendidikanOptions" :key="edu" :value="edu">{{ edu }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Penghasilan/Bulan</label>
          <select v-model="modelValue.penghasilan_ibu" class="form-input">
            <option value="">Pilih</option>
            <option v-for="income in penghasilanOptions" :key="income" :value="income">{{ income }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">No. HP</label>
          <input v-model="modelValue.no_hp_ibu" type="tel" class="form-input" placeholder="08xxxxxxxxxx" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  modelValue: { type: Object, required: true },
  errors: { type: Object, default: () => ({}) }
})

const pendidikanOptions = ['SD', 'SMP', 'SMA/SMK', 'D1', 'D2', 'D3', 'D4/S1', 'S2', 'S3']
const penghasilanOptions = [
  '< Rp 1.000.000',
  'Rp 1.000.000 - Rp 3.000.000',
  'Rp 3.000.000 - Rp 5.000.000',
  'Rp 5.000.000 - Rp 10.000.000',
  '> Rp 10.000.000'
]
</script>

<style scoped>
.form-input {
  @apply w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all;
}
</style>
