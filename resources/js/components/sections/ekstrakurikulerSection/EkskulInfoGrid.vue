<!--
  @component EkskulInfoGrid
  @description Grid info Jadwal/Tempat/Pembina dengan responsive mobile (vertical) dan desktop (horizontal) layout
  @props {Object} data - Ekskul data { schedule, location, mentor }
-->

<template>
  <div class="mb-6 flex-shrink-0">
    <!-- Mobile: Vertical Layout -->
    <div class="md:hidden border-y border-gray-300 dark:border-gray-700 py-4 space-y-4">
      <EkskulInfoItem v-if="data.schedule" :icon="Calendar" label="Jadwal" :value="data.schedule" />
      <EkskulInfoItem v-if="data.location" :icon="MapPin" label="Tempat" :value="data.location" />
      <EkskulInfoItem v-if="data.mentor" :icon="User" label="Pembina" :value="data.mentor" />
    </div>

    <!-- Desktop: Horizontal Grid Layout (3 columns only) -->
    <div class="hidden md:block border-y border-gray-300 dark:border-gray-700">
      <div
        class="flex justify-center items-stretch text-center divide-x divide-gray-300 dark:divide-gray-700 py-6"
      >
        <EkskulInfoColumn v-if="data.schedule" :icon="Calendar" label="Jadwal" :value="data.schedule" />
        <EkskulInfoColumn v-if="data.location" :icon="MapPin" label="Tempat" :value="data.location" />
        <EkskulInfoColumn v-if="data.mentor" :icon="User" label="Pembina" :value="data.mentor" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { Calendar, MapPin, User } from 'lucide-vue-next'
import EkskulInfoColumn from './EkskulInfoColumn.vue'
import EkskulInfoItem from './EkskulInfoItem.vue'

defineProps({
  data: {
    type: Object,
    required: true
  }
})
</script>
