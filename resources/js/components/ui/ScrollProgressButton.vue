<!--
  @component ScrollProgressButton
  @description Floating button dengan circular progress indicator dan scroll-to-top
  @behavior Muncul setelah scroll 100px, progress ring menunjukkan posisi scroll
  @animation Arrow rotates 180° saat di bottom, click untuk scroll to top
-->

<template>
  <Transition
    enter-active-class="transition-all duration-300 ease-out"
    enter-from-class="opacity-0 scale-90 translate-y-8"
    enter-to-class="opacity-100 scale-100 translate-y-0"
    leave-active-class="transition-all duration-200 ease-in"
    leave-from-class="opacity-100 scale-100 translate-y-0"
    leave-to-class="opacity-0 scale-90 translate-y-8"
  >
    <button
      v-if="showButton"
      @click="isAtBottom ? scrollToTop() : null"
      class="fixed bottom-8 right-8 z-50 w-14 h-14 rounded-full bg-white dark:bg-gray-800 shadow-xl transition-all duration-300 group"
      :class="{
        'hover:shadow-2xl hover:scale-110 cursor-pointer': isAtBottom,
        'cursor-default': !isAtBottom
      }"
      aria-label="Scroll to top"
    >
      <svg class="absolute inset-0 w-full h-full -rotate-90" viewBox="0 0 56 56">
        <circle
          cx="28"
          cy="28"
          r="24"
          fill="none"
          stroke="currentColor"
          stroke-width="3"
          class="text-gray-200 dark:text-gray-700"
        />

        <circle
          cx="28"
          cy="28"
          r="24"
          fill="none"
          stroke="currentColor"
          stroke-width="3"
          stroke-linecap="round"
          class="text-blue-600 dark:text-blue-500 transition-all duration-300"
          :style="{
            strokeDasharray: circumference,
            strokeDashoffset: progressOffset
          }"
        />
      </svg>

      <div class="absolute inset-0 flex items-center justify-center">
        <ArrowDown
          class="w-5 h-5 text-gray-700 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-500 transition-all duration-500"
          :class="{ 'rotate-180': isAtBottom }"
        />
      </div>
    </button>
  </Transition>
</template>

<script setup>
import { ArrowDown } from 'lucide-vue-next'
import { computed, onMounted, onUnmounted, ref } from 'vue'

const scrollProgress = ref(0)
const showButton = ref(false)
const isAtBottom = ref(false)

const radius = 24
const circumference = 2 * Math.PI * radius

const progressOffset = computed(() => {
  return circumference - (scrollProgress.value / 100) * circumference
})

const handleScroll = () => {
  const windowHeight = window.innerHeight
  const documentHeight = document.documentElement.scrollHeight
  const scrollTop = window.scrollY || document.documentElement.scrollTop

  const maxScroll = documentHeight - windowHeight
  const progress = (scrollTop / maxScroll) * 100
  scrollProgress.value = Math.min(Math.max(progress, 0), 100)

  showButton.value = scrollTop > 100

  isAtBottom.value = scrollTop >= maxScroll - 50
}

const scrollToTop = () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  })
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true })
  handleScroll()
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>
