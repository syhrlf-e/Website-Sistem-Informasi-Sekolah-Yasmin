<!--
  @component MobileOverlay
  @description Backdrop overlay untuk mobile sidebar (hidden di lg breakpoint)
  @props {Boolean} show - Toggle visibility
  @emits close - Ketika overlay diklik untuk menutup sidebar
-->
  
<template>
  <Transition
    enter-active-class="transition-opacity duration-300"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="transition-opacity duration-200"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div
      v-if="show"
      @click="$emit('close')"
      class="fixed inset-0 bg-black/50 z-40 lg:hidden"
    ></div>
  </Transition>
</template>

<script setup>
defineProps({
  show: {
    type: Boolean,
    default: false
  }
})

defineEmits(['close'])
</script>
