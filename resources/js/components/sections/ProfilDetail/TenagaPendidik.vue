<template>
  <section
    ref="guruSection"
    class="py-24 bg-gradient-to-b from-gray-50 to-white dark:from-gray-900 dark:to-gray-800 transition-colors duration-300 overflow-hidden"
  >
    <!-- OLD: container mx-auto px-6 -->
    <div class="container-content">
      <div class="text-center mb-16 relative z-10">
        >
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mt-3 font-poppins">
          Pendidik Inspiratif
        </h2>
        <p class="mt-4 text-gray-600 dark:text-gray-400 max-w-2xl mx-auto text-md">
          Dedikasi kami untuk membentuk masa depan generasi penerus bangsa.
        </p>
      </div>

      <!-- Modern Card Grid Layout -->
      <div class="relative max-w-7xl mx-auto">
        <!-- Decorative Background Elements -->
        <div
          class="absolute top-0 right-0 w-72 h-72 bg-blue-400/10 dark:bg-blue-600/10 rounded-full blur-3xl -z-10"
        ></div>
        <div
          class="absolute bottom-0 left-0 w-96 h-96 bg-indigo-400/10 dark:bg-indigo-600/10 rounded-full blur-3xl -z-10"
        ></div>

        <!-- Desktop Grid (hidden on mobile) -->
        <div class="hidden lg:grid grid-cols-4 gap-6">
          <!-- Loading Skeleton -->
          <div v-if="loading" v-for="n in 4" :key="'skeleton-' + n" class="group relative">
            <div class="relative bg-gray-200 dark:bg-gray-700 rounded-2xl overflow-hidden h-96 animate-pulse">
              <div class="h-64 bg-gray-300 dark:bg-gray-600"></div>
              <div class="p-6 space-y-3">
                <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-3/4"></div>
                <div class="h-3 bg-gray-300 dark:bg-gray-600 rounded w-1/2"></div>
              </div>
            </div>
          </div>

          <!-- Error State -->
          <div v-else-if="error" class="col-span-4 text-center py-12">
            <svg class="w-16 h-16 mx-auto text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-gray-600 dark:text-gray-400">{{ error }}</p>
          </div>

          <!-- Empty State -->
          <div v-else-if="displayedTeachers.length === 0" class="col-span-4 text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <p class="text-gray-600 dark:text-gray-400">Tidak ada guru ditemukan</p>
          </div>

          <!-- Teacher Cards (Limited to 8 for 2 rows) -->
          <div v-else v-for="(teacher, index) in displayedTeachers" :key="teacher.id" class="group relative">
            <!-- Modern Card -->
            <div
              class="relative bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 border border-gray-100 dark:border-gray-700"
            >
              <!-- Image Container -->
              <div class="relative h-64 overflow-hidden">
                <!-- Gradient Overlay -->
                <div
                  class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent z-10 opacity-60 group-hover:opacity-40 transition-opacity duration-500"
                ></div>

                <!-- Image -->
                <img
                  :src="teacher.photo"
                  :alt="teacher.name"
                  class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                />

                <!-- Subject Badge (Floating on Image) -->
                <div class="absolute top-4 right-4 z-20">
                  <span
                    class="px-3 py-1.5 bg-blue-600/90 backdrop-blur-sm text-white text-xs font-bold uppercase tracking-wider rounded-lg shadow-lg"
                  >
                    {{ teacher.subject }}
                  </span>
                </div>

                <!-- Decorative Corner Accent -->
                <div
                  class="absolute top-0 left-0 w-20 h-20 bg-gradient-to-br from-blue-500/20 to-transparent rounded-br-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"
                ></div>
              </div>

              <!-- Card Content -->
              <div class="p-6 relative">
                <!-- Decorative Line -->
                <div
                  class="absolute top-0 left-6 right-6 h-1 bg-gradient-to-r from-transparent via-blue-500 to-transparent transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"
                ></div>

                <h3
                  class="text-lg font-bold text-gray-900 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2"
                >
                  {{ teacher.name }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium mb-4">
                  {{ teacher.subject }}
                </p>
              </div>

              <!-- Hover Glow Effect -->
              <div
                class="absolute inset-0 rounded-2xl bg-gradient-to-t from-blue-500/0 via-blue-500/0 to-blue-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"
              ></div>
            </div>
          </div>
        </div>

        <!-- Mobile Carousel (visible on mobile only) -->
        <div class="lg:hidden relative">
          <div
            ref="carouselContainer"
            class="overflow-x-auto snap-x snap-mandatory scrollbar-hide"
            @scroll="handleScroll"
          >
            <div class="flex gap-6 pb-4">
              <!-- Spacer for first card to center -->
              <div class="flex-shrink-0 w-[12.5%] sm:w-[17.5%]"></div>

              <!-- Loading Skeleton -->
              <div v-if="loading" class="flex-shrink-0 snap-center w-[75%] sm:w-[65%]" v-for="n in 3" :key="'skeleton-mobile-' + n">
                <div class="relative bg-gray-200 dark:bg-gray-700 rounded-2xl overflow-hidden h-96 animate-pulse">
                  <div class="h-56 sm:h-64 bg-gray-300 dark:bg-gray-600"></div>
                  <div class="p-5 space-y-2">
                    <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-3/4"></div>
                    <div class="h-3 bg-gray-300 dark:bg-gray-600 rounded w-1/2"></div>
                  </div>
                </div>
              </div>

              <!-- Teacher Cards -->
              <div
                v-else
                v-for="(teacher, index) in carouselTeachers"
                :key="teacher.id"
                :ref="(el) => setCardRef(el, index)"
                class="flex-shrink-0 snap-center w-[75%] sm:w-[65%]"
                :style="getCardStyle(index)"
              >
                <!-- Modern Card -->
                <div
                  class="relative bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-lg border border-gray-100 dark:border-gray-700 h-full"
                >
                  <!-- Image Container -->
                  <div class="relative h-56 sm:h-64 overflow-hidden">
                    <!-- Gradient Overlay -->
                    <div
                      class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent z-10 opacity-60"
                    ></div>

                    <!-- Image -->
                    <img
                      :src="teacher.photo"
                      :alt="teacher.name"
                      class="w-full h-full object-cover"
                    />

                    <!-- Subject Badge (Floating on Image) -->
                    <div class="absolute top-3 right-3 z-20">
                      <span
                        class="px-2.5 py-1 bg-blue-600/90 backdrop-blur-sm text-white text-xs font-bold uppercase tracking-wider rounded-lg shadow-lg"
                      >
                        {{ teacher.subject }}
                      </span>
                    </div>

                    <!-- Decorative Corner Accent -->
                    <div
                      class="absolute top-0 left-0 w-16 h-16 bg-gradient-to-br from-blue-500/20 to-transparent rounded-br-full"
                    ></div>
                  </div>

                  <!-- Card Content -->
                  <div class="p-5 relative">
                    <!-- Decorative Line -->
                    <div
                      class="absolute top-0 left-5 right-5 h-1 bg-gradient-to-r from-transparent via-blue-500 to-transparent"
                    ></div>

                    <h3
                      class="text-base font-bold text-gray-900 dark:text-white mb-1.5 line-clamp-2"
                    >
                      {{ teacher.name }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                      {{ teacher.subject }}
                    </p>
                  </div>

                  <!-- Hover Glow Effect -->
                  <div
                    class="absolute inset-0 rounded-2xl bg-gradient-to-t from-blue-500/0 via-blue-500/0 to-blue-500/5 pointer-events-none"
                  ></div>
                </div>
              </div>

              <!-- Spacer for last card to center -->
              <div class="flex-shrink-0 w-[12.5%] sm:w-[17.5%]"></div>
            </div>
          </div>

          <!-- Right Arrow (when first card is centered) -->
          <Transition name="fade">
            <div
              v-if="centeredCardIndex === 0"
              class="absolute right-2 top-1/2 -translate-y-1/2 z-30 pointer-events-none"
            >
              <div class="flex flex-col items-center gap-1.5 animate-bounce-x">
                <div class="p-2 bg-blue-600/70 backdrop-blur-sm rounded-lg shadow-lg">
                  <svg
                    class="w-5 h-5 text-white"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                    viewBox="0 0 24 24"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6" />
                  </svg>
                </div>
                <span
                  class="text-[10px] font-bold text-blue-600 dark:text-blue-400 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm px-2 py-0.5 rounded-md shadow-md"
                >
                  Swipe
                </span>
              </div>
            </div>
          </Transition>

          <!-- Left Arrow (when last card is centered) -->
          <Transition name="fade">
            <div
              v-if="centeredCardIndex === carouselTeachers.length - 1"
              class="absolute left-2 top-1/2 -translate-y-1/2 z-30 pointer-events-none"
            >
              <div class="flex flex-col items-center gap-1.5 animate-bounce-x-reverse">
                <div class="p-2 bg-blue-600/70 backdrop-blur-sm rounded-lg shadow-lg">
                  <svg
                    class="w-5 h-5 text-white"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                    viewBox="0 0 24 24"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6" />
                  </svg>
                </div>
                <span
                  class="text-[10px] font-bold text-blue-600 dark:text-blue-400 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm px-2 py-0.5 rounded-md shadow-md"
                >
                  Swipe
                </span>
              </div>
            </div>
          </Transition>
        </div>
      </div>

      <!-- CTA Button -->
      <div class="text-center mt-16">
        <button
          @click="navigateToGuru"
          class="group relative px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden"
        >
          <span class="relative flex items-center gap-2">
            Lihat Semua Guru
            <svg
              class="w-5 h-5 transform group-hover:translate-x-1 transition-transform"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M17 8l4 4m0 0l-4 4m4-4H3"
              />
            </svg>
          </span>
          <!-- Button Shine Effect -->
          <div
            class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-1000"
          ></div>
        </button>
      </div>
    </div>
  </section>
</template>
<script setup>
import { onMounted, onUnmounted, ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

// Navigate to guru page using Inertia
const navigateToGuru = () => {
  router.visit('/guru')
}

// State
const teachers = ref([])
const loading = ref(true)
const error = ref(null)

// Carousel state
const carouselContainer = ref(null)
const cardRefs = ref([])
const cardScales = ref([])
const centeredCardIndex = ref(0)

// Computed - Limit to 8 teachers (2 rows of 4)
const displayedTeachers = computed(() => teachers.value.slice(0, 8))

// Carousel uses same 8 teachers
const carouselTeachers = computed(() => displayedTeachers.value)

// API Functions
const fetchTeachers = async () => {
  try {
    loading.value = true
    error.value = null
    
    const response = await axios.get('/api/guru')
    
    if (response.data.success) {
      teachers.value = response.data.data
    } else {
      throw new Error('Failed to fetch teachers')
    }
  } catch (err) {
    console.error('Error fetching teachers:', err)
    error.value = 'Gagal memuat data guru. Silakan coba lagi.'
  } finally {
    loading.value = false
  }
}

// Carousel Functions
const setCardRef = (el, index) => {
  if (el) {
    cardRefs.value[index] = el
  }
}

const handleScroll = () => {
  if (!carouselContainer.value || cardRefs.value.length === 0) return

  const container = carouselContainer.value
  const containerCenter = container.scrollLeft + container.clientWidth / 2

  // Find the card closest to center
  let closestIndex = 0
  let closestDistance = Infinity

  cardRefs.value.forEach((card, index) => {
    if (!card) return

    const cardRect = card.getBoundingClientRect()
    const containerRect = container.getBoundingClientRect()

    // Calculate card center relative to container
    const cardCenter =
      cardRect.left + cardRect.width / 2 - containerRect.left + container.scrollLeft

    // Distance from container center
    const distance = Math.abs(containerCenter - cardCenter)

    if (distance < closestDistance) {
      closestDistance = distance
      closestIndex = index
    }
  })

  // Update centered card index for arrow indicators
  centeredCardIndex.value = closestIndex

  // Now apply styles based on whether card is the closest one
  cardRefs.value.forEach((card, index) => {
    if (!card) return

    const cardRect = card.getBoundingClientRect()
    const containerRect = container.getBoundingClientRect()

    // Calculate card center relative to container
    const cardCenter =
      cardRect.left + cardRect.width / 2 - containerRect.left + container.scrollLeft

    // Distance from container center
    const distance = Math.abs(containerCenter - cardCenter)

    // Max distance for scaling calculation
    const maxDistance = container.clientWidth / 2

    // If this is the closest card, force scale to 1.0
    if (index === closestIndex) {
      cardScales.value[index] = {
        scale: 1.0,
        opacity: 1.0
      }
    } else {
      // Calculate scale: 1.0 at center, 0.7 at edges (DRAMATIC!)
      const normalizedDistance = Math.min(distance / maxDistance, 1)
      const scale = 1 - normalizedDistance * 0.3 // Range: 1.0 to 0.7

      // Calculate opacity: 1.0 at center, 0.5 at edges
      const opacity = 1 - normalizedDistance * 0.5 // Range: 1.0 to 0.5

      cardScales.value[index] = {
        scale: scale,
        opacity: opacity
      }
    }
  })
}

const getCardStyle = (index) => {
  const cardScale = cardScales.value[index] || { scale: 0.7, opacity: 0.5 }
  return {
    transform: `scale(${cardScale.scale})`,
    opacity: cardScale.opacity,
    filter: cardScale.scale < 0.95 ? 'blur(1px)' : 'blur(0px)',
    transition: 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)'
  }
}

onMounted(async () => {
  // Fetch teachers data
  await fetchTeachers()
  
  // Initialize carousel scales after data is loaded
  setTimeout(() => {
    cardScales.value = carouselTeachers.value.map(() => ({ scale: 0.7, opacity: 0.5 }))
    
    if (carouselContainer.value) {
      handleScroll()
    }
  }, 100)
})

onUnmounted(() => {
  cardRefs.value = []
  cardScales.value = []
})
</script>
<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}

@keyframes spin-slow {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.animate-spin-slow {
  animation: spin-slow 10s linear infinite;
}

/* Hide scrollbar for Chrome, Safari and Opera */
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}

/* Hide scrollbar for IE, Edge and Firefox */
.scrollbar-hide {
  -ms-overflow-style: none; /* IE and Edge */
  scrollbar-width: none; /* Firefox */
}

/* Smooth scroll behavior */
.snap-x {
  scroll-behavior: smooth;
}

/* Arrow bounce animations */
@keyframes bounce-x {
  0%,
  100% {
    transform: translateX(0);
  }
  50% {
    transform: translateX(10px);
  }
}

@keyframes bounce-x-reverse {
  0%,
  100% {
    transform: translateX(0);
  }
  50% {
    transform: translateX(-10px);
  }
}

.animate-bounce-x {
  animation: bounce-x 1.5s ease-in-out infinite;
}

.animate-bounce-x-reverse {
  animation: bounce-x-reverse 1.5s ease-in-out infinite;
}

/* Fade transition for arrows */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
