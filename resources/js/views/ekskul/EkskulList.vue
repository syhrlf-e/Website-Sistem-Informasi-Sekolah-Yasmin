/**
 * @component EkskulList
 * @description Parent component untuk halaman daftar ekstrakurikuler
 * @route /yasmin-panel/ekskul
 */

<template>
  <div class="space-y-6">
    <EkskulListHeader @add="showForm = true" />

    <EkskulListFilters
      v-model:searchQuery="searchQuery"
      v-model:badgeFilter="badgeFilter"
      v-model:statusFilter="statusFilter"
    />

    <div v-if="loading" class="py-20">
      <LoadingSpinner size="lg" color="blue" />
    </div>

    <div
      v-else-if="error"
      class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-6 text-center"
    >
      <p class="text-red-600 dark:text-red-400 font-poppins">{{ error }}</p>
    </div>

    <EkskulListTable
      v-else
      :items="filteredEkskul"
      @edit="handleEdit"
      @delete="handleDelete"
      @toggle-status="toggleStatus"
    />

    <EkskulListModal
      :show="showForm"
      :edit-mode="editMode"
      :form="form"
      :submitting="submitting"
      :can-generate-token="canGenerateToken"
      :generating-token="generatingToken"
      :token-copied="tokenCopied"
      :image-preview="imagePreview"
      @close="closeForm"
      @submit="handleSubmit"
      @generate-token="generateTokenFromAPI"
      @copy-token="copyTokenToClipboard"
      @image-crop="handleImageCrop"
      @remove-image="removeImage"
    />
  </div>
</template>

<script setup>
import LoadingSpinner from '@/components/ui/shared/LoadingSpinner.vue'
import api from '@/services/api'
import { usePopup } from '@/composables/usePopup'
import { computed, onMounted, ref, watch } from 'vue'
import EkskulListHeader from './ekskulList/EkskulListHeader.vue'
import EkskulListFilters from './ekskulList/EkskulListFilters.vue'
import EkskulListTable from './ekskulList/EkskulListTable.vue'
import EkskulListModal from './ekskulList/EkskulListModal.vue'

const searchQuery = ref('')
const badgeFilter = ref('')
const statusFilter = ref('')
const showForm = ref(false)

const { showSuccess, showError, showWarning, confirmDelete, showLoading, close } = usePopup()
const editMode = ref(false)
const loading = ref(true)
const error = ref(null)
const submitting = ref(false)
const fileInput = ref(null)
const imagePreview = ref(null)
const imageFile = ref(null)
const croppedBlob = ref(null)
const canGenerateToken = ref(true)
const generatingToken = ref(false)
const tokenCopied = ref(false)
const ekskulList = ref([])

const form = ref({
  nama: '',
  tagline: '',
  badge: '',
  deskripsi: '',
  benefits: [''],
  jadwal: '',
  pembina: '',
  lokasi: '',

  is_active: true,
  gambar: null,
  selectedDays: [],
  scheduleStartTime: '',
  scheduleEndTime: '',
  enableRegistration: false,
  registrationToken: '',
  maxParticipants: null,
  registrationDeadline: '',
  deadlineDate: '',
  deadlineTime: '',
  whatsappAdmin: '',
  tokenGeneratedAt: null,
  canGenerateNextAt: null
})

watch(showForm, (newValue) => {
  document.body.style.overflow = newValue ? 'hidden' : ''
})

const fetchEkskul = async () => {
  try {
    loading.value = true
    error.value = null
    const response = await api.get('/yasmin-panel/ekstrakurikuler')
    ekskulList.value = response.data.success ? response.data.data : response.data
  } catch (err) {
    error.value = 'Gagal memuat data ekstrakurikuler'
  } finally {
    loading.value = false
  }
}

const filteredEkskul = computed(() => {
  return ekskulList.value.filter((ekskul) => {
    // Null-safe check for nama and badge
    const namaValue = ekskul.nama || ''
    const badgeValue = ekskul.badge || ''
    const matchSearch = namaValue.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchBadge = badgeFilter.value === '' || badgeValue === badgeFilter.value
    const matchStatus = statusFilter.value === '' || ekskul.is_active === (statusFilter.value === '1')
    return matchSearch && matchBadge && matchStatus
  })
})

