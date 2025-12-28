<!--
  @component InfoColumn
  @description Desktop info column dengan icon centered dan label/value (vertical layout)
  @props {Component} icon - Lucide icon component
  @props {String} label - Label text
  @props {String} value - Value text
-->

<template>
  <div class="flex-1 px-6">
    <div class="flex flex-col items-center gap-2">
      <div
        class="w-12 h-12 rounded-full bg-gray-200 dark:bg-gray-800 flex items-center justify-center mb-2"
      >
        <component :is="icon" class="w-6 h-6 text-gray-700 dark:text-gray-300" />
      </div>
      <p class="text-sm font-bold text-gray-800 dark:text-white font-poppins">
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
