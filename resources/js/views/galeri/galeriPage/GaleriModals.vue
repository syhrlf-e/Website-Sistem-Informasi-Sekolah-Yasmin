/**
 * @component GaleriModals
 * @description Modals untuk upload dan edit galeri dengan multi-image support
 * - Unified card design untuk existing dan new images
 * - Arrow navigasi di samping card (vertically centered)
 * - Tiap foto punya title dan description sendiri
 */

<template>
  <!-- Edit Modal with Side Navigation Arrows -->
  <Transition enter-active-class="transition-opacity duration-300" enter-from-class="opacity-0" leave-active-class="transition-opacity duration-200" leave-to-class="opacity-0">
    <div v-if="showEdit" @click="handleCloseEdit" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
      <!-- Container with arrows positioned outside -->
      <div class="relative flex items-center gap-2">
        <!-- Left Arrow (hidden on first image) -->
        <button
          v-if="totalImages > 1 && !isAddingNewImage && currentImageIndex > 0"
          type="button"
          @click.stop="prevImage"
          class="w-10 h-10 flex items-center justify-center bg-white dark:bg-gray-800 rounded-full shadow-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
        >
          <ChevronLeft class="w-6 h-6 text-gray-700 dark:text-gray-300" />
        </button>
        <div v-else class="w-10"></div> <!-- Spacer -->
        
        <!-- Main Card -->
        <div @click.stop class="bg-white dark:bg-gray-800 rounded-2xl p-6 w-[28rem] max-w-[calc(100vw-120px)] shadow-2xl">
          <!-- Header -->
          <div class="flex items-center justify-between mb-6">
            <div>
              <h2 class="text-xl font-bold text-gray-900 dark:text-white font-poppins">
                {{ isAddingNewImage ? 'Tambah Foto Baru' : 'Edit' }} Slot {{ editData.grid_position }}
              </h2>
              <p class="text-sm text-gray-500">
                {{ isAddingNewImage ? `Foto ke-${totalImages + 1} dari 3` : `Foto ${currentImageIndex + 1} dari ${totalImages}` }}
              </p>
            </div>
            <button @click="handleCloseEdit" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
              <X class="w-5 h-5 text-gray-500" />
            </button>
          </div>

          <form @submit.prevent="handleSubmit" class="space-y-4">
            <!-- Image Preview / Upload Area -->
            <div class="relative aspect-video rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-900">
              <!-- Show image if exists with swipe transition -->
              <Transition :name="slideDirection" mode="out-in">
                <div v-if="currentDisplayImage" :key="currentDisplayImage" class="absolute inset-0">
                  <img :src="currentDisplayImage" alt="Preview" class="w-full h-full object-cover" />
                  <!-- Badge -->
                  <div v-if="isAddingNewImage && newImagePreview" class="absolute top-2 left-2 px-2 py-1 bg-green-500 text-white text-xs rounded-full font-medium">
                    ✓ Gambar baru
                  </div>
                  <!-- Ganti Gambar overlay -->
                  <button type="button" @click="handleSelectImage" class="absolute inset-0 bg-black/50 opacity-0 hover:opacity-100 transition-opacity flex items-center justify-center">
                    <div class="text-center text-white">
                      <Upload class="w-8 h-8 mx-auto mb-2" />
                      <p class="text-sm font-medium">Ganti Gambar</p>
                    </div>
                  </button>
                </div>
              </Transition>
              <!-- Empty upload area (when no image) -->
              <div v-if="!currentDisplayImage" @click="handleSelectImage" class="w-full h-full flex flex-col items-center justify-center cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-800 transition-colors">
                <Upload class="w-12 h-12 text-gray-400 mb-3" />
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Klik untuk pilih gambar</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, WEBP (max 2MB)</p>
              </div>
            </div>

            <!-- Title Input -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 font-poppins">
                Judul <span class="text-red-500">*</span>
              </label>
              <input
                v-model="currentTitle"
                type="text"
                required
                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                placeholder="Masukkan judul foto"
              />
            </div>
            
            <!-- Description Input -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 font-poppins">Deskripsi</label>
              <textarea
                v-model="currentDescription"
                rows="2"
                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none"
                placeholder="Masukkan deskripsi (opsional)"
              ></textarea>
            </div>
            
            <!-- Is Active (shown on all cards when not adding new) -->
            <div v-if="!isAddingNewImage" class="flex items-center gap-3">
              <input v-model="editData.is_active" type="checkbox" id="is_active_edit" class="w-5 h-5 text-purple-600 rounded" />
              <label for="is_active_edit" class="text-sm font-medium text-gray-700 dark:text-gray-300 font-poppins">Tampilkan di frontend</label>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col gap-2 pt-2">
              <!-- Add more photo button (if < 3 and not already adding) -->
              <button
                v-if="totalImages < 3 && !isAddingNewImage"
                type="button"
                @click="startAddImage"
                class="w-full px-4 py-2.5 border-2 border-dashed border-purple-300 dark:border-purple-600 text-purple-600 dark:text-purple-400 rounded-xl font-medium hover:bg-purple-50 dark:hover:bg-purple-900/20 font-poppins flex items-center justify-center gap-2"
              >
                <Plus class="w-4 h-4" />
                Tambah Foto ke {{ totalImages + 1 }}/3
              </button>

              <!-- Delete current image button (if > 1 image and not adding) -->
              <button
                v-if="totalImages > 1 && !isAddingNewImage"
                type="button"
                @click="$emit('delete-image', currentImage?.id)"
                class="w-full px-4 py-2.5 border border-red-300 dark:border-red-600 text-red-600 dark:text-red-400 rounded-xl font-medium hover:bg-red-50 dark:hover:bg-red-900/20 font-poppins flex items-center justify-center gap-2"
              >
                <Trash2 class="w-4 h-4" />
                Hapus Foto Ini
              </button>

              <!-- Main action buttons -->
              <div class="flex gap-3 pt-2">
                <button
                  type="button"
                  @click="isAddingNewImage ? cancelAddImage() : $emit('close-edit')"
                  class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-gray-700 font-poppins"
                >
                  {{ isAddingNewImage ? 'Kembali' : 'Batal' }}
                </button>
                <button
                  type="submit"
                  :disabled="saving || (isAddingNewImage && !newImagePreview)"
                  class="flex-1 px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-medium font-poppins disabled:opacity-50"
                >
                  {{ saving ? 'Menyimpan...' : 'Simpan' }}
                </button>
              </div>
            </div>
          </form>
        </div>

        <!-- Right Arrow (hidden on last image) -->
        <button
          v-if="totalImages > 1 && !isAddingNewImage && currentImageIndex < totalImages - 1"
          type="button"
          @click.stop="nextImage"
          class="w-10 h-10 flex items-center justify-center bg-white dark:bg-gray-800 rounded-full shadow-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
        >
          <ChevronRight class="w-6 h-6 text-gray-700 dark:text-gray-300" />
        </button>
        <div v-else class="w-10"></div> <!-- Spacer -->
      </div>
    </div>
  </Transition>

  <!-- Upload Modal (for new slot - first image) -->
  <Transition enter-active-class="transition-opacity duration-300" enter-from-class="opacity-0" leave-active-class="transition-opacity duration-200" leave-to-class="opacity-0">
    <div v-if="showUpload" @click="$emit('close-upload')" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
      <div @click.stop class="bg-white dark:bg-gray-800 rounded-2xl p-6 w-full max-w-md shadow-2xl">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-xl font-bold text-gray-900 dark:text-white font-poppins">Upload ke Slot {{ selectedSlot }}</h2>
          <button @click="$emit('close-upload')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
            <X class="w-5 h-5 text-gray-500" />
          </button>
        </div>
        <form @submit.prevent="$emit('submit-upload')" class="space-y-4">
          <!-- Image Preview -->
          <div v-if="uploadData.imagePreview" class="relative aspect-video rounded-xl overflow-hidden">
            <img :src="uploadData.imagePreview" alt="Preview" class="w-full h-full object-cover" />
            <button type="button" @click="$emit('select-file')" class="absolute top-2 right-2 p-2 bg-white/90 dark:bg-gray-800/90 rounded-lg hover:bg-white dark:hover:bg-gray-800">
              <Upload class="w-4 h-4 text-gray-700 dark:text-gray-300" />
            </button>
          </div>
          <!-- Upload Area -->
          <div v-else @click="$emit('select-file')" class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-8 text-center hover:border-purple-500 cursor-pointer">
            <Upload class="w-12 h-12 text-gray-400 mx-auto mb-3" />
            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Klik untuk pilih gambar</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, WEBP (max 2MB)</p>
          </div>
          <!-- Title -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 font-poppins">Judul <span class="text-red-500">*</span></label>
            <input v-model="uploadData.title" type="text" required class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white" placeholder="Masukkan judul foto" />
          </div>
          <!-- Description -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 font-poppins">Deskripsi</label>
            <textarea v-model="uploadData.description" rows="3" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none" placeholder="Masukkan deskripsi (opsional)"></textarea>
          </div>
          <!-- Buttons -->
          <div class="flex gap-3 pt-4">
            <button type="button" @click="$emit('close-upload')" class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-gray-700 font-poppins">Batal</button>
            <button type="submit" :disabled="!uploadData.image || uploading" class="flex-1 px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-medium font-poppins disabled:opacity-50">{{ uploading ? 'Uploading...' : 'Upload' }}</button>
          </div>
        </form>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { ChevronLeft, ChevronRight, Plus, Trash2, Upload, X } from 'lucide-vue-next'

