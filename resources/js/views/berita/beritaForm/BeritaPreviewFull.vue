/**
 * @component BeritaPreviewFull
 * @description Full article preview untuk berita dalam mode form - matching public page layout
 */

<template>
  <div class="preview-scroll max-h-[calc(100vh-200px)] overflow-y-auto">
    <!-- Card Container matching public page -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
      
      <!-- Header Section: Kategori → Judul → Meta -->
      <div class="p-6">
        <!-- Category Badge -->
        <div class="flex items-center gap-2 mb-4">
          <span class="px-3 py-1 bg-blue-600 text-white text-xs font-semibold rounded-full font-poppins">
            {{ getCategoryLabel(form.category) }}
          </span>
          <span
            v-if="form.is_featured"
            class="px-3 py-1 bg-blue-500 text-white text-xs font-semibold rounded-full font-poppins flex items-center gap-1"
          >
            <Star class="w-3 h-3 fill-current" />
            Berita Unggulan
          </span>
          <span
            :class="[
              'px-3 py-1 text-xs font-semibold rounded-full font-poppins',
              form.published
                ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400'
            ]"
          >
            {{ form.published ? 'Terbit' : 'Draf' }}
          </span>
        </div>

        <!-- Title -->
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white mb-3 font-poppins leading-tight">
          {{ form.title || 'Judul berita akan muncul di sini...' }}
        </h1>

        <!-- Meta Row - Pipe Separator Format -->
        <div class="flex flex-wrap items-center text-sm text-gray-600 dark:text-gray-400 mb-6">
          <span v-if="form.author">oleh {{ form.author }}</span>
          <span v-if="form.author" class="mx-2">|</span>
          <span>{{ formatDate(new Date()) }}</span>
          <span v-if="form.category" class="mx-2">|</span>
          <span v-if="form.category">{{ getCategoryLabel(form.category) }}</span>
          <span v-if="form.location" class="mx-2">|</span>
          <span v-if="form.location">{{ form.location }}</span>
        </div>
      </div>

      <!-- Hero Image -->
      <div class="px-6 pb-6">
        <div class="rounded-xl overflow-hidden bg-gray-900">
          <img
            v-if="imagePreview"
            :src="imagePreview"
            :alt="form.title || 'Preview'"
            class="w-full aspect-video object-cover"
          />
          <div v-else class="w-full aspect-video flex items-center justify-center bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-800">
            <Upload class="w-16 h-16 text-gray-400" />
          </div>
        </div>
      </div>

      <!-- Article Content -->
      <div class="p-6 pt-0">
        <!-- Excerpt (if any) -->
        <div
          v-if="form.excerpt"
          class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-600 rounded-r-xl"
        >
          <p class="text-gray-700 dark:text-gray-300 italic font-poppins">
            {{ form.excerpt }}
          </p>
        </div>

        <!-- Content -->
        <div class="preview-content">
          <div 
            v-if="form.content"
            v-html="form.content"
          ></div>
          <p v-else class="text-gray-400 dark:text-gray-500 italic font-poppins">
            Konten berita akan muncul di sini...
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Star, Upload } from 'lucide-vue-next'

defineProps({
  form: {
    type: Object,
    required: true
  },
  imagePreview: {
    type: String,
    default: null
  }
})

const getCategoryLabel = (category) => {
  const labels = {
    event: 'Acara',
    achievement: 'Prestasi',
    announcement: 'Pengumuman',
    other: 'Lainnya'
  }
  return labels[category] || 'Lainnya'
}

const formatDate = (date) => {
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  }).format(date)
}
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}

/* Modern Scrollbar */
.preview-scroll::-webkit-scrollbar {
  width: 6px;
}

.preview-scroll::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 3px;
}

.preview-scroll::-webkit-scrollbar-thumb {
  background: #94a3b8;
  border-radius: 3px;
}

.preview-scroll::-webkit-scrollbar-thumb:hover {
  background: #64748b;
}

.dark .preview-scroll::-webkit-scrollbar-track {
  background: #1e293b;
}

.dark .preview-scroll::-webkit-scrollbar-thumb {
  background: #475569;
}

.dark .preview-scroll::-webkit-scrollbar-thumb:hover {
  background: #64748b;
}

/* Preview content styling - Match public page */
.preview-content {
  color: #374151;
  line-height: 2;
  text-align: justify;
  hyphens: auto;
  -webkit-hyphens: auto;
}

.preview-content :deep(p) {
  color: #374151;
  margin-bottom: 1.5rem;
  line-height: 2;
  text-align: justify;
}

.preview-content :deep(strong) {
  font-weight: 600;
  color: #1f2937;
}

.preview-content :deep(em) {
  font-style: italic;
}

.preview-content :deep(ul),
.preview-content :deep(ol) {
  margin: 1.5rem 0;
  padding-left: 2rem;
}

.preview-content :deep(ul) {
  list-style-type: disc;
}

.preview-content :deep(ol) {
  list-style-type: decimal;
}

.preview-content :deep(li) {
  margin: 0.5rem 0;
  display: list-item;
  line-height: 1.75;
}

.preview-content :deep(li p) {
  margin: 0;
  display: inline;
}

/* Dark mode */
.dark .preview-content {
  color: #d1d5db;
}

.dark .preview-content :deep(p) {
  color: #d1d5db;
}

.dark .preview-content :deep(strong) {
  color: #f3f4f6;
}
</style>
