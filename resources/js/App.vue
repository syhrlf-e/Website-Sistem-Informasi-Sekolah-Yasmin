<template>
  <div id="app" class="relative">
    <div class="relative z-0">
      <router-view v-slot="{ Component }">
        <transition name="fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>

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
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import SessionWarningModal from './components/SessionWarningModal.vue'

const route = useRoute()
const { logout } = useAuth()
const isAdminRoute = computed(() => route.path.startsWith('/yasmin-panel'))

const { isWarningVisible, remainingSeconds, extendSession } = useInactivity(30) 
const handleLogout = async () => {
  await logout()
}
</script>
<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

#app {
  min-height: 100vh;
  background: #ffffff;
  transition: background 0.3s ease;
}

.dark #app {
  background: #0f172a;
}

@keyframes pulse {
  0%,
  100% {
    opacity: 1;
    transform: scale(1);
  }
  50% {
    opacity: 0.6;
    transform: scale(1.1);
  }
}

.animate-pulse {
  animation: pulse 6s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
