<!--
  @component EkskulLoadingSkeleton
  @description Skeleton placeholder cards saat loading data ekstrakurikuler
  @props {Number} count - Jumlah skeleton cards (default: 4)
-->

<template>
  <div class="flex flex-wrap justify-center gap-6 max-w-9xl mx-auto">
    <div
      v-for="i in count"
      :key="i"
      class="rounded-3xl bg-gray-200 dark:bg-gray-800 animate-pulse h-96 w-full sm:w-[calc(50%-12px)] lg:w-[calc(25%-18px)]"
    ></div>
  </div>
</template>

<script setup>
defineProps({
  count: {
    type: Number,
    default: 4
  }
})
</script>