const generateTokenFromAPI = async (nama) => {
  generatingToken.value = true
  
  // Word pool (same as backend)
  const words = [
    'TIGER', 'EAGLE', 'WOLF', 'LION', 'HAWK', 'BEAR', 'FOX', 'SHARK',
    'DRAGON', 'PHOENIX', 'FALCON', 'PANTHER', 'COBRA', 'VIPER', 'RAVEN',
    'THUNDER', 'STORM', 'BLAZE', 'FROST', 'SHADOW', 'LIGHT', 'STAR',
    'MOON', 'SUN', 'WIND', 'FIRE', 'WATER', 'EARTH', 'SKY', 'OCEAN'
  ]
  const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
  
  // Get ekskul name for token prefix (from parameter now)
  const ekskulName = nama 
    ? nama.toUpperCase().replace(/\s/g, '').substring(0, 10) 
    : 'EKSKUL'
  
  // Helper to generate random string of given length
  const randomString = (len, pool) => {
    let result = ''
    for (let i = 0; i < len; i++) {
      result += pool.charAt(Math.floor(Math.random() * pool.length))
    }
    return result
  }
  
  let scrambleCount = 0
  const maxScrambles = 15
  const letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
  const digits = '0123456789'
  
  // Start scramble animation - each part scrambles independently
  const scrambleInterval = setInterval(() => {
    const scrambledName = randomString(ekskulName.length, letters)
    const scrambledWord = randomString(6, letters) // average word length
    const scrambledNum = randomString(2, digits)
    form.value.registrationToken = `${scrambledName}-${scrambledWord}-${scrambledNum}`
    scrambleCount++
    
    if (scrambleCount >= maxScrambles) {
      clearInterval(scrambleInterval)
    }
  }, 50)
  
  // For NEW ekskul (no ID), generate local token with animation
  if (!form.value.id) {
    // Generate final token locally (same pattern as backend)
    const finalWord = words[Math.floor(Math.random() * words.length)]
    const finalNum = Math.floor(Math.random() * 90) + 10
    const finalToken = `${ekskulName}-${finalWord}-${finalNum}`
    
    // Wait for scramble to finish, then reveal character by character
    setTimeout(() => {
      clearInterval(scrambleInterval)
      
      // Final reveal animation (character by character)
      for (let i = 0; i <= finalToken.length; i++) {
        setTimeout(() => {
          let displayToken = finalToken.substring(0, i)
          for (let j = i; j < finalToken.length; j++) {
            if (finalToken[j] === '-') {
              displayToken += '-'
            } else {
              displayToken += chars.charAt(Math.floor(Math.random() * chars.length))
            }
          }
          form.value.registrationToken = displayToken
        }, i * 25)
      }
      
      setTimeout(() => {
        form.value.registrationToken = finalToken
        canGenerateToken.value = false
        generatingToken.value = false
      }, finalToken.length * 25 + 100)
    }, maxScrambles * 50)
    
    return
  }
  
  // For EXISTING ekskul, use API
  // Save old token in case of error
  const oldToken = form.value.registrationToken
  
  try {
    const response = await api.post(`/yasmin-panel/ekstrakurikuler/${form.value.id}/generate-token`)
    clearInterval(scrambleInterval)
    
    if (response.data.success) {
      // Final reveal animation
      const finalToken = response.data.data.token
      const tokenLen = finalToken.length
      
      for (let i = 0; i <= tokenLen; i++) {
        setTimeout(() => {
          let displayToken = finalToken.substring(0, i)
          for (let j = i; j < tokenLen; j++) {
            if (finalToken[j] === '-') {
              displayToken += '-'
            } else {
              displayToken += chars.charAt(Math.floor(Math.random() * chars.length))
            }
          }
          form.value.registrationToken = displayToken
        }, i * 25)
      }
      
      setTimeout(() => {
        form.value.registrationToken = finalToken
        form.value.tokenGeneratedAt = response.data.data.generated_at
        canGenerateToken.value = false
        generatingToken.value = false
      }, tokenLen * 25 + 100)
    }
  } catch (err) {
    clearInterval(scrambleInterval)
    // Restore old token instead of clearing
    form.value.registrationToken = oldToken
    generatingToken.value = false
    if (err.response?.status === 429) {
      await showWarning('Cooldown Active!', 'Kode hanya dapat di-generate setiap 24 jam.')
    } else {
      await showError('Gagal!', err.response?.data?.message || 'Gagal generate kode')
    }
  }
}

