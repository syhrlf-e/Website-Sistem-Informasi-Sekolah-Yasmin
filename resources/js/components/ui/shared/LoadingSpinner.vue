/**
 * @component LoadingSpinner
 * @description Modern animated loading spinner with pulsing dots
 * @props size - sm | md | lg | xl
 * @props text - Custom loading text (optional)
 * @props color - blue | teal | purple | emerald | white
 */

<template>
  <div class="flex flex-col justify-center items-center" :class="containerClass">
    <!-- 3 Pulsing Dots -->
    <div class="flex items-center gap-1" :class="gapClass">
      <span class="dot" :class="[sizeClass, colorClass]" style="animation-delay: 0s"></span>
      <span class="dot" :class="[sizeClass, colorClass]" style="animation-delay: 0.2s"></span>
      <span class="dot" :class="[sizeClass, colorClass]" style="animation-delay: 0.4s"></span>
    </div>
    <p v-if="showText" class="mt-4 text-sm font-medium text-gray-600 dark:text-gray-400 font-poppins">
      {{ text }}
    </p>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  size: { type: String, default: 'md', validator: (v) => ['sm', 'md', 'lg', 'xl'].includes(v) },
  text: { type: String, default: 'Tunggu Sebentar Ya...' },
  showText: { type: Boolean, default: true },
  color: { type: String, default: 'blue', validator: (v) => ['blue', 'teal', 'purple', 'emerald', 'white'].includes(v) },
  fullHeight: { type: Boolean, default: false }
})

const sizeClass = computed(() => ({
  sm: 'dot-sm',
  md: 'dot-md',
  lg: 'dot-lg',
  xl: 'dot-xl'
})[props.size])

const gapClass = computed(() => ({
  sm: 'gap-1',
  md: 'gap-1.5',
  lg: 'gap-2',
  xl: 'gap-3'
})[props.size])

const colorClass = computed(() => ({
  blue: 'dot-blue',
  teal: 'dot-teal',
  purple: 'dot-purple',
  emerald: 'dot-emerald',
  white: 'dot-white'
})[props.color])

const containerClass = computed(() => props.fullHeight ? 'min-h-[200px]' : '')
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }

.dot {
  border-radius: 50%;
  animation: pulse 1.4s ease-in-out infinite;
}

/* Sizes */
.dot-sm { width: 8px; height: 8px; }
.dot-md { width: 12px; height: 12px; }
.dot-lg { width: 16px; height: 16px; }
.dot-xl { width: 20px; height: 20px; }

/* Colors */
.dot-blue { background-color: #3b82f6; }
.dot-teal { background-color: #14b8a6; }
.dot-purple { background-color: #a855f7; }
.dot-emerald { background-color: #10b981; }
.dot-white { background-color: #ffffff; }

/* Dark mode adjustments */
:global(.dark) .dot-blue { background-color: #60a5fa; }
:global(.dark) .dot-teal { background-color: #2dd4bf; }
:global(.dark) .dot-purple { background-color: #c084fc; }
:global(.dark) .dot-emerald { background-color: #34d399; }

@keyframes pulse {
  0%, 80%, 100% {
    transform: scale(0.6);
    opacity: 0.5;
  }
  40% {
    transform: scale(1);
    opacity: 1;
  }
}
</style>
