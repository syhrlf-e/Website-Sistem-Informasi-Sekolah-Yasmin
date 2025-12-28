/**
 * @component UsersPage
 * @description Parent component untuk halaman manajemen users
 * @route /yasmin-panel/users
 */

<template>
  <div class="space-y-6">
    <UsersPageHeader @add="showForm = true" />

    <UsersPageTable
      :users="users"
      :loading="loading"
      :error="error"
      :current-user-id="currentUser?.id"
      @edit="handleEdit"
      @delete="handleDelete"
      @page-change="changePage"
    />

    <UsersPageModal
      :show="showForm"
      :edit-mode="editMode"
      :form="form"
      :form-error="formError"
      :submitting="submitting"
      :super-admin-exists="superAdminExists"
      @close="closeForm"
      @submit="handleSubmit"
    />

    <!-- Success Toast -->
    <Transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="opacity-0 -translate-y-4" leave-active-class="transition-all duration-200 ease-in" leave-to-class="opacity-0 -translate-y-4">
      <div v-if="showSuccessToast" class="fixed top-6 left-1/2 -translate-x-1/2 z-[10001] bg-green-500 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 font-poppins">
        <CheckCircle class="w-6 h-6" />
        <span class="font-semibold">{{ successMessage }}</span>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { useAuth } from '@/composables/useAuth'
import { usePopup } from '@/composables/usePopup'
import api from '@/services/api'
import { CheckCircle } from 'lucide-vue-next'
import { onMounted, ref } from 'vue'
import UsersPageHeader from './usersPage/UsersPageHeader.vue'
import UsersPageModal from './usersPage/UsersPageModal.vue'
import UsersPageTable from './usersPage/UsersPageTable.vue'

const { user: currentUser } = useAuth()
const { showError, confirm } = usePopup()

const showForm = ref(false)
const editMode = ref(false)
const loading = ref(false)
const submitting = ref(false)
const error = ref(null)
const formError = ref(null)
const superAdminExists = ref(false)
const showSuccessToast = ref(false)
const successMessage = ref('')

const form = ref({ id: null, name: '', email: '', password: '', role: '' })
const users = ref({ data: [], current_page: 1, last_page: 1, per_page: 10, total: 0, from: 0, to: 0 })

const fetchUsers = async (page = 1) => {
  loading.value = true
  error.value = null
  try {
    const response = await api.get(`/yasmin-panel/users?page=${page}`)
    users.value = response.data
  } catch (err) {
    error.value = err.response?.data?.message || 'Gagal memuat data users'
  } finally {
    loading.value = false
  }
}

const checkSuperAdmin = async () => {
  try {
    const response = await api.get('/yasmin-panel/users/check-super-admin')
    superAdminExists.value = response.data.super_admin_exists
  } catch (err) { /* silent */ }
}

const handleEdit = (user) => {
  editMode.value = true
  form.value = { id: user.id, name: user.name, email: user.email, password: '', role: user.role }
  showForm.value = true
}

const handleDelete = async (user) => {
  const result = await confirm(`Yakin ingin menghapus user "${user.name}"?`, 'Tindakan ini tidak dapat dibatalkan', 'Ya, Hapus')
  if (!result.isConfirmed) return
  try {
    await api.delete(`/yasmin-panel/users/${user.id}`)
    await fetchUsers(users.value.current_page)
    showSuccessMsg('User berhasil dihapus!')
  } catch (err) {
    await showError('Gagal Menghapus User', err.response?.data?.message || 'Gagal menghapus user')
  }
}

const handleSubmit = async () => {
  submitting.value = true
  formError.value = null
  try {
    if (editMode.value) {
      await api.put(`/yasmin-panel/users/${form.value.id}`, { name: form.value.name, email: form.value.email, role: form.value.role })
      showSuccessMsg('User berhasil diupdate!')
    } else {
      await api.post('/yasmin-panel/users', { name: form.value.name, email: form.value.email, password: form.value.password, role: form.value.role })
      showSuccessMsg('Admin berhasil ditambahkan!')
    }
    await fetchUsers(users.value.current_page)
    await checkSuperAdmin()
    closeForm()
  } catch (err) {
    formError.value = err.response?.data?.message || 'Gagal menyimpan user'
  } finally {
    submitting.value = false
  }
}

const showSuccessMsg = (message) => {
  successMessage.value = message
  showSuccessToast.value = true
  setTimeout(() => { showSuccessToast.value = false }, 3000)
}

const closeForm = () => {
  showForm.value = false
  editMode.value = false
  formError.value = null
  form.value = { id: null, name: '', email: '', password: '', role: '' }
}

const changePage = (page) => {
  if (page >= 1 && page <= users.value.last_page) fetchUsers(page)
}

onMounted(() => { fetchUsers(); checkSuperAdmin() })
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