const props = defineProps({
  showEdit: Boolean,
  showUpload: Boolean,
  editData: Object,
  uploadData: Object,
  selectedSlot: Number,
  saving: Boolean,
  uploading: Boolean,
  newImagePreview: String,
  newImageBlob: Object
})

const emit = defineEmits([
  'close-edit', 'close-upload', 'save-edit', 'submit-upload',
  'replace-image', 'select-file', 'add-image', 'delete-image',
  'select-new-image', 'save-new-image', 'cancel-add-image',
  'update-current-image'
])

// Current image index for navigation
const currentImageIndex = ref(0)

// Add new image mode
const isAddingNewImage = ref(false)

// Slide direction for swipe animation
const slideDirection = ref('slide-left')

// Local form data for current image
const currentTitle = ref('')
const currentDescription = ref('')

// Get all images from editData
const allImages = computed(() => {
  return props.editData?.all_images || []
})

// Total images count
const totalImages = computed(() => allImages.value.length)

// Current displayed image object
const currentImage = computed(() => {
  return allImages.value[currentImageIndex.value] || null
})

// Current display image URL (for preview)
const currentDisplayImage = computed(() => {
  if (isAddingNewImage.value) {
    return props.newImagePreview
  }
  return currentImage.value?.image_url || null
})

// Navigation functions
const prevImage = () => {
  // Save current before navigating
  saveCurrentToParent()
  
  slideDirection.value = 'slide-right' // Slide right when going back
  if (currentImageIndex.value > 0) {
    currentImageIndex.value--
  }
  loadCurrentFromImage()
}

