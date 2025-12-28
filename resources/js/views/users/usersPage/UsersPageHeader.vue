/**
 * @component UsersPageHeader
 * @description Header untuk halaman manajemen users
 */

<template>
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">Manajemen Pengguna</h1>
      <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Kelola admin dan editor</p>
    </div>
    <button @click="$emit('add')" class="flex items-center gap-2 px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium shadow-lg font-poppins">
      <UserPlus class="w-5 h-5" />
      Tambah Admin
    </button>
  </div>
</template>

<script setup>
import { UserPlus } from 'lucide-vue-next'
defineEmits(['add'])
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
