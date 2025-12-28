/**
 * @component BeritaPreviewCard
 * @description Card view preview untuk berita dalam mode form
 */

<template>
  <div class="p-4">
    <div class="bg-gray-50 dark:bg-gray-900 rounded-xl overflow-hidden hover:shadow-lg transition-shadow duration-300">
      <div class="relative h-48 bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-800">
        <img
          v-if="imagePreview"
          :src="imagePreview"
          :alt="form.title || 'Preview'"
          class="w-full h-full object-cover"
        />
        <div v-else class="flex items-center justify-center h-full">
          <Upload class="w-12 h-12 text-gray-400" />
        </div>

        <div class="absolute top-3 left-3">
          <span class="px-3 py-1 bg-blue-600 text-white text-xs font-semibold rounded-full font-poppins">
            {{ getCategoryLabel(form.category) }}
          </span>
        </div>

        <div v-if="form.is_featured" class="absolute top-3 right-3">
          <span class="px-3 py-1 bg-blue-500 text-white text-xs font-semibold rounded-full font-poppins flex items-center gap-1">
            <Star class="w-3 h-3 fill-current" />
            Berita Unggulan
          </span>
        </div>
      </div>

      <div class="p-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 font-poppins line-clamp-2">
          {{ form.title || 'Judul berita akan muncul di sini...' }}
        </h3>

        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-2 font-poppins">
          {{ form.excerpt || form.content.substring(0, 120) || 'Ringkasan berita akan muncul di sini...' }}
        </p>

        <!-- Meta Row - Pipe Separator Format -->
        <div class="flex flex-wrap items-center text-xs text-gray-500 dark:text-gray-500">
          <span v-if="form.author">oleh {{ form.author }}</span>
          <span v-if="form.author" class="mx-2">|</span>
          <span>{{ formatDate(new Date()) }}</span>
          <span v-if="form.location" class="mx-2">|</span>
          <span v-if="form.location">{{ form.location }}</span>
        </div>

        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
          <span
            :class="[
              'px-2 py-1 text-xs font-semibold rounded-full font-poppins',
              form.published
                ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400'
            ]"
          >
            {{ form.published ? 'Terbit' : 'Draf' }}
          </span>
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
.line-clamp-2 {
  display: -webkit-box;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
