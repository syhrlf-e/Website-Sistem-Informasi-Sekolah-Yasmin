<!--
  @component NewsDetail
  @description Halaman detail berita dengan 2-column layout (desktop) / single column (mobile)
  @route /news/:slug
  @redesign 27 Des 2024 - Clean minimalist design, responsive card/no-card
-->

<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Loading State -->
    <div v-if="newsStore.detailLoading" class="py-40">
      <LoadingSpinner size="xl" />
      <p class="text-center text-gray-600 dark:text-gray-400 mt-6">Memuat detail berita...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="newsStore.error || !news" class="py-40">
      <EmptyState
        title="Berita Tidak Ditemukan"
        :message="newsStore.error || 'Berita yang Anda cari tidak tersedia.'"
      >
        <template #action>
          <BackButton :to="null" text="Kembali ke Daftar Berita" variant="default" class="mt-4" />
        </template>
      </EmptyState>
    </div>

    <!-- Main Content -->
    <div v-else>
      <!-- Sticky Back Button -->
      <div class="sticky top-0 z-10 bg-gray-50 dark:bg-gray-900 py-4">
        <div class="container-content">
          <BackButton :to="backUrl" text="Kembali" variant="ghost" />
        </div>
      </div>

      <div class="container-content pb-8">
        <!-- 2-Column Layout: Article + Gap + Sidebar (Desktop) -->
        <div class="flex flex-col lg:flex-row lg:gap-[50px]">
          <!-- LEFT: Main Article -->
          <article class="flex-1 max-w-4xl">
            <!-- Card Container - Only visible on desktop (md+) -->
            <div class="
              md:bg-white md:dark:bg-gray-800 
              md:rounded-2xl md:shadow-sm 
              md:border md:border-gray-200 md:dark:border-gray-700
              md:overflow-hidden
            ">
              <!-- Header Section -->
              <div class="py-2 md:p-6 lg:p-8">
                <!-- Title -->
                <h1 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-normal sm:font-semibold text-gray-900 dark:text-white mb-3 leading-tight">
                  {{ news.title }}
                </h1>

                <!-- Meta Row - Pipe Separator Format -->
                <div class="flex flex-wrap items-center text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-4 sm:mb-6">
                  <span v-if="news.author">oleh {{ news.author }}</span>
                  <span v-if="news.author" class="mx-1 sm:mx-2">|</span>
                  <span>{{ formatDate(news.date) }}</span>
                  <span v-if="news.category" class="mx-1 sm:mx-2">|</span>
                  <span v-if="news.category">{{ getCategoryLabel(news.category) }}</span>
                  <span v-if="news.location" class="mx-1 sm:mx-2">|</span>
                  <span v-if="news.location">{{ news.location }}</span>
                </div>
              </div>

              <!-- Hero Image -->
              <div class="md:px-6 lg:px-8 pb-6">
                <div class="rounded-xl overflow-hidden bg-gray-900">
                  <img
                    :src="news.image"
                    :alt="news.title"
                    class="w-full aspect-video object-cover"
                  />
                </div>
              </div>

              <!-- Article Content -->
              <div class="py-4 md:p-6 lg:p-8">
                <div 
                  class="news-content"
                  v-html="sanitizedContent"
                ></div>

                <!-- Gallery -->
                <div v-if="news.gallery && news.gallery.length > 0" class="mt-8 pt-8 border-t border-gray-100 dark:border-gray-700">
                  <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <ImageIcon class="w-5 h-5 text-blue-500" />
                    Galeri Foto
                  </h3>
                  <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <div
                      v-for="(image, index) in news.gallery"
                      :key="index"
                      class="aspect-square overflow-hidden rounded-xl"
                    >
                      <img
                        :src="image"
                        :alt="`Galeri ${index + 1}`"
                        class="w-full h-full object-cover hover:scale-110 transition-transform duration-300 cursor-pointer"
                      />
                    </div>
                  </div>
                </div>

                <!-- Share Section Card -->
                <div class="mt-8 pt-8 border-t border-gray-100 dark:border-gray-700">
                  <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Bagikan</span>
                    <div class="flex items-center gap-2">
                      <!-- Copy Link -->
                      <button
                        @click="copyLink"
                        class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200"
                        :title="linkCopied ? 'Link Disalin!' : 'Salin Link'"
                      >
                        <Check v-if="linkCopied" class="w-5 h-5 text-green-500" />
                        <Link2 v-else class="w-5 h-5" />
                      </button>
                      <!-- WhatsApp -->
                      <button
                        @click="shareWhatsApp"
                        class="p-2 text-gray-600 dark:text-gray-400 hover:text-green-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200"
                        title="WhatsApp"
                      >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                          <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                      </button>
                      <!-- Facebook -->
                      <button
                        @click="shareFacebook"
                        class="p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200"
                        title="Facebook"
                      >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                          <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                      </button>
                      <!-- Twitter -->
                      <button
                        @click="shareTwitter"
                        class="p-2 text-gray-600 dark:text-gray-400 hover:text-sky-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200"
                        title="Twitter"
                      >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                          <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Mobile: Related News Section (below content) -->
            <div class="lg:hidden mt-8">
              <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                  <Newspaper class="w-5 h-5 text-blue-500" />
                  Berita Lainnya
                </h3>
                <div class="space-y-4">
                  <div
                    v-for="related in displayedRelatedNews"
                    :key="related.id"
                    @click="handleRelatedClick(related)"
                    class="flex gap-3 cursor-pointer group"
                  >
                    <img
                      :src="related.image"
                      :alt="related.title"
                      class="w-20 h-20 object-cover rounded-lg flex-shrink-0 group-hover:scale-105 transition-transform duration-200"
                    />
                    <div class="flex-1 min-w-0">
                      <h4 class="text-sm font-medium text-gray-900 dark:text-white line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200">
                        {{ related.title }}
                      </h4>
                      <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ formatDate(related.date) }}
                      </p>
                    </div>
                  </div>

                  <div v-if="!displayedRelatedNews.length" class="text-center py-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada berita lainnya</p>
                  </div>
                </div>

                <a
                  v-if="displayedRelatedNews.length >= 3"
                  href="/news"
                  class="block mt-4 text-center text-sm text-blue-600 dark:text-blue-400 hover:underline font-medium"
                >
                  Lihat Semua Berita →
                </a>
              </div>
            </div>
          </article>

          <!-- RIGHT: Sidebar - Fixed vertically centered (Desktop only) -->
          <aside class="hidden lg:block lg:w-80 flex-shrink-0">
            <!-- This is just a placeholder to maintain layout space -->
            <div class="h-1"></div>
          </aside>
        </div>
      </div>

      <!-- Fixed Sidebar - Vertically centered, positioned next to article (Desktop only) -->
      <div class="hidden lg:block fixed top-1/2 -translate-y-1/2 w-80 z-10 sidebar-position">
        <!-- Berita Lainnya Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
            <Newspaper class="w-5 h-5 text-blue-500" />
            Berita Lainnya
          </h3>
          <div class="space-y-4">
            <div
              v-for="related in displayedRelatedNews"
              :key="related.id"
              @click="handleRelatedClick(related)"
              class="flex gap-3 cursor-pointer group"
            >
              <img
                :src="related.image"
                :alt="related.title"
                class="w-20 h-20 object-cover rounded-lg flex-shrink-0 group-hover:scale-105 transition-transform duration-200"
              />
              <div class="flex-1 min-w-0">
                <h4 class="text-sm font-medium text-gray-900 dark:text-white line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200">
                  {{ related.title }}
                </h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                  {{ formatDate(related.date) }}
                </p>
              </div>
            </div>

            <div v-if="!displayedRelatedNews.length" class="text-center py-4">
              <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada berita lainnya</p>
            </div>
          </div>

          <a
            v-if="displayedRelatedNews.length >= 3"
            href="/news"
            class="block mt-4 text-center text-sm text-blue-600 dark:text-blue-400 hover:underline font-medium"
          >
            Lihat Semua Berita →
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import EmptyState from '@/components/ui/shared/EmptyState.vue'
import LoadingSpinner from '@/components/ui/shared/LoadingSpinner.vue'
import { useNewsStore } from '@/stores/news'
import { useHead } from '@vueuse/head'
import { Check, Image as ImageIcon, Link2, Newspaper } from 'lucide-vue-next'
import { computed, onMounted, ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import BackButton from '@/components/ui/BackButton.vue'
import { router as inertiaRouter } from '@inertiajs/vue3'
import DOMPurify from 'isomorphic-dompurify'

// Get Inertia page props
const page = usePage()
const newsStore = useNewsStore()
const linkCopied = ref(false)

// Determine back URL based on where user came from
const backUrl = computed(() => {
  // Check query param 'from' to determine back navigation
  const urlParams = new URLSearchParams(window.location.search)
  const from = urlParams.get('from')
  
  if (from === 'home') {
    return '/'
  }
  return '/news'
})

// Props from Inertia (server-side)
const props = defineProps({
  news: { type: Object, default: null },
  relatedNews: { type: Array, default: () => [] },
  meta: { type: Object, default: () => ({}) },
  schema: { type: Object, default: () => ({}) },
})

// Use Inertia props if available, otherwise fall back to store
const news = computed(() => props.news || newsStore.currentNews)
const relatedNews = computed(() => props.relatedNews || [])
const displayedRelatedNews = computed(() => {
  // Priority: props.relatedNews (from Inertia) > news.related_news (from API)
  const related = props.relatedNews || news.value?.related_news || []
  return related.slice(0, 3)
})

// Sanitize HTML content to prevent XSS attacks + strip legacy img tags
const sanitizedContent = computed(() => {
  if (!news.value?.content) return ''
  // First strip img tags (legacy TipTap content)
  const noImages = news.value.content.replace(/<img[^>]*>/gi, '')
  // Then sanitize with DOMPurify to remove any malicious scripts
  return DOMPurify.sanitize(noImages, {
    ALLOWED_TAGS: ['p', 'br', 'strong', 'em', 'u', 's', 'ul', 'ol', 'li', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote', 'a', 'span', 'div'],
    ALLOWED_ATTR: ['href', 'target', 'rel', 'class', 'style']
  })
})

// SEO Meta Tags
const seoTitle = computed(() => props.meta?.title || (news.value?.title ? `${news.value.title} - SMA Mutiara Insan Nusantara` : 'Detail Berita'))
const seoDescription = computed(() => props.meta?.description || (news.value?.content ? news.value.content.substring(0, 160) + '...' : ''))
const seoImage = computed(() => props.meta?.og_image || news.value?.image || '/images/og-image.png')

useHead({
  title: seoTitle,
  meta: [
    { name: 'description', content: seoDescription },
    { property: 'og:title', content: seoTitle },
    { property: 'og:description', content: seoDescription },
    { property: 'og:image', content: seoImage },
    { property: 'og:type', content: 'article' }
  ]
})

// Only fetch if no Inertia props (fallback for SPA navigation)
onMounted(async () => {
  if (!props.news && page.props?.news === undefined) {
    const slug = window.location.pathname.split('/').pop()
    if (slug) {
      await newsStore.fetchNewsDetail(slug)
    }
  }
})

const handleRelatedClick = (related) => {
  inertiaRouter.visit(`/news/${related.slug}`)
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })
}

