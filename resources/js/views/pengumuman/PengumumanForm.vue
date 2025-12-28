/**
 * @component PengumumanForm
 * @description Form sederhana untuk upload gambar pengumuman popup dengan image cropper
 * @route /yasmin-panel/pengumuman/create, /yasmin-panel/pengumuman/edit/:id
 */

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">
          {{ isEdit ? 'Edit Pengumuman' : 'Tambah Pengumuman Baru' }}
        </h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
          {{ isEdit ? 'Ganti gambar pengumuman' : 'Upload gambar untuk popup pengumuman' }}
        </p>
      </div>
      <router-link
        to="/yasmin-panel/pengumuman"
        class="flex items-center gap-2 px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors duration-200 font-poppins"
      >
        <ArrowLeft class="w-4 h-4" />
        Kembali
      </router-link>
    </div>

    <!-- Error Alert -->
    <div
      v-if="error"
      class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4"
    >
      <p class="text-sm text-red-600 dark:text-red-400 font-poppins">{{ error }}</p>
    </div>

    <!-- Form -->
    <form @submit.prevent="handleSubmit" class="space-y-6">
      <!-- Judul Pengumuman -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
        <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">
          Judul Pengumuman <span class="text-red-500">*</span>
        </label>
        <input
          v-model="form.title"
          type="text"
          required
          placeholder="Contoh: Pendaftaran Siswa Baru 2025/2026"
          class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white font-poppins"
        />
      </div>

      <!-- Gambar Upload with Cropper -->
      <div
        class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700"
      >
        <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-4 font-poppins">
          Gambar Pengumuman <span class="text-red-500">*</span>
        </label>

        <!-- Preview Cropped Image -->
        <div v-if="croppedPreview || imagePreview" class="mb-4">
          <div class="relative max-w-sm mx-auto group">
            <img
              :src="croppedPreview || imagePreview"
              alt="Preview"
              class="w-full rounded-xl border border-gray-200 dark:border-gray-700 shadow-lg"
            />
            <!-- Overlay with actions -->
            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity rounded-xl flex items-center justify-center gap-3">
              <button
                type="button"
                @click="$refs.fileInput.click()"
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
              Gambar dikrop
            </div>
          </div>
          <p v-if="imageDimensions.width" class="text-xs text-gray-500 dark:text-gray-400 mt-3 text-center">
            Dimensi: {{ imageDimensions.width }}x{{ imageDimensions.height }}px
          </p>
        </div>

        <!-- Upload Area -->
        <div
          v-else
          class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-12 text-center hover:border-blue-500 dark:hover:border-blue-400 transition-colors duration-200 cursor-pointer bg-gray-50 dark:bg-gray-900"
          @click="$refs.fileInput.click()"
        >
          <Upload class="w-16 h-16 text-gray-400 mx-auto mb-4" />
          <p class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2 font-poppins">
            Klik untuk upload gambar
          </p>
          <p class="text-sm text-gray-500 dark:text-gray-400">
            PNG, JPG, WEBP | Max 2MB | Akan di-crop otomatis
          </p>
        </div>

        <input
          ref="fileInput"
          type="file"
          accept="image/*"
          class="hidden"
          @change="handleFileSelect"
        />
      </div>

      <!-- Actions -->
      <div class="flex items-center gap-4">
        <button
          type="submit"
          :disabled="loading || (!isEdit && !croppedBlob && !form.image)"
          class="flex items-center gap-2 px-8 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white rounded-xl font-semibold transition-colors duration-200 shadow-lg hover:shadow-xl font-poppins"
        >
          <Save class="w-5 h-5" />
          {{ loading ? 'Menyimpan...' : isEdit ? 'Update' : 'Simpan' }}
        </button>
        <router-link
          to="/yasmin-panel/pengumuman"
          class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl font-semibold transition-colors duration-200 font-poppins"
        >
          Batal
        </router-link>
      </div>
    </form>

    <!-- Image Cropper Modal -->
    <ImageCropperModal
      :show="showCropper"
      :image-src="rawImageSrc"
      :aspect-ratio="4/5"
      @close="showCropper = false"
      @crop="handleCrop"
      @change-image="handleChangeImage"
    />
  </div>
</template>

<script setup>
import api from '@/services/api'
import { usePopup } from '@/composables/usePopup'
import { ArrowLeft, Check, Image as ImageIcon, Save, Trash2, Upload } from 'lucide-vue-next'
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import ImageCropperModal from '@/components/ui/shared/ImageCropperModal.vue'

