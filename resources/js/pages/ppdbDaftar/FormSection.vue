<!--
  @component FormSection
  @description Reusable accordion section for PPDB form with sequential lock support
-->

<template>
  <div 
    :class="[
      'bg-white dark:bg-gray-800 rounded-2xl border overflow-hidden transition-all duration-300',
      isLocked 
        ? 'border-gray-300 dark:border-gray-600 opacity-60' 
        : 'border-gray-200 dark:border-gray-700'
    ]"
  >
    <!-- Header -->
    <button
      @click="$emit('toggle')"
      :disabled="isLocked"
      :title="isLocked ? 'Lengkapi bagian sebelumnya untuk membuka' : ''"
      :class="[
        'w-full flex items-center justify-between p-4 transition-colors',
        isLocked 
          ? 'cursor-not-allowed bg-gray-50 dark:bg-gray-800/50' 
          : 'hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer'
      ]"
    >
      <div class="flex items-center gap-3">
        <div
          :class="[
            'w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm',
            isLocked 
              ? 'bg-gray-200 dark:bg-gray-600 text-gray-400 dark:text-gray-500'
              : isComplete 
                ? 'bg-green-100 dark:bg-green-900/30 text-green-600' 
                : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400'
          ]"
        >
          <Lock v-if="isLocked" class="w-4 h-4" />
          <CheckCircle v-else-if="isComplete" class="w-5 h-5" />
          <span v-else>{{ number }}</span>
        </div>
        <div class="flex flex-col items-start">
          <span 
            :class="[
              'font-semibold font-poppins',
              isLocked 
                ? 'text-gray-400 dark:text-gray-500' 
                : 'text-gray-900 dark:text-white'
            ]"
          >{{ title }}</span>
          <span v-if="isLocked" class="text-xs text-gray-400 dark:text-gray-500">
            Lengkapi bagian sebelumnya
          </span>
        </div>
      </div>
      <ChevronDown
        v-if="!isLocked"
        :class="isOpen ? 'rotate-180' : ''"
        class="w-5 h-5 text-gray-500 transition-transform duration-300"
      />
    </button>

    <!-- Content -->
    <Transition name="accordion">
      <div v-show="isOpen && !isLocked" class="border-t border-gray-200 dark:border-gray-700">
        <div class="p-6">
          <slot />
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { CheckCircle, ChevronDown, Lock } from 'lucide-vue-next'

defineProps({
  title: { type: String, required: true },
  icon: { type: String, default: 'User' },
  isOpen: { type: Boolean, default: false },
  isComplete: { type: Boolean, default: false },
  isLocked: { type: Boolean, default: false },
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

