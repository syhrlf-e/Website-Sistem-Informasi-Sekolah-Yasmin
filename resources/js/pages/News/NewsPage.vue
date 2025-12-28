<!--
  @component NewsPage
  @description Halaman daftar berita dengan search, filter, dan pagination
  @route /news
  @store useNewsStore - State management untuk berita
-->

<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-20">
    <div class="container-content">
      <div class="mb-12">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">
          Berita Sekolah
        </h1>
        <p class="text-gray-600 dark:text-gray-400 max-w-2xl">
          Temukan berbagai informasi terbaru seputar kegiatan, prestasi, dan event sekolah
        </p>
      </div>

      <div class="mb-8 flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
          <SearchBar v-model="searchQuery" placeholder="Cari berita..." @search="handleSearch" />
        </div>

        <FilterDropdown
          v-model="categoryFilter"
          :options="categoryOptions"
          @update:modelValue="handleCategoryChange"
        />
      </div>

      <div v-if="newsStore.loading" class="py-20">
        <LoadingSpinner size="lg" />
        <p class="text-center text-gray-600 dark:text-gray-400 mt-4">Memuat berita...</p>
      </div>

      <div v-else-if="newsStore.error" class="py-20">
        <EmptyState title="Gagal Memuat Berita" :message="newsStore.error">
          <template #action>
            <button
              @click="newsStore.fetchNews()"
              class="mt-4 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200"
            >
              Coba Lagi
            </button>
          </template>
        </EmptyState>
      </div>

      <div v-else-if="newsStore.items.length === 0" class="py-20">
        <EmptyState
          title="Tidak Ada Berita Ditemukan"
          message="Tidak ada berita yang sesuai dengan pencarian Anda. Coba kata kunci lain atau hapus filter."
        >
          <template #action>
            <button
              v-if="searchQuery || categoryFilter !== 'all'"
              @click="clearFilters"
              class="mt-4 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200"
            >
              Hapus Filter
            </button>
          </template>
        </EmptyState>
      </div>

      <div v-else>
        <div class="mb-6 flex items-center justify-between">
          <p class="text-gray-600 dark:text-gray-400">
            Menampilkan <span class="font-semibold">{{ newsStore.items.length }}</span> dari
            <span class="font-semibold">{{ newsStore.total }}</span> berita
          </p>

          <button
            v-if="searchQuery || categoryFilter !== 'all'"
            @click="clearFilters"
            class="text-blue-600 dark:text-blue-400 hover:underline text-sm font-medium"
          >
            Hapus Filter
          </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
          <NewsCard
            v-for="news in newsStore.items"
            :key="news.id"
            :id="news.id"
            :title="news.title"
            :location="news.location"
            :image="news.image"
            :date="news.date"
            :slug="news.slug"
            :show-date="true"
            @click="handleCardClick(news)"
          />
        </div>

        <div v-if="newsStore.lastPage > 1" class="mt-12">
          <Pagination
            :current-page="newsStore.currentPage"
            :last-page="newsStore.lastPage"
            @page-change="handlePageChange"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import NewsCard from '@/components/sections/NewsSection/NewsCard.vue'
import EmptyState from '@/components/ui/shared/EmptyState.vue'
import FilterDropdown from '@/components/ui/shared/FilterDropdown.vue'
import LoadingSpinner from '@/components/ui/shared/LoadingSpinner.vue'
import Pagination from '@/components/ui/shared/Pagination.vue'
import SearchBar from '@/components/ui/shared/SearchBar.vue'
import { useNewsStore } from '@/stores/news'
import { useHead } from '@vueuse/head'
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'

// SEO Meta Tags
useHead({
  title: 'Berita Sekolah - SMA Mutiara Insan Nusantara',
  meta: [
    { name: 'description', content: 'Temukan berbagai informasi terbaru seputar kegiatan, prestasi, dan pengumuman dari SMA Mutiara Insan Nusantara.' },
    { property: 'og:title', content: 'Berita Sekolah - SMA Mutiara Insan Nusantara' },
    { property: 'og:description', content: 'Temukan berbagai informasi terbaru seputar kegiatan, prestasi, dan pengumuman dari SMA Mutiara Insan Nusantara.' },
    { property: 'og:type', content: 'website' }
  ]
})

const router = useRouter()
const newsStore = useNewsStore()

const searchQuery = ref('')
const categoryFilter = ref('all')

const categoryOptions = [
  { label: 'Semua Kategori', value: 'all' },
  { label: 'Kegiatan', value: 'event' },
  { label: 'Prestasi', value: 'achievement' },
  { label: 'Pengumuman', value: 'announcement' },
  { label: 'Lainnya', value: 'other' }
]

onMounted(() => {
  newsStore.fetchNews(1)
})

const handleSearch = (query) => {
  newsStore.setSearchQuery(query)
}

const handleCategoryChange = (category) => {
  newsStore.setFilterCategory(category)
}

const handlePageChange = (page) => {
  newsStore.goToPage(page)
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const handleCardClick = (news) => {
  router.push({
    name: 'news.detail',
    params: { slug: news.slug }
  })
}

const clearFilters = () => {
  searchQuery.value = ''
  categoryFilter.value = 'all'
  newsStore.clearFilters()
}
</script>
