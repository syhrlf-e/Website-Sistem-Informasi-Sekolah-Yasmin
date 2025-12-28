/**
 * @component UsersPageTable
 * @description Table untuk menampilkan data users
 */

<template>
  <!-- Loading State -->
  <div v-if="loading" class="py-12">
    <LoadingSpinner size="lg" color="blue" />
  </div>

  <!-- Error State -->
  <div v-else-if="error" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
    <p class="text-red-600 dark:text-red-400 font-poppins">{{ error }}</p>
  </div>

  <!-- Table -->
  <div v-else class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
          <tr>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase font-poppins">#</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase font-poppins">User</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase font-poppins">Email</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase font-poppins">Role</th>
            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase font-poppins">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          <tr v-for="(user, index) in users.data" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white font-poppins">
              {{ (users.current_page - 1) * users.per_page + index + 1 }}
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white font-bold">
                  {{ user.name[0].toUpperCase() }}
                </div>
                <p class="font-semibold text-gray-900 dark:text-white font-poppins">{{ user.name }}</p>
              </div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 font-poppins">{{ user.email }}</td>
            <td class="px-6 py-4">
              <span :class="['inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold font-poppins',
                user.role === 'super_admin' ? 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400' : 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400']">
                {{ user.role === 'super_admin' ? 'Super Admin' : 'Admin' }}
              </span>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center justify-center gap-2">
                <button @click="$emit('edit', user)" class="p-2 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:bg-blue-100" title="Edit">
                  <Edit2 class="w-4 h-4" />
                </button>
                <button @click="$emit('delete', user)" :disabled="!canDelete(user, currentUserId)"
                  :class="['p-2 rounded-lg', canDelete(user, currentUserId)
                    ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100'
                    : 'bg-gray-100 dark:bg-gray-700 text-gray-400 cursor-not-allowed opacity-50']"
                  :title="!canDelete(user, currentUserId) ? (user.role === 'super_admin' ? 'Super Admin tidak bisa dihapus' : 'Tidak bisa menghapus diri sendiri') : 'Hapus'">
                  <Trash2 class="w-4 h-4" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="users.last_page > 1" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
      <p class="text-sm text-gray-600 dark:text-gray-400 font-poppins">
        Menampilkan {{ users.from }} - {{ users.to }} dari {{ users.total }} users
      </p>
      <div class="flex gap-2">
        <button @click="$emit('page-change', users.current_page - 1)" :disabled="users.current_page === 1"
          class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 disabled:opacity-50 font-poppins">Previous</button>
        <button @click="$emit('page-change', users.current_page + 1)" :disabled="users.current_page === users.last_page"
          class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 disabled:opacity-50 font-poppins">Next</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import LoadingSpinner from '@/components/ui/shared/LoadingSpinner.vue'
import { Edit2, Trash2 } from 'lucide-vue-next'

defineProps({
  users: Object,
  loading: Boolean,
  error: String,
  currentUserId: Number
})

defineEmits(['edit', 'delete', 'page-change'])

const canDelete = (user, currentUserId) => {
  if (user.role === 'super_admin') return false
  if (user.id === currentUserId) return false
  return true
}
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
