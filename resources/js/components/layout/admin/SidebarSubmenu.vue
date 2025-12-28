<!--
  @component SidebarSubmenu
  @description Collapsible submenu dengan animated expand/collapse transition
  @props {Component} icon - Lucide icon component untuk parent menu
  @props {String} label - Parent menu label
  @props {Array} items - Child items: [{ to: String, icon: Component, label: String }]
  @props {Boolean} isActive - True jika ada child yang active (auto-expand)
  @props {Boolean} defaultOpen - Initial open state
-->
<template>
  <div>
    <button
      @click="isOpen = !isOpen"
      class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-colors duration-200"
      :class="
        isActive
          ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 font-semibold'
          : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
      "
    >
      <div class="flex items-center gap-3">
        <component :is="icon" class="w-5 h-5" />
        <span class="font-medium font-poppins">{{ label }}</span>
      </div>
      <ChevronDown
        :class="isOpen ? 'rotate-180' : ''"
        class="w-4 h-4 transition-transform duration-200"
      />
    </button>
    
    <Transition
      enter-active-class="transition-all duration-200 ease-out"
      enter-from-class="opacity-0 max-h-0"
      enter-to-class="opacity-100 max-h-96"
      leave-active-class="transition-all duration-150 ease-in"
      leave-from-class="opacity-100 max-h-96"
      leave-to-class="opacity-0 max-h-0"
    >
      <div v-show="isOpen" class="ml-4 mt-2 space-y-1 overflow-hidden">
        <router-link
          v-for="item in items"
          :key="item.to"
          :to="item.to"
          class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200"
          active-class="bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white font-semibold"
        >
          <component :is="item.icon" class="w-4 h-4" />
          <span class="font-poppins">{{ item.label }}</span>
        </router-link>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ChevronDown } from 'lucide-vue-next'
import { ref, watch } from 'vue'

const props = defineProps({
  icon: {
    type: [Object, Function],
    required: true
  },
  label: {
    type: String,
    required: true
  },
  items: {
    type: Array,
    required: true
  },
  isActive: {
    type: Boolean,
    default: false
  },
  defaultOpen: {
    type: Boolean,
    default: true
  }
})

const isOpen = ref(props.defaultOpen)

/**
 * Watcher: auto-expand submenu ketika ada child yang active
 * Prevents closed submenu when user navigates directly to child route
 */
watch(
  () => props.isActive,
  (active) => {
    if (active) isOpen.value = true
  },
  { immediate: true }
)
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
