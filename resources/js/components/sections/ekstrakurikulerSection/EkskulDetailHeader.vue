<!--
  @component OverlayHeader
  @description Header section untuk overlay dengan title, badge, dan subtitle
  @props {Object} data - Ekskul data { name, category, badgeVariant, subtitle }
-->

<template>
  <div class="flex-shrink-0 p-5 md:p-8 pb-3 md:pb-4">
    <div class="md:hidden mb-3">
      <BaseBadge :label="data.category" :variant="data.badgeVariant" />
    </div>

    <div class="md:hidden mb-3">
      <h3 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white font-poppins">
        {{ data.name }}
      </h3>
    </div>

    <div class="hidden md:flex items-center gap-3 mb-3 flex-wrap">
      <h3 class="text-3xl font-bold text-gray-900 dark:text-white font-poppins">
        {{ data.name }}
      </h3>
      <BaseBadge :label="data.category" :variant="data.badgeVariant" />
    </div>

    <p class="text-sm md:text-base font-reguler text-gray-700 dark:text-gray-300 font-poppins">
      "{{ data.subtitle }}"
    </p>
  </div>
</template>

<script setup>
import BaseBadge from '@/components/ui/shared/BaseBadge.vue'

defineProps({
  data: {
    type: Object,
    required: true
  }
})
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
