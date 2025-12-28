<!--
  @component News/Index
  @description Halaman daftar berita dengan pagination
  @route /news
-->

<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <Navbar />
    
    <main class="pt-20">
      <!-- Hero Section -->
      <div class="relative py-20 bg-gradient-to-br from-blue-600 to-blue-700 text-white">
        <div class="container-content text-center">
          <h1 class="text-4xl md:text-5xl font-bold mb-4">Berita Sekolah</h1>
          <p class="text-lg text-blue-100 max-w-2xl mx-auto">
            Informasi terbaru dari SMA Mutiara Insan Nusantara
          </p>
        </div>
      </div>

      <!-- News List Content -->
      <div class="container-content py-12">
        <div v-if="news && news.data && news.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <NewsCard
            v-for="item in news.data"
            :key="item.id"
            :id="item.id"
            :title="item.title"
            :excerpt="item.excerpt"
            :location="item.location"
            :image="item.image"
            :date="item.date"
            :slug="item.slug"
            :show-date="true"
          />
        </div>

        <div v-else class="text-center py-20">
          <p class="text-gray-600 dark:text-gray-400">Belum ada berita</p>
        </div>

        <!-- Pagination -->
        <div v-if="news && news.last_page > 1" class="mt-12 flex justify-center">
          <nav class="flex items-center gap-2">
            <InertiaLink
              v-for="page in news.last_page"
              :key="page"
              :href="`/news?page=${page}`"
              :class="[
                'px-4 py-2 rounded-lg font-medium transition-colors',
                news.current_page === page
                  ? 'bg-blue-600 text-white'
                  : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
              ]"
            >
              {{ page }}
            </InertiaLink>
          </nav>
        </div>
      </div>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import Navbar from '@/components/ui/Navbar.vue'
import Footer from '@/components/ui/Footer.vue'
import NewsCard from '@/components/sections/NewsSection/NewsCard.vue'

const props = defineProps({
  news: { type: Object, default: () => ({ data: [] }) },
  filters: { type: Object, default: () => ({}) },
  meta: { type: Object, default: () => ({}) },
  schema: { type: Object, default: () => ({}) },
})
</script>
