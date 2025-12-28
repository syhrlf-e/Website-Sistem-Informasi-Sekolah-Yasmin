<template>
  <div
    class="rounded-3xl shadow-2xl overflow-hidden h-[580px] md:h-[380px] lg:h-[556px] flex transition-all duration-500 relative"
    :class="cardClass"
  >
    <!-- Main Card Content -->
    <div class="grid md:grid-cols-2 w-full h-full relative">
      <div class="pt-4 px-6 pb-6 md:p-6 lg:p-12 flex flex-col justify-start order-2 md:order-1 h-full relative overflow-hidden">
        <!-- Desktop Detail Overlay -->
        <Transition name="slide-left">
          <div
            v-if="showDesktopDetail"
            class="hidden md:block absolute inset-0 z-20"
            :class="isDark ? 'bg-gray-900 dark:bg-white' : 'bg-white dark:bg-gray-900'"
          >
            <div class="h-full flex flex-col p-8">
              <!-- Header with Close Button -->
              <div class="flex justify-between items-center pb-4 border-b flex-shrink-0" :class="isDark ? 'border-gray-700 dark:border-gray-300' : 'border-gray-200 dark:border-gray-700'">
                <h4 class="text-xl font-bold" :class="titleClass">Detail Prestasi</h4>
                <button
                  @click="closeDesktopDetail"
                  class="w-9 h-9 rounded-full flex items-center justify-center transition-colors"
                  :class="isDark ? 'bg-gray-800 dark:bg-gray-100 text-white dark:text-gray-900 hover:bg-gray-700 dark:hover:bg-gray-200' : 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-700'"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>

              <!-- Detail Content -->
              <div class="flex-1 min-h-0 overflow-y-auto py-5 scroll-smooth">
                <div class="space-y-5">
                  <!-- Baris 1: Nama Pemenang & Tingkat -->
                  <div class="grid grid-cols-2 gap-4">
                    <!-- Nama Pemenang -->
                    <div>
                      <p class="text-xs font-semibold mb-1.5 uppercase tracking-wide" :class="descriptionClass">Nama Pemenang</p>
                      <p class="text-base font-semibold" :class="titleClass">{{ winnerName || 'Nama Pemenang' }}</p>
                    </div>

                    <!-- Tingkat -->
                    <div v-if="tingkat" class="text-right">
                      <p class="text-xs font-semibold mb-1.5 uppercase tracking-wide" :class="descriptionClass">Tingkat</p>
                      <p class="text-base font-semibold" :class="titleClass">{{ tingkat }}</p>
                    </div>
                  </div>

                  <!-- Baris 2: Kategori & Tahun -->
                  <div class="grid grid-cols-2 gap-4">
                    <!-- Kategori -->
                    <div>
                      <p class="text-xs font-semibold mb-1.5 uppercase tracking-wide" :class="descriptionClass">Kategori</p>
                      <p class="text-base font-semibold" :class="titleClass">{{ badge }}</p>
                    </div>

                    <!-- Tahun -->
                    <div class="text-right">
                      <p class="text-xs font-semibold mb-1.5 uppercase tracking-wide" :class="descriptionClass">Tahun</p>
                      <p class="text-base font-semibold" :class="titleClass">{{ year || '2024' }}</p>
                    </div>
                  </div>

                  <!-- Garis Pemisah -->
                  <div class="border-t" :class="isDark ? 'border-gray-700 dark:border-gray-300' : 'border-gray-200 dark:border-gray-700'"></div>

                  <!-- Baris 3: Deskripsi (Full Width) -->
                  <div>
                    <p class="text-xs font-semibold mb-1.5 uppercase tracking-wide" :class="descriptionClass">Deskripsi</p>
                    <p class="text-sm leading-relaxed" :class="descriptionClass">{{ fullDescription || description }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </Transition>

        <div class="mb-0 md:mb-4 lg:mb-8 hidden md:block">
          <BaseBadge :label="badge" :variant="badgeVariant" />
        </div>

        <!-- Title - Desktop Only -->
        <h3 class="hidden md:block mt-0 text-2xl lg:text-4xl font-bold mb-4 lg:mb-6 leading-tight" :class="titleClass">
          {{ title }}
        </h3>

        <!-- Mobile Spacer for overlapping elements -->
        <div class="md:hidden h-40"></div>

        <!-- Desktop Only Content -->
        <div class="hidden md:block relative flex-1">
          <p class="text-sm lg:text-base leading-relaxed line-clamp-3 lg:line-clamp-5" :class="descriptionClass" style="display: -webkit-box; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis;">
            {{ description }}
          </p>
        </div>

        <ReadMoreButton
          :variant="isDark ? 'dark' : 'light'"
          @click="openDesktopDetail"
          class="hidden md:inline-flex md:w-fit md:mt-4"
        />
      </div>

      <!-- IMAGE SECTION -->
      <div class="relative h-64 md:h-full order-1 md:order-2 overflow-hidden">
        <img :src="image" :alt="imageAlt" class="absolute inset-0 w-full h-full object-cover" :style="imageStyle" />

        <!-- Overlay Nama (Desktop Only) - Rounded Full dengan Margin -->
        <div
          class="hidden md:block absolute m-6 bottom-0 left-0 right-0 bg-black/50 backdrop-blur-sm px-6 py-4 rounded-full"
        >
          <p class="text-white font-semibold text-lg text-center">
            {{ winnerName || 'Pemenang' }}
          </p>
        </div>

        <div class="absolute top-3 right-3 md:hidden">
          <BaseBadge :label="badge" :variant="badgeVariant" size="sm" />
        </div>
      </div>

      <!-- Overlapping Title (Mobile Only) - Glassmorphism -->
      <div class="md:hidden absolute left-0 right-0 z-20 px-6" style="top: calc(16rem - 2.5rem)">
        <div
          class="backdrop-blur-md rounded-2xl px-6 py-4 shadow-xl border"
          :class="
            isDark
              ? 'bg-gray-900/90 dark:bg-white/90 border-white/20 dark:border-gray-900/20'
              : 'bg-white/90 dark:bg-gray-900/90 border-white/20 dark:border-gray-900/20'
          "
        >
          <h3 class="text-xl font-bold leading-tight text-center" :class="titleClass">
            {{ title }}
          </h3>
        </div>

        <!-- Info Preview - 20px below title box -->
        <div class="mt-5 px-6 space-y-2">
          <!-- Winner Name -->
          <div class="flex items-center gap-2">
            <svg
              class="w-4 h-4 flex-shrink-0"
              :class="descriptionClass"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
              />
            </svg>
            <p class="text-sm font-semibold" :class="titleClass">
              {{ winnerName || 'Pemenang' }}
            </p>
          </div>

          <!-- Year -->
          <div class="flex items-center gap-2">
            <svg
              class="w-4 h-4 flex-shrink-0"
              :class="descriptionClass"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
              />
            </svg>
            <p class="text-sm font-semibold" :class="titleClass">
              {{ year || '2024' }}
            </p>
          </div>

          <!-- Description Preview -->
          <p class="text-sm leading-relaxed line-clamp-2 mt-3" :class="descriptionClass">
            {{ description }}
          </p>
        </div>
      </div>
    </div>

    <!-- Improved Arrow Button (Mobile Only) -->
    <button
      v-show="!showDetail"
      @click="openDetail"
      class="md:hidden absolute left-0 right-0 z-10 flex justify-center px-6 transition-all duration-300"
      :style="{ bottom: '2rem' }"
    >
      <div
        class="px-5 py-3 rounded-full flex items-center gap-2 shadow-xl transition-all duration-300 hover:scale-105"
        :class="
          isDark
            ? 'bg-white dark:bg-gray-900 text-gray-900 dark:text-white'
            : 'bg-gray-900 dark:bg-white text-white dark:text-gray-900'
        "
      >
        <span class="text-sm font-semibold">Lihat Detail</span>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </div>
    </button>
  </div>

  <!-- Slide Detail Panel (Mobile Only) - Fixed Viewport dengan Backdrop -->
  <Teleport to="body">
    <Transition name="backdrop">
      <div
        v-if="showDetail"
        @click="closeDetail"
        class="md:hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] transition-all duration-300"
      ></div>
    </Transition>

    <Transition name="slide-up">
      <div
        v-if="showDetail"
        class="md:hidden fixed bottom-0 left-0 right-0 z-[101] rounded-t-3xl shadow-2xl"
        :class="isDark ? 'bg-gray-900 dark:bg-white' : 'bg-white dark:bg-gray-900'"
      >
        <div class="h-[calc(100vh-100px)] flex flex-col">
          <!-- Drag Handle -->
          <div class="flex justify-center pt-3 pb-2 flex-shrink-0">
            <div
              class="w-12 h-1.5 rounded-full"
              :class="isDark ? 'bg-gray-700 dark:bg-gray-300' : 'bg-gray-300 dark:bg-gray-700'"
            ></div>
          </div>

          <!-- Header with Close Button -->
          <div
            class="flex justify-between items-center px-6 pb-4 border-b flex-shrink-0"
            :class="
              isDark
                ? 'border-gray-800 dark:border-gray-200'
                : 'border-gray-200 dark:border-gray-800'
            "
          >
            <h4 class="text-xl font-bold" :class="titleClass">Detail Prestasi</h4>
            <button
              @click="closeDetail"
              class="w-9 h-9 rounded-full flex items-center justify-center transition-colors"
              :class="
                isDark
                  ? 'bg-gray-800 dark:bg-gray-100 text-white dark:text-gray-900 hover:bg-gray-700 dark:hover:bg-gray-200'
                  : 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-700'
              "
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"
                />
              </svg>
            </button>
          </div>

          <!-- Detail Content -->
          <div class="flex-1 min-h-0 overflow-y-auto px-6 py-5 scroll-smooth">
            <div class="space-y-5">
              <!-- Baris 1: Nama Pemenang & Tingkat -->
              <div class="grid grid-cols-2 gap-4">
                <!-- Nama Pemenang -->
                <div>
                  <p
                    class="text-xs font-semibold mb-1.5 uppercase tracking-wide"
                    :class="descriptionClass"
                  >
                    Nama Pemenang
                  </p>
                  <p class="text-base font-semibold" :class="titleClass">
                    {{ winnerName || 'Nama Pemenang' }}
                  </p>
                </div>

                <!-- Tingkat -->
                <div v-if="tingkat" class="text-right">
                  <p
                    class="text-xs font-semibold mb-1.5 uppercase tracking-wide"
                    :class="descriptionClass"
                  >
                    Tingkat
                  </p>
                  <p class="text-base font-semibold" :class="titleClass">{{ tingkat }}</p>
                </div>
              </div>

              <!-- Baris 2: Kategori & Tahun -->
              <div class="grid grid-cols-2 gap-4">
                <!-- Kategori -->
                <div>
                  <p
                    class="text-xs font-semibold mb-1.5 uppercase tracking-wide"
                    :class="descriptionClass"
                  >
                    Kategori
                  </p>
                  <p class="text-base font-semibold" :class="titleClass">{{ badge }}</p>
                </div>

                <!-- Tahun -->
                <div class="text-right">
                  <p
                    class="text-xs font-semibold mb-1.5 uppercase tracking-wide"
                    :class="descriptionClass"
                  >
                    Tahun
                  </p>
                  <p class="text-base font-semibold" :class="titleClass">{{ year || '2024' }}</p>
                </div>
              </div>

              <!-- Garis Pemisah -->
              <div
                class="border-t"
                :class="
                  isDark
                    ? 'border-gray-700 dark:border-gray-300'
                    : 'border-gray-200 dark:border-gray-700'
                "
              ></div>

              <!-- Baris 3: Deskripsi (Full Width) -->
              <div>
                <p
                  class="text-xs font-semibold mb-1.5 uppercase tracking-wide"
                  :class="descriptionClass"
                >
                  Deskripsi
                </p>
                <p class="text-sm leading-relaxed" :class="descriptionClass">
                  {{ fullDescription || description }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed, ref } from 'vue'
import BaseBadge from '@/components/ui/shared/BaseBadge.vue'
import ReadMoreButton from './ReadMoreButton.vue'

const props = defineProps({
  badge: {
    type: String,
    required: true
  },
  badgeVariant: {
    type: String,
    default: 'default'
  },
  title: {
    type: String,
    required: true
  },
  description: {
    type: String,
    required: true
  },
  image: {
    type: String,
    required: true
  },
  imageAlt: {
    type: String,
    required: true
  },
  isDark: {
    type: Boolean,
    default: false
  },
  // New props for detail
  winnerName: {
    type: String,
    default: ''
  },
  year: {
    type: String,
    default: ''
  },
  fullDescription: {
    type: String,
    default: ''
  },
  tingkat: {
    type: String,
    default: ''
  },
  imageCrop: {
    type: Object,
    default: null
    // Format: { x, y, width, height, imageWidth, imageHeight }
  }
})

defineEmits(['read-more'])

const showDetail = ref(false)
const showDesktopDetail = ref(false)

const openDetail = () => {
  showDetail.value = true
  // Prevent body scroll when modal is open
  document.body.style.overflow = 'hidden'
}

const closeDetail = () => {
  showDetail.value = false
  // Restore body scroll
  document.body.style.overflow = ''
}

const openDesktopDetail = () => {
  showDesktopDetail.value = true
}

const closeDesktopDetail = () => {
  showDesktopDetail.value = false
}

const cardClass = computed(() => {
  return props.isDark
    ? 'bg-gray-900 dark:bg-white border border-gray-800 dark:border-gray-200'
    : 'bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800'
})

const titleClass = computed(() => {
  return props.isDark ? 'text-white dark:text-gray-900' : 'text-gray-900 dark:text-white'
})

const descriptionClass = computed(() => {
  return props.isDark ? 'text-gray-300 dark:text-gray-600' : 'text-gray-600 dark:text-gray-300'
})

const gradientClass = computed(() => {
  return props.isDark ? 'from-gray-900 dark:from-white' : 'from-white dark:from-gray-900'
})

// Compute image style based on crop data
const imageStyle = computed(() => {
  if (!props.imageCrop || !props.imageCrop.width || !props.imageCrop.height) {
    return {}
  }
  
  const { x, y, width, height, imageWidth, imageHeight } = props.imageCrop
  
  // Calculate object position to center on crop area
  const cropCenterX = x + width / 2
  const cropCenterY = y + height / 2
  const posX = (cropCenterX / imageWidth) * 100
  const posY = (cropCenterY / imageHeight) * 100
  
  return {
    objectPosition: `${posX}% ${posY}%`
  }
})
</script>

<style scoped>
/* Backdrop Transition */
.backdrop-enter-active,
.backdrop-leave-active {
  transition: opacity 300ms ease-out;
}

.backdrop-enter-from,
.backdrop-leave-to {
  opacity: 0;
}

/* Slide Up Transition */
.slide-up-enter-active,
.slide-up-leave-active {
  transition: transform 300ms ease-out;
}

.slide-up-enter-from,
.slide-up-leave-to {
  transform: translateY(100%);
}

/* Slide Left Transition (Desktop Detail) */
.slide-left-enter-active,
.slide-left-leave-active {
  transition: transform 400ms cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-left-enter-from {
  transform: translateX(100%);
}

.slide-left-leave-to {
  transform: translateX(-100%);
}

/* Hide Scrollbar Completely - No Exceptions */
.overflow-y-auto {
  scrollbar-width: none; /* Firefox */
  -ms-overflow-style: none; /* IE and Edge */
}

.overflow-y-auto::-webkit-scrollbar {
  display: none; /* Chrome, Safari, Opera */
}
</style>
