<!--
  @component FormTextarea
  @description Reusable form textarea field dengan label, error/valid states
  @props {String} label - Label text
  @props {String} modelValue - v-model value
  @props {String} placeholder - Placeholder text
  @props {Number} rows - Number of rows
  @props {Boolean} required - Required field
  @props {String} error - Error message
  @props {Boolean} valid - Valid state
  @emits update:modelValue - v-model emit
  @emits blur - Native blur event
-->

<template>
  <div>
    <label
      class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins"
    >
      {{ label }} <span v-if="required" class="text-red-500">*</span>
    </label>
    <textarea
      :value="modelValue"
      @input="handleInput"
      @blur="$emit('blur', $event)"
      :required="required"
      :rows="rows"
      :placeholder="placeholder"
      class="w-full px-3 md:px-4 py-2.5 md:py-3 text-sm md:text-base bg-gray-50 dark:bg-gray-800 rounded-xl focus:outline-none text-gray-900 dark:text-white font-poppins resize-none placeholder:text-xs md:placeholder:text-sm transition-colors duration-200"
      :class="inputClasses"
    ></textarea>
    <p v-if="error" class="mt-1 text-sm text-red-500 font-poppins">
      {{ error }}
    </p>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  label: { type: String, required: true },
  modelValue: { type: String, default: '' },
  placeholder: { type: String, default: '' },
  rows: { type: Number, default: 4 },
  required: { type: Boolean, default: false },
  error: { type: String, default: '' },
  valid: { type: Boolean, default: false }
})

const emit = defineEmits(['update:modelValue', 'blur'])

const handleInput = (event) => {
  emit('update:modelValue', event.target.value)
}

const inputClasses = computed(() => {
  if (props.error) {
    // Error: red border, no focus ring
    return 'border-2 border-red-500'
  }
  if (props.valid) {
    // Valid: blue border with focus ring
    return 'border-2 border-blue-500 focus:ring-2 focus:ring-blue-500'
  }
  // Default: gray border with blue focus
  return 'border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500'
})
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
