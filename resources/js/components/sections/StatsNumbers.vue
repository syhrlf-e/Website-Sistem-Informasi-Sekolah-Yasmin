<!--
  @component StatsNumbers
  @description Animated counter section: Siswa Aktif, Tenaga Pendidik, Akreditasi
  @behavior IntersectionObserver triggers count-up animation on scroll
  @animation easeOutQuart easing untuk smooth counter effect
-->

<template>
  <section class="py-[100px] md:py-12 lg:py-[100px] transition-colors duration-300">
    <div class="w-[80%] mx-auto">
      <div class="border-y border-10px border-gray-300 dark:border-gray-800">
        <div
          ref="statsSection"
          class="flex flex-col md:flex-row justify-center items-center text-center divide-y md:divide-y-0 md:divide-x divide-gray-300 dark:divide-gray-800 py-10"
        >
          <div class="flex-1 px-4 py-8 md:py-0 group">
            <h3
              class="text-5xl md:text-5xl lg:text-6xl font-bold mb-4 font-poppins tracking-tight transition-all duration-500 stats-gradient dark:stats-glow"
              :class="{
                'opacity-0 blur-md translate-y-4': !isVisible,
                'opacity-100 blur-0 translate-y-0': isVisible
              }"
            >
              {{ animatedSiswa }}+
            </h3>
            <p
              class="text-base md:text-base lg:text-xl font-bold text-gray-900 dark:text-white mb-2 font-poppins transition-all duration-500 delay-100"
              :class="{
                'opacity-0 blur-md translate-y-4': !isVisible,
                'opacity-100 blur-0 translate-y-0': isVisible
              }"
            >
              Siswa Aktif
            </p>
            <p
              class="text-sm text-gray-500 dark:text-gray-400 max-w-xs mx-auto leading-relaxed transition-all duration-500 delay-200"
              :class="{
                'opacity-0 blur-md translate-y-4': !isVisible,
                'opacity-100 blur-0 translate-y-0': isVisible
              }"
            >
              Generasi penerus yang siap bersaing dan berkarakter mulia.
            </p>
          </div>

          <div class="flex-1 px-4 py-8 md:py-0 group">
            <h3
              class="text-5xl md:text-5xl lg:text-6xl font-bold mb-4 font-poppins tracking-tight transition-all duration-500 delay-300 stats-gradient dark:stats-glow"
              :class="{
                'opacity-0 blur-md translate-y-4': !isVisible,
                'opacity-100 blur-0 translate-y-0': isVisible
              }"
            >
              {{ animatedGuru }}+
            </h3>
            <p
              class="text-base md:text-base lg:text-xl font-bold text-gray-900 dark:text-white mb-2 font-poppins transition-all duration-500 delay-400"
              :class="{
                'opacity-0 blur-md translate-y-4': !isVisible,
                'opacity-100 blur-0 translate-y-0': isVisible
              }"
            >
              Tenaga Pendidik
            </p>
            <p
              class="text-sm text-gray-500 dark:text-gray-400 max-w-xs mx-auto leading-relaxed transition-all duration-500 delay-500"
              :class="{
                'opacity-0 blur-md translate-y-4': !isVisible,
                'opacity-100 blur-0 translate-y-0': isVisible
              }"
            >
              Guru profesional, tersertifikasi, dan berdedikasi tinggi.
            </p>
          </div>

          <div class="flex-1 px-4 py-8 md:py-0 group">
            <h3
              class="text-5xl md:text-5xl lg:text-6xl font-bold mb-4 font-poppins tracking-tight transition-all duration-500 delay-[600ms] stats-gradient dark:stats-glow"
              :class="{
                'opacity-0 blur-md translate-y-4': !isVisible,
                'opacity-100 blur-0 translate-y-0': isVisible
              }"
            >
              {{ animatedAkreditasi }}%
            </h3>
            <p
              class="text-base md:text-base lg:text-xl font-bold text-gray-900 dark:text-white mb-2 font-poppins transition-all duration-500 delay-700"
              :class="{
                'opacity-0 blur-md translate-y-4': !isVisible,
                'opacity-100 blur-0 translate-y-0': isVisible
              }"
            >
              Prestasi & Akreditasi
            </p>
            <p
              class="text-sm text-gray-500 dark:text-gray-400 max-w-xs mx-auto leading-relaxed transition-all duration-500 delay-[800ms]"
              :class="{
                'opacity-0 blur-md translate-y-4': !isVisible,
                'opacity-100 blur-0 translate-y-0': isVisible
              }"
            >
              Sekolah terakreditasi A dengan ratusan penghargaan.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { onMounted, onUnmounted, ref } from 'vue'

const targetSiswa = 4000
const targetGuru = 85
const targetAkreditasi = 100

const animatedSiswa = ref(0)
const animatedGuru = ref(0)
const animatedAkreditasi = ref(0)

const isVisible = ref(false)
const statsSection = ref(null)
const hasAnimated = ref(false)

const easeOutQuart = (t) => {
  return 1 - Math.pow(1 - t, 4)
}

const animateCounter = (target, current, duration, callback) => {
  const startTime = Date.now()
  const startValue = current.value

  const animate = () => {
    const currentTime = Date.now()
    const elapsed = currentTime - startTime
    const progress = Math.min(elapsed / duration, 1)

    const easedProgress = easeOutQuart(progress)
    current.value = Math.floor(startValue + (target - startValue) * easedProgress)

    if (progress < 1) {
      requestAnimationFrame(animate)
    } else {
      current.value = target
      if (callback) callback()
    }
  }

  animate()
}

const startAnimations = () => {
  if (hasAnimated.value) return

  isVisible.value = true
  hasAnimated.value = true

  setTimeout(() => {
    animateCounter(targetSiswa, animatedSiswa, 2000)
    animateCounter(targetGuru, animatedGuru, 2000)
    animateCounter(targetAkreditasi, animatedAkreditasi, 2000)
  }, 300)
}

const observerCallback = (entries) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting && !hasAnimated.value) {
      startAnimations()
    }
  })
}

let observer = null

onMounted(() => {
  observer = new IntersectionObserver(observerCallback, {
    threshold: 0.3,
    rootMargin: '0px'
  })

  if (statsSection.value) {
    observer.observe(statsSection.value)
  }
})

onUnmounted(() => {
  if (observer && statsSection.value) {
    observer.unobserve(statsSection.value)
  }
})
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}

h3 {
  transform-origin: center;
}

.stats-gradient {
  background: linear-gradient(135deg, #2563eb 0%, #9333ea 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.dark .stats-glow {
  background: linear-gradient(135deg, #3b82f6 0%, #a855f7 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  filter: drop-shadow(0 0 20px rgba(139, 92, 246, 0.5))
    drop-shadow(0 0 400px rgba(139, 92, 246, 0.3));
}

/* Blur utilities for animation */
.blur-0 {
  filter: blur(0);
}
.blur-md {
  filter: blur(8px);
}
</style>
