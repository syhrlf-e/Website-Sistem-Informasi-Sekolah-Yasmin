<!--
  @component BackButton
  @description Reusable back button component with consistent styling
  @usage <BackButton to="/profil" text="Kembali ke Profil" />
-->

<template>
  <button
    @click="handleClick"
    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl font-medium transition-all duration-200 group"
    :class="variantClasses"
  >
    <svg
      class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform duration-200"
      fill="none"
      stroke="currentColor"
      viewBox="0 0 24 24"
    >
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M10 19l-7-7m0 0l7-7m-7 7h18"
      />
    </svg>
    <span>{{ text || 'Kembali' }}</span>
  </button>
</template>

<script setup>
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  // Destination URL (if not provided, uses browser history.back())
  to: {
    type: String,
    default: null
  },
  // Button text
  text: {
    type: String,
    default: 'Kembali'
  },
  // Visual variant: 'default', 'light' (for dark backgrounds), 'ghost'
  variant: {
    type: String,
    default: 'default',
    validator: (v) => ['default', 'light', 'ghost'].includes(v)
  },
  // Use Vue Router instead of Inertia (for admin SPA)
  useRouter: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['click'])

const variantClasses = computed(() => {
  switch (props.variant) {
    case 'light':
      // For dark hero backgrounds
      return 'bg-white/10 hover:bg-white/20 backdrop-blur-sm text-white'
    case 'ghost':
      // Minimal style
      return 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800'
    default:
      // Standard style
      return 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'
  }
})

const handleClick = () => {
  emit('click')
  
  if (props.to) {
    if (props.useRouter) {
      // For Vue Router (admin SPA)
      // This will be handled by the parent if needed
      window.location.href = props.to
    } else {
      // For Inertia (public pages)
      router.visit(props.to)
    }
  } else {
    // Fallback to browser back
    window.history.back()
  }
}
</script>
