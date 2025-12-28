<!--
  @component Pagination
  @description Pagination dengan page numbers, prev/next, dan ellipsis
  @props {Number} currentPage - Halaman aktif
  @props {Number} lastPage - Total halaman
  @props {Number} maxVisible - Max page numbers ditampilkan (default: 5)
  @emits page-change - Ketika halaman berubah
-->

<template>
  <div class="flex items-center justify-center gap-2 flex-wrap">
    <button
      @click="goToPrev"
      :disabled="currentPage === 1"
      class="p-2 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
      :class="{
        'text-gray-400 dark:text-gray-600': currentPage === 1,
        'text-gray-700 dark:text-gray-300': currentPage > 1
      }"
    >
      <ChevronLeft class="w-5 h-5" />
    </button>

    <template v-for="page in visiblePages" :key="page">
      <span v-if="page === '...'" class="px-3 py-2 text-gray-400 dark:text-gray-600"> ... </span>

      <button
        v-else
        @click="goToPage(page)"
        class="min-w-[40px] px-3 py-2 rounded-lg border transition-colors duration-200"
        :class="{
          'bg-blue-600 text-white border-blue-600': currentPage === page,
          'border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300':
            currentPage !== page
        }"
      >
        {{ page }}
      </button>
    </template>

    <button
      @click="goToNext"
      :disabled="currentPage === lastPage"
      class="p-2 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
      :class="{
        'text-gray-400 dark:text-gray-600': currentPage === lastPage,
        'text-gray-700 dark:text-gray-300': currentPage < lastPage
      }"
    >
      <ChevronRight class="w-5 h-5" />
    </button>
  </div>
</template>

<script setup>
import { ChevronLeft, ChevronRight } from 'lucide-vue-next'
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
  maxVisible: {
    type: Number,
    default: 5
  }
})

const emit = defineEmits(['page-change'])

const visiblePages = computed(() => {
  const pages = []
  const half = Math.floor(props.maxVisible / 2)

  let start = Math.max(1, props.currentPage - half)
  let end = Math.min(props.lastPage, start + props.maxVisible - 1)

  if (end - start < props.maxVisible - 1) {
    start = Math.max(1, end - props.maxVisible + 1)
  }

  if (start > 1) {
    pages.push(1)
    if (start > 2) {
      pages.push('...')
    }
  }

  for (let i = start; i <= end; i++) {
    pages.push(i)
  }

  if (end < props.lastPage) {
    if (end < props.lastPage - 1) {
      pages.push('...')
    }
    pages.push(props.lastPage)
  }

  return pages
})

const goToPage = (page) => {
  if (page !== '...' && page !== props.currentPage) {
    emit('page-change', page)
  }
}

const goToPrev = () => {
  if (props.currentPage > 1) {
    emit('page-change', props.currentPage - 1)
  }
}

const goToNext = () => {
  if (props.currentPage < props.lastPage) {
    emit('page-change', props.currentPage + 1)
  }
}
</script>
