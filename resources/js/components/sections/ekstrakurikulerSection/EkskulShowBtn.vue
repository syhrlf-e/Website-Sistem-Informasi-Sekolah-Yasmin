<!--
  @component ShowButton
  @description Small pill button dengan Eye icon untuk preview action
  @emits click - Ketika button diklik
-->

<template>
  <button
    @click="handleClick"
    class="flex-shrink-0 px-4 py-2 bg-white/80 hover:bg-white text-gray-900 text-xs font-semibold transition-all duration-200 shadow-sm hover:shadow-md active:scale-95 group rounded-full"
  >
    <span class="flex items-center gap-1.5">
      <span>Lihat</span>
      <Eye class="w-3.5 h-3.5 group-hover:scale-110 transition-transform duration-200" />
    </span>
  </button>
</template>

<script setup>
import { Eye } from 'lucide-vue-next'

const emit = defineEmits(['click'])

const handleClick = () => {
  emit('click')
}
</script>

<style scoped>
button {
  font-family: 'Poppins', sans-serif;
}
</style>
