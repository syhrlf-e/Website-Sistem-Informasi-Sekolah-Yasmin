/**
 * @component GuruListModal
 * @description Modal form untuk tambah/edit guru dengan image cropper 1:1
 */

<template>
  <Teleport to="body">
    <Transition enter-active-class="transition-opacity duration-300" enter-from-class="opacity-0" leave-active-class="transition-opacity duration-200" leave-to-class="opacity-0">
      <div v-if="show" @click="$emit('close')" class="fixed inset-0 z-[9999] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.stop class="bg-white dark:bg-gray-800 rounded-2xl p-6 w-full max-w-md border border-gray-200 dark:border-gray-700 shadow-2xl">
          <h2 class="text-xl font-bold text-gray-900 dark:text-white font-poppins mb-4">
            {{ editMode ? 'Edit Guru' : 'Tambah Guru' }}
          </h2>

          <form @submit.prevent="$emit('submit')" class="space-y-4">
            <!-- Photo Upload with Cropper -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Foto Guru</label>
              
              <!-- Preview dengan hover overlay -->
              <div v-if="croppedPreview || photoPreview" class="mb-3">
                <div class="relative group w-32 h-32 mx-auto">
                  <img 
                    :src="croppedPreview || photoPreview" 
                    class="w-full h-full object-cover rounded-xl border-2 border-gray-200 dark:border-gray-600" 
                  />
                  <!-- Overlay -->
                  <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity rounded-xl flex items-center justify-center gap-2">
                    <button
                      type="button"
                      @click="triggerFileInput"
                      class="p-2 bg-white/90 text-gray-900 rounded-lg hover:bg-white transition-colors"
                      title="Ganti"
                    >
                      <ImageIcon class="w-4 h-4" />
                    </button>
                    <button
                      type="button"
                      @click="handleRemovePhoto"
                      class="p-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors"
                      title="Hapus"
                    >
                      <Trash2 class="w-4 h-4" />
                    </button>
                  </div>
                  <!-- Cropped Badge -->
                  <div v-if="croppedPreview" class="absolute -top-2 -right-2 px-2 py-1 bg-green-500 text-white text-[10px] rounded-full font-medium flex items-center gap-1">
                    <Check class="w-3 h-3" />
                    1:1
                  </div>
                </div>
              </div>
              
              <!-- Upload Area -->
              <div
                v-else
                class="w-32 h-32 mx-auto border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl flex flex-col items-center justify-center cursor-pointer hover:border-blue-500 dark:hover:border-blue-400 transition-colors"
                @click="triggerFileInput"
              >
                <User class="w-10 h-10 text-gray-400 mb-2" />
                <span class="text-xs text-gray-500 dark:text-gray-400">Klik untuk upload</span>
              </div>
              
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-center">Max 2MB. Akan di-crop ke rasio 1:1.</p>
              
              <input 
                ref="photoInput" 
                type="file" 
                accept="image/*" 
                class="hidden"
                @change="handleFileSelect" 
              />
            </div>

            <!-- Nama -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Nama Guru <span class="text-red-500">*</span>
              </label>
              <input v-model="form.nama" type="text" required
                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white font-poppins"
                placeholder="Contoh: Budi Santoso, S.Pd" />
            </div>

            <!-- Pelajaran -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Mata Pelajaran <span class="text-red-500">*</span>
              </label>
              <input v-model="form.pelajaran" type="text" required
                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white font-poppins"
                placeholder="Contoh: Matematika, Fisika" />
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3 pt-4">
              <button type="button" @click="$emit('close')"
                class="flex-1 px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 font-poppins font-medium">
                Batal
              </button>
              <button type="submit" :disabled="submitting"
                class="flex-1 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 disabled:from-gray-400 disabled:to-gray-400 text-white rounded-xl font-medium font-poppins">
                {{ submitting ? 'Menyimpan...' : 'Simpan' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>

    <!-- Image Cropper Modal -->
    <ImageCropperModal
      :show="showCropper"
      :image-src="rawImageSrc"
      :aspect-ratio="1"
      @close="showCropper = false"
      @crop="handleCrop"
      @change-image="handleChangeImage"
    />
  </Teleport>
</template>

<script setup>
import { Check, Image as ImageIcon, Trash2, User } from 'lucide-vue-next'
import { ref, watch } from 'vue'
import ImageCropperModal from '@/components/ui/shared/ImageCropperModal.vue'

const props = defineProps({
  show: Boolean,
  editMode: Boolean,
  form: Object,
  photoPreview: String,
  submitting: Boolean
})

const emit = defineEmits(['close', 'submit', 'photo-crop', 'remove-photo'])

const photoInput = ref(null)
const showCropper = ref(false)
const rawImageSrc = ref('')
const croppedPreview = ref('')

const triggerFileInput = () => {
  photoInput.value?.click()
}

// Handle file selection - open cropper
const handleFileSelect = (event) => {
  const file = event.target.files?.[0]
  if (!file) return

  if (!file.type.startsWith('image/')) {
    alert('Pilih file gambar yang valid!')
    return
  }

  if (file.size > 2 * 1024 * 1024) {
    alert('Ukuran file maksimal 2MB!')
    return
  }

  // Read file and open cropper
  const reader = new FileReader()
  reader.onload = (e) => {
    rawImageSrc.value = e.target.result
    showCropper.value = true
  }
  reader.readAsDataURL(file)
  
  // Reset input
  event.target.value = ''
}

// Handle cropped result from cropper modal
const handleCrop = (blob) => {
  croppedPreview.value = URL.createObjectURL(blob)
  emit('photo-crop', blob)
}

// Handle image change from cropper modal
const handleChangeImage = (newImageSrc) => {
  rawImageSrc.value = newImageSrc
}

// Handle remove photo
const handleRemovePhoto = () => {
  croppedPreview.value = ''
  rawImageSrc.value = ''
  emit('remove-photo')
}

// Reset cropper state when modal closes
watch(() => props.show, (isVisible) => {
  if (!isVisible) {
    croppedPreview.value = ''
    rawImageSrc.value = ''
  }
})
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
