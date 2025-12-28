<!--
  @component HeroSection
  @description Hero banner section dengan background image responsive (light/dark mode)
  @section id="hero" - Target scroll untuk navigasi
  @store useThemeStore - Untuk menentukan light/dark background image
-->

<template>
  <!-- Background Only (tetap di sticky wrapper) -->
  <section
    id="hero"
    class="relative h-full overflow-hidden"
  >
    <div class="absolute inset-0 z-0">
      <div
        class="absolute inset-0 transition-colors duration-300"
        :class="themeStore.isDark ? 'bg-gray-900' : 'bg-gray-100'"
      ></div>
      <picture>
        <source srcset="/images/hero/hero-light.webp" type="image/webp" />
        <img
          src="/images/hero/hero-light.png"
          alt="SMAS Mutiara Building"
          width="1920"
          height="1080"
          class="absolute inset-0 w-full h-full object-cover transition-opacity duration-700"
          :class="themeStore.isDark ? 'opacity-0' : 'opacity-100'"
          fetchpriority="high"
          loading="eager"
          decoding="async"
        />
      </picture>
      <picture>
        <source srcset="/images/hero/hero-dark.webp" type="image/webp" />
        <img
          src="/images/hero/hero-dark.png"
          alt="SMAS Mutiara Building Dark"
          width="1920"
          height="1080"
          class="absolute inset-0 w-full h-full object-cover transition-opacity duration-700"
          :class="themeStore.isDark ? 'opacity-100' : 'opacity-0'"
          loading="lazy"
        />
      </picture>
      <div
        class="absolute inset-0 bg-gradient-to-b from-black/50 via-black/30 to-black/60 transition-opacity duration-700"
        :class="themeStore.isDark ? 'opacity-0' : 'opacity-100'"
      ></div>
    </div>
  </section>
</template>
<script setup>
import { useThemeStore } from '../../stores/theme'

const themeStore = useThemeStore()
</script>

