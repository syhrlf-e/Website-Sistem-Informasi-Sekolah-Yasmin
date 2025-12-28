/**
 * @component BeritaTrash
 * @description Parent component untuk halaman tempat sampah berita
 * @route /yasmin-panel/berita/trash
 */

<template>
  <div class="space-y-6">
    <BeritaTrashHeader />

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
      <BeritaTrashTable
        :items="beritaList"
        :start-index="(pagination.current_page - 1) * pagination.per_page"
        @restore="handleRestore"
        @force-delete="handleForceDelete"
      />

      <BeritaTrashPagination
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
import BeritaTrashHeader from './beritaTrash/BeritaTrashHeader.vue'
import BeritaTrashTable from './beritaTrash/BeritaTrashTable.vue'
import BeritaTrashPagination from './beritaTrash/BeritaTrashPagination.vue'

const newsStore = useAdminNewsStore()
const { showSuccess, showError, showInfo, confirm } = usePopup()

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
  newsStore.currentPage = page
  newsStore.fetchTrashedNews()
}

const handleRestore = async (id) => {
  const result = await confirm('Pulihkan Berita?', 'Berita akan dikembalikan ke daftar berita.', 'Ya, pulihkan!')

  if (result.isConfirmed) {
    try {
      await newsStore.restoreNews(id)
      await showSuccess('Berhasil!', 'Berita telah dipulihkan.')
    } catch (err) {
      await showError('Gagal!', 'Terjadi kesalahan saat memulihkan berita.')
    }
  }
}

const handleForceDelete = async (id) => {
  const result = await confirm('Hapus Permanen?', 'Berita akan dihapus PERMANEN dan tidak bisa dikembalikan!', 'Ya, hapus permanen!')

  if (result.isConfirmed) {
    try {
      await newsStore.forceDeleteNews(id)
      await showSuccess('Berhasil!', 'Berita telah dihapus permanen.')
    } catch (err) {
      await showError('Gagal!', 'Terjadi kesalahan saat menghapus berita.')
    }
  }
}

onMounted(() => {
  newsStore.fetchTrashedNews()
})
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
