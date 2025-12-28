<!--
  @component TestimoniSection
  @description Marquee carousel testimoni dari siswa, alumni, dan orang tua
  @endpoint GET /api/testimonials
  @behavior Desktop: auto-scroll marquee, Mobile: touch scroll, Drag untuk pause
-->

<template>
  <section class="py-24 overflow-hidden">
    <div class="container mx-auto px-4 mb-12 text-center">
      <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4 font-poppins">
        Apa Kata Mereka?
      </h2>
      <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto font-poppins">
        Dengarkan pengalaman langsung dari siswa, alumni, dan orang tua tentang perjalanan mereka
        bersama kami.
      </p>
    </div>
    <div class="relative w-full">
      <div
        class="hidden md:block absolute top-0 left-0 w-32 h-full bg-gradient-to-r from-gray-50 dark:from-gray-900 to-transparent z-10 pointer-events-none"
      ></div>
      <div
        class="hidden md:block absolute top-0 right-0 w-32 h-full bg-gradient-to-l from-gray-50 dark:from-gray-900 to-transparent z-10 pointer-events-none"
      ></div>

      <!-- Loading State -->
      <div v-if="loading" class="flex overflow-x-auto overflow-y-hidden scrollbar-hide">
        <div class="flex items-stretch gap-6 px-3">
          <div v-for="n in 3" :key="'skeleton-' + n" class="flex-shrink-0 w-[300px] md:w-[400px]">
            <div class="h-full bg-gray-200 dark:bg-gray-700 rounded-xl p-6 md:p-8 animate-pulse">
              <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 rounded-full bg-gray-300 dark:bg-gray-600"></div>
                <div class="flex-1">
                  <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-3/4 mb-2"></div>
                  <div class="h-3 bg-gray-300 dark:bg-gray-600 rounded w-1/2"></div>
                </div>
              </div>
              <div class="space-y-2">
                <div class="h-3 bg-gray-300 dark:bg-gray-600 rounded"></div>
                <div class="h-3 bg-gray-300 dark:bg-gray-600 rounded"></div>
                <div class="h-3 bg-gray-300 dark:bg-gray-600 rounded w-5/6"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="text-center py-12">
        <svg class="w-16 h-16 mx-auto text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="text-gray-600 dark:text-gray-400 font-poppins">{{ error }}</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="testimonials.length === 0" class="text-center py-12">
        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        <p class="text-gray-600 dark:text-gray-400 font-poppins">Belum ada testimoni</p>
      </div>

      <div
        v-else
        ref="scrollContainer"
        class="flex overflow-x-auto overflow-y-hidden scrollbar-hide md:overflow-hidden"
        @mousedown="handleMouseDown"
        @mousemove="handleMouseMove"
        @mouseup="handleMouseUp"
        @mouseleave="handleMouseUp"
        @touchstart="handleTouchStart"
        @touchmove="handleTouchMove"
        @touchend="handleTouchEnd"
      >
        <div
          :class="isPaused ? '' : 'animate-marquee'"
          class="flex items-stretch gap-6 px-3"
          :style="{ animationPlayState: isPaused ? 'paused' : 'running' }"
        >
          <div
            v-for="(item, index) in testimonials"
            :key="'a-' + index"
            class="flex-shrink-0 w-[300px] md:w-[400px]"
          >
            <div
              class="h-full bg-white dark:bg-gray-800 rounded-xl p-6 md:p-8 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-300 flex flex-col"
            >
              <div class="flex items-center gap-4 mb-6">
                <div
                  class="w-12 h-12 rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden flex-shrink-0"
                >
                  <img
                    :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(item.author)}&background=random`"
                    :alt="item.author"
                    class="w-full h-full object-cover"
                  />
                </div>
                <div>
                  <h3
                    class="font-bold text-gray-900 dark:text-white font-poppins text-sm md:text-base"
                  >
                    {{ item.author }}
                  </h3>
                  <p class="text-xs text-gray-500 dark:text-gray-400 font-poppins">
                    {{ item.role }}
                  </p>
                </div>
              </div>

              <p
                class="text-gray-600 dark:text-gray-300 text-sm md:text-base leading-relaxed font-poppins flex-grow"
              >
                "{{ item.text }}"
              </p>
            </div>
          </div>
        </div>

        <div
          :class="isPaused ? '' : 'animate-marquee'"
          class="flex items-stretch gap-6 px-3"
          aria-hidden="true"
          :style="{ animationPlayState: isPaused ? 'paused' : 'running' }"
        >
          <div
            v-for="(item, index) in testimonials"
            :key="'b-' + index"
            class="flex-shrink-0 w-[300px] md:w-[400px]"
          >
            <div
              class="h-full bg-white dark:bg-gray-800 rounded-xl p-6 md:p-8 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-300 flex flex-col"
            >
              <div class="flex items-center gap-4 mb-6">
                <div
                  class="w-12 h-12 rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden flex-shrink-0"
                >
                  <img
                    :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(item.author)}&background=random`"
                    :alt="item.author"
                    class="w-full h-full object-cover"
                  />
                </div>
                <div>
                  <h3
                    class="font-bold text-gray-900 dark:text-white font-poppins text-sm md:text-base"
                  >
                    {{ item.author }}
                  </h3>
                  <p class="text-xs text-gray-500 dark:text-gray-400 font-poppins">
                    {{ item.role }}
                  </p>
                </div>
              </div>
              <p
                class="text-gray-600 dark:text-gray-300 text-sm md:text-base leading-relaxed font-poppins flex-grow"
              >
                "{{ item.text }}"
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue'

