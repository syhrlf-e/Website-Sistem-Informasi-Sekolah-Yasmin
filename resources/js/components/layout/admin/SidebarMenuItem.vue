<!--
  @component SidebarMenuItem
  @description Reusable menu item link dengan icon, label, dan optional notification dot
  @props {String} to - Router link destination path
  @props {Component} icon - Lucide icon component
  @props {String} label - Menu label text
  @props {Number} badge - Optional notification count (shows red dot if > 0, no number displayed)
  @props {Boolean} showIf - Conditional rendering (role-based visibility)
-->
  
<template>
  <router-link
    v-if="showIf"
    :to="to"
    class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 relative"
    active-class="bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 font-semibold"
  >
    <component :is="icon" class="w-5 h-5" />
    <span class="font-medium font-poppins">{{ label }}</span>
    <!-- Notification dot with bounce entrance animation -->
    <Transition name="bounce">
      <span
        v-if="badge && badge > 0"
        class="absolute right-3 top-1/2 -translate-y-1/2 w-2.5 h-2.5 bg-red-500 rounded-full notification-dot"
      ></span>
    </Transition>
  </router-link>
</template>

<script setup>
defineProps({
  to: {
    type: String,
    required: true
  },
  icon: {
    type: [Object, Function],
    required: true
  },
  label: {
    type: String,
    required: true
  },
  badge: {
    type: Number,
    default: 0
  },
  showIf: {
    type: Boolean,
    default: true
  }
})
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}

/* Notification dot pulse animation */
.notification-dot {
  animation: pulse-glow 2s ease-in-out infinite;
}

@keyframes pulse-glow {
  0%, 100% {
    box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
    transform: translateY(-50%) scale(1);
  }
  50% {
    box-shadow: 0 0 0 4px rgba(239, 68, 68, 0);
    transform: translateY(-50%) scale(1.1);
  }
}

/* Bounce entrance animation */
.bounce-enter-active {
  animation: bounce-in 0.5s ease-out;
}

.bounce-leave-active {
  animation: bounce-out 0.3s ease-in;
}

@keyframes bounce-in {
  0% {
    opacity: 0;
    transform: translateY(-50%) scale(0);
  }
  50% {
    transform: translateY(-50%) scale(1.3);
  }
  70% {
    transform: translateY(-50%) scale(0.9);
  }
  100% {
    opacity: 1;
    transform: translateY(-50%) scale(1);
  }
}

@keyframes bounce-out {
  0% {
    opacity: 1;
    transform: translateY(-50%) scale(1);
  }
  100% {
    opacity: 0;
    transform: translateY(-50%) scale(0);
  }
}
</style>
