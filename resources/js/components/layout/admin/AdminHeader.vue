<!--
  @component AdminHeader
  @description Top navigation bar dengan mobile toggle, page title, dark mode, dan user info
  @props {String} pageTitle - Dynamic page title dari route
  @props {Object} user - User data { name, role }
  @props {Boolean} isDark - Current dark mode state
  @emits toggle-sidebar - Toggle mobile sidebar visibility
  @emits toggle-dark-mode - Toggle dark/light theme
-->
<template>
  <header
    class="h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between px-6 flex-shrink-0"
  >
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

    <div class="flex items-center gap-4">
      <button
        @click="$emit('toggle-dark-mode')"
        class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
      >
        <Moon v-if="!isDark" class="w-5 h-5 text-gray-700" />
        <Sun v-else class="w-5 h-5 text-gray-300" />
      </button>

      <UserAvatar :user="user" :show-info="true" />
    </div>
  </header>
</template>

<script setup>
import { Menu, Moon, Sun } from 'lucide-vue-next'
import UserAvatar from './UserAvatar.vue'

defineProps({
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

defineEmits(['toggle-sidebar', 'toggle-dark-mode'])
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
