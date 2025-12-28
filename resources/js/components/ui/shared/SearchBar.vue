<!--
  @component SearchBar
  @description Input search dengan debounce, clear button, dan v-model support
  @props {String} modelValue - v-model value
  @props {String} placeholder - Placeholder text
  @props {Number} debounce - Delay sebelum emit search (default: 500ms)
  @emits update:modelValue - Untuk v-model
  @emits search - Setelah debounce selesai
-->

<template>
  <div class="relative">
    <div class="relative">
      <Search class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />

      <input
        v-model="searchValue"
        type="text"
        :placeholder="placeholder"
        class="w-full pl-12 pr-12 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
        @input="handleInput"
      />

      <button
        v-if="searchValue"
        @click="clearSearch"
        class="absolute right-4 top-1/2 transform -translate-y-1/2 p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors duration-200"
      >
        <X class="w-4 h-4 text-gray-400" />
      </button>
    </div>
  </div>
</template>

<script setup>
import { Search, X } from 'lucide-vue-next'
import { ref, watch } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Cari berita...'
  },
  debounce: {
    type: Number,
    default: 500
  }
})

const emit = defineEmits(['update:modelValue', 'search'])

const searchValue = ref(props.modelValue)
let debounceTimeout = null

watch(
  () => props.modelValue,
  (newValue) => {
    searchValue.value = newValue
  }
)

const handleInput = () => {
  if (debounceTimeout) {
    clearTimeout(debounceTimeout)
  }

  emit('update:modelValue', searchValue.value)

  debounceTimeout = setTimeout(() => {
    emit('search', searchValue.value)
  }, props.debounce)
}

const clearSearch = () => {
  searchValue.value = ''
  emit('update:modelValue', '')
  emit('search', '')
}
</script>
