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
        <input v-model="modelValue.nik_wali" type="text" maxlength="16" class="form-input" placeholder="16 digit NIK" />
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
        <input v-model="modelValue.no_hp_wali" type="tel" class="form-input" placeholder="08xxxxxxxxxx" />
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
</script>

<style scoped>
.form-input {
  @apply w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all;
}
</style>
