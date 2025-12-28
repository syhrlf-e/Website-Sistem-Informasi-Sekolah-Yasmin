<!--
  @component PrestasiList
  @description Halaman daftar semua prestasi dengan filter dan pagination
  @route /prestasi
  @endpoint GET /api/prestasi
  @filters search, kategori, tingkat
-->

<template>
  <div class="min-h-screen bg-white dark:bg-gray-900 transition-colors duration-300">
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-16">
      <div class="container mx-auto px-4 max-w-6xl">
        <BackButton variant="light" text="Kembali" />

        <nav class="text-sm mb-4">
          <a href="/" class="hover:underline opacity-90">Home</a>
          <span class="mx-2">›</span>
          <span>Prestasi</span>
        </nav>
        <h1 class="text-4xl md:text-5xl font-bold">Semua Prestasi</h1>
        <p class="mt-3 text-lg opacity-90">Pencapaian membanggakan siswa-siswi kami</p>
      </div>
    </div>

    <div
      class="sticky top-0 z-30 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 shadow-sm"
    >
      <div class="container mx-auto px-4 max-w-6xl py-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
          <div class="md:col-span-2">
            <input
              v-model="filters.search"
              type="text"
              placeholder="Cari prestasi..."
              class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white"
              @input="handleSearch"
            />
          </div>

          <select
            v-model="filters.kategori"
            class="px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white"
            @change="fetchPrestasi"
          >
            <option value="">Semua Kategori</option>
            <option value="Akademik">Akademik</option>
            <option value="Olahraga">Olahraga</option>
            <option value="Seni">Seni</option>
            <option value="Teknologi">Teknologi</option>
            <option value="Lainnya">Lainnya</option>
          </select>

          <select
            v-model="filters.tingkat"
            class="px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white"
            @change="fetchPrestasi"
          >
            <option value="">Semua Tingkat</option>
            <option value="Kecamatan">Kecamatan</option>
            <option value="Kabupaten">Kabupaten</option>
            <option value="Provinsi">Provinsi</option>
            <option value="Nasional">Nasional</option>
            <option value="Internasional">Internasional</option>
          </select>
        </div>

        <button
          v-if="hasActiveFilters"
          @click="resetFilters"
          class="mt-3 text-sm text-blue-600 dark:text-blue-400 hover:underline"
        >
          Reset Filter
        </button>
      </div>
    </div>

    <div v-if="loading" class="container mx-auto px-4 max-w-6xl py-12">
      <div class="flex justify-center items-center h-64">
        <LoadingSpinner size="lg" color="blue" text="Memuat prestasi..." />
      </div>
    </div>

    <div v-else class="container mx-auto px-4 max-w-6xl py-8">
      <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
        Menampilkan {{ prestasiList.length }} dari {{ pagination.total }} prestasi
      </p>

      <div v-if="prestasiList.length === 0" class="text-center py-16">
        <svg
          class="w-24 h-24 mx-auto text-gray-400 mb-4"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="1.5"
            d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"
          />
        </svg>
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
          Tidak ada prestasi ditemukan
        </h3>
        <p class="text-gray-600 dark:text-gray-400">Coba ubah filter atau kata kunci pencarian</p>
      </div>

      <div v-else class="space-y-0">
        <a
          v-for="prestasi in prestasiList"
          :key="prestasi.id"
          :href="`/prestasi/${prestasi.slug}`"
          class="flex items-center gap-4 p-4 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all duration-200 cursor-pointer group"
        >
          <img
            :src="getImageUrl(prestasi.image)"
            :alt="prestasi.nama_prestasi"
            class="w-20 h-20 md:w-24 md:h-24 object-cover rounded-lg flex-shrink-0"
          />

          <div class="flex-1 min-w-0">
            <h3
              class="text-lg font-bold text-gray-900 dark:text-white mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-1"
            >
              {{ prestasi.nama_prestasi }}
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 flex flex-wrap items-center gap-2">
              <span>{{ getWinnerName(prestasi.peserta) }}</span>
              <span class="hidden md:inline">•</span>
              <span>{{ prestasi.tahun }}</span>
              <span class="hidden md:inline">•</span>
              <span>{{ prestasi.tingkat }}</span>
            </p>
          </div>

          <div class="flex-shrink-0">
            <BaseBadge
              :label="prestasi.kategori"
              :variant="getBadgeVariant(prestasi.kategori)"
              size="sm"
            />
          </div>
        </a>
      </div>

      <div v-if="pagination.totalPages > 1" class="mt-8 flex justify-center items-center gap-2">
        <button
          @click="changePage(pagination.currentPage - 1)"
          :disabled="pagination.currentPage === 1"
          class="px-4 py-2 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          Previous
        </button>

        <span class="text-sm text-gray-600 dark:text-gray-400">
          Page {{ pagination.currentPage }} of {{ pagination.totalPages }}
        </span>

        <button
          @click="changePage(pagination.currentPage + 1)"
          :disabled="pagination.currentPage === pagination.totalPages"
          class="px-4 py-2 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          Next
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import BackButton from '@/components/ui/BackButton.vue'
import LoadingSpinner from '@/components/ui/shared/LoadingSpinner.vue'
import BaseBadge from '@/components/ui/shared/BaseBadge.vue'
import { useHead } from '@vueuse/head'
import { computed, onMounted, ref } from 'vue'

