<template>
  <section
    ref="heroSection"
    class="sticky top-0 min-h-screen flex items-center justify-center bg-white dark:bg-gray-900 overflow-hidden z-10"
  >
    <div class="relative z-10 text-center px-4">
      <div class="min-h-[120px] flex flex-col items-center justify-center gap-2 md:gap-3">
        <!-- Eyebrow text: Selamat Datang di -->
        <p
          class="text-sm md:text-base text-gray-500 dark:text-gray-400 font-medium tracking-wider uppercase font-poppins transition-all duration-1000 ease-out"
          :class="showText ? 'opacity-100 blur-0 translate-y-0' : 'opacity-0 blur-sm translate-y-4'"
        >
          Selamat Datang di
        </p>
        <!-- Main heading: Profil Kami -->
        <h1
          class="text-4xl md:text-6xl font-bold text-gray-900 dark:text-white font-poppins transition-all duration-1000 ease-out delay-300"
          :class="showText ? 'opacity-100 blur-0 translate-y-0' : 'opacity-0 blur-md translate-y-6'"
        >
          Profil Kami
        </h1>
      </div>
    </div>
    <transition
      enter-active-class="transition-all duration-1000 ease-out"
      enter-from-class="opacity-0 translate-y-10"
      enter-to-class="opacity-100 translate-y-0"
    >
      <div
        v-if="showArrow"
        class="absolute bottom-10 left-1/2 -translate-x-1/2 animate-smooth-bounce cursor-pointer"
        @click="scrollToNext"
      >
        <div class="flex flex-col items-center gap-2">
          <span
            class="text-sm text-gray-500 dark:text-gray-400 font-medium tracking-widest uppercase"
            >Scroll</span
          >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="32"
            height="32"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            class="text-gray-900 dark:text-white"
          >
            <path d="M12 5v14" />
            <path d="m19 12-7 7-7-7" />
          </svg>
        </div>
      </div>
    </transition>
  </section>
</template>

<script setup>
import { onMounted, ref } from 'vue'

const heroSection = ref(null)
const showText = ref(false)
const showArrow = ref(false)

const scrollToNext = () => {
  window.scrollTo({
    top: window.innerHeight,
    behavior: 'smooth'
  })
}

onMounted(() => {
  // Trigger blur reveal after small delay
  setTimeout(() => {
    showText.value = true
  }, 200)
  
  // Show arrow after text animation
  setTimeout(() => {
    showArrow.value = true
  }, 1500)
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
.blur-sm {
  filter: blur(4px);
}
.blur-md {
  filter: blur(8px);
}

@keyframes smooth-bounce {
  0%,
  100% {
    transform: translateY(0) translateX(-50%);
  }
  50% {
    transform: translateY(15px) translateX(-50%);
  }
}
.animate-smooth-bounce {
  animation: smooth-bounce 2s infinite ease-in-out;
}

h1, p {
  user-select: none;
}
</style>
