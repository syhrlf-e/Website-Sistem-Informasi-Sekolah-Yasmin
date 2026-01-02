<!--
  @component AdminLayout
  @description Main layout wrapper untuk admin panel dengan sidebar, header, dan content area
  @requires useAuth composable untuk user state dan logout
  @behavior Auto-fetches pending count setiap 30s dan saat tab visible
-->
<template>
  <div
    class="h-screen bg-gray-50 dark:bg-gray-900 flex overflow-hidden transition-colors duration-300"
  >
    <AdminSidebar
      :is-open="sidebarOpen"
      :pending-count="isOnPendaftarRoute ? 0 : pendingCount"
      :is-super-admin="isSuperAdmin"
      :is-admin-ppdb="isAdminPpdb"
    />

    <MobileOverlay :show="sidebarOpen" @close="sidebarOpen = false" />

    <div class="flex-1 flex flex-col overflow-hidden">
      <AdminHeader
        :page-title="pageTitle"
        :user="user"
        :is-dark="isDark"
        @toggle-sidebar="sidebarOpen = !sidebarOpen"
        @toggle-dark-mode="toggleDarkMode"
        @logout="handleLogout"
      />

      <main class="flex-1 p-6 overflow-y-auto bg-gray-50 dark:bg-gray-900">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import { useAuth } from '@/composables/useAuth'
import { computed, onMounted, onUnmounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AdminHeader from './admin/AdminHeader.vue'
import AdminSidebar from './admin/AdminSidebar.vue'
import MobileOverlay from './admin/MobileOverlay.vue'

const router = useRouter()
const route = useRoute()
const { user, logout, initAuth, isSuperAdmin, isAdminPpdb } = useAuth()

const sidebarOpen = ref(false)
const isDark = ref(false)
const pendingCount = ref(0)

/**
 * Route-to-title mapping untuk dynamic page title
 * @note Tambah entry baru di sini jika ada route baru
 */
const pageTitle = computed(() => {
  const titles = {
    '/yasmin-panel/dashboard': 'Dashboard',
    '/yasmin-panel/berita': 'Kelola Konten',
    '/yasmin-panel/ekskul': 'Kelola Konten',
    '/yasmin-panel/galeri': 'Kelola Konten',
    '/yasmin-panel/prestasi': 'Kelola Konten',
    '/yasmin-panel/pengumuman': 'Kelola Pengumuman',
    '/yasmin-panel/testimoni': 'Kelola Testimoni',
    '/yasmin-panel/dokumen': 'Kelola Dokumen PPDB',
    '/yasmin-panel/guru': 'Kelola Data Guru',
    '/yasmin-panel/pendaftar': 'Kelola Pendaftar Ekstrakurikuler',
    '/yasmin-panel/users': 'Kelola Pengguna'
  }
  return titles[route.path] || 'Admin Panel'
})

/**
 * Check if user is currently on Pendaftar route
 * If true, hide badge because user is already viewing pending count on that page
 */
const isOnPendaftarRoute = computed(() => {
  return route.path === '/yasmin-panel/pendaftar'
})

/**
 * Fetch pending pendaftar count untuk sidebar badge
 * @endpoint GET /api/yasmin-panel/dashboard/stats
 */
const fetchSidebarStats = async () => {
  try {
    const token = sessionStorage.getItem('admin_token')
    const response = await fetch('/api/yasmin-panel/dashboard/stats', {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json'
      }
    })
    const data = await response.json()
    if (data.success) {
      pendingCount.value = data.data.pendaftar_pending || 0
    }
  } catch (error) {
    console.error('Error fetching sidebar stats:', error)
  }
}

/**
 * Visibility change handler untuk refresh data saat tab aktif kembali
 */
const handleVisibilityChange = () => {
  if (!document.hidden) {
    fetchSidebarStats()
  }
}

let refreshInterval = null

/**
 * Prefetch common admin route components untuk mengurangi delay first-load
 * Ini akan load chunk di background setelah layout dimuat
 */
const prefetchAdminRoutes = () => {
  // Delay sedikit agar tidak mengganggu initial render
  setTimeout(() => {
    // Prefetch semua admin route components menggunakan @/ alias
    import('@/views/Dashboard.vue')
    import('@/views/berita/BeritaList.vue')
    import('@/views/berita/BeritaForm.vue')
    import('@/views/ekskul/EkskulList.vue')
    import('@/views/galeri/GaleriPage.vue')
    import('@/views/prestasi/PrestasiList.vue')
    import('@/views/pendaftar/PendaftarList.vue')
    import('@/views/pengumuman/PengumumanList.vue')
    import('@/views/testimoni/TestimoniList.vue')
    import('@/views/dokumen/DokumenList.vue')
    import('@/views/guru/GuruList.vue')
    import('@/views/users/UsersPage.vue')
  }, 500) // Delay 500ms setelah layout load
}

onMounted(() => {
  initAuth()
  fetchSidebarStats()
  prefetchAdminRoutes() // Prefetch admin routes di background
  document.addEventListener('visibilitychange', handleVisibilityChange)
  // Polling setiap 15s (lebih santai)
  refreshInterval = setInterval(fetchSidebarStats, 15000)
})

onUnmounted(() => {
  document.removeEventListener('visibilitychange', handleVisibilityChange)
  if (refreshInterval) {
    clearInterval(refreshInterval)
  }
})

/**
 * Toggle dark mode via document class
 * @note State isDark tidak di-persist, akan reset saat refresh
 */
const toggleDarkMode = () => {
  isDark.value = !isDark.value
  document.documentElement.classList.toggle('dark')
}

/**
 * Logout handler dengan cleanup session/localStorage
 * Redirect ke Login page dengan cache-busting query
 */
const handleLogout = async () => {
  try {
    await logout()
    await new Promise((resolve) => setTimeout(resolve, 50))
    sessionStorage.clear()
    localStorage.clear()
    await router.replace({
      name: 'Login',
      query: { t: Date.now() }
    })
  } catch (error) {
    console.error('Logout error:', error)
    sessionStorage.clear()
    localStorage.clear()
    await router.replace({
      name: 'Login',
      query: { t: Date.now() }
    })
  }
}
</script>
