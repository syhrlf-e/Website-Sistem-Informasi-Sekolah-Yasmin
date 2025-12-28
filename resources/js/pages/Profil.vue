<!--
  @component Profil
  @description Halaman profil sekolah dengan informasi visi misi dan guru
  @route /profil
-->

<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <Navbar />
    
    <main>
      <!-- Content sections will be loaded here -->
      <ProfilDetail />
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import Navbar from '@/components/ui/Navbar.vue'
import Footer from '@/components/ui/Footer.vue'
import ProfilDetail from '@/components/sections/ProfilDetail.vue'

// Receive props from Inertia
const props = defineProps({
  guru: { type: Array, default: () => [] },
  meta: { type: Object, default: () => ({}) },
  schema: { type: Object, default: () => ({}) },
})
</script>
