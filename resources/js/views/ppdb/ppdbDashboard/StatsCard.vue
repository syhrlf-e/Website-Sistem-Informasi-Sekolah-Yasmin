/**
 * @component StatsCard
 * @description Reusable stats card for PPDB dashboard
 */

<template>
  <div
    class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow"
  >
    <div class="flex items-center gap-3">
      <div
        :class="iconBgClass"
        class="p-3 rounded-xl"
      >
        <component :is="icon" class="w-5 h-5" :class="iconClass" />
      </div>
      <div>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ value }}</p>
        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ label }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  label: { type: String, required: true },
  value: { type: Number, default: 0 },
  icon: { type: Object, required: true },
  color: { type: String, default: 'blue' }
})

const colorClasses = {
  blue: { bg: 'bg-blue-100 dark:bg-blue-900/30', icon: 'text-blue-600 dark:text-blue-400' },
  yellow: { bg: 'bg-yellow-100 dark:bg-yellow-900/30', icon: 'text-yellow-600 dark:text-yellow-400' },
  cyan: { bg: 'bg-cyan-100 dark:bg-cyan-900/30', icon: 'text-cyan-600 dark:text-cyan-400' },
  purple: { bg: 'bg-purple-100 dark:bg-purple-900/30', icon: 'text-purple-600 dark:text-purple-400' },
  green: { bg: 'bg-green-100 dark:bg-green-900/30', icon: 'text-green-600 dark:text-green-400' },
  red: { bg: 'bg-red-100 dark:bg-red-900/30', icon: 'text-red-600 dark:text-red-400' },
}

const iconBgClass = computed(() => colorClasses[props.color]?.bg || colorClasses.blue.bg)
const iconClass = computed(() => colorClasses[props.color]?.icon || colorClasses.blue.icon)
</script>
