/**
 * @component BeritaForm
 * @description Parent form untuk create/edit berita dengan live preview dan image cropper
 * @route /yasmin-panel/berita/create, /yasmin-panel/berita/edit/:id
 */

<template>
  <div class="space-y-6">
    <BeritaFormHeader
      :is-edit="isEdit"
      :show-preview="showPreview"
      @toggle-preview="showPreview = !showPreview"
    />

    <div
      v-if="error"
      class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4"
    >
      <p class="text-sm text-red-600 dark:text-red-400 font-poppins">{{ error }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
      <div class="lg:col-span-3 space-y-6">
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <BeritaFormInputs
            v-model="form"
            :image-preview="imagePreview"
            @image-crop="handleImageCrop"
            @remove-image="removeImage"
          />

          <BeritaFormSettings
            v-model="form"
            :loading="loading"
            :is-edit="isEdit"
          />
        </form>
      </div>

      <div v-if="showPreview" class="hidden lg:block lg:col-span-2">
        <div class="sticky top-6 space-y-4">
          <div class="flex items-center gap-2 bg-white dark:bg-gray-800 rounded-xl p-3 border border-gray-200 dark:border-gray-700">
            <button
              @click="previewMode = 'card'"
              :class="[
                'flex-1 px-3 py-2 rounded-lg font-medium text-sm transition-all duration-200 font-poppins',
                previewMode === 'card'
                  ? 'bg-blue-600 text-white shadow-md'
                  : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'
              ]"
            >
              Tampilan Kartu
            </button>
            <button
              @click="previewMode = 'full'"
              :class="[
                'flex-1 px-3 py-2 rounded-lg font-medium text-sm transition-all duration-200 font-poppins',
                previewMode === 'full'
                  ? 'bg-blue-600 text-white shadow-md'
                  : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'
              ]"
            >
              Artikel Lengkap
            </button>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <BeritaPreviewCard
              v-if="previewMode === 'card'"
              :form="form"
              :image-preview="croppedPreviewUrl || imagePreview"
            />
            <BeritaPreviewFull
              v-else
              :form="form"
              :image-preview="croppedPreviewUrl || imagePreview"
            />
          </div>

          <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-3">
            <p class="text-xs text-blue-700 dark:text-blue-400 font-poppins flex items-center gap-2">
              <Eye class="w-4 h-4" />
              Preview real-time - Perubahan akan langsung terlihat
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import api from '@/services/api'
import { usePopup } from '@/composables/usePopup'
import { Eye } from 'lucide-vue-next'
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import BeritaFormHeader from './beritaForm/BeritaFormHeader.vue'
import BeritaFormInputs from './beritaForm/BeritaFormInputs.vue'
import BeritaFormSettings from './beritaForm/BeritaFormSettings.vue'
import BeritaPreviewCard from './beritaForm/BeritaPreviewCard.vue'
import BeritaPreviewFull from './beritaForm/BeritaPreviewFull.vue'

const route = useRoute()
const router = useRouter()
const { showSuccess } = usePopup()

const isEdit = ref(false)
const loading = ref(false)
const error = ref(null)
const imagePreview = ref(null)
const showPreview = ref(true)
const previewMode = ref('card')

// Cropped image state
const croppedBlob = ref(null)
const croppedPreviewUrl = ref(null)

const form = ref({
  title: '',
  excerpt: '',
  content: '',
  location: '',
  category: 'other',
  author: '',
  image: null,
  published: false,
  is_featured: false
})

const generateSlug = (text) => {
  return text
    .toLowerCase()
    .replace(/[^a-z0-9\s-]/g, '')
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-')
    .trim()
}

// Handle cropped image from child component
const handleImageCrop = (blob) => {
  croppedBlob.value = blob
  croppedPreviewUrl.value = URL.createObjectURL(blob)
  // Update form.image to indicate we have a new image
  form.value.image = blob
}

const removeImage = () => {
  form.value.image = null
  imagePreview.value = null
  croppedBlob.value = null
  croppedPreviewUrl.value = null
  if (isEdit.value) {
    form.value.remove_image = true
  }
}

const fetchBerita = async (id) => {
  try {
    loading.value = true
    const response = await api.get(`/yasmin-panel/news/${id}`)
    if (response.data.success) {
      const data = response.data.data
      form.value = {
        title: data.title,
        excerpt: data.excerpt || '',
        content: data.content,
        location: data.location || '',
        category: data.category,
        author: data.author || '',
        image: null,
        published: data.published,
        is_featured: data.is_featured
      }
      if (data.image) {
        imagePreview.value = data.image
      }
    }
  } catch (err) {
    error.value = 'Gagal memuat data berita'
  } finally {
    loading.value = false
  }
}

const handleSubmit = async () => {
  error.value = null
  loading.value = true
  try {
    const formData = new FormData()
    formData.append('title', form.value.title)
    formData.append('slug', generateSlug(form.value.title))
    formData.append('content', form.value.content)
    formData.append('category', form.value.category)
    formData.append('published', form.value.published ? '1' : '0')
    formData.append('is_featured', form.value.is_featured ? '1' : '0')
    if (form.value.excerpt) formData.append('excerpt', form.value.excerpt)
    if (form.value.location) formData.append('location', form.value.location)
    if (form.value.author) formData.append('author', form.value.author)
    
    // Use cropped blob if available, otherwise use original file
    if (croppedBlob.value) {
      formData.append('image', croppedBlob.value, 'cropped-image.webp')
    } else if (form.value.image && form.value.image instanceof File) {
      formData.append('image', form.value.image)
    }
    
    if (form.value.remove_image) formData.append('remove_image', '1')

    let response
    if (isEdit.value) {
      response = await api.post(`/yasmin-panel/news/${route.params.id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    } else {
      response = await api.post('/yasmin-panel/news', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    }

    if (response.data.success) {
      await showSuccess(
        'Berhasil!',
        response.data.message ||
          (isEdit.value ? 'Berita berhasil diupdate!' : 'Berita berhasil ditambahkan!')
      )
      router.push('/yasmin-panel/berita')
    }
  } catch (err) {
    if (err.response?.data?.errors) {
      const errors = Object.values(err.response.data.errors).flat()
      error.value = errors.join(', ')
    } else {
      error.value = err.response?.data?.message || 'Terjadi kesalahan saat menyimpan berita'
    }
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  if (route.params.id) {
    isEdit.value = true
    fetchBerita(route.params.id)
  }
})
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>

