<!--
  @component BenefitsList
  @description List benefits ekstrakurikuler dengan checkmark icons
  @props {Array} benefits - Array of benefit strings
-->

<template>
  <div v-if="benefits && benefits.length > 0" class="mb-6 flex-grow">
    <h4 class="text-lg font-bold text-gray-800 dark:text-white mb-3 font-poppins">
      Yang Kamu Dapatkan
    </h4>
    <ul class="space-y-2">
      <li
        v-for="(benefit, index) in benefits"
        :key="index"
        class="flex items-start gap-2"
      >
        <Check
          class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0 mt-0.5"
        />
        <span class="text-sm text-gray-700 dark:text-gray-400 font-poppins">{{ benefit }}</span>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { Check } from 'lucide-vue-next'

defineProps({
  benefits: {
    type: Array,
    default: () => []
  }
})
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
