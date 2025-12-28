<!--
  @component FilterDropdown
  @description Dropdown filter dengan v-model support dan animasi
  @props {String|Number} modelValue - Selected value (v-model)
  @props {Array} options - Array of { label, value }
  @emits update:modelValue - Ketika nilai berubah
-->

<template>
  <div class="relative">
    <button
      @click="isOpen = !isOpen"
      class="flex items-center gap-2 px-4 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 min-w-[200px]"
    >
      <Filter class="w-4 h-4" />
      <span class="flex-1 text-left">{{ selectedLabel }}</span>
      <ChevronDown
        class="w-4 h-4 transition-transform duration-200"
        :class="{ 'rotate-180': isOpen }"
      />
    </button>

    <transition
      enter-active-class="transition-all duration-200 ease-out"
      enter-from-class="opacity-0 scale-95 -translate-y-2"
      enter-to-class="opacity-100 scale-100 translate-y-0"
      leave-active-class="transition-all duration-150 ease-in"
      leave-from-class="opacity-100 scale-100 translate-y-0"
      leave-to-class="opacity-0 scale-95 -translate-y-2"
    >
      <div
        v-if="isOpen"
        class="absolute z-10 mt-2 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden"
      >
        <button
          v-for="option in options"
          :key="option.value"
          @click="selectOption(option)"
          class="w-full px-4 py-3 text-left hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150 flex items-center justify-between"
          :class="{
            'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400':
              modelValue === option.value
          }"
        >
          <span>{{ option.label }}</span>
          <Check v-if="modelValue === option.value" class="w-4 h-4" />
        </button>
      </div>
    </transition>

    <div v-if="isOpen" @click="isOpen = false" class="fixed inset-0 z-[5]"></div>
  </div>
</template>

<script setup>
import { Check, ChevronDown, Filter } from 'lucide-vue-next'
import { computed, ref } from 'vue'

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: 'all'
  },
  options: {
    type: Array,
    required: true
  }
})

const emit = defineEmits(['update:modelValue'])

const isOpen = ref(false)

const selectedLabel = computed(() => {
  const selected = props.options.find((opt) => opt.value === props.modelValue)
  return selected ? selected.label : 'Pilih filter'
})

const selectOption = (option) => {
  emit('update:modelValue', option.value)
  isOpen.value = false
}
</script>
