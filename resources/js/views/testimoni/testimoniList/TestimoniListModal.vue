/**
 * @component TestimoniListModal
 * @description Modal form untuk tambah/edit testimoni
 */

<template>
  <Teleport to="body">
    <Transition enter-active-class="transition-opacity duration-300" enter-from-class="opacity-0" leave-active-class="transition-opacity duration-200" leave-to-class="opacity-0">
      <div v-if="show" class="fixed inset-0 z-[9999] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4" @click.self="$emit('close')">
        <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-2xl w-full border border-gray-200 dark:border-gray-700 shadow-2xl">
          <!-- Header -->
          <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">{{ editMode ? 'Edit Testimoni' : 'Tambah Testimoni Baru' }}</h2>
            <button @click="$emit('close')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
              <X class="w-5 h-5 text-gray-500" />
            </button>
          </div>

          <!-- Form -->
          <form @submit.prevent="$emit('submit')" class="p-6 space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">Nama <span class="text-red-500">*</span></label>
              <input v-model="form.author" type="text" required placeholder="Contoh: Rina Amelia"
                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white font-poppins" />
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">Role/Status <span class="text-red-500">*</span></label>
              <input v-model="form.role" type="text" required placeholder="Contoh: Alumni Angkatan 2024"
                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white font-poppins" />
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">Testimoni <span class="text-red-500">*</span></label>
              <textarea v-model="form.text" rows="4" required placeholder="Tulis testimoni di sini..."
                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white font-poppins resize-none"></textarea>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">Foto (Opsional)</label>
              <div v-if="photoPreview || (editMode && form.photo)" class="mb-3">
                <img :src="photoPreview || getPhotoUrl(form.photo)" alt="Preview" class="w-24 h-24 rounded-full object-cover border-2 border-gray-200 dark:border-gray-700" />
                <button type="button" @click="$emit('remove-photo')" class="mt-2 text-sm text-red-600 dark:text-red-400 hover:underline font-poppins">Hapus Foto</button>
              </div>
              <input ref="photoInput" type="file" accept="image/*" @change="$emit('photo-change', $event)"
                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl font-poppins file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 file:font-semibold" />
              <p class="mt-1 text-xs text-gray-500 font-poppins">Jika tidak diisi, akan menggunakan avatar otomatis dari nama</p>
            </div>

            <div class="flex items-center gap-3 pt-2">
              <input v-model="form.is_active" type="checkbox" id="is_active" class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500" />
              <label for="is_active" class="text-sm font-medium text-gray-900 dark:text-white font-poppins">Tampilkan di website</label>
            </div>

            <div class="flex items-center gap-3 pt-4">
              <button type="button" @click="$emit('close')" class="flex-1 px-4 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-semibold font-poppins">Batal</button>
              <button type="submit" :disabled="submitting" class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-600 to-purple-600 disabled:from-gray-400 disabled:to-gray-400 text-white rounded-xl font-semibold font-poppins">
                {{ submitting ? 'Menyimpan...' : editMode ? 'Update' : 'Simpan' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { X } from 'lucide-vue-next'

defineProps({
  show: Boolean,
  editMode: Boolean,
  form: Object,
  photoPreview: String,
  submitting: Boolean
})

defineEmits(['close', 'submit', 'photo-change', 'remove-photo'])

const getPhotoUrl = (photo) => {
  if (!photo) return null
  return photo.startsWith('http') ? photo : `/storage/testimonials/${photo}`
}
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
