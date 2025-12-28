/**
 * @component GaleriPage
 * @description Parent component untuk halaman galeri grid homepage
 * @route /yasmin-panel/galeri
 */

<template>
  <div class="space-y-6">
    <GaleriHeader :loading="isLoading" @refresh="fetchGridGalleries" />

    <GaleriInfoBox :alert="alertMessage" @close-alert="alertMessage.show = false" />

    <GaleriGrid
      :loading="isLoading"
      :galleries="gridGalleries"
      :available-slots="availableSlots"
      :uploading-slot="uploadingSlot"
      @upload="handleUploadClick"
      @edit="handleEdit"
      @delete="handleDelete"
    />

    <!-- Hidden File Input -->
    <input ref="fileInput" type="file" accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden" @change="handleFileSelected" />

    <GaleriModals
      :show-edit="showEditModal"
      :show-upload="showUploadModal"
      :edit-data="editForm"
      :upload-data="uploadForm"
      :selected-slot="selectedSlot"
      :saving="isSaving"
      :uploading="isUploading"
      :new-image-preview="newImagePreview"
      :new-image-blob="newImageBlob"
      @close-edit="closeEditModal"
      @close-upload="closeUploadModal"
      @save-edit="saveEdit"
      @submit-upload="submitUpload"
      @replace-image="handleReplaceImage"
      @select-file="fileInput.click()"
      @add-image="handleAddImage"
      @delete-image="handleDeleteImage"
      @select-new-image="handleSelectNewImage"
      @save-new-image="handleSaveNewImage"
      @cancel-add-image="handleCancelAddImage"
      @update-current-image="handleUpdateCurrentImage"
    />

    <!-- Image Cropper Modal -->
    <ImageCropperModal
      :show="showCropper"
      :image-src="cropperImageSrc"
      :aspect-ratio="cropperAspectRatio"
      @close="showCropper = false"
      @crop="handleCropComplete"
      @change-image="handleCropperChangeImage"
    />
  </div>
</template>

<script setup>
import api from '@/services/api'
import { computed, onMounted, ref } from 'vue'
import GaleriGrid from './galeriPage/GaleriGrid.vue'
import GaleriHeader from './galeriPage/GaleriHeader.vue'
import GaleriInfoBox from './galeriPage/GaleriInfoBox.vue'
import GaleriModals from './galeriPage/GaleriModals.vue'
import ImageCropperModal from '@/components/ui/shared/ImageCropperModal.vue'

const API_URL = '/yasmin-panel/galeri/grid'

const isLoading = ref(false)
const isUploading = ref(false)
const isSaving = ref(false)
const uploadingSlot = ref(null)
const selectedSlot = ref(null)
const showUploadModal = ref(false)
const showEditModal = ref(false)
const fileInput = ref(null)

// Cropper state
const showCropper = ref(false)
const cropperImageSrc = ref('')
const pendingFile = ref(null)
const isAddingImage = ref(false) // Track if adding new image (not replacing)

const gridGalleries = ref(Array(9).fill(null))
const availableSlots = ref({})

const alertMessage = ref({ show: false, type: 'success', text: '' })

const uploadForm = ref({ image: null, imagePreview: null, title: '', description: '' })
const editForm = ref({ id: null, grid_position: null, title: '', description: '', is_active: true, image_url: '' })

// New image state (for adding additional image)
const newImagePreview = ref(null)
const newImageBlob = ref(null)

// Auto-detect aspect ratio based on slot number
// Slot 1, 2, 8 → 16:9 (landscape)
// Slot 3, 4, 5, 6, 7, 9 → 1:1 (square)
const cropperAspectRatio = computed(() => {
  const landscapeSlots = [1, 2, 8]
  if (landscapeSlots.includes(selectedSlot.value)) {
    return 16 / 9
  }
  return 1 // Square
})

const showAlert = (type, text) => {
  alertMessage.value = { show: true, type, text }
  setTimeout(() => { alertMessage.value.show = false }, 5000)
}

const fetchGridGalleries = async () => {
  isLoading.value = true
  try {
    const response = await api.get(API_URL)
    gridGalleries.value = response.data
    const slotsResponse = await api.get(`${API_URL}/available-slots`)
    availableSlots.value = slotsResponse.data.available_slots
  } catch (error) {
    showAlert('error', 'Gagal memuat galeri grid')
  } finally {
    isLoading.value = false
  }
}

