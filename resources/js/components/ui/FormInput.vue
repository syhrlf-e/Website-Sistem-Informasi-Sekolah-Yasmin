<template>
  <div>
    <label
      class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins"
    >
      {{ label }} <span v-if="required" class="text-red-500">*</span>
    </label>
    <input
      :value="modelValue"
      @keydown="handleKeydown"
      @input="handleInput"
      @blur="$emit('blur', $event)"
      @paste="handlePaste"
      :type="type"
      :required="required"
      :placeholder="placeholder"
      :maxlength="maxlength"
      class="w-full px-3 md:px-4 py-2.5 md:py-3 text-sm md:text-base bg-gray-50 dark:bg-gray-800 rounded-xl focus:outline-none text-gray-900 dark:text-white font-poppins placeholder:text-xs md:placeholder:text-sm transition-colors duration-200"
      :class="inputClasses"
    />
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
  type: { type: String, default: 'text' },
  placeholder: { type: String, default: '' },
  required: { type: Boolean, default: false },
  error: { type: String, default: '' },
  valid: { type: Boolean, default: false },
  maxlength: { type: Number, default: null },
  allowedPattern: { type: RegExp, default: null }
})

const emit = defineEmits(['update:modelValue', 'input', 'blur'])

const handleKeydown = (event) => {
  if (!props.allowedPattern) return
  
  const controlKeys = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Tab', 'Enter', 'Home', 'End']
  if (controlKeys.includes(event.key)) return
  
  if (event.ctrlKey || event.metaKey) return
  
  if (!props.allowedPattern.test(event.key)) {
    event.preventDefault()
  }
}

const handlePaste = (event) => {
  if (!props.allowedPattern) return
  
  const pastedText = event.clipboardData.getData('text')
  const filteredText = pastedText.split('').filter(char => props.allowedPattern.test(char)).join('')
  
  if (filteredText !== pastedText) {
    event.preventDefault()
    const input = event.target
    const start = input.selectionStart
    const end = input.selectionEnd
    const newValue = props.modelValue.slice(0, start) + filteredText + props.modelValue.slice(end)
    emit('update:modelValue', newValue)
  }
}

const handleInput = (event) => {
  emit('update:modelValue', event.target.value)
  emit('input', event)
}

const inputClasses = computed(() => {
  if (props.error) {
    return 'border-2 border-red-500'
  }
  if (props.valid) {
    return 'border-2 border-blue-500 focus:ring-2 focus:ring-blue-500'
  }
  return 'border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500'
})
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
