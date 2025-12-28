<!--
  @component InfoItem
  @description Mobile info item dengan icon dan label/value (horizontal layout)
  @props {Component} icon - Lucide icon component
  @props {String} label - Label text
  @props {String} value - Value text
-->

<template>
  <div class="flex items-start gap-3">
    <div
      class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-800 flex items-center justify-center flex-shrink-0"
    >
      <component :is="icon" class="w-5 h-5 text-gray-700 dark:text-gray-300" />
    </div>
    <div>
      <p class="font-semibold text-gray-800 dark:text-white font-poppins">
        {{ label }}
      </p>
      <p class="text-sm text-gray-700 dark:text-gray-400 font-poppins">
        {{ value }}
      </p>
    </div>
  </div>
</template>

<script setup>
defineProps({
  icon: {
    type: [Object, Function],
    required: true
  },
  label: {
    type: String,
    required: true
  },
  value: {
    type: String,
    required: true
  }
})
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
