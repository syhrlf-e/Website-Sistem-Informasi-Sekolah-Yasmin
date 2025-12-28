<!--
  @component ProfilDetail
  @description Page wrapper untuk halaman profil sekolah lengkap
  @children WelcomeHero, SambutanKepsek, SejarahTimeline, TenagaPendidik
-->

<template>
  <div class="profil-detail-page">
    <WelcomeHero />
    <div class="relative z-20">
      <SambutanKepsek />
      <SejarahTimeline />
      <TenagaPendidik />
    </div>
  </div>
</template>

<script setup>
import { useHead } from '@vueuse/head'
import SambutanKepsek from './ProfilDetail/SambutanKepsek.vue'
import SejarahTimeline from './ProfilDetail/SejarahTimeline.vue'
import TenagaPendidik from './ProfilDetail/TenagaPendidik.vue'
import WelcomeHero from './ProfilDetail/WelcomeHero.vue'

// SEO Meta Tags
useHead({
  title: 'Profil Sekolah - SMA Mutiara Insan Nusantara',
  meta: [
    { name: 'description', content: 'Profil lengkap SMA Mutiara Insan Nusantara. Sejarah sekolah, visi misi, sambutan kepala sekolah, dan daftar tenaga pendidik profesional.' },
    { property: 'og:title', content: 'Profil Sekolah - SMA Mutiara Insan Nusantara' },
    { property: 'og:description', content: 'Profil lengkap SMA Mutiara Insan Nusantara. Sejarah sekolah, visi misi, dan tenaga pendidik.' },
    { property: 'og:type', content: 'website' }
  ]
})
</script>

<style scoped>
.profil-detail-page {
  min-height: 100vh;
  overflow-x: hidden;
}

.profil-detail-page {
  scroll-behavior: smooth;
}
</style>
