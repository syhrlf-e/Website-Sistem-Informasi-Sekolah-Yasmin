<!--
  @component Admin/App
  @description Admin panel wrapper that uses Vue Router for admin navigation
  @route /yasmin-panel/*
-->

<template>
  <div id="admin-app" class="relative">
    <div class="relative z-0">
      <router-view />
      
      <SessionWarningModal
        v-if="isAdminRoute"
        :show="isWarningVisible"
        :remaining-seconds="remainingSeconds"
        @extend="extendSession"
        @logout="handleLogout"
      />
    </div>
  </div>
</template>

<script setup>
import { useAuth } from '@/composables/useAuth'
import { useInactivity } from '@/composables/useInactivity'
import { computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import SessionWarningModal from '@/components/SessionWarningModal.vue'
import router from '@/router'

// Initialize Vue Router for admin panel
import { createApp } from 'vue'

const route = useRoute()
const { logout } = useAuth()

const isAdminRoute = computed(() => route.path.startsWith('/yasmin-panel'))

const { isWarningVisible, remainingSeconds, extendSession } = useInactivity(30)

const handleLogout = async () => {
  await logout()
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

#admin-app {
  min-height: 100vh;
  background: #f8fafc;
  transition: background 0.3s ease;
}

.dark #admin-app {
  background: #0f172a;
}
</style>
