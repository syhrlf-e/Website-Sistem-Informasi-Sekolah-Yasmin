<!--
  @component ImageCropperModal
  @description Modal untuk crop gambar dengan rasio 4:3 sebelum upload
  @uses vue-advanced-cropper
  @props {Boolean} show - Tampilkan modal
  @props {String} imageSrc - Source gambar yang akan di-crop
  @props {Number} aspectRatio - Rasio aspek crop area (default 4:3)
  @emits close - Modal ditutup
  @emits crop - Gambar selesai di-crop (returns Blob)
  @emits change-image - Request untuk ganti foto
-->

<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-opacity duration-300"
      enter-from-class="opacity-0"
      leave-active-class="transition-opacity duration-200"
      leave-to-class="opacity-0"
    >
      <div
        v-if="show"
        class="fixed inset-0 z-[99999] bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
      >
        <div
          class="bg-white dark:bg-gray-800 rounded-2xl max-w-3xl w-full max-h-[90vh] flex flex-col border border-gray-200 dark:border-gray-700 shadow-2xl"
        >
          <!-- Header -->
          <div
            class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700 flex-shrink-0"
          >
            <div>
              <h2 class="text-xl font-bold text-gray-900 dark:text-white font-poppins">
                Crop Gambar
              </h2>
              <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Pilih area yang akan ditampilkan (rasio 4:3)
              </p>
            </div>
            <button
              @click="handleClose"
              class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
            >
              <X class="w-5 h-5 text-gray-500" />
            </button>
          </div>

          <!-- Cropper Area -->
          <div class="flex-1 overflow-hidden p-4 bg-gray-100 dark:bg-gray-900">
            <div class="relative w-full h-[400px] bg-gray-900 rounded-xl overflow-hidden">
              <Cropper
                ref="cropperRef"
                :src="imageSrc"
                :stencil-props="{
                  aspectRatio: aspectRatio,
                  movable: true,
                  resizable: true
                }"
                :resize-image="{
                  adjustStencil: false
                }"
                :default-size="defaultSize"
                image-restriction="stencil"
                class="cropper"
              />

              <!-- Crop Info Badge -->
              <div
                class="absolute bottom-4 left-1/2 -translate-x-1/2 px-4 py-2 bg-black/70 rounded-full text-white text-sm font-medium"
              >
                <span v-if="aspectRatio === 4 / 3">Rasio 4:3 (Landscape)</span>
                <span v-else-if="aspectRatio === 3 / 4">Rasio 3:4 (Portrait)</span>
                <span v-else-if="aspectRatio === 1">Rasio 1:1 (Square)</span>
                <span v-else-if="aspectRatio === 16 / 9">Rasio 16:9 (Widescreen)</span>
                <span v-else>Bebas</span>
              </div>
            </div>
          </div>

          <!-- Zoom Controls -->
          <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
            <div class="flex items-center gap-4">
              <ZoomOut class="w-5 h-5 text-gray-500" />
              <input
                type="range"
                min="0.5"
                max="3"
                step="0.1"
                v-model.number="zoomLevel"
                @input="handleZoom"
                class="flex-1 h-2 bg-gray-200 dark:bg-gray-700 rounded-lg appearance-none cursor-pointer accent-blue-600"
              />
              <ZoomIn class="w-5 h-5 text-gray-500" />
            </div>
          </div>

          <!-- Footer Actions -->
          <div
            class="p-4 border-t border-gray-200 dark:border-gray-700 flex gap-3 flex-shrink-0"
          >
            <!-- Ganti Foto Button -->
            <button
              @click="handleChangeImage"
              class="px-4 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-medium font-poppins transition-colors flex items-center gap-2"
            >
              <ImageIcon class="w-4 h-4" />
              Ganti Foto
            </button>
            
            <div class="flex-1"></div>
            
            <button
              @click="handleClose"
              class="px-6 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-semibold font-poppins transition-colors"
            >
              Batal
            </button>
            <button
              @click="handleCrop"
              :disabled="isCropping"
              class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 disabled:from-gray-400 disabled:to-gray-400 text-white rounded-xl font-semibold font-poppins shadow-lg transition-all flex items-center justify-center gap-2"
            >
              <Crop v-if="!isCropping" class="w-5 h-5" />
              <LoadingSpinner v-else size="sm" color="white" />
              {{ isCropping ? 'Memproses...' : 'Terapkan Crop' }}
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Hidden file input for changing image -->
    <input
      ref="fileInputRef"
      type="file"
      accept="image/*"
      class="hidden"
      @change="handleFileChange"
    />
  </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue'
