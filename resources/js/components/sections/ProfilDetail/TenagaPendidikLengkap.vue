<template>
  <section
    ref="guruSection"
    class="bg-gradient-to-b from-gray-50 to-white dark:from-gray-900 dark:to-gray-800 transition-colors duration-300 overflow-hidden"
  >
    <!-- OLD: container mx-auto px-6 -->
    <div class="container-content">
      <!-- Back Button -->
      <div class="pt-8 mb-8">
        <BackButton to="/profil" text="Kembali ke Profil" variant="ghost" />
      </div>

      <!-- Section Title -->
      <div class="text-center mb-16 relative z-10">
        >
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mt-3 font-poppins">
          Pendidik Inspiratif
        </h2>
        <p class="mt-4 text-gray-600 dark:text-gray-400 max-w-2xl mx-auto text-md">
          Dedikasi kami untuk membentuk masa depan generasi penerus bangsa.
        </p>

        <!-- Search Bar -->
        <div class="mt-8 max-w-md mx-auto">
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Cari guru / pelajaran..."
              class="w-full px-5 py-3 pl-12 pr-12 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
              @input="handleSearch"
            />
            <!-- Search Icon -->
            <svg
              class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
              />
            </svg>
            <!-- Clear Button -->
            <button
              v-if="searchQuery"
              @click="clearSearch"
              class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"
                />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Modern Card Grid Layout -->
      <div class="relative max-w-7xl mx-auto">
        <!-- Decorative Background Elements -->
        <div
          class="absolute top-0 right-0 w-72 h-72 bg-blue-400/10 dark:bg-blue-600/10 rounded-full blur-3xl -z-10"
        ></div>
        <div
          class="absolute bottom-0 left-0 w-96 h-96 bg-indigo-400/10 dark:bg-indigo-600/10 rounded-full blur-3xl -z-10"
        ></div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 justify-items-center lg:justify-items-stretch">
          <!-- Loading Skeleton -->
          <div v-if="loading" v-for="n in 8" :key="'skeleton-' + n" class="group relative w-full max-w-sm lg:max-w-none">
            <div class="relative bg-gray-200 dark:bg-gray-700 rounded-2xl overflow-hidden h-96 animate-pulse">
              <div class="h-64 bg-gray-300 dark:bg-gray-600"></div>
              <div class="p-6 space-y-3">
                <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-3/4"></div>
                <div class="h-3 bg-gray-300 dark:bg-gray-600 rounded w-1/2"></div>
              </div>
            </div>
          </div>

          <!-- Error State -->
          <div v-else-if="error" class="col-span-4 text-center py-12">
            <svg class="w-16 h-16 mx-auto text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-gray-600 dark:text-gray-400">{{ error }}</p>
          </div>

          <!-- Empty State -->
          <div v-else-if="filteredTeachers.length === 0" class="col-span-4 text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <p class="text-gray-600 dark:text-gray-400">Tidak ada guru ditemukan</p>
          </div>

          <!-- Teacher Cards -->
          <div v-else v-for="(teacher, index) in filteredTeachers" :key="teacher.id" class="group relative w-full max-w-sm lg:max-w-none">
            <!-- Modern Card -->
            <div
              class="relative bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 border border-gray-100 dark:border-gray-700"
            >
              <!-- Image Container -->
              <div class="relative h-64 overflow-hidden">
                <!-- Gradient Overlay -->
                <div
                  class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent z-10 opacity-60 group-hover:opacity-40 transition-opacity duration-500"
                ></div>

                <!-- Image -->
                <img
                  :src="teacher.photo"
                  :alt="teacher.name"
                  class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                />

                <!-- Subject Badge (Floating on Image) -->
                <div class="absolute top-4 right-4 z-20">
                  <span
                    class="px-3 py-1.5 bg-blue-600/90 backdrop-blur-sm text-white text-xs font-bold uppercase tracking-wider rounded-lg shadow-lg"
                  >
                    {{ teacher.subject }}
                  </span>
                </div>

                <!-- Decorative Corner Accent -->
                <div
                  class="absolute top-0 left-0 w-20 h-20 bg-gradient-to-br from-blue-500/20 to-transparent rounded-br-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"
                ></div>
              </div>

              <!-- Card Content -->
              <div class="p-6 relative">
                <!-- Decorative Line -->
                <div
                  class="absolute top-0 left-6 right-6 h-1 bg-gradient-to-r from-transparent via-blue-500 to-transparent transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"
                ></div>

                <h3
                  class="text-lg font-bold text-gray-900 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2"
                >
                  {{ teacher.name }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium mb-4">
                  {{ teacher.subject }}
                </p>

                <!-- Social Icons -->
                <div
                  class="flex gap-3 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0"
                >
                  <a
                    href="#"
                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-blue-600 hover:text-white transition-all duration-300"
                  >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                      <path
                        d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 2.468-4.678 3.062-7.57 2.675 2.212 1.306 4.841 2.063 7.638 2.063 9.154 0 14.16-7.586 14.16-14.16 0-.216-.004-.432-.011-.645.966-.697 1.797-1.562 2.457-2.549z"
                      />
                    </svg>
                  </a>
                  <a
                    href="#"
                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-pink-600 hover:text-white transition-all duration-300"
                  >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                      <path
                        d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"
                      />
                    </svg>
                  </a>
                </div>
              </div>

              <!-- Hover Glow Effect -->
              <div
                class="absolute inset-0 rounded-2xl bg-gradient-to-t from-blue-500/0 via-blue-500/0 to-blue-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"
              ></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
<script setup>
import BackButton from '@/components/ui/BackButton.vue'
import { useHead } from '@vueuse/head'
import { onMounted, ref, computed } from 'vue'
import axios from 'axios'

// SEO Meta Tags
useHead({
  title: 'Direktori Guru - SMA Mutiara Insan Nusantara',
  meta: [
    { name: 'description', content: 'Daftar lengkap tenaga pendidik profesional SMA Mutiara Insan Nusantara. Guru-guru berpengalaman dan berdedikasi untuk membentuk generasi unggul.' },
    { property: 'og:title', content: 'Direktori Guru - SMA Mutiara Insan Nusantara' },
    { property: 'og:description', content: 'Daftar lengkap tenaga pendidik profesional SMA Mutiara Insan Nusantara.' },
    { property: 'og:type', content: 'website' }
  ]
})

// State
const teachers = ref([])
const loading = ref(true)
const error = ref(null)
const searchQuery = ref('')

// Computed
const filteredTeachers = computed(() => {
  if (!searchQuery.value) return teachers.value
  
  const query = searchQuery.value.toLowerCase()
  return teachers.value.filter(teacher => 
    teacher.name.toLowerCase().includes(query) ||
    teacher.subject.toLowerCase().includes(query)
  )
})

// API Functions
const fetchTeachers = async () => {
  try {
    loading.value = true
    error.value = null
    
    const response = await axios.get('/api/guru')
    
    if (response.data.success) {
      teachers.value = response.data.data
    } else {
      throw new Error('Failed to fetch teachers')
    }
  } catch (err) {
    console.error('Error fetching teachers:', err)
    error.value = 'Gagal memuat data guru. Silakan coba lagi.'
  } finally {
    loading.value = false
  }
}

const handleSearch = () => {
  // Search is reactive via computed property
}

const clearSearch = () => {
  searchQuery.value = ''
}

onMounted(async () => {
  await fetchTeachers()
})
</script>
<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
@keyframes spin-slow {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
.animate-spin-slow {
  animation: spin-slow 10s linear infinite;
}
</style>
