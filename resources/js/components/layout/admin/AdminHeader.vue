<!--
  @component AdminHeader
  @description Top navigation bar dengan mobile toggle, page title, notifications, settings, dan user info
  @props {String} pageTitle - Dynamic page title dari route
  @props {Object} user - User data { name, role }
  @props {Boolean} isDark - Current dark mode state
  @emits toggle-sidebar - Toggle mobile sidebar visibility
  @emits toggle-dark-mode - Toggle dark/light theme
  @emits logout - Trigger logout
-->
<template>
  <header
    class="h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between px-6 flex-shrink-0"
  >
    <!-- Left: Mobile menu toggle & Page title -->
    <div class="flex items-center gap-4">
      <button
        @click="$emit('toggle-sidebar')"
        class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
      >
        <Menu class="w-6 h-6 text-gray-700 dark:text-gray-300" />
      </button>

      <h2
        class="text-lg font-semibold text-gray-900 dark:text-white font-poppins hidden lg:block"
      >
        {{ pageTitle }}
      </h2>
    </div>

    <!-- Right: Actions & User -->
    <div class="flex items-center gap-2">
      <!-- Notifications Bell -->
      <button
        class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors relative"
        title="Notifikasi"
      >
        <Bell class="w-5 h-5 text-gray-600 dark:text-gray-300" />
        <!-- Notification badge (optional) -->
        <!-- <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span> -->
      </button>

      <!-- Settings Dropdown -->
      <div class="relative" ref="settingsRef">
        <button
          @click="toggleSettings"
          class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
          :class="{ 'bg-gray-100 dark:bg-gray-700': showSettings }"
          title="Pengaturan"
        >
          <Settings class="w-5 h-5 text-gray-600 dark:text-gray-300" />
        </button>

        <!-- Dropdown Menu -->
        <Transition
          enter-active-class="transition ease-out duration-100"
          enter-from-class="transform opacity-0 scale-95"
          enter-to-class="transform opacity-100 scale-100"
          leave-active-class="transition ease-in duration-75"
          leave-from-class="transform opacity-100 scale-100"
          leave-to-class="transform opacity-0 scale-95"
        >
          <div
            v-if="showSettings"
            class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 py-2 z-50"
          >
            <!-- Theme Toggle -->
            <button
              @click="handleThemeToggle"
              class="w-full px-4 py-2.5 flex items-center gap-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-left"
            >
              <Moon v-if="!isDark" class="w-5 h-5 text-gray-500 dark:text-gray-400" />
              <Sun v-else class="w-5 h-5 text-yellow-500" />
              <span class="text-sm text-gray-700 dark:text-gray-200">
                {{ isDark ? 'Mode Terang' : 'Mode Gelap' }}
              </span>
            </button>

            <!-- Activity Logs -->
            <a
              href="/yasmin-panel/activity-logs"
              class="w-full px-4 py-2.5 flex items-center gap-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            >
              <History class="w-5 h-5 text-gray-500 dark:text-gray-400" />
              <span class="text-sm text-gray-700 dark:text-gray-200">Riwayat Aktifitas</span>
            </a>

            <!-- Help -->
            <button
              @click="handleHelp"
              class="w-full px-4 py-2.5 flex items-center gap-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-left"
            >
              <HelpCircle class="w-5 h-5 text-gray-500 dark:text-gray-400" />
              <span class="text-sm text-gray-700 dark:text-gray-200">Bantuan</span>
            </button>

            <div class="my-2 border-t border-gray-200 dark:border-gray-700"></div>

            <!-- Logout -->
            <button
              @click="handleLogout"
              class="w-full px-4 py-2.5 flex items-center gap-3 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors text-left"
            >
              <LogOut class="w-5 h-5 text-red-500" />
              <span class="text-sm text-red-600 dark:text-red-400">Keluar</span>
            </button>
          </div>
        </Transition>
      </div>

      <!-- Divider -->
      <div class="w-px h-8 bg-gray-200 dark:bg-gray-700 mx-2"></div>

      <!-- User Avatar -->
      <UserAvatar :user="user" :show-info="true" />
    </div>
  </header>
</template>

<script setup>
import { Bell, HelpCircle, History, LogOut, Menu, Moon, Settings, Sun } from 'lucide-vue-next'
import { onMounted, onUnmounted, ref } from 'vue'
import UserAvatar from './UserAvatar.vue'

const props = defineProps({
  pageTitle: {
    type: String,
    default: 'Admin Panel'
  },
  user: {
    type: Object,
    default: null
  },
  isDark: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['toggle-sidebar', 'toggle-dark-mode', 'logout'])

const showSettings = ref(false)
const settingsRef = ref(null)

const toggleSettings = () => {
  showSettings.value = !showSettings.value
}

const handleThemeToggle = () => {
  emit('toggle-dark-mode')
  // Keep dropdown open to see the change
}

const handleHelp = () => {
  showSettings.value = false
  // TODO: Open help modal or navigate to help page
  window.open('https://wa.me/6285155258099?text=Halo, saya butuh bantuan dengan Yasmin Panel', '_blank')
}

const handleLogout = () => {
  showSettings.value = false
  emit('logout')
}

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  if (settingsRef.value && !settingsRef.value.contains(event.target)) {
    showSettings.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
