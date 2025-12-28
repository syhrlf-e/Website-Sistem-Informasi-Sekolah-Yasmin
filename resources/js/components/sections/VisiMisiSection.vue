<!--
  @component VisiMisiSection
  @description Section Visi, Misi, dan Komitmen sekolah dengan blur reveal animation
  @layout Mobile: stacked, Desktop: 2-column with misi spanning 2 rows
-->

<template>
  <section ref="sectionRef" class="py-20 md:pt-20 md:pb-8 lg:py-20 transition-colors duration-300">
    <div class="container mx-auto px-4">
      <div class="max-w-6xl mx-auto">
        <div class="grid md:grid-cols-2">
          <!-- Visi -->
          <div
            class="bg-white dark:bg-gray-800/0 p-6 md:p-8 border-b border-gray-200 dark:border-gray-700 shadow-sm h-full order-1 md:order-1 transition-all duration-700 ease-out"
            :class="isVisible ? 'opacity-100 blur-0 translate-y-0' : 'opacity-0 blur-md translate-y-6'"
          >
            <h3 class="text-xl md:text-2xl font-semibold text-gray-900 dark:text-white mb-4 font-poppins">
              Visi Kami
            </h3>
            <p class="text-sm md:text-base text-gray-700 dark:text-gray-300 leading-relaxed font-poppins font-normal">
              Terciptanya SMA Mutiara Insan Nusantara Yang Unggul Dalam IPTEK, Berakhlahqul Karimah,
              Berkarakter dan Berbudaya Serta Siap Bersaing diera Global
            </p>
          </div>

          <!-- Misi -->
          <div
            class="bg-white dark:bg-gray-800/0 p-6 md:p-8 border-b md:border-b-0 md:border-l md:border-t-0 border-gray-200 dark:border-gray-700 shadow-sm h-full order-2 md:order-2 md:row-span-2 transition-all duration-700 ease-out delay-200"
            :class="isVisible ? 'opacity-100 blur-0 translate-y-0' : 'opacity-0 blur-md translate-y-6'"
          >
            <h3 class="text-xl md:text-2xl font-semibold text-gray-900 dark:text-white mb-6 font-poppins">
              Misi Kami
            </h3>
            <ul class="space-y-4">
              <li
                v-for="(item, index) in misiItems"
                :key="index"
                class="flex gap-3 text-sm md:text-base text-gray-700 dark:text-gray-300"
              >
                <span class="mt-[0.4rem] w-1.5 h-1.5 bg-gray-400 rounded-full flex-shrink-0"></span>
                <span class="leading-relaxed font-poppins font-normal">{{ item }}</span>
              </li>
            </ul>
          </div>

          <!-- Komitmen -->
          <div
            class="bg-white dark:bg-gray-800/0 p-6 md:p-8 border-b md:border-b-0 border-gray-200 dark:border-gray-700 shadow-sm h-full min-h-[200px] order-3 md:order-3 transition-all duration-700 ease-out delay-[400ms]"
            :class="isVisible ? 'opacity-100 blur-0 translate-y-0' : 'opacity-0 blur-md translate-y-6'"
          >
            <h3 class="text-xl md:text-2xl font-semibold text-gray-900 dark:text-white mb-4 font-poppins">
              Komitmen Kami
            </h3>
            <p class="text-sm md:text-base text-gray-700 dark:text-gray-300 leading-relaxed font-poppins font-normal">
              Menghadirkan pendidikan berkualitas dengan pendekatan holistik. Membangun karakter
              unggul, mengasah potensi maksimal, dan mempersiapkan generasi yang siap menghadapi
              tantangan masa depan.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { onMounted, onUnmounted, ref } from 'vue'

const misiItems = [
  'Menciptakan Lingkungan Pembelajaran yang Kondusif Dalam Upaya Meningkatkan Mutu Pembelajaran.',
  'Menciptakan Siswa Yang Agamis.',
  'Menciptakan Siswa Yang Nasionalis, Demokratis, dan Kritis.',
  'Mewujudkan Hasil Pendidikan Yang Cerdas Dalam Ilmu Pengetahuan, Beretika, Berjiwa Seni dan Pandai Dalam Berkomunikasi.'
]

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
    { threshold: 0.2 }
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
</style>
