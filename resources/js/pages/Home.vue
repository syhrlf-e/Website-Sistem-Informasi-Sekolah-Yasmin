<!--
  @component Home
  @description Landing page utama dengan semua sections
  @route / atau /sma-yasmin
  @children HeroSection, InfoCards, SambutanSection, VisiMisiSection, StatsNumbers, PrestasiSection, Testimoni, EkstrakurikulerSection, GaleriSection, NewsSection
-->

<template>
  <div class="home-page relative">
    <SplashScreen v-if="showSplash" @complete="handleSplashComplete" />
    <div v-if="!showSplash">
      <AnnouncementPopup />
      <Transition name="fade-navbar">
        <Navbar />
      </Transition>
      <ScrollProgressButton />
      
      <!-- Hero Section (Sticky Background) -->
      <div class="sticky-hero-wrapper">
        <HeroSection />
      </div>
      
      <!-- Content that scrolls over hero -->
      <div class="content-wrapper">
        <!-- Hero Text (scrolls with content) -->
        <HeroContent />
        
        <!-- Other Sections -->
        <div class="sections-content relative bg-white dark:bg-slate-950 transition-colors duration-300">
          <!-- Curved Section Divider - Scroll-animated curve -->
          <div class="curved-divider">
            <svg
              viewBox="0 0 1440 180"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
              preserveAspectRatio="none"
              class="w-full h-[10px] md:h-[25px] lg:h-[50px] block"
            >
              <path
                ref="curvePath"
                :d="curvePathD"
                class="fill-white dark:fill-slate-950 transition-colors duration-300"
              />
            </svg>
          </div>
          
          <!-- Decorative background blobs (inside content) -->
          <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div
              class="absolute top-0 right-0 w-[400px] h-[400px] rounded-full blur-3xl bubble-float-1"
            ></div>

            <div
              class="absolute bottom-0 left-0 w-[350px] h-[350px] rounded-full blur-3xl bubble-float-2"
            ></div>

            <div
              class="absolute top-1/2 right-10 w-[250px] h-[250px] rounded-full blur-3xl bubble-float-3"
            ></div>
          </div>
          
          <!-- Sections with relative z-index -->
          <div class="relative z-10">
            <LazySambutanSection />
            <LazyVisiMisiSection />
            <StatsNumbers />
            <LazyPrestasiSection :prestasi="prestasi" />
            <LazyTestimoni :testimonials="testimonials" />
            <LazyEkstrakurikulerSection
              :ekstrakurikuler="ekstrakurikuler"
              @modal-open="handleModalOpen"
              @modal-close="handleModalClose"
            />
            <LazyGaleriSection :galleries="galleries" />
            <LazyNewsSection :featured-news="featuredNews" />
            <Footer />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useHead } from '@vueuse/head'
import { defineAsyncComponent, nextTick, onMounted, onUnmounted, ref, computed } from 'vue'
import Footer from '../components/ui/Footer.vue'
import HeroSection from '../components/sections/HeroSection.vue'
import HeroContent from '../components/sections/HeroContent.vue'
import Navbar from '../components/ui/Navbar.vue'
import ScrollProgressButton from '../components/ui/ScrollProgressButton.vue'
import SplashScreen from '../components/ui/SplashScreen.vue'
import StatsNumbers from '../components/sections/StatsNumbers.vue'
import AnnouncementPopup from '../components/AnnouncementPopup.vue'

// Receive props from Inertia (optional - for progressive migration)
const props = defineProps({
  featuredNews: { type: Array, default: () => [] },
  galleries: { type: Array, default: () => [] },
  prestasi: { type: Array, default: () => [] },
  ekstrakurikuler: { type: Array, default: () => [] },
  testimonials: { type: Array, default: () => [] },
  meta: { type: Object, default: () => ({}) },
  schema: { type: Object, default: () => ({}) },
})

// SEO Meta Tags - use Inertia props if available, fallback to defaults
// Note: Primary SEO is handled server-side in app.blade.php
// This is for client-side updates during SPA navigation
useHead({
  title: computed(() => props.meta?.title || 'SMA Mutiara Insan Nusantara - Beranda'),
  meta: [
    { name: 'description', content: computed(() => props.meta?.description || 'Selamat datang di SMA Mutiara Insan Nusantara. Sekolah menengah atas yang berkomitmen menghasilkan generasi cerdas, berkarakter, berprestasi, dan berbudaya.') },
    { property: 'og:title', content: computed(() => props.meta?.og_title || 'SMA Mutiara Insan Nusantara - Beranda') },
    { property: 'og:description', content: computed(() => props.meta?.og_description || 'Sekolah menengah atas yang berkomitmen menghasilkan generasi cerdas, berkarakter, berprestasi, dan berbudaya.') },
    { property: 'og:type', content: computed(() => props.meta?.og_type || 'website') }
  ]
})

// Splash hanya tampil sekali per session (tidak tampil saat refresh)
const splashShown = sessionStorage.getItem('splash_shown')
const showSplash = ref(!splashShown)

const handleSplashComplete = () => {
  sessionStorage.setItem('splash_shown', 'true')
  showSplash.value = false
  restoreScrollPosition()
}

const isModalOpen = ref(false)

const handleModalOpen = () => {
  isModalOpen.value = true
}

const handleModalClose = () => {
  isModalOpen.value = false
}

// ========================================
// Scroll-animated Curve Divider
// ========================================
const curvePath = ref(null)
const curveProgress = ref(0) // 0 = very curved, 1 = flat

