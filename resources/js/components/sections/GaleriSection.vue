<!--
  @component GaleriSection
  @description Landing page section untuk menampilkan galeri foto dalam bento grid layout
  @section id="galeri" - Target scroll untuk navigasi
  @endpoint GET /api/galeri/grid - Fetch 9 slot grid dari API
  @behavior Mobile: custom order layout, Desktop: 4-column bento grid, Lightbox on click
-->

<template>
  <section id="galeri" class="py-20 transition-colors duration-300">
    <div class="container-content">
      <div class="text-center mb-16">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4 font-poppins">
          Galeri Kami
        </h2>
      </div>
      <!-- Loading State with Shimmer Skeleton -->
      <div v-if="loading" class="flex justify-center w-full">
        <!-- Desktop/Tablet Shimmer Grid (same approach as admin) -->
        <div class="hidden md:grid grid-cols-2 lg:grid-cols-4 gap-[23px] max-w-[1437px] w-full">
          <div
            v-for="i in 9"
            :key="'desktop-' + i"
            class="skeleton-shimmer rounded-2xl overflow-hidden"
            :class="[1, 2, 8].includes(i) ? 'col-span-2' : 'col-span-1'"
            style="height: 342px"
          >
            <div class="shimmer-effect"></div>
          </div>
        </div>
        <!-- Mobile Shimmer Grid -->
        <div class="md:hidden grid grid-cols-2 gap-[23px] w-full">
          <div
            v-for="i in 9"
            :key="'mobile-' + i"
            class="skeleton-shimmer rounded-2xl overflow-hidden"
            :class="[1, 2, 8].includes(i) ? 'col-span-2' : 'col-span-1'"
            :style="{ height: [1, 2, 8].includes(i) ? '280px' : '200px' }"
          >
            <div class="shimmer-effect"></div>
          </div>
        </div>
      </div>
      <!-- Error State -->
      <div
        v-else-if="error"
        class="text-center py-20 bg-red-50 dark:bg-red-900/20 rounded-2xl border border-red-200 dark:border-red-800"
      >
        <p class="text-red-600 dark:text-red-400">{{ error }}</p>
      </div>
      <!-- Empty State -->
      <div
        v-else-if="displayGalleries.length === 0"
        class="text-center py-20 bg-gray-50 dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700"
      >
        <p class="text-gray-600 dark:text-gray-400">Belum ada galeri yang ditampilkan</p>
      </div>
      <!-- Gallery Grid -->
      <div v-else class="flex justify-center">
        <!-- Mobile Layout (Custom Order) -->
        <div class="block lg:hidden w-full max-w-[1437px]">
          <div class="grid grid-cols-2 gap-[23px]">
            <!-- Hero: Full Width Opening (Slot 1 - Index 0) -->
            <div
              v-if="gridSlots[0]"
              class="col-span-2 group relative overflow-hidden rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 cursor-pointer aspect-video"
              @click="openLightbox(0)"
            >
              <img
                :src="getSlotImage(0)"
                :alt="getCurrentImageData(0).title"
                class="w-full h-full object-cover transform group-hover:scale-105 transition-all duration-700"
                :key="getSlotImage(0)"
                loading="lazy"
              />
              <div
                class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4"
              >
                <div>
                  <p class="text-white font-bold text-base transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                    {{ getCurrentImageData(0).title }}
                  </p>
                  <p
                    v-if="getCurrentImageData(0).description"
                    class="text-gray-200 text-xs mt-1 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 delay-75 line-clamp-2"
                  >
                    {{ getCurrentImageData(0).description }}
                  </p>
                </div>
              </div>
            </div>
            <!-- Pair 1: Two Squares (Slot 3, 4 - Index 2, 3) -->
            <template v-for="slotIndex in [2, 3]" :key="'pair1-' + slotIndex">
              <div
                v-if="gridSlots[slotIndex]"
                class="col-span-1 group relative overflow-hidden rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 cursor-pointer aspect-square"
                @click="openLightbox(slotIndex)"
              >
                <img
                  :src="getSlotImage(slotIndex)"
                  :alt="getCurrentImageData(slotIndex).title"
                  class="w-full h-full object-cover transform group-hover:scale-105 transition-all duration-500"
                  :key="getSlotImage(slotIndex)"
                />
                <div
                  class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-3"
                >
                  <div>
                    <p class="text-white font-bold text-sm transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 line-clamp-1">
                      {{ getCurrentImageData(slotIndex).title }}
                    </p>
                    <p
                      v-if="getCurrentImageData(slotIndex).description"
                      class="text-gray-200 text-xs mt-0.5 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 delay-75 line-clamp-1"
                    >
                      {{ getCurrentImageData(slotIndex).description }}
                    </p>
                  </div>
                </div>
              </div>
            </template>
            <!-- Feature: Full Width Middle (Slot 2 - Index 1) -->
            <div
              v-if="gridSlots[1]"
              class="col-span-2 group relative overflow-hidden rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 cursor-pointer aspect-video"
              @click="openLightbox(1)"
            >
              <img
                :src="getSlotImage(1)"
                :alt="getCurrentImageData(1).title"
                class="w-full h-full object-cover transform group-hover:scale-105 transition-all duration-500"
                :key="getSlotImage(1)"
              />
              <div
                class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4"
              >
                <div>
                  <p class="text-white font-bold text-base transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                    {{ getCurrentImageData(1).title }}
                  </p>
                  <p
                    v-if="getCurrentImageData(1).description"
                    class="text-gray-200 text-xs mt-1 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 delay-75 line-clamp-2"
                  >
                    {{ getCurrentImageData(1).description }}
                  </p>
                </div>
              </div>
            </div>
            <!-- Pair 2: Two Squares (Slot 5, 6 - Index 4, 5) -->
            <template v-for="slotIndex in [4, 5]" :key="'pair2-' + slotIndex">
              <div
                v-if="gridSlots[slotIndex]"
                class="col-span-1 group relative overflow-hidden rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 cursor-pointer aspect-square"
                @click="openLightbox(slotIndex)"
              >
                <img
                  :src="getSlotImage(slotIndex)"
                  :alt="getCurrentImageData(slotIndex).title"
                  class="w-full h-full object-cover transform group-hover:scale-105 transition-all duration-500"
                  :key="getSlotImage(slotIndex)"
                />
                <div
                  class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-3"
                >
                  <div>
                    <p class="text-white font-bold text-sm transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 line-clamp-1">
                      {{ getCurrentImageData(slotIndex).title }}
                    </p>
                    <p
                      v-if="getCurrentImageData(slotIndex).description"
                      class="text-gray-200 text-xs mt-0.5 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 delay-75 line-clamp-1"
                    >
                      {{ getCurrentImageData(slotIndex).description }}
                    </p>
                  </div>
                </div>
              </div>
            </template>
            <!-- Pair 3: Two Squares (Slot 7, 9 - Index 6, 8) -->
            <template v-for="slotIndex in [6, 8]" :key="'pair3-' + slotIndex">
              <div
                v-if="gridSlots[slotIndex]"
                class="col-span-1 group relative overflow-hidden rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 cursor-pointer aspect-square"
                @click="openLightbox(slotIndex)"
              >
                <img
                  :src="getSlotImage(slotIndex)"
                  :alt="getCurrentImageData(slotIndex).title"
                  class="w-full h-full object-cover transform group-hover:scale-105 transition-all duration-500"
                  :key="getSlotImage(slotIndex)"
                />
                <div
                  class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-3"
                >
                  <div>
                    <p class="text-white font-bold text-sm transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 line-clamp-1">
                      {{ getCurrentImageData(slotIndex).title }}
                    </p>
                    <p
                      v-if="getCurrentImageData(slotIndex).description"
                      class="text-gray-200 text-xs mt-0.5 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 delay-75 line-clamp-1"
                    >
                      {{ getCurrentImageData(slotIndex).description }}
                    </p>
                  </div>
                </div>
              </div>
            </template>
            <!-- Closing: Full Width (Slot 8 - Index 7) -->
            <div
              v-if="gridSlots[7]"
              class="col-span-2 group relative overflow-hidden rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 cursor-pointer aspect-video"
              @click="openLightbox(7)"
            >
              <img
                :src="getSlotImage(7)"
                :alt="getCurrentImageData(7).title"
                class="w-full h-full object-cover transform group-hover:scale-105 transition-all duration-500"
                :key="getSlotImage(7)"
              />
              <div
                class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4"
              >
                <div>
                  <p class="text-white font-bold text-base transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                    {{ getCurrentImageData(7).title }}
                  </p>
                  <p
                    v-if="getCurrentImageData(7).description"
                    class="text-gray-200 text-xs mt-1 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 delay-75 line-clamp-2"
                  >
                    {{ getCurrentImageData(7).description }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Desktop Layout (Bento Grid - 4 kolom) -->
        <div class="hidden lg:grid grid-cols-4 gap-[23px] max-w-[1437px]">
          <template v-for="(gallery, index) in gridSlots" :key="'desktop-' + index">
            <div
              v-if="gallery"
              class="group relative overflow-hidden rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 cursor-pointer"
              :class="getSpanClass(index)"
              :style="{ height: '342px' }"
              @click="openLightbox(index)"
            >
              <!-- Current image (maintains height, in normal flow) -->
              <img
                :src="getSlotImage(index)"
                :alt="getCurrentImageData(index).title"
                class="w-full h-full object-cover transform group-hover:scale-105 transition-all duration-700"
              />
              <!-- Previous image overlay (fades out during transition) -->
              <img
                v-if="previousSlotImages[index] && slotTransitioning[index]"
                :src="previousSlotImages[index]"
                alt=""
                class="absolute inset-0 w-full h-full object-cover transition-opacity duration-700"
                :class="slotTransitioning[index] ? 'opacity-0' : 'opacity-100'"
              />
              <div
                class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-6"
              >
                <div>
                  <p
                    class="text-white font-bold text-lg transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300"
                  >
                    {{ getCurrentImageData(index).title }}
                  </p>
                  <p
                    v-if="getCurrentImageData(index).description"
                    class="text-gray-200 text-sm mt-1 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 delay-75"
                  >
                    {{ getCurrentImageData(index).description }}
                  </p>
                </div>
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>
  </section>
</template>
<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue'

// Accept galleries prop from parent (Home.vue)
const props = defineProps({
  galleries: {
    type: Array,
    default: () => []
  }
})

// Slideshow state - each slot has its own current index
const slotImageIndexes = ref({})
// Previous image URLs for crossfade effect
const previousSlotImages = ref({})
// Transition state per slot (for crossfade animation)
const slotTransitioning = ref({})
let slideshowInterval = null

// Map galleries prop to grid slots format (9 items array)
const gridSlots = computed(() => {
  // Create 9-slot array, fill with galleries by grid_position
  const slots = new Array(9).fill(null)
  props.galleries.forEach(gallery => {
    const position = gallery.grid_position - 1 // Convert to 0-indexed
    if (position >= 0 && position < 9) {
      slots[position] = {
        ...gallery,
        image_url: gallery.image,
        // Include all_images for slideshow
        all_images: gallery.all_images || [{ image_url: gallery.image }]
      }
    }
  })
  return slots
})

// Get current image URL for a slot (for slideshow)
const getSlotImage = (slotIndex) => {
  const slot = gridSlots.value[slotIndex]
  if (!slot) return null
  
  const images = slot.all_images || []
  if (images.length === 0) return slot.image_url
  
  const currentIndex = slotImageIndexes.value[slotIndex] || 0
  return images[currentIndex]?.image_url || slot.image_url
}

// Get current image data (title, description) for a slot
const getCurrentImageData = (slotIndex) => {
  const slot = gridSlots.value[slotIndex]
  if (!slot) return { title: '', description: '' }
  
  const images = slot.all_images || []
  if (images.length === 0) return { title: slot.title, description: slot.description }
  
  const currentIndex = slotImageIndexes.value[slotIndex] || 0
  const currentImg = images[currentIndex]
  return {
    title: currentImg?.title || slot.title,
    description: currentImg?.description || slot.description
  }
}

// Check if slot has multiple images
const hasMultipleImages = (slotIndex) => {
  const slot = gridSlots.value[slotIndex]
  if (!slot) return false
  return (slot.all_images?.length || 0) > 1
}

// Initialize slideshow
const initSlideshow = () => {
  // Initialize each slot with random starting index to stagger animations
  gridSlots.value.forEach((slot, index) => {
    if (slot && slot.all_images && slot.all_images.length > 1) {
      slotImageIndexes.value[index] = Math.floor(Math.random() * slot.all_images.length)
    } else {
      slotImageIndexes.value[index] = 0
    }
  })
  
  // Start slideshow interval (5 seconds)
  slideshowInterval = setInterval(() => {
    gridSlots.value.forEach((slot, index) => {
      if (slot && slot.all_images && slot.all_images.length > 1) {
        const currentIndex = slotImageIndexes.value[index] || 0
        const nextIndex = (currentIndex + 1) % slot.all_images.length
        
        // Save current image URL as previous (for crossfade)
        previousSlotImages.value[index] = slot.all_images[currentIndex]?.image_url
        
        // Start transition
        slotTransitioning.value[index] = true
        
        // Change to next image
        slotImageIndexes.value[index] = nextIndex
        
        // End transition after animation (700ms)
        setTimeout(() => {
          slotTransitioning.value[index] = false
        }, 700)
      }
    })
  }, 5000)
}

const loading = false
const error = null

// Filter only filled galleries for display count
const displayGalleries = computed(() => {
  return gridSlots.value.filter((slot) => slot !== null)
})

// Get span class based on slot index (fixed pattern)
const getSpanClass = (index) => {
  // Slot 1, 2, 8 (index 0, 1, 7) = Wide (2 kotak)
  // Slot 3, 4, 5, 6, 7, 9 (index 2, 3, 4, 5, 6, 8) = Small (1 kotak)
  if (index === 0 || index === 1 || index === 7) {
    return 'col-span-2'
  }
  return 'col-span-1'
}

// Open lightbox (you can implement lightbox modal later)
const openLightbox = (index) => {
  console.log('Open lightbox for gallery:', gridSlots.value[index])
  // TODO: Implement lightbox modal
}

// View all gallery (navigate to full gallery page)
const viewAllGallery = () => {
  console.log('Navigate to full gallery page')
  // TODO: Implement navigation to full gallery page
  // Example: router.push('/galeri')
}

// Lifecycle
onMounted(() => {
  initSlideshow()
})

onUnmounted(() => {
  if (slideshowInterval) {
    clearInterval(slideshowInterval)
  }
})
</script>
<style scoped>
/* Blur effect saat gambar loading */
img[lazy='loading'] {
  filter: blur(10px);
  transition: filter 0.3s;
  /* Fix: ensure image fills container during loading */
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
}
img[lazy='loaded'] {
  filter: blur(0);
}
img[lazy='error'] {
  filter: grayscale(100%);
}

/* Fix: Add shimmer background while image loads */
.group img[lazy='loading']::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(90deg, #e5e7eb 0%, #f3f4f6 50%, #e5e7eb 100%);
}

/* Skeleton with Shimmer Effect */
.skeleton-shimmer {
  position: relative;
  background: linear-gradient(90deg, #e5e7eb 0%, #f3f4f6 50%, #e5e7eb 100%);
  background-size: 200% 100%;
}

:global(.dark) .skeleton-shimmer {
  background: linear-gradient(90deg, #374151 0%, #4b5563 50%, #374151 100%);
  background-size: 200% 100%;
}

/* Shimmer sweep effect */
.shimmer-effect {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(
    90deg,
    transparent 0%,
    rgba(255, 255, 255, 0.4) 50%,
    transparent 100%
  );
  animation: shimmer 1.5s infinite;
}

:global(.dark) .shimmer-effect {
  background: linear-gradient(
    90deg,
    transparent 0%,
    rgba(255, 255, 255, 0.1) 50%,
    transparent 100%
  );
}

@keyframes shimmer {
  0% {
    transform: translateX(-100%);
  }
  100% {
    transform: translateX(100%);
  }
}

/* Slideshow Fade/Slide Transition */
.gallery-slide-enter-active,
.gallery-slide-leave-active {
  transition: all 0.5s ease-out;
}
.gallery-slide-enter-from {
  opacity: 0;
  transform: translateX(20px);
}
.gallery-slide-leave-to {
  opacity: 0;
  transform: translateX(-20px);
}
</style>
