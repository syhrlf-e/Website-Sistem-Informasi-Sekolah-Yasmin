<template>
  <button
    @click="handleClick"
    class="group inline-flex items-center gap-3 px-8 py-4 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1"
  >
    <span>Lihat Berita Lainnya</span>
    <ArrowRight
      class="w-5 h-5 transform group-hover:translate-x-1 transition-transform duration-200"
    />
  </button>
</template>

<script setup>
import { ArrowRight } from 'lucide-vue-next'
import { useRouter } from 'vue-router'

const router = useRouter()

const handleClick = () => {
  router.push({ name: 'news.index' })
}
</script>