const copyTokenToClipboard = async () => {
  if (!form.value.registrationToken) return
  try {
    await navigator.clipboard.writeText(form.value.registrationToken)
    tokenCopied.value = true
    setTimeout(() => { tokenCopied.value = false }, 2000)
  } catch (err) { /* Silent fail */ }
}

// Handle cropped image from modal
const handleImageCrop = (blob) => {
  croppedBlob.value = blob
  imagePreview.value = URL.createObjectURL(blob)
}

const removeImage = () => {
  imagePreview.value = null
  imageFile.value = null
  croppedBlob.value = null
  form.value.gambar = null
}

const handleEdit = (ekskul) => {
  editMode.value = true
  imagePreview.value = null
  imageFile.value = null

  // Check if can generate new token
  // 1. If no token exists, can generate
  // 2. If token is expired (token_expires_at is in past), can generate
  // 3. If token_generated_at + 24h has passed (cooldown), can generate
  if (!ekskul.registration_token) {
    // No token exists
    canGenerateToken.value = true
  } else if (ekskul.token_expires_at) {
    // Check if token has expired
    const expiresAt = new Date(ekskul.token_expires_at)
    canGenerateToken.value = new Date() >= expiresAt
  } else if (ekskul.token_generated_at) {
    // Fallback: check 24h cooldown from generation time
    const generatedAt = new Date(ekskul.token_generated_at)
    const canGenerateAt = new Date(generatedAt.getTime() + 24 * 60 * 60 * 1000)
    canGenerateToken.value = new Date() >= canGenerateAt
  } else {
    // Old data: has token but no timestamps, allow generate
    canGenerateToken.value = true
  }

  let parsedDays = [], startTime = '', endTime = ''
  if (ekskul.jadwal) {
    const jadwalParts = ekskul.jadwal.split(',')
    if (jadwalParts.length >= 2) {
      const daysPart = jadwalParts.slice(0, -1).join(',').trim()
      parsedDays = daysPart.split(',').map(d => d.trim()).filter(d => d)
      const timePart = jadwalParts[jadwalParts.length - 1].trim()
      const timeMatch = timePart.match(/(\d{2}:\d{2})-(\d{2}:\d{2})/)
      if (timeMatch) { startTime = timeMatch[1]; endTime = timeMatch[2] }
    }
  }

  let deadlineDate = '', deadlineTime = ''
  if (ekskul.registration_deadline) {
    const deadline = new Date(ekskul.registration_deadline)
    deadlineDate = deadline.toISOString().split('T')[0]
    deadlineTime = deadline.toTimeString().slice(0, 5)
  }

  form.value = {
    id: ekskul.id, nama: ekskul.nama, tagline: ekskul.tagline || '', badge: ekskul.badge || '',
    deskripsi: ekskul.deskripsi, benefits: Array.isArray(ekskul.benefits) && ekskul.benefits.length > 0 ? [...ekskul.benefits] : [''],
    jadwal: ekskul.jadwal, pembina: ekskul.pembina, lokasi: ekskul.lokasi,
    is_active: ekskul.is_active, gambar: ekskul.gambar, selectedDays: parsedDays,
    scheduleStartTime: startTime, scheduleEndTime: endTime, enableRegistration: !!ekskul.enable_registration,
    registrationToken: ekskul.registration_token || '', maxParticipants: ekskul.max_participants || null,
    registrationDeadline: ekskul.registration_deadline || '', deadlineDate, deadlineTime,
    whatsappAdmin: ekskul.whatsapp_admin || '',
    tokenGeneratedAt: ekskul.token_generated_at || null,
    canGenerateNextAt: ekskul.token_generated_at ? new Date(new Date(ekskul.token_generated_at).getTime() + 24 * 60 * 60 * 1000).toISOString() : null
  }
  showForm.value = true
}

const toggleStatus = async (ekskul) => {
  const oldStatus = ekskul.enable_registration
  const index = ekskulList.value.findIndex(e => e.id === ekskul.id)
  if (index !== -1) ekskulList.value[index].enable_registration = !oldStatus

  try {
    const response = await api.put(`/yasmin-panel/ekstrakurikuler/${ekskul.id}/toggle-registration`)
    if (response.data.success && index !== -1) {
      ekskulList.value[index] = response.data.data
      await Swal.fire({ title: 'Berhasil!', text: response.data.message, icon: 'success', timer: 1500, showConfirmButton: false })
    }
  } catch (err) {
    if (index !== -1) ekskulList.value[index].enable_registration = oldStatus
    await showError('Gagal!', err.response?.data?.message || 'Gagal mengubah status')
  }
}

