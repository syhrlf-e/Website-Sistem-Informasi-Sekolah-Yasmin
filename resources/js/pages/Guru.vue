<!--
  @component Guru
  @description Halaman daftar guru dan tenaga pendidik
  @route /guru
-->

<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <Navbar />
    
    <main class="pt-20">
      <!-- Guru List -->
      <TenagaPendidikLengkap :guru-data="guru" />
    </main>

    <Footer />
  </div>
</template>

<script setup>
import Navbar from '@/components/ui/Navbar.vue'
import Footer from '@/components/ui/Footer.vue'
import TenagaPendidikLengkap from '@/components/sections/ProfilDetail/TenagaPendidikLengkap.vue'

const props = defineProps({
  guru: { type: Array, default: () => [] },
  meta: { type: Object, default: () => ({}) },
  schema: { type: Object, default: () => ({}) },
})
</script>
