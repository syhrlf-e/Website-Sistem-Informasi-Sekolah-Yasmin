/**
 * @component TestimoniListHeader
 * @description Header untuk halaman testimoni
 */

<template>
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-3">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">Testimoni</h1>
        <span v-if="pendingCount > 0" class="px-3 py-1 bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 text-sm font-bold rounded-full">
          {{ pendingCount }} Pending
        </span>
      </div>
      <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Kelola testimoni yang tampil di homepage</p>
    </div>
    <div class="flex items-center gap-3">
      <select v-model="localStatus" @change="$emit('update:statusFilter', localStatus)"
        class="px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white font-poppins focus:ring-2 focus:ring-blue-500">
        <option value="all">Semua Testimoni</option>
        <option value="active">Aktif</option>
        <option value="pending">Pending Review</option>
      </select>
      <button @click="$emit('add')" class="flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl font-medium shadow-lg font-poppins">
        <Plus class="w-5 h-5" />
        Tambah Testimoni
      </button>
    </div>
  </div>
</template>

<script setup>
import { Plus } from 'lucide-vue-next'
import { ref, watch } from 'vue'

const props = defineProps({
  pendingCount: { type: Number, default: 0 },
  statusFilter: { type: String, default: 'all' }
})

const emit = defineEmits(['add', 'update:statusFilter'])

const localStatus = ref(props.statusFilter)
watch(() => props.statusFilter, (val) => { localStatus.value = val })
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