const route = useRoute()
const router = useRouter()
const { showSuccess } = usePopup()

const isEdit = ref(false)
const loading = ref(false)
const error = ref(null)
const imagePreview = ref(null)
const imageDimensions = ref({ width: 0, height: 0 })

// Cropper state
const showCropper = ref(false)
const rawImageSrc = ref('')
const croppedPreview = ref('')
const croppedBlob = ref(null)

const form = ref({
  title: '',
  image: null,
  remove_image: false
})

onMounted(() => {
  if (route.params.id) {
    isEdit.value = true
    fetchAnnouncement(route.params.id)
  }
})

const fetchAnnouncement = async (id) => {
  try {
    loading.value = true
    const response = await api.get(`/yasmin-panel/announcements/${id}`)
    if (response.data.success && response.data.data) {
      const data = response.data.data
      form.value.title = data.title || ''
      if (data.image) {
        imagePreview.value = data.image
      }
    }
  } catch (err) {
    error.value = 'Gagal memuat data pengumuman'
  } finally {
    loading.value = false
  }
}

// Handle file selection - open cropper
const handleFileSelect = (event) => {
  const file = event.target.files?.[0]
  if (!file) return

  if (!file.type.startsWith('image/')) {
    error.value = 'Pilih file gambar yang valid!'
    return
  }

  if (file.size > 2 * 1024 * 1024) {
    error.value = 'Ukuran file maksimal 2MB'
    return
  }

  // Read file and open cropper
  const reader = new FileReader()
  reader.onload = (e) => {
    rawImageSrc.value = e.target.result
    showCropper.value = true
    error.value = null
  }
  reader.readAsDataURL(file)
  
  // Reset input
  event.target.value = ''
}

// Handle cropped result from cropper modal
const handleCrop = (blob) => {
  croppedBlob.value = blob
  croppedPreview.value = URL.createObjectURL(blob)
  
  // Get dimensions of cropped image
  const img = new Image()
  img.onload = () => {
    imageDimensions.value = { width: img.width, height: img.height }
  }
  img.src = croppedPreview.value
}

// Handle image change from cropper modal
const handleChangeImage = (newImageSrc) => {
  rawImageSrc.value = newImageSrc
}

const removeImage = () => {
  form.value.image = null
  imagePreview.value = null
  croppedPreview.value = ''
  croppedBlob.value = null
  imageDimensions.value = { width: 0, height: 0 }
  if (isEdit.value) {
    form.value.remove_image = true
  }
}

const handleSubmit = async () => {
  error.value = null
  loading.value = true

  // Validate image for new announcement
  if (!isEdit.value && !croppedBlob.value && !form.value.image) {
    error.value = 'Gambar pengumuman wajib diupload'
    loading.value = false
    return
  }

  try {
    const formData = new FormData()
    
    // Form values
    formData.append('title', form.value.title)
    formData.append('is_active', '1')
    formData.append('priority', '0')
    
    // Set dates: today to 1 year from now (always active)
    const today = new Date()
    const nextYear = new Date(today)
    nextYear.setFullYear(today.getFullYear() + 1)
    formData.append('start_date', today.toISOString().split('T')[0])
    formData.append('end_date', nextYear.toISOString().split('T')[0])
    
    // Use cropped blob if available
    if (croppedBlob.value) {
      formData.append('image', croppedBlob.value, 'cropped-announcement.webp')
    } else if (form.value.image) {
      formData.append('image', form.value.image)
    }
    
    if (form.value.remove_image) formData.append('remove_image', '1')

    let response
    if (isEdit.value) {
      // Laravel method spoofing - karena FormData tidak support PUT
      formData.append('_method', 'PUT')
      response = await api.post(`/yasmin-panel/announcements/${route.params.id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    } else {
      response = await api.post('/yasmin-panel/announcements', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    }

    if (response.data.success) {
      await showSuccess(
        'Berhasil!',
        isEdit.value ? 'Pengumuman berhasil diupdate!' : 'Pengumuman berhasil ditambahkan!'
      )
      router.push('/yasmin-panel/pengumuman')
    }
  } catch (err) {
    console.error(err)
    error.value = err.response?.data?.message || 'Terjadi kesalahan saat menyimpan'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
