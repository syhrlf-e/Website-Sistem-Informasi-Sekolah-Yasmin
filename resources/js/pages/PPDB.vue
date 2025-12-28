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
          Dokumen PPDB
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-300 font-poppins">
          Download berkas dan dokumen pelengkap pendaftaran
        </p>
      </div>

      <div v-if="loading" class="py-20">
        <LoadingSpinner size="lg" color="blue" text="Memuat dokumen..." />
      </div>

      <div
        v-else-if="documents.length > 0"
        class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden"
      >
        <!-- Table Header -->
        <div class="grid grid-cols-12 gap-4 px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
          <div class="col-span-5 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Nama Dokumen</div>
          <div class="col-span-2 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Tipe</div>
          <div class="col-span-2 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Ukuran</div>
          <div class="col-span-2 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Tanggal</div>
          <div class="col-span-1 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase text-center">Aksi</div>
        </div>
        
        <!-- Table Body -->
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
          <div
            v-for="doc in documents"
            :key="doc.id"
            class="grid grid-cols-12 gap-4 px-4 py-3 items-center hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
          >
            <div class="col-span-5">
              <span class="text-sm text-gray-900 dark:text-white font-poppins">{{ doc.title }}</span>
            </div>
            <div class="col-span-2">
              <span class="px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                {{ doc.file_type.toUpperCase() }}
              </span>
            </div>
            <div class="col-span-2">
              <span class="text-sm text-gray-600 dark:text-gray-400">{{ formatFileSize(doc.file_size) }}</span>
            </div>
            <div class="col-span-2">
              <span class="text-sm text-gray-500 dark:text-gray-400">{{ formatDate(doc.created_at) }}</span>
            </div>
            <div class="col-span-1 text-center">
              <button
                @click="downloadDocument(doc.id, doc.file_name)"
                class="p-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-lg transition-colors"
                title="Download"
              >
                <Download class="w-4 h-4" />
              </button>
            </div>
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
