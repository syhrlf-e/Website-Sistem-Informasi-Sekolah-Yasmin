<!--
  @component FormSection
  @description Reusable accordion section for PPDB form
-->

<template>
  <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <!-- Header -->
    <button
      @click="$emit('toggle')"
      class="w-full flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
    >
      <div class="flex items-center gap-3">
        <div
          :class="isComplete ? 'bg-green-100 dark:bg-green-900/30 text-green-600' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400'"
          class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm"
        >
          <CheckCircle v-if="isComplete" class="w-5 h-5" />
          <span v-else>{{ number }}</span>
        </div>
        <span class="font-semibold text-gray-900 dark:text-white font-poppins">{{ title }}</span>
      </div>
      <ChevronDown
        :class="isOpen ? 'rotate-180' : ''"
        class="w-5 h-5 text-gray-500 transition-transform duration-300"
      />
    </button>

    <!-- Content -->
    <Transition name="accordion">
      <div v-show="isOpen" class="border-t border-gray-200 dark:border-gray-700">
        <div class="p-6">
          <slot />
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { CheckCircle, ChevronDown } from 'lucide-vue-next'

defineProps({
  title: { type: String, required: true },
  icon: { type: String, default: 'User' },
  isOpen: { type: Boolean, default: false },
  isComplete: { type: Boolean, default: false },
  number: { type: Number, default: 1 }
})

defineEmits(['toggle'])
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }

.accordion-enter-active, .accordion-leave-active {
  transition: all 0.3s ease;
  overflow: hidden;
}
.accordion-enter-from, .accordion-leave-to {
  max-height: 0;
  opacity: 0;
}
.accordion-enter-to, .accordion-leave-from {
  max-height: 800px;
  opacity: 1;
}
</style>