const handleUploadClick = (slotNumber) => {
  if (!availableSlots.value[slotNumber]) {
    showAlert('error', 'Slot ini belum bisa diisi. Lengkapi baris sebelumnya terlebih dahulu.')
    return
  }
  selectedSlot.value = slotNumber
  showUploadModal.value = true
}

const handleFileSelected = (event) => {
  const file = event.target.files[0]
  if (!file) return
  if (file.size > 2 * 1024 * 1024) { showAlert('error', 'File terlalu besar (maksimal 2MB)'); event.target.value = ''; return }
  if (!['image/jpeg', 'image/png', 'image/jpg', 'image/webp'].includes(file.type)) { showAlert('error', 'Format file tidak didukung'); event.target.value = ''; return }
  
  // Store pending file and open cropper
  pendingFile.value = file
  const reader = new FileReader()
  reader.onload = (e) => {
    cropperImageSrc.value = e.target.result
    showCropper.value = true
  }
  reader.readAsDataURL(file)
  event.target.value = ''
}

// Handle cropped image from cropper modal
const handleCropComplete = (blob) => {
  // If adding additional image to slot
  if (isAddingImage.value && showEditModal.value) {
    // Store blob for later save (user will click save button)
    newImageBlob.value = blob
    newImagePreview.value = URL.createObjectURL(blob)
  } else {
    // Normal mode: store blob for upload/replace
    uploadForm.value.image = blob
    uploadForm.value.imagePreview = URL.createObjectURL(blob)
  }
  
  showCropper.value = false
  pendingFile.value = null
}

// Handle image change from within cropper modal
const handleCropperChangeImage = (newImageSrc, file) => {
  cropperImageSrc.value = newImageSrc
  pendingFile.value = file
}

const submitUpload = async () => {
  if (!uploadForm.value.image) { showAlert('error', 'Pilih gambar terlebih dahulu'); return }
  isUploading.value = true
  uploadingSlot.value = selectedSlot.value
  const formData = new FormData()
  formData.append('image', uploadForm.value.image, 'cropped.webp')
  formData.append('grid_position', selectedSlot.value)
  formData.append('title', uploadForm.value.title)
  formData.append('description', uploadForm.value.description || '')
  try {
    await api.post(API_URL, formData, { headers: { 'Content-Type': 'multipart/form-data' } })
    showAlert('success', `Berhasil upload ke Slot ${selectedSlot.value}!`)
    closeUploadModal()
    await fetchGridGalleries()
  } catch (error) {
    showAlert('error', error.response?.data?.message || 'Gagal mengupload foto')
  } finally {
    isUploading.value = false
    uploadingSlot.value = null
  }
}

const closeUploadModal = () => {
  showUploadModal.value = false
  selectedSlot.value = null
  uploadForm.value = { image: null, imagePreview: null, title: '', description: '' }
  pendingFile.value = null
  cropperImageSrc.value = ''
}

const handleEdit = (gallery) => {
  editForm.value = {
    id: gallery.id,
    grid_position: gallery.grid_position,
    title: gallery.title,
    description: gallery.description || '',
    is_active: gallery.is_active,
    image_url: gallery.image_url,
    all_images: gallery.all_images || [] // Include all images for multi-image navigation
  }
  selectedSlot.value = gallery.grid_position
  showEditModal.value = true
}

const handleReplaceImage = () => {
  selectedSlot.value = editForm.value.grid_position
  fileInput.value.click()
}

