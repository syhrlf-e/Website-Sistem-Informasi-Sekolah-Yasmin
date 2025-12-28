/**
 * @component BeritaTrashPagination
 * @description Pagination controls untuk halaman tempat sampah berita
 */

<template>
  <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
    <p class="text-sm text-gray-600 dark:text-gray-400 font-poppins">
      Showing {{ from || 0 }} to {{ to || 0 }} of {{ total || 0 }} entries
    </p>
    <div class="flex items-center gap-2">
      <button
        @click="$emit('page-change', currentPage - 1)"
        :disabled="currentPage === 1"
        class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200 font-poppins disabled:opacity-50 disabled:cursor-not-allowed"
      >
        Previous
      </button>

      <template v-for="page in visiblePages" :key="page">
        <button
          @click="$emit('page-change', page)"
          :class="[
            'px-4 py-2 rounded-lg font-semibold font-poppins transition-colors duration-200',
            page === currentPage
              ? 'bg-blue-600 text-white'
              : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
          ]"
        >
          {{ page }}
        </button>
      </template>

      <button
        @click="$emit('page-change', currentPage + 1)"
        :disabled="currentPage === lastPage"
        class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200 font-poppins disabled:opacity-50 disabled:cursor-not-allowed"
      >
        Next
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  currentPage: {
    type: Number,
    required: true
  },
  lastPage: {
    type: Number,
    required: true
  },
  from: {
    type: Number,
    default: 0
  },
  to: {
    type: Number,
    default: 0
  },
  total: {
    type: Number,
    default: 0
  }
})

defineEmits(['page-change'])

const visiblePages = computed(() => {
  const pages = []
  const current = props.currentPage
  const last = props.lastPage

  let start = Math.max(1, current - 2)
  let end = Math.min(last, start + 4)

  if (end - start < 4) {
    start = Math.max(1, end - 4)
  }

  for (let i = start; i <= end; i++) {
    pages.push(i)
  }

  return pages
})
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
