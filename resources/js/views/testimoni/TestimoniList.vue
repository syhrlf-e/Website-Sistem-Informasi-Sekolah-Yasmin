/**
 * @component TestimoniList
 * @description Parent component untuk halaman testimoni
 * @route /yasmin-panel/testimoni
 */

<template>
  <div class="space-y-6">
    <TestimoniListHeader
      :pending-count="pendingCount"
      :status-filter="statusFilter"
      @add="showModal = true"
      @update:status-filter="statusFilter = $event"
    />

    <TestimoniListTable
      :items="filteredTestimonials"
      :loading="loading"
      :error="error"
      @add="showModal = true"
      @edit="handleEdit"
      @delete="handleDelete"
      @toggle="toggleActive"
      @drag-start="draggedIndex = $event"
      @drop="handleDrop"
    />

    <TestimoniListModal
      :show="showModal"
      :edit-mode="editMode"
      :form="form"
      :photo-preview="photoPreview"
      :submitting="submitting"
      @close="closeModal"
      @submit="handleSubmit"
      @photo-change="handlePhotoUpload"
      @remove-photo="removePhoto"
    />
  </div>
</template>

<script setup>
import { usePopup } from '@/composables/usePopup'
import api from '@/services/api'
import { computed, onMounted, ref } from 'vue'
import TestimoniListHeader from './testimoniList/TestimoniListHeader.vue'
import TestimoniListModal from './testimoniList/TestimoniListModal.vue'
import TestimoniListTable from './testimoniList/TestimoniListTable.vue'

const { showSuccess, showError, confirmDelete } = usePopup()

const testimonials = ref([])
const loading = ref(false)
const error = ref(null)
const showModal = ref(false)
const editMode = ref(false)
const submitting = ref(false)
const photoPreview = ref(null)
const draggedIndex = ref(null)
const statusFilter = ref('all')

const form = ref({ author: '', role: '', text: '', photo: null, is_active: true })

const filteredTestimonials = computed(() => {
  if (statusFilter.value === 'active') return testimonials.value.filter(t => t.is_active)
  if (statusFilter.value === 'pending') return testimonials.value.filter(t => !t.is_active)
  return testimonials.value
})

const pendingCount = computed(() => testimonials.value.filter(t => !t.is_active).length)

onMounted(() => { fetchTestimonials() })

const fetchTestimonials = async () => {
  loading.value = true
  error.value = null
  try {
    const response = await api.get('/yasmin-panel/testimonials')
    if (response.data.success) testimonials.value = response.data.data
  } catch (err) {
    error.value = err.response?.data?.message || 'Gagal memuat data testimoni'
  } finally {
    loading.value = false
  }
}

const handlePhotoUpload = (e) => {
  const file = e.target.files[0]
  if (file) {
    form.value.photoFile = file
    const reader = new FileReader()
    reader.onload = (e) => { photoPreview.value = e.target.result }
    reader.readAsDataURL(file)
  }
}

const removePhoto = () => {
  form.value.photo = null
  form.value.photoFile = null
  form.value.remove_photo = true
  photoPreview.value = null
}

const handleEdit = (testimonial) => {
  editMode.value = true
  form.value = { id: testimonial.id, author: testimonial.author, role: testimonial.role, text: testimonial.text, photo: testimonial.photo, is_active: testimonial.is_active, remove_photo: false }
  photoPreview.value = null
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editMode.value = false
  form.value = { author: '', role: '', text: '', photo: null, is_active: true }
  photoPreview.value = null
}

const handleSubmit = async () => {
  submitting.value = true
  try {
    const formData = new FormData()
    formData.append('author', form.value.author)
    formData.append('role', form.value.role)
    formData.append('text', form.value.text)
    formData.append('is_active', form.value.is_active ? '1' : '0')
    if (form.value.photoFile) formData.append('photo', form.value.photoFile)
    if (editMode.value && form.value.remove_photo) formData.append('remove_photo', '1')

    let response
    if (editMode.value) {
      response = await api.post(`/yasmin-panel/testimonials/${form.value.id}`, formData, { headers: { 'Content-Type': 'multipart/form-data' }, params: { _method: 'PUT' } })
    } else {
      response = await api.post('/yasmin-panel/testimonials', formData, { headers: { 'Content-Type': 'multipart/form-data' } })
    }
    if (response.data.success) {
      await showSuccess('Berhasil!', editMode.value ? 'Testimoni berhasil diupdate!' : 'Testimoni berhasil ditambahkan!')
      closeModal()
      fetchTestimonials()
    }
  } catch (err) {
    await showError('Gagal!', err.response?.data?.message || 'Gagal menyimpan testimoni')
  } finally {
    submitting.value = false
  }
}

const toggleActive = async (testimonial) => {
  try {
    const response = await api.put(`/yasmin-panel/testimonials/${testimonial.id}/toggle`)
    if (response.data.success) {
      testimonial.is_active = !testimonial.is_active
      await showSuccess('Berhasil!', 'Status testimoni berhasil diubah!')
    }
  } catch (err) {
    await showError('Gagal!', err.response?.data?.message || 'Gagal mengubah status')
  }
}

const handleDelete = async (id) => {
  const result = await confirmDelete('Hapus Testimoni?', 'Testimoni yang dihapus tidak dapat dikembalikan')
  if (!result.isConfirmed) return
  try {
    const response = await api.delete(`/yasmin-panel/testimonials/${id}`)
    if (response.data.success) { await showSuccess('Berhasil!', 'Testimoni berhasil dihapus!'); fetchTestimonials() }
  } catch (err) {
    await showError('Gagal!', err.response?.data?.message || 'Gagal menghapus testimoni')
  }
}

const handleDrop = async (dropIndex) => {
  if (draggedIndex.value === null || draggedIndex.value === dropIndex) return
  const items = [...testimonials.value]
  const [draggedItem] = items.splice(draggedIndex.value, 1)
  items.splice(dropIndex, 0, draggedItem)
  const updatedItems = items.map((item, index) => ({ id: item.id, order: index }))
  try {
    const response = await api.post('/yasmin-panel/testimonials/reorder', { items: updatedItems })
    if (response.data.success) {
      testimonials.value = items.map((item, index) => ({ ...item, order: index }))
      await showSuccess('Berhasil!', 'Urutan testimoni berhasil diubah!')
    }
  } catch (err) {
    await showError('Gagal!', err.response?.data?.message || 'Gagal mengubah urutan')
    fetchTestimonials()
  }
  draggedIndex.value = null
}
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
