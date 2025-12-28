/**
 * @component PrestasiListModal
 * @description Modal form untuk tambah/edit prestasi dengan image cropper
 * Gambar di-crop dengan rasio 4:3 dan disimpan langsung ke server
 */

<template>
  <Teleport to="body">
    <Transition enter-active-class="transition-opacity duration-300" enter-from-class="opacity-0" leave-active-class="transition-opacity duration-200" leave-to-class="opacity-0">
      <div v-if="show" class="fixed inset-0 z-[9999] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-3xl w-full max-h-[90vh] flex flex-col border border-gray-200 dark:border-gray-700 shadow-2xl">
          <!-- Header -->
          <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">{{ editMode ? 'Edit Prestasi' : 'Tambah Prestasi Baru' }}</h2>
            <button @click="$emit('close')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
              <X class="w-5 h-5 text-gray-500" />
            </button>
          </div>

          <!-- Form Body -->
          <div class="overflow-y-auto flex-1 p-6 modal-scrollbar">
            <div class="space-y-4">
              <!-- Nama Prestasi -->
              <div>
                <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">Nama Prestasi <span class="text-red-500">*</span></label>
                <input v-model="localForm.nama_prestasi" type="text" required placeholder="Contoh: Juara 1 Olimpiade Matematika Nasional"
                  class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white font-poppins" />
              </div>

              <!-- Kategori & Tingkat -->
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">Kategori <span class="text-red-500">*</span></label>
                  <select v-model="localForm.kategori" required class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white font-poppins">
                    <option value="">Pilih Kategori</option>
                    <option value="Akademik">Akademik</option>
                    <option value="Olahraga">Olahraga</option>
                    <option value="Seni">Seni</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">Tingkat <span class="text-red-500">*</span></label>
                  <select v-model="localForm.tingkat" required class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white font-poppins">
                    <option value="">Pilih Tingkat</option>
                    <option value="Sekolah">Sekolah</option>
                    <option value="Kota">Kota</option>
                    <option value="Provinsi">Provinsi</option>
                    <option value="Nasional">Nasional</option>
                    <option value="Internasional">Internasional</option>
                  </select>
                </div>
              </div>

              <!-- Tahun -->
              <div>
                <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">Tahun <span class="text-red-500">*</span></label>
                <input v-model="localForm.tahun" type="number" required :min="2000" :max="new Date().getFullYear() + 1" placeholder="2024"
                  class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white font-poppins" />
              </div>

              <!-- Peserta -->
              <div>
                <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">Peserta/Pemenang <span class="text-red-500">*</span></label>
                <div class="space-y-2">
                  <div v-for="(peserta, index) in localForm.peserta" :key="index" class="flex items-center gap-2">
                    <input v-model="localForm.peserta[index]" type="text" required placeholder="Nama peserta"
                      class="flex-1 px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white font-poppins" />
                    <button v-if="localForm.peserta.length > 1" type="button" @click="removePeserta(index)"
                      class="p-3 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-xl">
                      <Trash2 class="w-4 h-4" />
                    </button>
                  </div>
                  <button type="button" @click="addPeserta"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-dashed border-gray-300 dark:border-gray-600 rounded-xl text-gray-600 dark:text-gray-400 font-poppins font-medium">+ Tambah Peserta</button>
                </div>
              </div>

              <!-- Image Upload with Cropper -->
              <div>
                <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">Gambar Prestasi</label>
                
                <!-- Preview Cropped Image -->
                <div v-if="croppedPreview || (editMode && localForm.image)" class="mb-3">
                  <div class="relative group">
                    <img 
                      :src="croppedPreview || getImageUrl(localForm.image)" 
                      alt="Preview" 
                      class="w-full h-48 object-cover rounded-xl border border-gray-200 dark:border-gray-700" 
                    />
                    <!-- Overlay dengan badge -->
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity rounded-xl flex items-center justify-center gap-3">
                      <button 
                        type="button" 
                        @click="triggerFileInput"
                        class="px-4 py-2 bg-white/90 text-gray-900 rounded-lg font-medium text-sm flex items-center gap-2 hover:bg-white transition-colors"
                      >
                        <ImageIcon class="w-4 h-4" />
                        Ganti
                      </button>
                      <button 
                        type="button" 
                        @click="removeImage"
                        class="px-4 py-2 bg-red-500 text-white rounded-lg font-medium text-sm flex items-center gap-2 hover:bg-red-600 transition-colors"
                      >
                        <Trash2 class="w-4 h-4" />
                        Hapus
                      </button>
                    </div>
                    <!-- Cropped Badge -->
                    <div v-if="croppedPreview" class="absolute top-2 left-2 px-2 py-1 bg-green-500 text-white text-xs rounded-full font-medium flex items-center gap-1">
                      <Check class="w-3 h-3" />
                      Gambar dikrop (4:3)
                    </div>
                  </div>
                </div>

                <!-- File Input - always in DOM but hidden when has preview -->
                <div :class="{ 'hidden': croppedPreview || (editMode && localForm.image) }">
                  <input 
                    ref="fileInput" 
                    type="file" 
                    accept="image/*" 
                    @change="handleFileSelect"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl font-poppins file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 file:font-semibold" 
                  />
                </div>
                <p class="mt-1 text-xs text-gray-500 font-poppins">
                  Format: JPG, PNG, WEBP. Max: 2MB. Gambar akan di-crop otomatis ke rasio 4:3.
                </p>
              </div>

              <!-- Deskripsi -->
              <div>
                <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">Deskripsi <span class="text-red-500">*</span></label>
                <textarea v-model="localForm.deskripsi" rows="4" required placeholder="Deskripsi lengkap tentang prestasi..."
                  class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white font-poppins resize-none"></textarea>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 flex-shrink-0">
            <button @click="handleSubmit" :disabled="submitting"
              class="w-full px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 disabled:from-gray-400 disabled:to-gray-400 text-white rounded-xl font-semibold font-poppins shadow-lg">
              {{ submitting ? 'Menyimpan...' : editMode ? 'Update' : 'Simpan' }}
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Image Cropper Modal -->
    <ImageCropperModal
      :show="showCropper"
      :image-src="rawImageSrc"
      :aspect-ratio="4/3"
      @close="showCropper = false"
      @crop="handleCrop"
      @change-image="handleChangeImage"
    />
  </Teleport>