const getCategoryLabel = (category) => {
  const labels = {
    event: 'Kegiatan',
    achievement: 'Prestasi',
    announcement: 'Pengumuman',
    other: 'Lainnya'
  }
  return labels[category] || category
}

// Share Functions
const shareWhatsApp = () => {
  const url = window.location.href
  const text = `${news.value?.title} - ${url}`
  window.open(`https://wa.me/?text=${encodeURIComponent(text)}`, '_blank')
}

const shareFacebook = () => {
  const url = window.location.href
  window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank')
}

const shareTwitter = () => {
  const url = window.location.href
  const text = news.value?.title || 'Berita'
  window.open(`https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}`, '_blank')
}

const copyLink = async () => {
  try {
    await navigator.clipboard.writeText(window.location.href)
    linkCopied.value = true
    setTimeout(() => {
      linkCopied.value = false
    }, 2000)
  } catch (err) {
    console.error('Failed to copy link:', err)
  }
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.container-content {
  @apply max-w-7xl mx-auto sm:px-6 lg:px-8;
  padding-left: 1rem;
  padding-right: 1rem;
}

@media (min-width: 640px) {
  .container-content {
    padding-left: 1.5rem;
    padding-right: 1.5rem;
  }
}

/* Position sidebar: container max-w-7xl = 80rem, article max-w-4xl = 56rem */
/* Sidebar position: container-start + article-width + 50px gap */
.sidebar-position {
  /* Calculate: 50% viewport - half container + article width + gap */
  /* container = 80rem, article = 56rem, gap = 50px, sidebar = 20rem */
  left: calc(50% - 40rem + 56rem + 50px);
}

@media (max-width: 1536px) {
  .sidebar-position {
    /* For smaller screens, position from right */
    left: auto;
    right: 2rem;
  }
}

/* News content styling for v-html content */
.news-content {
  color: #374151;
  line-height: 2;
  font-size: 1rem;
}

.news-content :deep(p) {
  color: #374151;
  line-height: 2;
  text-align: justify;
  margin-bottom: 1.5rem;
  hyphens: auto;
  -webkit-hyphens: auto;
}

/* Handle empty paragraphs (spacing) from TipTap */
.news-content :deep(p:empty) {
  min-height: 1.5rem;
}

.news-content :deep(strong) {
  color: #1f2937;
  font-weight: 600;
}

.news-content :deep(em) {
  font-style: italic;
}

/* List styling */
.news-content :deep(ul) {
  list-style-type: disc;
  margin: 1.5rem 0;
  padding-left: 2rem;
  color: #374151;
}

.news-content :deep(ol) {
  list-style-type: decimal;
  margin: 1.5rem 0;
  padding-left: 2rem;
  color: #374151;
}

.news-content :deep(li) {
  display: list-item;
  margin: 0.5rem 0;
  line-height: 1.75;
}

/* Fix nested p inside li (TipTap output) */
.news-content :deep(li p) {
  margin: 0;
  display: inline;
}

/* Mobile responsiveness */
@media (max-width: 640px) {
  .news-content {
    line-height: 1.75;
  }
  
  .news-content :deep(p) {
    line-height: 1.75;
    margin-bottom: 1rem;
  }
  
  .news-content :deep(ul),
  .news-content :deep(ol) {
    padding-left: 1.5rem;
    margin: 1rem 0;
  }
}
</style>