// Generate dynamic SVG path based on scroll progress
const curvePathD = computed(() => {
  // Curve values: from VERY curved (0) to flat (180)
  // startY: 20 (very curved) -> 180 (flat)  
  // controlY: -30 (dramatic curve) -> 180 (flat)
  const progress = curveProgress.value
  const startY = 20 + (160 * progress) // 20 -> 180
  const controlY = -30 + (210 * progress) // -30 -> 180
  
  return `M0 180V${startY}C200 ${controlY} 1240 ${controlY} 1440 ${startY}V180H0Z`
})

const updateCurveOnScroll = () => {
  // Calculate progress based on scroll position
  // Curve animation happens in first 300px of scroll
  const scrollY = window.scrollY
  const maxScroll = 300
  const progress = Math.min(scrollY / maxScroll, 1)
  curveProgress.value = progress
}

// Scroll position preservation
const saveScrollPosition = () => {
  sessionStorage.setItem('home_scroll_position', window.scrollY.toString())
}

const restoreScrollPosition = () => {
  const savedPosition = sessionStorage.getItem('home_scroll_position')
  if (savedPosition) {
    nextTick(() => {
      setTimeout(() => {
        window.scrollTo(0, parseInt(savedPosition))
        sessionStorage.removeItem('home_scroll_position')
      }, 100)
    })
  }
}

onMounted(() => {
  window.addEventListener('beforeunload', saveScrollPosition)
  window.addEventListener('scroll', updateCurveOnScroll, { passive: true })
  
  // Initial curve state
  updateCurveOnScroll()
  
  // Restore scroll jika tidak ada splash (refresh case)
  if (!showSplash.value) {
    restoreScrollPosition()
  }
})

onUnmounted(() => {
  window.removeEventListener('beforeunload', saveScrollPosition)
  window.removeEventListener('scroll', updateCurveOnScroll)
})

const LazySambutanSection = defineAsyncComponent(
  () => import('../components/sections/SambutanSection.vue')
)
const LazyVisiMisiSection = defineAsyncComponent(
  () => import('../components/sections/VisiMisiSection.vue')
)
const LazyPrestasiSection = defineAsyncComponent(
  () => import('../components/sections/PrestasiSection.vue')
)
const LazyTestimoni = defineAsyncComponent(() => import('../components/sections/TestimoniSection.vue'))
const LazyEkstrakurikulerSection = defineAsyncComponent(
  () => import('../components/sections/EkstrakurikulerSection.vue')
)
const LazyGaleriSection = defineAsyncComponent(() => import('../components/sections/GaleriSection.vue'))
const LazyNewsSection = defineAsyncComponent(() => import('../components/sections/NewsSection.vue'))
</script>

<style scoped>
.home-page {
  width: 100%;
  min-height: 100vh;
}

/* Sticky Hero - stays in place while content scrolls over */
.sticky-hero-wrapper {
  position: sticky;
  top: 0;
  z-index: 10;
  height: 100vh;
}

/* Content wrapper - starts at top of viewport, overlaying the sticky hero */
.content-wrapper {
  position: relative;
  z-index: 20;
  margin-top: -100vh; /* Overlap the sticky hero */
}

/* Sections content with solid background */
.sections-content {
  position: relative;
  z-index: 1;
}

/* Curved Divider - melengkung ke atas */
.curved-divider {
  position: relative;
  margin-top: 0; /* Tidak overlap, posisi di bawah HeroContent */
  z-index: 2;
}

.bubble-float-1 {
  background: linear-gradient(to bottom right, rgba(219, 234, 254, 0.3), rgba(207, 250, 254, 0.2));
  animation: float 12s ease-in-out infinite;
}

.dark .bubble-float-1 {
  background: linear-gradient(to bottom right, rgba(30, 58, 138, 0.15), rgba(22, 78, 99, 0.08));
}

.bubble-float-2 {
  background: linear-gradient(to top right, rgba(250, 245, 255, 0.25), rgba(252, 231, 243, 0.15));
  animation: float-reverse 15s ease-in-out infinite;
}

.dark .bubble-float-2 {
  background: linear-gradient(to top right, rgba(88, 28, 135, 0.12), rgba(131, 24, 67, 0.06));
}

.bubble-float-3 {
  background: linear-gradient(to left, rgba(238, 242, 255, 0.2), transparent);
  animation: float-slow 20s ease-in-out infinite;
}

.dark .bubble-float-3 {
  background: linear-gradient(to left, rgba(49, 46, 129, 0.08), transparent);
}

@keyframes float {
  0%,
  100% {
    transform: translate(0, 0) scale(1);
  }
  33% {
    transform: translate(30px, -30px) scale(1.05);
  }
  66% {
    transform: translate(-20px, 20px) scale(0.95);
  }
}

@keyframes float-reverse {
  0%,
  100% {
    transform: translate(0, 0) scale(1);
  }
  33% {
    transform: translate(-30px, 30px) scale(1.05);
  }
  66% {
    transform: translate(20px, -20px) scale(0.95);
  }
}

@keyframes float-slow {
  0%,
  100% {
    transform: translate(0, 0) scale(1);
  }
  50% {
    transform: translate(15px, 15px) scale(1.03);
  }
}

.fade-navbar-enter-active,
.fade-navbar-leave-active {
  transition: opacity 0.3s ease;
}

.fade-navbar-enter-from,
.fade-navbar-leave-to {
  opacity: 0;
}
</style>
