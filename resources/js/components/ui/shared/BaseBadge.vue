<!--
  @component BaseBadge
  @description Unified badge component for categories, status, and labels
  @props {String} label - Text to display
  @props {String} variant - Color variant: blue|green|purple|orange|gray|red|emerald|indigo|yellow|rose
  @props {String} size - Size variant: sm|md
-->

<template>
  <span
    class="inline-flex items-center font-medium font-poppins rounded-full"
    :class="[sizeClass, variantClass]"
  >
    {{ label }}
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  label: {
    type: String,
    required: true
  },
  variant: {
    type: String,
    default: 'gray',
    validator: (value) => [
      'default', 'blue', 'green', 'purple', 'orange', 'gray', 'red',
      'emerald', 'indigo', 'yellow', 'rose'
    ].includes(value)
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md'].includes(value)
  }
})

const sizeClass = computed(() => {
  const sizes = {
    sm: 'px-2.5 py-1 text-xs',
    md: 'px-4 py-1.5 text-xs'
  }
  return sizes[props.size]
})

const variantClass = computed(() => {
  const variants = {
    // Category badges
    default: 'bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-sm',
    blue: 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
    green: 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
    purple: 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400',
    orange: 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400',
    gray: 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300',
    red: 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',
    // Ekskul card variants (more opaque for visibility on gradient backgrounds)
    emerald: 'bg-emerald-200/80 dark:bg-emerald-900/50 text-emerald-800 dark:text-emerald-300 ring-1 ring-emerald-300 dark:ring-emerald-800',
    indigo: 'bg-indigo-200/80 dark:bg-indigo-900/50 text-indigo-800 dark:text-indigo-300 ring-1 ring-indigo-300 dark:ring-indigo-800',
    yellow: 'bg-yellow-200/80 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-300 ring-1 ring-yellow-300 dark:ring-yellow-800',
    rose: 'bg-rose-200/80 dark:bg-rose-900/50 text-rose-800 dark:text-rose-300 ring-1 ring-rose-300 dark:ring-rose-800'
  }
  return variants[props.variant]
})
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
