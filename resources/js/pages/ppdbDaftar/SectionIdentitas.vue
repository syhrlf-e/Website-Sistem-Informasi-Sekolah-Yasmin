<!--
  @component SectionIdentitas
  @description Form section for personal identity data
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
      <input v-model="modelValue.nik" type="text" maxlength="16" class="form-input" placeholder="16 digit NIK" />
      <p v-if="errors?.nik" class="text-red-500 text-xs mt-1">{{ errors.nik[0] }}</p>
    </div>

    <!-- NISN -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">NISN</label>
      <input v-model="modelValue.nisn" type="text" maxlength="10" class="form-input" placeholder="10 digit NISN" />
    </div>

    <!-- Tempat Lahir -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tempat Lahir <span class="text-red-500">*</span></label>
      <input v-model="modelValue.tempat_lahir" type="text" class="form-input" placeholder="Kota/Kabupaten" />
    </div>

    <!-- Tanggal Lahir -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Lahir <span class="text-red-500">*</span></label>
      <input v-model="modelValue.tanggal_lahir" type="date" class="form-input" />
    </div>

    <!-- Jenis Kelamin -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
      <select v-model="modelValue.jenis_kelamin" class="form-input">
        <option value="">Pilih</option>
        <option value="Laki-laki">Laki-laki</option>
        <option value="Perempuan">Perempuan</option>
      </select>
    </div>

    <!-- Agama -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Agama <span class="text-red-500">*</span></label>
      <select v-model="modelValue.agama" class="form-input">
        <option value="">Pilih</option>
        <option value="Islam">Islam</option>
        <option value="Kristen">Kristen</option>
        <option value="Katolik">Katolik</option>
        <option value="Hindu">Hindu</option>
        <option value="Buddha">Buddha</option>
        <option value="Konghucu">Konghucu</option>
      </select>
    </div>

    <!-- Anak Ke -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Anak Ke</label>
      <input v-model.number="modelValue.anak_ke" type="number" min="1" class="form-input" />
    </div>

    <!-- Jumlah Saudara -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jumlah Saudara</label>
      <input v-model.number="modelValue.jumlah_saudara" type="number" min="0" class="form-input" />
    </div>

    <!-- No HP -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">No. HP/WhatsApp <span class="text-red-500">*</span></label>
      <input v-model="modelValue.no_hp" type="tel" class="form-input" placeholder="08xxxxxxxxxx" />
    </div>

    <!-- Email -->
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
      <input v-model="modelValue.email" type="email" class="form-input" placeholder="email@example.com" />
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