const nextImage = () => {
  // Save current before navigating
  saveCurrentToParent()
  
  slideDirection.value = 'slide-left' // Slide left when going forward
  if (currentImageIndex.value < allImages.value.length - 1) {
    currentImageIndex.value++
  }
  loadCurrentFromImage()
}

// Load title/description from current image
const loadCurrentFromImage = () => {
  if (currentImage.value) {
    currentTitle.value = currentImage.value.title || ''
    currentDescription.value = currentImage.value.description || ''
  }
}

// Save current title/description to parent (for later submission)
const saveCurrentToParent = () => {
  if (!isAddingNewImage.value && currentImage.value) {
    emit('update-current-image', {
      id: currentImage.value.id,
      title: currentTitle.value,
      description: currentDescription.value
    })
  }
}

// Start adding new image (with limit check)
const startAddImage = () => {
  // Guard: don't allow if already at max (3 photos)
  if (totalImages.value >= 3) {
    return
  }
  
  saveCurrentToParent()
  isAddingNewImage.value = true
  currentTitle.value = ''
  currentDescription.value = ''
  emit('add-image')
}

// Cancel adding new image
const cancelAddImage = () => {
  isAddingNewImage.value = false
  loadCurrentFromImage()
  emit('cancel-add-image')
}

// Handle form submit
const handleSubmit = () => {
  if (isAddingNewImage.value) {
    emit('save-new-image', {
      title: currentTitle.value,
      description: currentDescription.value
    })
  } else {
    // Update current image data first
    saveCurrentToParent()
    emit('save-edit')
  }
}

// Handle select image (for replace or new)
const handleSelectImage = () => {
  if (isAddingNewImage.value) {
    emit('select-new-image')
  } else {
    emit('replace-image')
  }
}

// Handle close - also reset add mode
const handleCloseEdit = () => {
  isAddingNewImage.value = false
  emit('close-edit')
}

// Reset index and load data when modal opens
watch(() => props.showEdit, (newVal) => {
  if (newVal) {
    currentImageIndex.value = 0
    isAddingNewImage.value = false
    loadCurrentFromImage()
  }
})

// Watch for image changes (after add/delete)
watch(() => allImages.value, () => {
  if (currentImageIndex.value >= allImages.value.length) {
    currentImageIndex.value = Math.max(0, allImages.value.length - 1)
  }
  // Auto-exit add mode if we've reached 3 images
  if (allImages.value.length >= 3 && isAddingNewImage.value) {
    isAddingNewImage.value = false
  }
  loadCurrentFromImage()
}, { deep: true })
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }

/* Slide Left Animation (swipe to left - for next) */
.slide-left-enter-active,
.slide-left-leave-active {
  transition: all 0.3s ease-out;
}
.slide-left-enter-from {
  opacity: 0;
  transform: translateX(100%);
}
.slide-left-leave-to {
  opacity: 0;
  transform: translateX(-100%);
}

/* Slide Right Animation (swipe to right - for prev) */
.slide-right-enter-active,
.slide-right-leave-active {
  transition: all 0.3s ease-out;
}
.slide-right-enter-from {
  opacity: 0;
  transform: translateX(-100%);
}
.slide-right-leave-to {
  opacity: 0;
  transform: translateX(100%);
}
</style>
