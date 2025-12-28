/**
 * @component UsersPageModal
 * @description Modal form untuk tambah/edit user
 */

<template>
  <Transition enter-active-class="transition-opacity duration-300" enter-from-class="opacity-0" leave-active-class="transition-opacity duration-200" leave-to-class="opacity-0">
    <div v-if="show" class="fixed inset-0 z-[9999] bg-black/60 backdrop-blur-sm flex items-center justify-center overflow-y-auto">
      <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 m-4 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">{{ editMode ? 'Edit User' : 'Tambah Admin Baru' }}</h2>
          <button @click="$emit('close')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
            <X class="w-5 h-5 text-gray-500" />
          </button>
        </div>

        <!-- Form Error -->
        <div v-if="formError" class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
          <p class="text-sm text-red-600 dark:text-red-400 font-poppins">{{ formError }}</p>
        </div>

        <div class="space-y-4">
          <div>
            <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">Nama Lengkap <span class="text-red-500">*</span></label>
            <input v-model="form.name" type="text" required placeholder="Masukkan nama lengkap"
              class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white font-poppins" />
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">Email <span class="text-red-500">*</span></label>
            <input v-model="form.email" type="email" required autocomplete="off" placeholder="admin@smk.com"
              class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white font-poppins" />
          </div>

          <div v-if="!editMode">
            <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">Password <span class="text-red-500">*</span></label>
            <input v-model="form.password" type="password" :required="!editMode" autocomplete="new-password" placeholder="Min. 6 karakter"
              class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white font-poppins" />
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">Role <span class="text-red-500">*</span></label>
            <select v-model="form.role" required class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white font-poppins">
              <option value="">Pilih Role</option>
              <option value="admin">Admin</option>
              <option value="admin_ppdb">Admin PPDB</option>
              <option value="super_admin" :disabled="superAdminExists && !editingSuperAdmin">
                Super Admin {{ superAdminExists && !editingSuperAdmin ? '(Sudah ada)' : '' }}
              </option>
            </select>
            <p v-if="superAdminExists && !editingSuperAdmin" class="mt-2 text-xs text-gray-500 font-poppins">⚠️ Super Admin hanya boleh 1 orang</p>
          </div>

          <div class="flex items-center gap-3 pt-4">
            <button @click="$emit('submit')" :disabled="submitting"
              class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold font-poppins disabled:opacity-50">
              {{ submitting ? 'Menyimpan...' : editMode ? 'Update' : 'Simpan' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { X } from 'lucide-vue-next'
import { computed } from 'vue'

const props = defineProps({
  show: Boolean,
  editMode: Boolean,
  form: Object,
  formError: String,
  submitting: Boolean,
  superAdminExists: Boolean
})

defineEmits(['close', 'submit'])

const editingSuperAdmin = computed(() => props.editMode && props.form.role === 'super_admin')
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
