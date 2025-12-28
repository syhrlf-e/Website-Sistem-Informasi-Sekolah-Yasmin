<template>
  <button
    @click="$emit('click')"
    :class="buttonClass"
    class="mt-6 w-full inline-flex items-center justify-between gap-3 px-3 py-2 md:px-3.5 md:py-2 border-2 rounded-full transition-all duration-300 group"
  >
    <span :class="textClass" class="text-sm font-medium"> Baca Selengkapnya </span>
    <div
      :class="circleClass"
      class="relative w-7 h-7 rounded-full flex items-center justify-center overflow-hidden transition-colors duration-300"
    >
      <ArrowRight :size="16" :class="iconClass" class="absolute arrow-exit -rotate-45" />
      <ArrowRight :size="16" :class="iconClass" class="absolute arrow-enter -rotate-45" />
    </div>
  </button>
</template>

<script setup>
import { ArrowRight } from 'lucide-vue-next'
import { computed } from 'vue'

const props = defineProps({
  variant: {
    type: String,
    default: 'light',
    validator: (value) => ['light', 'dark'].includes(value)
  }
})

defineEmits(['click'])

const buttonClass = computed(() => {
  return props.variant === 'dark'
    ? 'bg-gray-800 dark:bg-gray-200 border-gray-700 dark:border-gray-300 hover:border-gray-600 dark:hover:border-gray-400'
    : 'bg-gray-100 dark:bg-gray-800 border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'
})

const textClass = computed(() => {
  return props.variant === 'dark'
    ? 'text-gray-200 dark:text-gray-800'
    : 'text-gray-700 dark:text-gray-300'
})

const circleClass = computed(() => {
  return props.variant === 'dark'
    ? 'bg-blue-500 group-hover:bg-blue-400'
    : 'bg-gray-700 group-hover:bg-gray-800 dark:bg-gray-600 dark:group-hover:bg-gray-500'
})

const iconClass = computed(() => {
  return 'text-white'
})
</script>

<style scoped>
/* Panah Keluar */
@keyframes arrowExit {
  0% {
    transform: translate(0, 0) rotate(-45deg);
    opacity: 1;
  }
  30% {
    transform: translate(12px, -12px) rotate(-45deg);
    opacity: 0;
  }
  100% {
    transform: translate(12px, -12px) rotate(-45deg);
    opacity: 0;
  }
}

/* Panah Masuk */
@keyframes arrowEnter {
  0% {
    transform: translate(-12px, 12px) rotate(-45deg);
    opacity: 0;
  }
  30% {
    transform: translate(-12px, 12px) rotate(-45deg);
    opacity: 0;
  }
  60% {
    transform: translate(0, 0) rotate(-45deg);
    opacity: 1;
  }
  100% {
    transform: translate(0, 0) rotate(-45deg);
    opacity: 1;
  }
}

.arrow-exit {
  animation: arrowExit 1.2s cubic-bezier(0.4, 0, 0.2, 1) infinite;
}

.arrow-enter {
  animation: arrowEnter 1.2s cubic-bezier(0.4, 0, 0.2, 1) infinite;
}

.group:hover .arrow-exit,
.group:hover .arrow-enter {
  animation-play-state: paused;
}

button:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
</style>
