<!--
  @component NewsSection
  @description Landing page section untuk menampilkan berita terbaru dalam grid
  @section id="berita" - Target scroll untuk navigasi
  @store useNewsStore - State management untuk berita
  @endpoint GET via newsStore.fetchFeaturedNews()
-->

<template>
  <section id="berita" class="py-20 transition-colors duration-300">
    <div class="container-content">
      <div class="text-center mb-12">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4 font-poppins">
          Berita Kami
        </h2>
        <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
          Update terbaru seputar kegiatan dan prestasi sekolah
        </p>
      </div>

      <div v-if="featuredNews.length === 0" class="py-12">
        <EmptyState
          title="Tidak Ada Berita Saat Ini"
          message="Belum ada berita terbaru. Silakan cek kembali nanti!"
        />
      </div>

      <div v-else>
        <NewsGrid>
          <NewsCard
            v-for="(news, index) in featuredNews"
            :key="news.id"
            :id="news.id"
            :title="news.title"
            :location="news.location"
            :image="news.image"
            :date="news.date"
            :slug="news.slug"
            :show-date="true"
            :loading="index > 2 ? 'lazy' : 'eager'"
            @click="handleCardClick(news)"
          />
        </NewsGrid>

        <div v-if="featuredNews.length >= 6" class="mt-12 text-center">
          <ViewMoreButton />
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import NewsCard from '@/components/sections/NewsSection/NewsCard.vue'
import NewsGrid from '@/components/sections/NewsSection/NewsGrid.vue'
import ViewMoreButton from '@/components/sections/NewsSection/ViewMoreButton.vue'
import EmptyState from '@/components/ui/shared/EmptyState.vue'

// Accept featuredNews as prop from parent (Home.vue)
const props = defineProps({
  featuredNews: {
    type: Array,
    default: () => []
  }
})

const handleCardClick = (news) => {
  window.location.href = `/news/${news.slug}?from=home`
}
</script>

<style scoped>
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.container-content > div {
  animation: fadeIn 0.6s ease-out;
}
</style>
