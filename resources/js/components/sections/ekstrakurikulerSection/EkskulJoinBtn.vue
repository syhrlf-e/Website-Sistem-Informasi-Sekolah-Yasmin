<!--
  @component JoinButton
  @description CTA button untuk bergabung ekstrakurikuler
  @props {String} label - Button text (default: "Gabung Disini")
  @props {Number} ekskulId - ID ekskul untuk emit ke parent
  @emits click - Dengan ekskulId sebagai payload
-->

<template>
  <button
    class="inline-flex items-center justify-center px-6 py-2.5 bg-white/80 hover:bg-white text-gray-800 text-sm font-semibold rounded-xl transition-all duration-300 shadow-sm hover:shadow-md group-hover:scale-105 w-fit font-poppins"
    @click="handleClick"
  >
    {{ label }}
  </button>
</template>

<script setup>
const props = defineProps({
  label: {
    type: String,
    default: 'Gabung Disini'
  },
  ekskulId: {
    type: Number,
    required: false
  }
})

const emit = defineEmits(['click'])

const handleClick = () => {
  emit('click', props.ekskulId)
}
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
