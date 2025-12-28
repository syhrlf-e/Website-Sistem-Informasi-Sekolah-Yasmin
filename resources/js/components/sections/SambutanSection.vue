<!--
  @component SambutanSection
  @description Section dengan tagline "Membangun Generasi Unggul Sejak Tahun 2018" dengan blur reveal animation
-->

<template>
  <section ref="sectionRef" class="py-16 md:pt-24 md:pb-8 lg:py-24 transition-colors duration-300">
    <!-- Mobile only: Centered layout, year as hero -->
    <div class="md:hidden px-4 text-center">
      <!-- Gambar Akreditasi -->
      <div class="flex justify-center mb-6">
        <img
          src="/images/akreditasi.png"
          alt="Akreditasi A"
          class="w-36 sm:w-40 h-auto object-contain"
        />
      </div>
      <!-- Sejak Tahun 2018 - Hero text -->
      <h2
        class="text-4xl sm:text-5xl font-bold text-gray-900 dark:text-white font-poppins leading-tight mb-1 "
        :class="isVisible ? 'opacity-100 blur-0 translate-y-0' : 'opacity-0 blur-md translate-y-6'"
      >
        Sejak Tahun
        <span class="year-gradient dark:year-glow">2018</span>
      </h2>
      <!-- Membangun Generasi Unggul - Tagline -->
      <p
        class="text-xl sm:text-base font-medium text-gray-800 dark:text-gray-400 font-poppins tracking-wide "
        :class="isVisible ? 'opacity-100 blur-0 translate-y-0' : 'opacity-0 blur-md translate-y-6'"
      >
        Membangun Generasi Unggul
      </p>
    </div>

    <!-- Tablet & Desktop: Layout dengan styling lengkap -->
    <div class="hidden md:block container-content">
      <div class="mx-auto text-center">
        <!-- Gambar Akreditasi -->
        <div class="flex justify-center mb-8">
          <img
            src="/images/akreditasi.png"
            alt="Akreditasi A"
            class="w-44 lg:w-52 h-auto object-contain"
          />
        </div>
        <!-- Line 1: Membangun Generasi Unggul -->
        <h2
          class="text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white font-poppins leading-tight mb-4 transition-all duration-1000 ease-out"
          :class="isVisible ? 'opacity-100 blur-0 translate-y-0' : 'opacity-0 blur-md translate-y-6'"
        >
          Membangun Generasi Unggul
        </h2>

        <!-- Line 2: Sejak Tahun 2018 -->
        <h2
          class="text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white font-poppins transition-all duration-1000 ease-out delay-300"
          :class="isVisible ? 'opacity-100 blur-0 translate-y-0' : 'opacity-0 blur-md translate-y-6'"
        >
          Sejak Tahun
          <span class="relative inline-block whitespace-nowrap year-gradient dark:year-glow">
            2018
            <svg
              class="absolute -bottom-2 left-0 w-full h-4 text-purple-600/30 dark:text-purple-400/30"
              viewBox="0 0 200 9"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M2.00025 6.99997C25.7501 2.49994 132.5 -3.50004 198 4.99997"
                stroke="currentColor"
                stroke-width="3"
                stroke-linecap="round"
              />
            </svg>
          </span>
        </h2>
      </div>
    </div>
  </section>
</template>

<script setup>
import { onMounted, onUnmounted, ref } from 'vue'

const sectionRef = ref(null)
const isVisible = ref(false)

// Intersection Observer for scroll-triggered animation
let observer = null

onMounted(() => {
  observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          isVisible.value = true
        }
      })
    },
    { threshold: 0.3 }
  )
  
  if (sectionRef.value) {
    observer.observe(sectionRef.value)
  }
})

onUnmounted(() => {
  if (observer) {
    observer.disconnect()
  }
})
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}

/* Blur utilities for animation */
.blur-0 {
  filter: blur(0);
}
.blur-md {
  filter: blur(8px);
}

/* Gradient untuk tahun 2018 - konsisten dengan StatsNumbers dan button admin */
.year-gradient {
  background: linear-gradient(135deg, #2563eb 0%, #9333ea 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.dark .year-glow {
  background: linear-gradient(135deg, #3b82f6 0%, #a855f7 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  filter: drop-shadow(0 0 15px rgba(139, 92, 246, 0.4));
}
</style>
