<template>
  <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
    <span class="text-sm text-gray-500 dark:text-gray-400">{{ label }}</span>
    <span class="text-sm font-medium text-gray-900 dark:text-white text-right">{{ value || '-' }}</span>
  </div>
</template>

<script setup>
defineProps({
  label: { type: String, required: true },
  value: { type: [String, Number], default: '' }
})
</script>
