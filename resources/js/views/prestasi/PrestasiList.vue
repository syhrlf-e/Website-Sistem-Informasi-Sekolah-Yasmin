/**
 * @component PrestasiList
 * @description Parent component untuk halaman prestasi
 * @route /yasmin-panel/prestasi
 */

<template>
  <div class="space-y-6">
    <PrestasiListHeader @add="showForm = true" />

    <PrestasiListFilters
      v-model:search="searchQuery"
      v-model:category="categoryFilter"
      v-model:tingkat="tingkatFilter"
    />

    <PrestasiListTable
      :items="filteredPrestasi"
      :loading="loading"
      :error="error"
      @edit="handleEdit"
      @delete="handleDelete"
    />

    <PrestasiListModal
      :show="showForm"
      :edit-mode="editMode"
      :form="form"
      :image-preview="imagePreview"
      :submitting="submitting"
      @close="closeForm"
      @submit="handleSubmit"
      @image-change="handleImageUpload"
      @remove-image="removeImage"
    />
  </div>
</template>

<script setup>
import { usePopup } from '@/composables/usePopup'
import { computed, ref, watch } from 'vue'
import PrestasiListFilters from './prestasiList/PrestasiListFilters.vue'
import PrestasiListHeader from './prestasiList/PrestasiListHeader.vue'
import PrestasiListModal from './prestasiList/PrestasiListModal.vue'
import PrestasiListTable from './prestasiList/PrestasiListTable.vue'

const { showSuccess, showError, confirmDelete } = usePopup()

const searchQuery = ref('')
const categoryFilter = ref('')
const tingkatFilter = ref('')
const showForm = ref(false)
const editMode = ref(false)
const loading = ref(true)
const error = ref(null)
const submitting = ref(false)
const imagePreview = ref(null)
const imageFile = ref(null)

const form = ref({
  nama_prestasi: '',
  kategori: '',
  tingkat: '',
  tahun: new Date().getFullYear(),
  peserta: [''],
  deskripsi: '',
  image: null
})

const prestasiList = ref([])

watch(showForm, (newValue) => {
  document.body.style.overflow = newValue ? 'hidden' : ''
})

const fetchPrestasi = async () => {
  try {
    loading.value = true
    error.value = null
    const token = sessionStorage.getItem('admin_token')
    const response = await fetch('/api/yasmin-panel/prestasi', {
      headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' }
    })
    if (!response.ok) throw new Error('Gagal memuat data')
    const data = await response.json()
    prestasiList.value = data.success ? data.data : data
  } catch (err) {
    error.value = 'Gagal memuat data prestasi'
  } finally {
    loading.value = false
  }
}

const filteredPrestasi = computed(() => {
  return prestasiList.value.filter((prestasi) => {
    const matchSearch = prestasi.nama_prestasi.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchCategory = categoryFilter.value === '' || prestasi.kategori === categoryFilter.value
    const matchTingkat = tingkatFilter.value === '' || prestasi.tingkat === tingkatFilter.value
    return matchSearch && matchCategory && matchTingkat
  })
})

const handleImageUpload = (event) => {
  const file = event.target.files[0]
  if (!file) return
  if (file.size > 2 * 1024 * 1024) { showError('Ukuran File Terlalu Besar', 'Maksimal 2MB'); return }
  if (!file.type.startsWith('image/')) { showError('Format File Salah', 'File harus gambar'); return }
  imageFile.value = file
  const reader = new FileReader()
  reader.onload = (e) => { imagePreview.value = e.target.result }
  reader.readAsDataURL(file)
}

const removeImage = () => {
  imagePreview.value = null
  imageFile.value = null
  form.value.image = null
}

const handleEdit = (prestasi) => {
  editMode.value = true
  form.value = {
    id: prestasi.id,
    nama_prestasi: prestasi.nama_prestasi,
    kategori: prestasi.kategori,
    tingkat: prestasi.tingkat,
    tahun: prestasi.tahun,
    peserta: Array.isArray(prestasi.peserta) ? [...prestasi.peserta] : typeof prestasi.peserta === 'string' ? JSON.parse(prestasi.peserta) : [''],
    deskripsi: prestasi.deskripsi,
    image: prestasi.image
  }
  showForm.value = true
}

const handleDelete = async (id) => {
  const result = await confirmDelete('Hapus Prestasi?', 'Data tidak dapat dikembalikan!')
  if (!result.isConfirmed) return
  try {
    const token = sessionStorage.getItem('admin_token')
    const response = await fetch(`/api/yasmin-panel/prestasi/${id}`, {
      method: 'DELETE', headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' }
    })
    if (!response.ok) throw new Error('Gagal menghapus')
    const data = await response.json()
    await showSuccess('Berhasil!', data.message || 'Prestasi berhasil dihapus!')
    await fetchPrestasi()
  } catch (err) {
    await showError('Gagal!', 'Gagal menghapus prestasi')
  }
}

const handleSubmit = async (formData) => {
  try {
    submitting.value = true
    const token = sessionStorage.getItem('admin_token')
    const data = new FormData()
    data.append('nama_prestasi', formData.nama_prestasi)
    data.append('kategori', formData.kategori)
    data.append('tingkat', formData.tingkat)
    data.append('tahun', formData.tahun)
    data.append('deskripsi', formData.deskripsi)
    formData.peserta.forEach((peserta, index) => { if (peserta.trim()) data.append(`peserta[${index}]`, peserta.trim()) })
    
    // Send cropped image blob if available
    if (formData._croppedImage) {
      data.append('image', formData._croppedImage, 'cropped.webp')
    }

    const url = editMode.value ? `/api/yasmin-panel/prestasi/${form.value.id}` : '/api/yasmin-panel/prestasi'
    if (editMode.value) data.append('_method', 'PUT')

    const response = await fetch(url, { method: 'POST', headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' }, body: data })
    if (!response.ok) throw new Error('Gagal menyimpan')
    const result = await response.json()
    await showSuccess('Berhasil!', result.message || (editMode.value ? 'Prestasi berhasil diupdate!' : 'Prestasi berhasil ditambahkan!'))
    closeForm()
    await fetchPrestasi()
  } catch (err) {
    await showError('Gagal!', 'Gagal menyimpan prestasi')
  } finally {
    submitting.value = false
  }
}

const closeForm = () => {
  showForm.value = false
  editMode.value = false
  imagePreview.value = null
  imageFile.value = null
  form.value = { nama_prestasi: '', kategori: '', tingkat: '', tahun: new Date().getFullYear(), peserta: [''], deskripsi: '', image: null }
  document.body.style.overflow = ''
}

fetchPrestasi()
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