const handleDelete = async (id) => {
  const result = await confirmDelete('Hapus Ekstrakurikuler?', 'Data tidak dapat dikembalikan!')
  if (!result.isConfirmed) return

  try {
    const token = sessionStorage.getItem('admin_token')
    const response = await fetch(`/api/yasmin-panel/ekstrakurikuler/${id}`, {
      method: 'DELETE', headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' }
    })
    if (!response.ok) throw new Error('Gagal menghapus')
    await showSuccess('Berhasil!', 'Ekstrakurikuler dihapus!')
    await fetchEkskul()
  } catch (err) {
    await showError('Gagal!', 'Gagal menghapus ekstrakurikuler')
  }
}

const handleSubmit = async (formData) => {
  showForm.value = false
  document.body.style.overflow = ''
  await new Promise(resolve => setTimeout(resolve, 200))

  showLoading(editMode.value ? 'Memperbarui Data...' : 'Menyimpan Data...')

  try {
    submitting.value = true
    const token = sessionStorage.getItem('admin_token')
    const data = new FormData()
    data.append('nama', formData.nama)
    data.append('tagline', formData.tagline || '')
    data.append('badge', formData.badge)
    data.append('deskripsi', formData.deskripsi)

    let jadwalString = ''
    if (formData.selectedDays.length > 0 && formData.scheduleStartTime && formData.scheduleEndTime) {
      jadwalString = `${formData.selectedDays.join(', ')}, ${formData.scheduleStartTime}-${formData.scheduleEndTime}`
    }
    data.append('jadwal', jadwalString)
    data.append('pembina', formData.pembina)
    data.append('lokasi', formData.lokasi)
    data.append('is_active', formData.is_active ? '1' : '0')
    if (formData.maxParticipants) data.append('max_participants', formData.maxParticipants)
    data.append('enable_registration', formData.enableRegistration ? '1' : '0')
    if (formData.deadlineDate && formData.deadlineTime) {
      data.append('registration_deadline', `${formData.deadlineDate}T${formData.deadlineTime}`)
    }
    formData.benefits.forEach((benefit, index) => {
      if (benefit.trim()) data.append(`benefits[${index}]`, benefit.trim())
    })
    if (formData.whatsappAdmin) data.append('whatsapp_admin', formData.whatsappAdmin)
    
    // Use cropped blob if available
    if (croppedBlob.value) {
      data.append('gambar', croppedBlob.value, 'cropped-image.webp')
    } else if (imageFile.value) {
      data.append('gambar', imageFile.value)
    }

    const url = editMode.value ? `/api/yasmin-panel/ekstrakurikuler/${form.value.id}` : '/api/yasmin-panel/ekstrakurikuler'
    const response = await fetch(url, { method: 'POST', headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' }, body: data })
    if (!response.ok) {
      const errorData = await response.json()
      throw new Error(errorData.message || 'Gagal menyimpan')
    }

    close()
    await showSuccess('Berhasil!', editMode.value ? 'Data diupdate!' : 'Data ditambahkan!')
    resetForm()
    await fetchEkskul()
  } catch (err) {
    close()
    await showError('Gagal!', err.message || 'Gagal menyimpan')
  } finally {
    submitting.value = false
  }
}

const closeForm = () => {
  showForm.value = false
  resetForm()
  document.body.style.overflow = ''
}

const resetForm = () => {
  editMode.value = false
  imagePreview.value = null
  imageFile.value = null
  croppedBlob.value = null
  canGenerateToken.value = true
  generatingToken.value = false
  form.value = {
    nama: '', tagline: '', badge: '', deskripsi: '', benefits: [''], jadwal: '', pembina: '', lokasi: '',
    is_active: true, gambar: null, selectedDays: [], scheduleStartTime: '', scheduleEndTime: '',
    enableRegistration: false, registrationToken: '', maxParticipants: null, registrationDeadline: '', deadlineDate: '', deadlineTime: '', whatsappAdmin: ''
  }
}

onMounted(() => { fetchEkskul() })
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
