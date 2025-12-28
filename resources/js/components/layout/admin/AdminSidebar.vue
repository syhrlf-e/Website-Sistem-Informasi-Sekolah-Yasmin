<!--
  @component AdminSidebar
  @description Fixed sidebar navigation untuk admin panel dengan responsive behavior
  @props {Boolean} isOpen - Toggle visibility di mobile (lg:always-visible)
  @props {Number} pendingCount - Badge count untuk menu Pendaftar Ekskul  
  @props {Boolean} isSuperAdmin - Role-based visibility untuk menu Users
  @props {Boolean} isAdminPpdb - Role-based visibility untuk admin PPDB (only PPDB menus)
  @emits logout - Trigger logout flow di parent
-->

<template>
  <aside
    :class="isOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-transform duration-300 ease-in-out flex flex-col"
  >
    <div
      class="h-16 flex items-center px-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0"
    >
      <h1 class="text-xl font-bold text-gray-900 dark:text-white font-poppins">Yasmin Panel</h1>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto custom-scrollbar">
      <SidebarMenuItem
        to="/yasmin-panel/dashboard"
        :icon="LayoutDashboard"
        label="Dashboard"
      />

      <!-- Konten Submenu - Hidden for admin_ppdb -->
      <SidebarSubmenu
        v-if="!isAdminPpdb"
        :icon="FileText"
        label="Konten"
        :items="kontenItems"
        :is-active="isKontenActive"
        :default-open="true"
      />

      <!-- Regular menus - Hidden for admin_ppdb -->
      <SidebarMenuItem
        v-if="!isAdminPpdb"
        to="/yasmin-panel/pengumuman"
        :icon="Megaphone"
        label="Pengumuman"
      />

      <SidebarMenuItem
        v-if="!isAdminPpdb"
        to="/yasmin-panel/testimoni"
        :icon="MessageSquareQuote"
        label="Testimoni"
      />

      <SidebarMenuItem
        v-if="!isAdminPpdb"
        to="/yasmin-panel/dokumen"
        :icon="FileText"
        label="Dokumen PPDB"
      />

      <SidebarMenuItem
        v-if="!isAdminPpdb"
        to="/yasmin-panel/guru"
        :icon="GraduationCap"
        label="Data Guru"
      />

      <SidebarMenuItem
        v-if="!isAdminPpdb"
        to="/yasmin-panel/pendaftar"
        :icon="ClipboardList"
        label="Pendaftar Ekskul"
        :badge="pendingCount"
      />

      <!-- TODO: PPDB Menu akan ditambahkan di Phase 3 -->
      <!-- PPDB Submenu - Visible for admin_ppdb, super_admin, admin -->

      <SidebarMenuItem
        to="/yasmin-panel/users"
        :icon="Users"
        label="Pengguna"
        :show-if="isSuperAdmin"
      />
    </nav>

    <!-- Bottom Section -->
    <div class="flex-shrink-0">
      <!-- Riwayat Aktivitas (Super Admin only) -->
      <div v-if="isSuperAdmin" class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
        <router-link
          to="/yasmin-panel/activity-logs"
          class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 font-medium font-poppins"
        >
          <History class="w-5 h-5" />
          <span>Riwayat Aktivitas</span>
        </router-link>
      </div>

      <!-- Logout Button -->
      <div class="p-4 border-t border-gray-200 dark:border-gray-700">
        <button
          @click="$emit('logout')"
          class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200 font-medium font-poppins"
        >
          <LogOut class="w-5 h-5" />
          <span>Logout</span>
        </button>
      </div>
    </div>
  </aside>
</template>

<script setup>
import {
  ClipboardList,
  FileText,
  GraduationCap,
  History,
  Image,
  LayoutDashboard,
  LogOut,
  Megaphone,
  MessageSquareQuote,
  Newspaper,
  Target,
  Trophy,
  Users
} from 'lucide-vue-next'
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import SidebarMenuItem from './SidebarMenuItem.vue'
import SidebarSubmenu from './SidebarSubmenu.vue'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  pendingCount: {
    type: Number,
    default: 0
  },
  isSuperAdmin: {
    type: Boolean,
    default: false
  },
  isAdminPpdb: {
    type: Boolean,
    default: false
  }
})

defineEmits(['logout'])

const route = useRoute()

/**
 * Submenu items untuk group "Konten"
 * @note Tambah item baru di sini jika ada menu konten baru
 */
const kontenItems = [
  { to: '/yasmin-panel/berita', icon: Newspaper, label: 'Berita' },
  { to: '/yasmin-panel/ekskul', icon: Target, label: 'Ekstrakurikuler' },
  { to: '/yasmin-panel/galeri', icon: Image, label: 'Galeri' },
  { to: '/yasmin-panel/prestasi', icon: Trophy, label: 'Prestasi' }
]

/**
 * Computed: detect active state untuk parent submenu "Konten"
 * @returns {Boolean} true jika current route match dengan salah satu kontenRoutes
 */
const isKontenActive = computed(() => {
  const kontenRoutes = [
    '/yasmin-panel/berita',
    '/yasmin-panel/ekskul',
    '/yasmin-panel/galeri',
    '/yasmin-panel/prestasi'
  ]
  return kontenRoutes.some((path) => route.path.startsWith(path))
})
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}

.custom-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: rgba(156, 163, 175, 0.3) transparent;
}
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: rgba(156, 163, 175, 0.3);
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: rgba(156, 163, 175, 0.5);
}
.dark .custom-scrollbar {
  scrollbar-color: rgba(75, 85, 99, 0.5) transparent;
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: rgba(75, 85, 99, 0.5);
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: rgba(75, 85, 99, 0.7);
}
</style>
