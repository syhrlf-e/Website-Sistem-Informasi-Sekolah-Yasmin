<!--
  @component Prestasi/Index
  @description Halaman daftar prestasi dengan pagination
  @route /prestasi
-->

<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <Navbar />
    
    <main class="pt-20">
      <!-- Hero Section -->
      <div class="relative py-20 bg-gradient-to-br from-amber-500 to-orange-600 text-white">
        <div class="container-content text-center">
          <h1 class="text-4xl md:text-5xl font-bold mb-4">Prestasi Siswa</h1>
          <p class="text-lg text-amber-100 max-w-2xl mx-auto">
            Pencapaian membanggakan siswa-siswi SMA Mutiara Insan Nusantara
          </p>
        </div>
      </div>

      <!-- Prestasi List -->
      <div class="container-content py-12">
        <div v-if="prestasi && prestasi.data && prestasi.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <PrestasiCard
            v-for="item in prestasi.data"
            :key="item.id"
            :prestasi="item"
          />
        </div>

        <div v-else class="text-center py-20">
          <p class="text-gray-600 dark:text-gray-400">Belum ada data prestasi</p>
        </div>

        <!-- Pagination -->
        <div v-if="prestasi && prestasi.last_page > 1" class="mt-12 flex justify-center">
          <nav class="flex items-center gap-2">
            <InertiaLink
              v-for="page in prestasi.last_page"
              :key="page"
              :href="`/prestasi?page=${page}`"
              :class="[
                'px-4 py-2 rounded-lg font-medium transition-colors',
                prestasi.current_page === page
                  ? 'bg-amber-600 text-white'
                  : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
              ]"
            >
              {{ page }}
            </InertiaLink>
          </nav>
        </div>
      </div>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import Navbar from '@/components/ui/Navbar.vue'
import Footer from '@/components/ui/Footer.vue'
import PrestasiCard from '@/components/sections/prestasiSection/PrestasiCard.vue'

const props = defineProps({
  prestasi: { type: Object, default: () => ({ data: [] }) },
  meta: { type: Object, default: () => ({}) },
  schema: { type: Object, default: () => ({}) },
})
</script>