import { Cropper } from 'vue-advanced-cropper'
import 'vue-advanced-cropper/dist/style.css'
import { Crop, Image as ImageIcon, X, ZoomIn, ZoomOut } from 'lucide-vue-next'
import LoadingSpinner from '@/components/ui/shared/LoadingSpinner.vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  imageSrc: {
    type: String,
    default: ''
  },
  aspectRatio: {
    type: Number,
    default: 4 / 3 // Default landscape 4:3
  }
})

const emit = defineEmits(['close', 'crop', 'change-image'])

const cropperRef = ref(null)
const fileInputRef = ref(null)
const zoomLevel = ref(1)
const isCropping = ref(false)

// Default size for stencil
const defaultSize = ({ imageSize }) => {
  return {
    width: Math.min(imageSize.width * 0.8, imageSize.height * 0.8 * props.aspectRatio),
    height: Math.min(imageSize.height * 0.8, (imageSize.width * 0.8) / props.aspectRatio)
  }
}

// Handle zoom change
const handleZoom = () => {
  if (cropperRef.value) {
    cropperRef.value.zoom(zoomLevel.value)
  }
}

// Handle close
const handleClose = () => {
  zoomLevel.value = 1
  isCropping.value = false
  emit('close')
}

// Handle crop and return blob
const handleCrop = async () => {
  if (!cropperRef.value) return

  isCropping.value = true

  try {
    const { canvas } = cropperRef.value.getResult()

    if (canvas) {
      // Convert canvas to blob as WebP
      canvas.toBlob(
        (blob) => {
          emit('crop', blob)
          isCropping.value = false
          handleClose()
        },
        'image/webp',
        0.85 // Quality 85%
      )
    } else {
      isCropping.value = false
    }
  } catch (error) {
    console.error('Error cropping image:', error)
    isCropping.value = false
  }
}

// Handle change image button click
const handleChangeImage = () => {
  fileInputRef.value?.click()
}

// Handle file change from hidden input
const handleFileChange = (event) => {
  const file = event.target.files?.[0]
  if (!file) return

  // Check file type
  if (!file.type.startsWith('image/')) {
    alert('Pilih file gambar yang valid!')
    return
  }

  // Check file size (2MB max)
  if (file.size > 2 * 1024 * 1024) {
    alert('Ukuran file maksimal 2MB!')
    return
  }

  // Read and emit new image
  const reader = new FileReader()
  reader.onload = (e) => {
    emit('change-image', e.target.result, file)
  }
  reader.readAsDataURL(file)
  
  // Reset input
  event.target.value = ''
}

// Reset zoom when new image
watch(
  () => props.imageSrc,
  () => {
    zoomLevel.value = 1
  }
)
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}

.cropper {
  width: 100%;
  height: 100%;
}

/* Custom range slider */
input[type='range']::-webkit-slider-thumb {
  -webkit-appearance: none;
  height: 20px;
  width: 20px;
  border-radius: 50%;
  background: linear-gradient(135deg, #2563eb, #7c3aed);
  cursor: pointer;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
}

input[type='range']::-moz-range-thumb {
  height: 20px;
  width: 20px;
  border-radius: 50%;
  background: linear-gradient(135deg, #2563eb, #7c3aed);
  cursor: pointer;
  border: none;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
}
</style>
