<!--
  @component EmptyState
  @description Reusable empty/error state dengan icon, title, message, dan action slot
  @props {String} title - Judul pesan
  @props {String} message - Deskripsi pesan
  @slots icon - Custom icon (default: FileText)
  @slots action - Action button area
-->

<template>
  <div class="text-center py-12 px-4">
    <slot name="icon">
      <div class="flex justify-center mb-6">
        <div class="p-6 bg-gray-100 dark:bg-gray-700 rounded-full">
          <FileText class="w-16 h-16 text-gray-400 dark:text-gray-500" />
        </div>
      </div>
    </slot>

    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
      {{ title }}
    </h3>

    <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto mb-6">
      {{ message }}
    </p>

    <slot name="action"></slot>
  </div>
</template>

<script setup>
import { FileText } from 'lucide-vue-next'

defineProps({
  title: {
    type: String,
    required: true
  },
  message: {
    type: String,
    required: true
  }
})
</script>
