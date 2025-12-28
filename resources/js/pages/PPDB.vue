<!--
  @component PPDB
  @description Halaman informasi PPDB dengan daftar dokumen yang bisa didownload
  @route /ppdb
  @endpoint GET /api/documents
  @behavior Auto-refresh setiap 30 detik
-->

<template>
  <div
    class="min-h-screen bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-12 px-4"
  >
    <div class="max-w-6xl mx-auto">
      <BackButton to="/" text="Kembali ke Beranda" variant="ghost" />

      <div class="text-center mb-12">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4 font-poppins">
          Informasi PPDB
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-300 font-poppins mb-6">
          Penerimaan Peserta Didik Baru - Dokumen & Informasi
        </p>
        
        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
          <a
            href="/ppdb/daftar"
            class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl font-semibold transition-all shadow-lg hover:shadow-xl font-poppins"
          >
            <UserPlus class="w-5 h-5" />
            Daftar Sekarang
          </a>
          <a
            href="/ppdb/status"
            class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-blue-400 rounded-xl font-semibold transition-all font-poppins"
          >
            <Search class="w-5 h-5" />
            Cek Status
          </a>
        </div>
      </div>

      <div v-if="loading" class="py-20">
        <LoadingSpinner size="lg" color="blue" text="Memuat dokumen..." />
      </div>

      <div
        v-else-if="documents.length > 0"
        class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden"
      >
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
          <div
            v-for="doc in documents"
            :key="doc.id"
            class="flex items-center gap-4 p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
          >
            <div
              class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0"
              :class="getFileIconBg(doc.file_type)"
            >
              <FileText class="w-6 h-6" :class="getFileIconColor(doc.file_type)" />
            </div>

            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 mb-1">
                <h3
                  class="text-base font-semibold text-gray-900 dark:text-white font-poppins truncate"
                >
                  {{ doc.title }}
                </h3>
                <span class="px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 flex-shrink-0">
                  {{ doc.file_type.toUpperCase() }}
                </span>
              </div>
              <p
                v-if="doc.description"
                class="text-sm text-gray-600 dark:text-gray-400 mb-1 line-clamp-1"
              >
                {{ doc.description }}
              </p>
              <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                <span>{{ formatFileSize(doc.file_size) }}</span>
                <span>•</span>
                <span>{{ formatDate(doc.created_at) }}</span>
              </div>
            </div>

            <button
              @click="downloadDocument(doc.id, doc.file_name)"
              class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-lg font-medium transition-colors font-poppins flex-shrink-0"
            >
              <Download class="w-4 h-4" />
              <span class="hidden sm:inline">Download</span>
            </button>
          </div>
        </div>
      </div>

      <div v-else class="text-center py-20">
        <FileText class="w-16 h-16 mx-auto mb-4 text-gray-400" />
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 font-poppins">
          Belum Ada Dokumen
        </h3>
        <p class="text-gray-600 dark:text-gray-400 font-poppins">
          Dokumen PPDB akan tersedia segera
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import BackButton from '@/components/ui/BackButton.vue'
import LoadingSpinner from '@/components/ui/shared/LoadingSpinner.vue'
import { useHead } from '@vueuse/head'
import { Download, FileText, Search, UserPlus } from 'lucide-vue-next'
import { onBeforeUnmount, onMounted, ref, computed } from 'vue'

// Props from Inertia
const props = defineProps({
  documents: { type: Array, default: () => [] },
  meta: { type: Object, default: () => ({}) },
  schema: { type: Object, default: () => ({}) },
})

// Use Inertia props if available, otherwise fetch from API
const localDocuments = ref([])
const documents = computed(() => props.documents.length > 0 ? props.documents : localDocuments.value)
const loading = ref(false)
let refreshInterval = null

// SEO Meta Tags - use props if available
useHead({
  title: computed(() => props.meta?.title || 'Informasi PPDB - SMA Mutiara Insan Nusantara'),
  meta: [
    { name: 'description', content: computed(() => props.meta?.description || 'Informasi Penerimaan Peserta Didik Baru (PPDB) SMA Mutiara Insan Nusantara.') },
    { property: 'og:title', content: computed(() => props.meta?.og_title || 'Informasi PPDB - SMA Mutiara Insan Nusantara') },
    { property: 'og:description', content: computed(() => props.meta?.og_description || 'Informasi Penerimaan Peserta Didik Baru (PPDB) SMA Mutiara Insan Nusantara.') },
    { property: 'og:type', content: 'website' }
  ]
})

const fetchDocuments = async () => {
  // Skip if we have data from Inertia props
  if (props.documents.length > 0) return
  
  loading.value = true
  try {
    const response = await fetch('/api/documents')
    const data = await response.json()
    if (data.success) {
      localDocuments.value = data.data
    }
  } catch (error) {
    console.error('Error fetching documents:', error)
  } finally {
    loading.value = false
  }
}

const downloadDocument = async (id, filename) => {
  try {
    const response = await fetch(`/api/documents/${id}/download`)
    const blob = await response.blob()
    const url = window.URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = filename
    document.body.appendChild(a)
    a.click()
    window.URL.revokeObjectURL(url)
    document.body.removeChild(a)
  } catch (error) {
    console.error('Error downloading document:', error)
  }
}

const getFileIconBg = (type) => {
  const colors = {
    pdf: 'bg-red-100 dark:bg-red-900/20',
    doc: 'bg-blue-100 dark:bg-blue-900/20',
    docx: 'bg-blue-100 dark:bg-blue-900/20',
    xls: 'bg-green-100 dark:bg-green-900/20',
    xlsx: 'bg-green-100 dark:bg-green-900/20'
  }
  return colors[type] || 'bg-gray-100 dark:bg-gray-700'
}

const getFileIconColor = (type) => {
  const colors = {
    pdf: 'text-red-600 dark:text-red-400',
    doc: 'text-blue-600 dark:text-blue-400',
    docx: 'text-blue-600 dark:text-blue-400',
    xls: 'text-green-600 dark:text-green-400',
    xlsx: 'text-green-600 dark:text-green-400'
  }
  return colors[type] || 'text-gray-600 dark:text-gray-400'
}



const formatFileSize = (bytes) => {
  if (bytes >= 1048576) return (bytes / 1048576).toFixed(2) + ' MB'
  if (bytes >= 1024) return (bytes / 1024).toFixed(2) + ' KB'
  return bytes + ' B'
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

onMounted(() => {
  fetchDocuments()
  refreshInterval = setInterval(() => {
    fetchDocuments()
  }, 30000)
})

onBeforeUnmount(() => {
  if (refreshInterval) {
    clearInterval(refreshInterval)
  }
})
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}

.line-clamp-2 {
  display: -webkit-box;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