// Accept testimonials prop from parent (Home.vue)
const props = defineProps({
  testimonials: {
    type: Array,
    default: () => []
  }
})

// Transform prop data to match template format
const testimonials = computed(() => {
  return props.testimonials.map(item => ({
    author: item.name,
    role: item.role,
    text: item.content
  }))
})

const loading = false
const error = null

const scrollContainer = ref(null)
const isPaused = ref(false)
const isMobile = ref(false)

let isDown = false
let startX = 0
let scrollLeft = 0

const checkMobile = () => {
  isMobile.value = window.innerWidth < 768
}

const handleMouseDown = (e) => {
  if (isMobile.value) return
  isDown = true
  isPaused.value = true
  startX = e.pageX - scrollContainer.value.offsetLeft
  scrollLeft = scrollContainer.value.scrollLeft
}

const handleMouseMove = (e) => {
  if (!isDown || isMobile.value) return
  e.preventDefault()
  const x = e.pageX - scrollContainer.value.offsetLeft
  const walk = (x - startX) * 2
  scrollContainer.value.scrollLeft = scrollLeft - walk
}

const handleMouseUp = () => {
  if (isMobile.value) return
  isDown = false
  setTimeout(() => {
    isPaused.value = false
  }, 1000)
}

let touchStartX = 0
let touchStartScrollLeft = 0

const handleTouchStart = (e) => {
  if (!isMobile.value) return
  isPaused.value = true
  touchStartX = e.touches[0].pageX
  touchStartScrollLeft = scrollContainer.value.scrollLeft
}

const handleTouchMove = (e) => {
  if (!isMobile.value) return
  const touchX = e.touches[0].pageX
  const walk = (touchStartX - touchX) * 1.5
  scrollContainer.value.scrollLeft = touchStartScrollLeft + walk
}

const handleTouchEnd = () => {
  if (!isMobile.value) return
  setTimeout(() => {
    isPaused.value = false
  }, 2000)
}

onMounted(() => {
  checkMobile()
  window.addEventListener('resize', checkMobile)
})

onUnmounted(() => {
  window.removeEventListener('resize', checkMobile)
})
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}

.animate-marquee {
  animation: marquee 40s linear infinite;
}

@keyframes marquee {
  0% {
    transform: translateX(0);
  }
  100% {
    transform: translateX(-100%);
  }
}

/* Hide scrollbar */
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
  display: none;
}

/* Smooth scrolling for mobile */
@media (max-width: 768px) {
  .overflow-x-auto {
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
  }
}
</style>
