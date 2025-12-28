/**
 * @component BeritaFormInputs
 * @description Form inputs untuk berita: judul, excerpt, kategori, lokasi, penulis, gambar, konten
 * @uses ImageCropperModal untuk crop gambar dengan rasio 16:9
 * @redesign 27 Des 2024 - Single container design (no island)
 */

<template>
  <!-- Single Container for All Inputs -->
  <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
    <div class="space-y-6">
      <!-- Judul Berita -->
      <div>
        <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">
          Judul Berita <span class="text-red-500">*</span>
        </label>
        <input
          :value="modelValue.title"
          @input="updateField('title', $event.target.value)"
          type="text"
          required
          placeholder="Masukkan judul berita..."
          class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white font-poppins"
        />
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
          Slug: <span class="font-mono">{{ generateSlug(modelValue.title) }}</span>
        </p>
      </div>

      <!-- Ringkasan (Excerpt) -->
      <div>
        <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">
          Ringkasan (Excerpt)
        </label>
        <textarea
          :value="modelValue.excerpt"
          @input="updateField('excerpt', $event.target.value)"
          rows="3"
          placeholder="Ringkasan singkat berita (opsional)..."
          class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white font-poppins resize-none"
        ></textarea>
      </div>

      <!-- Kategori & Lokasi (2 columns) -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">
            Kategori <span class="text-red-500">*</span>
          </label>
          <select
            :value="modelValue.category"
            @change="updateField('category', $event.target.value)"
            required
            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white font-poppins"
          >
            <option value="event">Event</option>
            <option value="achievement">Prestasi</option>
            <option value="announcement">Pengumuman</option>
            <option value="other">Lainnya</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">
            Lokasi
          </label>
          <input
            :value="modelValue.location"
            @input="updateField('location', $event.target.value)"
            type="text"
            placeholder="Contoh: Aula Sekolah"
            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white font-poppins"
          />
        </div>
      </div>

      <!-- Penulis -->
      <div>
        <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">
          Penulis
        </label>
        <input
          :value="modelValue.author"
          @input="updateField('author', $event.target.value)"
          type="text"
          placeholder="Nama penulis (opsional)"
          class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white font-poppins"
        />
      </div>

      <!-- Gambar Featured -->
      <div>
        <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">
          Gambar Featured <span class="text-red-500">*</span>
        </label>
        
        <!-- Preview Cropped Image -->
        <div v-if="croppedPreview || imagePreview" class="mb-4">
          <div class="relative group">
            <img
              :src="croppedPreview || imagePreview"
              alt="Preview"
              class="w-full h-64 object-cover rounded-xl border border-gray-200 dark:border-gray-700"
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
                @click="handleRemoveImage"
                class="px-4 py-2 bg-red-500 text-white rounded-lg font-medium text-sm flex items-center gap-2 hover:bg-red-600 transition-colors"
              >
                <Trash2 class="w-4 h-4" />
                Hapus
              </button>
            </div>
            <!-- Cropped Badge -->
            <div v-if="croppedPreview" class="absolute top-2 left-2 px-2 py-1 bg-green-500 text-white text-xs rounded-full font-medium flex items-center gap-1">
              <Check class="w-3 h-3" />
              Gambar dikrop (16:9)
            </div>
          </div>
        </div>

        <!-- File Input Area -->
        <div
          v-if="!croppedPreview && !imagePreview"
          class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-8 text-center hover:border-blue-500 dark:hover:border-blue-400 transition-colors duration-200 cursor-pointer"
          @click="triggerFileInput"
        >
          <Upload class="w-12 h-12 text-gray-400 mx-auto mb-3" />
          <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 font-poppins">
            Klik untuk upload gambar
          </p>
          <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, WEBP maksimal 2MB. Gambar akan di-crop ke rasio 16:9.</p>
        </div>
        
        <input
          ref="fileInput"
          type="file"
          accept="image/*"
          class="hidden"
          @change="handleFileSelect"
        />
      </div>

      <!-- Konten (Rich Text Editor) -->
      <div>
        <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">
          Konten <span class="text-red-500">*</span>
        </label>
        <TipTapEditor
          :modelValue="modelValue.content"
          @update:modelValue="updateField('content', $event)"
          placeholder="Tulis konten berita di sini..."
        />
      </div>
    </div>

    <!-- Image Cropper Modal -->
    <ImageCropperModal
      :show="showCropper"
      :image-src="rawImageSrc"
      :aspect-ratio="16/9"
      @close="showCropper = false"
      @crop="handleCrop"
      @change-image="handleChangeImage"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Check, Image as ImageIcon, Trash2, Upload } from 'lucide-vue-next'
import ImageCropperModal from '@/components/ui/shared/ImageCropperModal.vue'
import TipTapEditor from '@/components/ui/shared/TipTapEditor.vue'

const props = defineProps({
  modelValue: {
    type: Object,
    required: true
  },
  imagePreview: {
    type: String,
    default: null
  }
})

const emit = defineEmits(['update:modelValue', 'image-upload', 'image-crop', 'remove-image'])

const fileInput = ref(null)
const showCropper = ref(false)
const rawImageSrc = ref('')
const croppedPreview = ref('')

const updateField = (field, value) => {
  emit('update:modelValue', { ...props.modelValue, [field]: value })
}

const generateSlug = (text) => {
  return text
    .toLowerCase()
    .replace(/[^a-z0-9\s-]/g, '')
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-')
    .trim()
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
  
  // Reset input
  event.target.value = ''
}

// Handle cropped result from cropper modal
const handleCrop = (blob) => {
  croppedPreview.value = URL.createObjectURL(blob)
  emit('image-crop', blob)
}

// Handle image change from cropper modal
const handleChangeImage = (newImageSrc) => {
  rawImageSrc.value = newImageSrc
  // Cropper modal stays open with new image
}

// Handle remove image
const handleRemoveImage = () => {
  croppedPreview.value = ''
  rawImageSrc.value = ''
  if (fileInput.value) {
    fileInput.value.value = ''
  }
  emit('remove-image')
}
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