</template>

<script setup>
import { Check, Image as ImageIcon, Trash2, X } from 'lucide-vue-next'
import { ref, watch } from 'vue'
import ImageCropperModal from '@/components/ui/shared/ImageCropperModal.vue'

const props = defineProps({
  show: Boolean,
  editMode: Boolean,
  form: Object,
  imagePreview: String,
  submitting: Boolean
})

const emit = defineEmits(['close', 'submit', 'image-change', 'remove-image'])

const localForm = ref({ ...props.form })
const fileInput = ref(null)
const showCropper = ref(false)
const rawImageSrc = ref('')
const croppedPreview = ref('')
const croppedBlob = ref(null)

// Watch form changes
watch(() => props.form, (newForm) => { 
  localForm.value = { ...newForm }
  // Reset local state when form changes
  if (!props.show) {
    croppedPreview.value = ''
    croppedBlob.value = null
    rawImageSrc.value = ''
  }
}, { deep: true })

// Reset state when modal closes
watch(() => props.show, (isVisible) => {
  if (!isVisible) {
    croppedPreview.value = ''
    croppedBlob.value = null
    rawImageSrc.value = ''
  }
})

const addPeserta = () => localForm.value.peserta.push('')
const removePeserta = (index) => localForm.value.peserta.splice(index, 1)

const getImageUrl = (imagePath) => {
  if (!imagePath) return null
  if (imagePath.startsWith('http')) return imagePath
  if (imagePath.startsWith('/storage')) return imagePath
  if (imagePath.startsWith('data:')) return imagePath
  if (imagePath.includes('/')) {
    return `/storage/${imagePath}`
  }
  return `/storage/prestasi/${imagePath}`
}

const triggerFileInput = () => {
  fileInput.value?.click()
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
}

// Handle cropped result from cropper modal
const handleCrop = (blob) => {
  croppedBlob.value = blob
  croppedPreview.value = URL.createObjectURL(blob)
}

// Handle image change from cropper modal
const handleChangeImage = (newImageSrc, file) => {
  rawImageSrc.value = newImageSrc
  // Cropper modal stays open with new image
}

// Remove image
const removeImage = () => {
  croppedPreview.value = ''
  croppedBlob.value = null
  rawImageSrc.value = ''
  localForm.value.image = null
  if (fileInput.value) {
    fileInput.value.value = ''
  }
  emit('remove-image')
}

// Handle form submit
const handleSubmit = () => {
  const formData = { ...localForm.value }
  
  // Include cropped blob for upload
  if (croppedBlob.value) {
    formData._croppedImage = croppedBlob.value
  }
  
  emit('submit', formData)
}
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
.modal-scrollbar { scrollbar-width: thin; scrollbar-color: rgba(37, 99, 235, 0.3) transparent; }
.modal-scrollbar::-webkit-scrollbar { width: 8px; }
.modal-scrollbar::-webkit-scrollbar-thumb { background: linear-gradient(180deg, rgba(37, 99, 235, 0.4), rgba(37, 99, 235, 0.6)); border-radius: 10px; }
</style>
