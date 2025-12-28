<!--
  @component SectionAlamat
  @description Form section for address data
-->

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- Alamat Lengkap -->
    <div class="md:col-span-2">
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alamat Lengkap <span class="text-red-500">*</span></label>
      <textarea v-model="modelValue.alamat_lengkap" rows="2" class="form-input" placeholder="Jalan, No. Rumah, Gang, dll"></textarea>
    </div>

    <!-- RT -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">RT <span class="text-red-500">*</span></label>
      <input v-model="modelValue.rt" type="text" maxlength="5" class="form-input" placeholder="001" />
    </div>

    <!-- RW -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">RW <span class="text-red-500">*</span></label>
      <input v-model="modelValue.rw" type="text" maxlength="5" class="form-input" placeholder="001" />
    </div>

    <!-- Kelurahan -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kelurahan/Desa <span class="text-red-500">*</span></label>
      <input v-model="modelValue.kelurahan" type="text" class="form-input" placeholder="Nama kelurahan" />
    </div>

    <!-- Kecamatan -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kecamatan <span class="text-red-500">*</span></label>
      <input v-model="modelValue.kecamatan" type="text" class="form-input" placeholder="Nama kecamatan" />
    </div>

    <!-- Kota/Kabupaten -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kota/Kabupaten <span class="text-red-500">*</span></label>
      <input v-model="modelValue.kota_kabupaten" type="text" class="form-input" placeholder="Nama kota/kabupaten" />
    </div>

    <!-- Provinsi -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Provinsi <span class="text-red-500">*</span></label>
      <input v-model="modelValue.provinsi" type="text" class="form-input" placeholder="Nama provinsi" />
    </div>

    <!-- Kode Pos -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kode Pos</label>
      <input v-model="modelValue.kode_pos" type="text" maxlength="10" class="form-input" placeholder="12345" />
    </div>
  </div>
</template>

<script setup>
defineProps({
  modelValue: { type: Object, required: true },
  errors: { type: Object, default: () => ({}) }
})
</script>

<style scoped>
.form-input {
  @apply w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all;
}
</style>
