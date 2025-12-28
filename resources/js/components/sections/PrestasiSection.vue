<!--
  @component PrestasiSection
  @description Landing page section dengan sticky stacking cards untuk prestasi
  @section id="prestasi" - Target scroll untuk navigasi
  @endpoint GET /api/prestasi - Fetch max 5 items + CTA card jika lebih
  @behavior Scroll-triggered scale animation, lazy load saat section visible
-->

<template>
  <section ref="elementRef" id="prestasi" class="pt-[50px] transition-colors duration-300">
    <!-- OLD: container mx-auto px-4 -->
    <div class="container-content relative w-full pb-32">
      <!-- Header Sticky -->
      <div class="sticky top-[80px] z-10 py-6 mb-6 pointer-events-none">
        <div class="text-center">
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white tracking-tight font-poppins">
            Prestasi Kami
          </h2>
        </div>
      </div>

      <div v-if="loading" class="h-96 flex items-center justify-center">
        <LoadingSpinner size="lg" color="blue" text="Memuat prestasi..." />
      </div>

      <div v-else class="flex flex-col items-center w-full max-w-5xl mx-auto relative z-20">
        <div
          v-for="(prestasi, index) in prestasiList"
          :key="prestasi.id"
          ref="cardRefs"
          class="sticky w-full origin-top transition-transform duration-300 ease-out"
          :style="{
            top: `calc(140px + ${index * 10}px)`,
            zIndex: 20 + index,
            marginBottom: `calc(4rem + ${(prestasiList.length - 1 - index) * 10}px)`,
            transform: `scale(${cardScales[index] || 1})`,
            filter: `brightness(${cardBrightness[index] || 1})`
          }"
        >
          <div
            class="bg-white dark:bg-gray-800 rounded-3xl overflow-hidden shadow-2xl border border-gray-100 dark:border-gray-700"
          >
            <PrestasiCard
              :badge="prestasi.category"
              :badge-variant="getBadgeVariant(prestasi.category)"
              :title="prestasi.title"
              :description="prestasi.description"
              :image="prestasi.image"
              :image-alt="prestasi.title"
              :is-dark="false"
              :winner-name="getWinnerName(prestasi.participants)"
              :year="prestasi.year?.toString()"
              :full-description="prestasi.description"
              :tingkat="prestasi.level"
              @read-more="goToDetail(prestasi.slug)"
            />
          </div>
        </div>

        <div
          v-if="totalPrestasi > 5"
          class="sticky w-full origin-top transition-transform duration-300 ease-out"
          :style="{
            top: `calc(140px + ${prestasiList.length * 10}px)`,
            zIndex: 20 + prestasiList.length,
            marginBottom: '4rem'
          }"
        >
          <div
            class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-3xl overflow-hidden shadow-2xl h-[580px] md:h-[380px] lg:h-[556px] flex items-center justify-center p-8"
          >
            <div class="text-center text-white space-y-6 max-w-md">
              <div class="flex justify-center">
                <svg
                  class="w-20 h-20 md:w-24 md:h-24"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.5"
                    d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"
                  />
                </svg>
              </div>

              <div class="space-y-3">
                <h3 class="text-2xl md:text-3xl font-bold leading-tight">
                  Prestasi Kami Bukan Hanya Itu!
                </h3>
                <p class="text-base md:text-lg text-white/90 leading-relaxed">
                  Lihat prestasi kami lainnya dengan klik tombol di bawah
                </p>
              </div>

              <a
                href="/prestasi"
                class="inline-flex items-center gap-2 px-8 py-4 bg-white text-blue-600 font-semibold rounded-full shadow-xl hover:shadow-2xl transition-all duration-300"
              >
                <span>Lihat Semua Prestasi</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M17 8l4 4m0 0l-4 4m4-4H3"
                  />
                </svg>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import LoadingSpinner from '@/components/ui/shared/LoadingSpinner.vue'
import PrestasiCard from '@/components/sections/prestasiSection/PrestasiCard.vue'
import { useIntersectionObserver } from '@/composables/useIntersectionObserver'
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue'

// Accept prestasi prop from parent (Home.vue)
const props = defineProps({
  prestasi: {
    type: Array,
    default: () => []
  }
})

const cardRefs = ref([])
const prestasiList = computed(() => props.prestasi.slice(0, 5))
const cardScales = ref(new Array(5).fill(1))
const cardBrightness = ref(new Array(5).fill(1))
const loading = false
const totalPrestasi = computed(() => props.prestasi.length)
const allStacked = ref(false)

// Initialize scroll handling when data is ready
watch(() => props.prestasi, (newVal) => {
  if (newVal && newVal.length > 0) {
    nextTick(() => {
      window.addEventListener('scroll', handleScroll, { passive: true })
      handleScroll()
    })
  }
}, { immediate: true })

const HEADER_OFFSET = 140
const STACK_GAP = 10

const getBadgeVariant = (kategori) => {
  const variants = {
    Akademik: 'blue',
    Olahraga: 'green',
    Seni: 'purple',
    Teknologi: 'orange',
    Lainnya: 'gray'
  }
  return variants[kategori] || 'gray'
}

const getImageUrl = (path) => {
  if (!path) return '/img/placeholder.jpg'
  if (path.startsWith('http')) return path
  if (path.startsWith('/storage')) return path
  // If path already includes folder (e.g., 'prestasi/xxx.webp')
  if (path.includes('/')) {
    return `/storage/${path}`
  }
  // Legacy format: just filename (old data)
  return `/storage/prestasi/${path}`
}

const getWinnerName = (peserta) => {
  if (!peserta || peserta.length === 0) return 'Peserta'

  // Jika peserta adalah array, join dengan koma
  if (Array.isArray(peserta)) {
    return peserta.join(', ')
  }

  return peserta
}

const goToDetail = (slug) => {
  window.open(`/prestasi/${slug}`, '_blank')
}

const handleScroll = () => {
  if (!cardRefs.value.length) return

  const lastCard = cardRefs.value[cardRefs.value.length - 1]
  if (lastCard) {
    const rect = lastCard.getBoundingClientRect()
    const targetPos = HEADER_OFFSET + (cardRefs.value.length - 1) * STACK_GAP

    allStacked.value = rect.top <= targetPos + 20
  }

  cardRefs.value.forEach((card, index) => {
    const rect = card.getBoundingClientRect()
    const targetTop = HEADER_OFFSET + index * STACK_GAP
    const dist = rect.top - targetTop

    let scale = 1
    let brightness = 1

    if (dist <= 0.5) {
      const nextCard = cardRefs.value[index + 1]
      if (nextCard) {
        const nextRect = nextCard.getBoundingClientRect()
        const nextTarget = HEADER_OFFSET + (index + 1) * STACK_GAP
        const distNext = nextRect.top - nextTarget

        if (distNext < 600 && distNext > 0) {
          const progress = 1 - distNext / 600
          scale = 1 - progress * 0.03
        } else if (distNext <= 0) {
          scale = 0.97
        }
      }
    }

    cardScales.value[index] = scale
    cardBrightness.value[index] = brightness
  })
}

const elementRef = ref(null)

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style scoped>
.header-shadow {
  box-shadow: 0 0 60px 40px rgba(255, 255, 255, 0.8);
}

.dark .header-shadow {
  box-shadow: 0 0 60px 40px rgba(17, 24, 39, 0.8);
}
</style>
