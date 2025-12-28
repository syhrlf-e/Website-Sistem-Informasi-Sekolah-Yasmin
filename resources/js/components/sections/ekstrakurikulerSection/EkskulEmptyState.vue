<!--
  @component EkskulEmptyState
  @description Empty state message ketika tidak ada data ekstrakurikuler
  @props {String} message - Custom message (optional)
-->

<template>
  <div class="text-center py-12">
    <p class="text-gray-600 dark:text-gray-400 font-poppins">
      {{ message }}
    </p>
  </div>
</template>

<script setup>
defineProps({
  message: {
    type: String,
    default: 'Belum ada ekstrakurikuler tersedia'
  }
})
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
