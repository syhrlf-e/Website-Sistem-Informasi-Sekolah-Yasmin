<template>
  <div
    @click="handleClick"
    class="group relative overflow-hidden rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 cursor-pointer"
  >
    <!-- Image with Lazy Loading -->
    <div class="aspect-[4/3] overflow-hidden bg-gray-200 dark:bg-gray-700">
      <!-- Skeleton Loading -->
      <div
        v-if="!imageLoaded && !imageError"
        class="w-full h-full animate-pulse bg-gradient-to-r from-gray-200 via-gray-300 to-gray-200 dark:from-gray-700 dark:via-gray-600 dark:to-gray-700"
      >
        <!-- Icon placeholder -->
        <div class="flex items-center justify-center h-full">
          <svg
            class="w-16 h-16 text-gray-400 dark:text-gray-500"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
            />
          </svg>
        </div>
      </div>

      <!-- Actual Image -->
      <img
        v-show="imageLoaded"
        ref="imageRef"
        :data-src="image"
        :alt="title"
        class="w-full h-full object-cover transition-all duration-300"
        :class="{ 'blur-sm': !imageLoaded, 'blur-0': imageLoaded }"
        :loading="loading"
        @load="onImageLoad"
        @error="onImageError"
      />

      <!-- Error State -->
      <div
        v-if="imageError"
        class="w-full h-full flex items-center justify-center bg-gray-300 dark:bg-gray-600"
      >
        <div class="text-center text-gray-500 dark:text-gray-400">
          <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
          <p class="text-sm">Gambar tidak tersedia</p>
        </div>
      </div>
    </div>

    <!-- Location Badge - Top Right -->
    <div v-if="location" class="absolute top-3 right-3 z-10">
      <span class="inline-flex items-center gap-1 px-2 py-1 bg-black/50 backdrop-blur-sm rounded-full text-xs text-white/90">
        <MapPin class="w-3 h-3" />
        <span>{{ location }}</span>
      </span>
    </div>

    <!-- Overlay Content -->
    <div
      class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent flex items-end p-6"
    >
      <div class="text-white w-full">
        <!-- Title -->
        <h3
          class="text-xl font-bold mb-2 line-clamp-2 group-hover:text-blue-400 transition-colors duration-200"
        >
          {{ title }}
        </h3>

        <!-- Date -->
        <div v-if="showDate && date" class="text-sm text-white/80">
          {{ formattedDate }}
        </div>
      </div>
    </div>

    <!-- Hover Overlay Effect -->
    <div
      class="absolute inset-0 bg-blue-600/0 group-hover:bg-blue-600/10 transition-colors duration-300 pointer-events-none"
    ></div>
  </div>
</template>

<script setup>
import { MapPin } from 'lucide-vue-next'
import { computed, onMounted, onUnmounted, ref } from 'vue'

const props = defineProps({
  id: {
    type: Number,
    required: true
  },
  title: {
    type: String,
    required: true
  },
  location: {
    type: String,
    required: true
  },
  image: {
    type: String,
    required: true
  },
  date: {
    type: String,
    default: null
  },
  slug: {
    type: String,
    required: true
  },
  showDate: {
    type: Boolean,
    default: true
  },
  // 🔥 NEW: Priority loading prop
  loading: {
    type: String,
    default: 'lazy',
    validator: (value) => ['lazy', 'eager'].includes(value)
  }
})

const emit = defineEmits(['click'])

const imageRef = ref(null)
const imageLoaded = ref(false)
const imageError = ref(false)
let observer = null

// Format date
const formattedDate = computed(() => {
  if (!props.date) return ''

  const date = new Date(props.date)
  const options = { year: 'numeric', month: 'long', day: 'numeric' }
  return date.toLocaleDateString('id-ID', options)
})

// 🔥 OPTIMIZED: Lazy load dengan Intersection Observer
onMounted(() => {
  if (!imageRef.value) return

  // Kalau loading="eager", langsung load tanpa observer
  if (props.loading === 'eager') {
    const src = imageRef.value.getAttribute('data-src')
    if (src) {
      imageRef.value.src = src
    }
    return
  }

  // Intersection Observer untuk lazy loading
  observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const img = entry.target
          const src = img.getAttribute('data-src')

          if (src) {
            img.src = src
          }

          if (observer) {
            observer.unobserve(img)
          }
        }
      })
    },
    {
      rootMargin: '100px', // 🔥 OPTIMIZED: Load 100px sebelum masuk viewport
      threshold: 0.01
    }
  )

  observer.observe(imageRef.value)
})

onUnmounted(() => {
  if (observer) {
    observer.disconnect()
    observer = null
  }
})

const onImageLoad = () => {
  imageLoaded.value = true
  imageError.value = false
}

const onImageError = () => {
  imageError.value = true
  imageLoaded.value = false
  console.error(`Failed to load image: ${props.image}`)
}

const handleClick = () => {
  emit('click', {
    id: props.id,
    slug: props.slug,
    title: props.title
  })
}
</script>

<style scoped>
/* Smooth transition untuk blur effect */
img {
  transition:
    filter 0.3s ease,
    transform 0.5s ease;
}
</style>