const saveEdit = async () => {
  isSaving.value = true
  
  // Apply pending updates to editForm first
  // The first image (index 0) in all_images is the main gallery image
  if (editForm.value.all_images?.length > 0) {
    const mainImage = editForm.value.all_images[0]
    if (pendingImageUpdates.value[mainImage.id]) {
      editForm.value.title = pendingImageUpdates.value[mainImage.id].title
      editForm.value.description = pendingImageUpdates.value[mainImage.id].description
    }
  }
  
  const formData = new FormData()
  formData.append('title', editForm.value.title)
  formData.append('description', editForm.value.description || '')
  formData.append('is_active', editForm.value.is_active ? '1' : '0')
  if (uploadForm.value.image) formData.append('image', uploadForm.value.image, 'cropped.webp')
  
  try {
    await api.post(`${API_URL}/${editForm.value.id}`, formData, { headers: { 'Content-Type': 'multipart/form-data' } })
    
    // Clear pending updates after successful save
    pendingImageUpdates.value = {}
    
    showAlert('success', 'Galeri berhasil diupdate!')
    closeEditModal()
    await fetchGridGalleries()
  } catch (error) {
    showAlert('error', 'Gagal mengupdate galeri')
  } finally {
    isSaving.value = false
  }
}

const closeEditModal = () => {
  showEditModal.value = false
  editForm.value = { id: null, grid_position: null, title: '', description: '', is_active: true, image_url: '' }
  uploadForm.value = { image: null, imagePreview: null, title: '', description: '' }
  pendingFile.value = null
  cropperImageSrc.value = ''
  // Reset new image state
  isAddingImage.value = false
  newImageBlob.value = null
  newImagePreview.value = null
}

const handleDelete = async (gallery) => {
  if (!confirm(`Yakin ingin menghapus foto dari Slot ${gallery.grid_position}?`)) return
  try {
    await api.delete(`${API_URL}/${gallery.id}`)
    showAlert('success', 'Foto berhasil dihapus!')
    await fetchGridGalleries()
  } catch (error) {
    showAlert('error', 'Gagal menghapus foto')
  }
}

// Enter add-image mode (just sets the flag, modal handles the UI)
const handleAddImage = () => {
  isAddingImage.value = true
  // Don't click file input here - modal will show empty form first
}

// Select new image file (called from modal's empty form)
const handleSelectNewImage = () => {
  fileInput.value.click()
}

// Save new image to server (receives title/description from modal)
const handleSaveNewImage = async (data) => {
  if (!newImageBlob.value) {
    showAlert('error', 'Pilih gambar terlebih dahulu')
    return
  }
  
  try {
    isSaving.value = true
    const formData = new FormData()
    formData.append('image', newImageBlob.value, 'cropped.webp')
    formData.append('title', data?.title || 'Foto Baru')
    formData.append('description', data?.description || '')
    await api.post(`${API_URL}/${editForm.value.id}/add-image`, formData, { headers: { 'Content-Type': 'multipart/form-data' } })
    showAlert('success', 'Foto berhasil ditambahkan!')
    await fetchGridGalleries()
    // Update editForm with new data
    const gallery = gridGalleries.value.find(g => g && g.id === editForm.value.id)
    if (gallery) {
      editForm.value.all_images = gallery.all_images
    }
    // Reset add mode
    isAddingImage.value = false
    newImageBlob.value = null
    newImagePreview.value = null
  } catch (error) {
    showAlert('error', error.response?.data?.message || 'Gagal menambah foto')
  } finally {
    isSaving.value = false
  }
}

// Cancel add image mode
const handleCancelAddImage = () => {
  isAddingImage.value = false
  newImageBlob.value = null
  newImagePreview.value = null
}

// Track pending image updates (for multi-image title/desc changes)
const pendingImageUpdates = ref({})

// Update current image data (title/description) when navigating
const handleUpdateCurrentImage = (data) => {
  if (data?.id) {
    pendingImageUpdates.value[data.id] = {
      title: data.title,
      description: data.description
    }
  }
}

// Delete specific image from slot
const handleDeleteImage = async (imageId) => {
  if (!confirm('Yakin ingin menghapus foto ini?')) return
  try {
    await api.delete(`${API_URL}/${editForm.value.id}/image`, { data: { image_id: imageId } })
    showAlert('success', 'Foto berhasil dihapus!')
    await fetchGridGalleries()
    // Update editForm with new data
    const gallery = gridGalleries.value.find(g => g && g.id === editForm.value.id)
    if (gallery) {
      editForm.value.all_images = gallery.all_images
    }
  } catch (error) {
    showAlert('error', error.response?.data?.message || 'Gagal menghapus foto')
  }
}

onMounted(() => { fetchGridGalleries() })
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>

