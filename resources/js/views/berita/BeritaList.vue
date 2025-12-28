/**
 * @component BeritaList
 * @description Parent component untuk halaman daftar berita admin
 * @route /yasmin-panel/berita
 */

<template>
  <div class="space-y-6">
    <BeritaListHeader />

    <BeritaListFilters
      :search-query="newsStore.searchQuery"
      :filter-category="newsStore.filterCategory"
      :filter-status="newsStore.filterStatus"
      @update:search-query="newsStore.setSearch($event)"
      @update:filter-category="newsStore.setCategory($event)"
      @update:filter-status="newsStore.setStatus($event)"
    />

    <div
      v-if="loading"
      class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 py-12"
    >
      <LoadingSpinner size="md" color="blue" />
    </div>

    <div
      v-else-if="error"
      class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4"
    >
      <p class="text-sm text-red-600 dark:text-red-400 font-poppins">{{ error }}</p>
    </div>

    <template v-else>
      <BeritaListTable
        :items="beritaList"
        :start-index="(pagination.current_page - 1) * pagination.per_page"
        @edit="handleEdit"
        @preview="handlePreview"
        @delete="handleDelete"
      />

      <BeritaListPagination
        v-if="beritaList.length > 0"
        :current-page="pagination.current_page"
        :last-page="pagination.last_page"
        :from="pagination.from"
        :to="pagination.to"
        :total="pagination.total"
        @page-change="changePage"
      />
    </template>
  </div>
</template>

<script setup>
import LoadingSpinner from '@/components/ui/shared/LoadingSpinner.vue'
import { useAdminNewsStore } from '@/stores/adminNews'
import { usePopup } from '@/composables/usePopup'
import { computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import BeritaListHeader from './beritaList/BeritaListHeader.vue'
import BeritaListFilters from './beritaList/BeritaListFilters.vue'
import BeritaListTable from './beritaList/BeritaListTable.vue'
import BeritaListPagination from './beritaList/BeritaListPagination.vue'

const router = useRouter()
const newsStore = useAdminNewsStore()
const { showSuccess, showError, confirmDelete } = usePopup()

const loading = computed(() => newsStore.loading)
const error = computed(() => newsStore.error)
const beritaList = computed(() => newsStore.items)

const pagination = computed(() => ({
  current_page: newsStore.currentPage,
  last_page: newsStore.lastPage,
  per_page: newsStore.perPage,
  total: newsStore.total,
  from: newsStore.from,
  to: newsStore.to
}))

const changePage = (page) => {
  newsStore.goToPage(page)
}

const handleEdit = (id) => {
  router.push(`/yasmin-panel/berita/edit/${id}`)
}

const handlePreview = (berita) => {
  // Open public news page in new tab
  window.open(`/news/${berita.slug}`, '_blank')
}

const handleDelete = async (id) => {
  const result = await confirmDelete('Yakin ingin menghapus?', 'Berita yang dihapus tidak bisa dikembalikan.')

  if (result.isConfirmed) {
    try {
      await newsStore.deleteNews(id)
      await showSuccess('Berhasil!', 'Berita telah dihapus.')
    } catch (err) {
      await showError('Gagal!', 'Terjadi kesalahan saat menghapus berita.')
    }
  }
}

onMounted(() => {
  newsStore.fetchNews()
})
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>

