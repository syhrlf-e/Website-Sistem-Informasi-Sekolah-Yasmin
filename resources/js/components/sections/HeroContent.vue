<!--
  @component HeroContent
  @description Hero text content dengan blur reveal animation
-->

<template>
  <div class="hero-content min-h-screen flex items-start justify-center pt-40 md:pt-72 pb-20">
    <div class="container-content relative z-10 text-center">
      <div class="space-y-2 md:space-y-2">
        <p
          class="text-md md:text-2xl lg:text-2xl leading-tight text-white drop-shadow-lg transition-all duration-1000 ease-out"
          :class="showText ? 'opacity-100 blur-0 translate-y-0' : 'opacity-0 blur-sm translate-y-4'"
          style="font-family: 'Poppins', sans-serif; font-weight: 400"
        >
          Selamat Datang di
        </p>
        <h1
          class="text-4xl md:text-4xl lg:text-7xl leading-tight text-white drop-shadow-lg transition-all duration-1000 ease-out delay-200"
          :class="showText ? 'opacity-100 blur-0 translate-y-0' : 'opacity-0 blur-md translate-y-6'"
          style="font-family: 'Poppins', sans-serif; font-weight: 700"
        >
          SMA MUTIARA INSAN NUSANTARA
        </h1>
        <p
          class="text-sm md:text-base lg:text-2xl  mx-auto drop-shadow-lg transition-all duration-1000 ease-out delay-500"
          :class="[
            showText ? 'opacity-100 blur-0 translate-y-0' : 'opacity-0 blur-sm translate-y-4',
            themeStore.isDark ? 'text-gray-200' : 'text-white'
          ]"
        >
          Membentuk Generasi Berakhlak, Berkarakter, Berprestasi, dan Berbudaya.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useThemeStore } from '../../stores/theme'

const themeStore = useThemeStore()
const showText = ref(false)

onMounted(() => {
  // Trigger blur reveal after small delay
  setTimeout(() => {
    showText.value = true
  }, 200)
})
</script>

<style scoped>
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

h1, p {
  user-select: none;
}
</style>
