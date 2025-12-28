<!--
  @component TokenInput
  @description Specialized input untuk token registrasi dengan Key icon, auto-uppercase
  @props {String} modelValue - v-model value
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
      Token Registrasi <span class="text-red-500">*</span>
    </label>
    <div class="relative">
      <input
        :value="modelValue"
        @input="handleInput"
        @blur="$emit('blur', $event)"
        type="text"
        required
        placeholder="Masukkan token registrasi"
        class="w-full px-3 md:px-4 py-2.5 md:py-3 pr-10 md:pr-12 text-sm md:text-base bg-gray-50 dark:bg-gray-800 border-2 rounded-xl focus:outline-none text-gray-900 dark:text-white font-poppins font-mono tracking-wider placeholder:text-xs md:placeholder:text-sm uppercase transition-colors duration-200"
        :class="inputClasses"
      />
      <div
        class="absolute right-3 top-1/2 -translate-y-1/2"
        :class="iconClasses"
      >
        <Key class="w-5 h-5" />
      </div>
    </div>
    <p v-if="error" class="mt-1 text-sm text-red-500 font-poppins">
      {{ error }}
    </p>
    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400 font-poppins">
      💡 Token dapat diperoleh dari pihak sekolah atau pembina ekstrakurikuler
    </p>
  </div>
</template>

<script setup>
import { Key } from 'lucide-vue-next'
import { computed } from 'vue'

const props = defineProps({
  modelValue: { type: String, default: '' },
  error: { type: String, default: '' },
  valid: { type: Boolean, default: false }
})

const emit = defineEmits(['update:modelValue', 'blur'])

const handleInput = (event) => {
  // Auto uppercase
  emit('update:modelValue', event.target.value.toUpperCase())
}

const inputClasses = computed(() => {
  if (props.error) {
    // Error: red border, no focus ring
    return 'border-red-500'
  }
  if (props.valid) {
    // Valid: blue border with focus ring
    return 'border-blue-500 focus:ring-2 focus:ring-blue-500'
  }
  // Default: blue border with focus ring
  return 'border-blue-200 dark:border-blue-700 focus:ring-2 focus:ring-blue-500'
})

const iconClasses = computed(() => {
  if (props.error) return 'text-red-500'
  return 'text-blue-600 dark:text-blue-400'
})
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