// SEO Meta Tags
useHead({
  title: 'Prestasi Siswa - SMA Mutiara Insan Nusantara',
  meta: [
    { name: 'description', content: 'Daftar prestasi membanggakan siswa-siswi SMA Mutiara Insan Nusantara. Pencapaian di bidang akademik, olahraga, seni, dan teknologi.' },
    { property: 'og:title', content: 'Prestasi Siswa - SMA Mutiara Insan Nusantara' },
    { property: 'og:description', content: 'Daftar prestasi membanggakan siswa-siswi SMA Mutiara Insan Nusantara.' },
    { property: 'og:type', content: 'website' }
  ]
})

const prestasiList = ref([])
const loading = ref(true)
const filters = ref({
  search: '',
  kategori: '',
  tingkat: ''
})
const pagination = ref({
  currentPage: 1,
  totalPages: 1,
  total: 0,
  perPage: 20
})

const hasActiveFilters = computed(() => {
  return filters.value.search || filters.value.kategori || filters.value.tingkat
})

const getImageUrl = (path) => {
  if (!path) return '/img/placeholder.jpg'
  if (path.startsWith('http')) return path

  return `/storage/prestasi/${path}`
}

const getWinnerName = (peserta) => {
  if (!peserta || peserta.length === 0) return 'Peserta'
  if (Array.isArray(peserta)) {
    return peserta.join(', ')
  }
  return peserta
}

const getBadgeVariant = (kategori) => {
  const variants = {
    Akademik: 'blue',
    Olahraga: 'green',
    Seni: 'purple',
    Teknologi: 'orange',
    Lainnya: 'gray'
  }
  return variants[kategori] || 'gray'
}

let searchTimeout = null

const handleSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    pagination.value.currentPage = 1
    fetchPrestasi()
  }, 500)
}

const resetFilters = () => {
  filters.value = {
    search: '',
    kategori: '',
    tingkat: ''
  }
  pagination.value.currentPage = 1
  fetchPrestasi()
}

const changePage = (page) => {
  if (page < 1 || page > pagination.value.totalPages) return
  pagination.value.currentPage = page
  fetchPrestasi()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const fetchPrestasi = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams({
      page: pagination.value.currentPage,
      per_page: pagination.value.perPage,
      offset: 5
    })

    if (filters.value.search) params.append('search', filters.value.search)
    if (filters.value.kategori) params.append('kategori', filters.value.kategori)
    if (filters.value.tingkat) params.append('tingkat', filters.value.tingkat)

    const response = await fetch(`/api/prestasi?${params}`)
    const data = await response.json()

    if (data.success) {
      let prestasiData = data.data
      let totalCount = data.total || data.data.length

      if (!hasActiveFilters.value && totalCount > 5) {
        if (pagination.value.currentPage === 1) {
          prestasiData = prestasiData.slice(5)
        }
        totalCount = totalCount - 5
      }

      prestasiList.value = prestasiData
      pagination.value.total = totalCount
      pagination.value.totalPages = Math.ceil(pagination.value.total / pagination.value.perPage)
    }
  } catch (error) {
    console.error('Error fetching prestasi:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchPrestasi()
})
</script>
