/**
 * @component GuruList
 * @description Parent component untuk halaman data guru
 * @route /yasmin-panel/guru
 */

<template>
  <div class="space-y-6">
    <GuruListHeader @add="showModal = true" />

    <GuruListSearch v-model="guruStore.searchQuery" @update:model-value="guruStore.setSearch($event)" />

    <GuruListTable
      :items="guruList"
      :loading="loading"
      :error="error"
      :pagination="pagination"
      @edit="handleEdit"
      @delete="handleDelete"
      @page-change="changePage"
    />

    <GuruListModal
      :show="showModal"
      :edit-mode="editMode"
      :form="form"
      :photo-preview="photoPreview"
      :submitting="submitting"
      @close="closeModal"
      @submit="handleSubmit"
      @photo-crop="handlePhotoCrop"
      @remove-photo="handleRemovePhoto"
    />
  </div>
</template>

<script setup>
import { useAdminGuruStore } from '@/stores/adminGuru'
import { usePopup } from '@/composables/usePopup'
import { computed, onMounted, ref } from 'vue'
import GuruListHeader from './guruList/GuruListHeader.vue'
import GuruListModal from './guruList/GuruListModal.vue'
import GuruListSearch from './guruList/GuruListSearch.vue'
import GuruListTable from './guruList/GuruListTable.vue'

const guruStore = useAdminGuruStore()
const { showSuccess, showError, showWarning, confirmDelete } = usePopup()

const showModal = ref(false)
const editMode = ref(false)
const currentGuruId = ref(null)
const submitting = ref(false)
const photoPreview = ref(null)
const photoFile = ref(null)
const croppedBlob = ref(null)
const form = ref({ nama: '', pelajaran: '' })

const loading = computed(() => guruStore.loading)
const error = computed(() => guruStore.error)
const guruList = computed(() => guruStore.items)
const pagination = computed(() => ({
  current_page: guruStore.currentPage,
  last_page: guruStore.lastPage,
  per_page: guruStore.perPage,
  total: guruStore.total,
  from: guruStore.from,
  to: guruStore.to
}))

const changePage = (page) => guruStore.goToPage(page)

// Handle cropped photo from modal
const handlePhotoCrop = (blob) => {
  croppedBlob.value = blob
  photoPreview.value = URL.createObjectURL(blob)
}

// Handle remove photo
const handleRemovePhoto = () => {
  photoPreview.value = null
  photoFile.value = null
  croppedBlob.value = null
}

const handleEdit = (guru) => {
  editMode.value = true
  currentGuruId.value = guru.id
  form.value.nama = guru.nama
  form.value.pelajaran = guru.pelajaran
  photoPreview.value = guru.foto
  photoFile.value = null
  croppedBlob.value = null
  showModal.value = true
}

const handleSubmit = async () => {
  if (!editMode.value && !croppedBlob.value && !photoFile.value) {
    await showWarning('Foto Diperlukan', 'Silakan upload foto guru')
    return
  }
  submitting.value = true
  try {
    const formData = new FormData()
    formData.append('nama', form.value.nama)
    formData.append('pelajaran', form.value.pelajaran)
    
    // Use cropped blob if available
    if (croppedBlob.value) {
      formData.append('foto', croppedBlob.value, 'cropped-photo.webp')
    } else if (photoFile.value) {
      formData.append('foto', photoFile.value)
    }

    if (editMode.value) {
      await guruStore.updateGuru(currentGuruId.value, formData)
      await showSuccess('Berhasil!', 'Data guru berhasil diupdate')
    } else {
      await guruStore.createGuru(formData)
      await showSuccess('Berhasil!', 'Guru berhasil ditambahkan')
    }
    closeModal()
    guruStore.fetchGuru()
  } catch (err) {
    await showError('Gagal!', err.response?.data?.message || 'Terjadi kesalahan')
  } finally {
    submitting.value = false
  }
}

const handleDelete = async (id) => {
  const result = await confirmDelete('Yakin ingin menghapus?', 'Data guru yang dihapus tidak bisa dikembalikan.')
  if (result.isConfirmed) {
    try {
      await guruStore.deleteGuru(id)
      await showSuccess('Berhasil!', 'Guru telah dihapus.')
    } catch (err) {
      await showError('Gagal!', 'Terjadi kesalahan saat menghapus guru.')
    }
  }
}

const closeModal = () => {
  showModal.value = false
  editMode.value = false
  currentGuruId.value = null
  form.value.nama = ''
  form.value.pelajaran = ''
  photoPreview.value = null
  photoFile.value = null
  croppedBlob.value = null
}

onMounted(() => { guruStore.fetchGuru() })
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
