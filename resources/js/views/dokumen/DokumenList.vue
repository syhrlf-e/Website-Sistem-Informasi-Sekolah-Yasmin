/**
 * @component DokumenList
 * @description Parent component untuk halaman daftar dokumen PPDB
 * @route /yasmin-panel/dokumen
 */

<template>
  <div class="space-y-6">
    <DokumenListHeader :has-documents="documents.length > 0" @upload="showModal = true" />

    <div v-if="loading" class="py-20">
      <LoadingSpinner size="lg" color="blue" />
    </div>

    <DokumenListTable
      v-else
      :items="documents"
      @upload="showModal = true"
      @edit="handleEdit"
      @delete="handleDelete"
      @toggle-active="toggleActive"
    />

    <DokumenListModal
      :show="showModal"
      :edit-mode="editMode"
      :form="form"
      :submitting="submitting"
      @close="closeModal"
      @submit="handleSubmit"
      @file-change="handleFileChange"
    />
  </div>
</template>

<script setup>
import LoadingSpinner from '@/components/ui/shared/LoadingSpinner.vue'
import { usePopup } from '@/composables/usePopup'
import api from '@/services/api'
import { onBeforeUnmount, onMounted, ref } from 'vue'
import DokumenListHeader from './dokumenList/DokumenListHeader.vue'
import DokumenListTable from './dokumenList/DokumenListTable.vue'
import DokumenListModal from './dokumenList/DokumenListModal.vue'

const { showSuccess, showError, confirmDelete } = usePopup()

const documents = ref([])
const loading = ref(false)
const showModal = ref(false)
const editMode = ref(false)
const submitting = ref(false)
let refreshInterval = null

const form = ref({
  title: '',
  description: '',
  file: null
})

onMounted(() => {
  fetchDocuments()
  refreshInterval = setInterval(() => {
    fetchDocuments()
  }, 30000)
})

onBeforeUnmount(() => {
  if (refreshInterval) {
    clearInterval(refreshInterval)
  }
})

const fetchDocuments = async () => {
  loading.value = true
  try {
    const response = await api.get('/yasmin-panel/documents')
    if (response.data.success) {
      documents.value = response.data.data
    }
  } catch (err) {
    // Silent fail
  } finally {
    loading.value = false
  }
}

const handleFileChange = (file) => {
  form.value.file = file
}

const handleSubmit = async (formData) => {
  submitting.value = true
  try {
    const data = new FormData()
    data.append('title', formData.title)
    if (formData.description) data.append('description', formData.description)
    if (formData.file) data.append('file', formData.file)

    let response
    if (editMode.value) {
      response = await api.put(`/yasmin-panel/documents/${form.value.id}`, {
        title: formData.title,
        description: formData.description
      })
    } else {
      response = await api.post('/yasmin-panel/documents', data, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    }

    if (response.data.success) {
      await showSuccess(
        'Berhasil!',
        editMode.value ? 'Dokumen berhasil diupdate!' : 'Dokumen berhasil diupload!'
      )
      closeModal()
      fetchDocuments()
    }
  } catch (err) {
    await showError('Gagal!', err.response?.data?.message || 'Gagal menyimpan dokumen')
  } finally {
    submitting.value = false
  }
}

const handleEdit = (doc) => {
  editMode.value = true
  form.value = {
    id: doc.id,
    title: doc.title,
    description: doc.description || ''
  }
  showModal.value = true
}

const handleDelete = async (id) => {
  const result = await confirmDelete(
    'Hapus Dokumen?',
    'Dokumen yang dihapus tidak dapat dikembalikan'
  )

  if (!result.isConfirmed) return

  try {
    const response = await api.delete(`/yasmin-panel/documents/${id}`)
    if (response.data.success) {
      await showSuccess('Berhasil!', 'Dokumen berhasil dihapus!')
      fetchDocuments()
    }
  } catch (err) {
    await showError('Gagal!', 'Gagal menghapus dokumen')
  }
}

const toggleActive = async (doc) => {
  try {
    const response = await api.put(`/yasmin-panel/documents/${doc.id}/toggle`)
    if (response.data.success) {
      doc.is_active = !doc.is_active
      await showSuccess('Berhasil!', 'Status dokumen berhasil diubah!')
    }
  } catch (err) {
    await showError('Gagal!', 'Gagal mengubah status dokumen')
  }
}

const closeModal = () => {
  showModal.value = false
  editMode.value = false
  form.value = { title: '', description: '', file: null }
}
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
