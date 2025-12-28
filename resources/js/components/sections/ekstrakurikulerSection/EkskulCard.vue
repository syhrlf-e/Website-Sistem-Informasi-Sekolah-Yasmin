<!--
  @component EkskulCard
  @description Card komponen untuk menampilkan preview ekstrakurikuler (no hover animation, fixed height)
  @props {Object} ekskul - Data ekskul { id, name, category, subtitle, description, bgClass, badgeVariant }
  @emits click - Ketika card diklik, emit ekskul object ke parent
-->

<template>
  <div
    @click="$emit('click', ekskul)"
    class="group relative overflow-hidden rounded-3xl shadow-sm cursor-pointer w-full sm:w-[calc(50%-12px)] lg:w-[calc(25%-18px)] h-[280px] sm:h-[300px]"
    :class="ekskul.bgClass"
  >
    <div class="p-6 sm:p-8 h-full flex flex-col">
      <!-- Badge only -->
      <div class="mb-3">
        <BaseBadge :label="ekskul.category" :variant="ekskul.badgeVariant" />
      </div>
      
      <h3 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white font-poppins mb-2 line-clamp-1">
        {{ ekskul.name }}
      </h3>
      <p class="text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 font-poppins mb-3 line-clamp-1">
        {{ ekskul.subtitle }}
      </p>
      <p
        class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 leading-relaxed font-poppins font-normal line-clamp-3"
      >
        {{ ekskul.description }}
      </p>
      
      <!-- Bottom row: Arrow only -->
      <div class="absolute bottom-5 right-5 sm:bottom-6 sm:right-6">
        <!-- Arrow Icon -->
        <div
          class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-gray-900/10 dark:bg-white/20 flex items-center justify-center overflow-hidden"
        >
          <ArrowUpRight
            class="w-4 h-4 sm:w-5 sm:h-5 text-gray-900 dark:text-white transition-all duration-300 group-hover:translate-x-3 group-hover:-translate-y-3 group-hover:opacity-0"
          />
          <ArrowUpRight
            class="w-4 h-4 sm:w-5 sm:h-5 text-gray-900 dark:text-white absolute -translate-x-3 translate-y-3 opacity-0 transition-all duration-300 group-hover:translate-x-0 group-hover:translate-y-0 group-hover:opacity-100"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import BaseBadge from '@/components/ui/shared/BaseBadge.vue'
import { ArrowUpRight } from 'lucide-vue-next'
import { computed } from 'vue'

const props = defineProps({
  ekskul: {
    type: Object,
    required: true
  }
})

defineEmits(['click'])

/**
 * Computed: Dot color class based on slot availability
 * - Green: slots available
 * - Red: slots full
 * - Gray: registration closed (manual or deadline)
 */
const slotDotClass = computed(() => {
  if (!props.ekskul.enableRegistration) {
    return 'bg-gray-400 dark:bg-gray-500' // Closed
  }
  if (props.ekskul.isSlotFull) {
    return 'bg-red-500' // Full
  }
  return 'bg-green-500' // Available
})

/**
 * Computed: Tooltip text for dot hover
 */
const slotTooltip = computed(() => {
  if (!props.ekskul.enableRegistration) {
    return 'Pendaftaran ditutup'
  }
  if (props.ekskul.isSlotFull) {
    return 'Slot penuh'
  }
  return `Tersedia ${props.ekskul.availableSlots} dari ${props.ekskul.maxPeserta} slot`
})

/**
 * Computed: Short slot text for display
 */
const slotText = computed(() => {
  if (!props.ekskul.enableRegistration) {
    return 'Ditutup'
  }
  if (props.ekskul.isSlotFull) {
    return 'Penuh'
  }
  return `Tersedia ${props.ekskul.availableSlots}/${props.ekskul.maxPeserta}`
})
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}

/* Line clamp utilities - need both webkit prefix and standard for browser support */
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Slot dot pulse animation - same as pendaftar ekskul */
.slot-dot {
  animation: slot-pulse-glow 2s ease-in-out infinite;
}

@keyframes slot-pulse-glow {
  0%, 100% {
    box-shadow: 0 0 0 0 currentColor;
    transform: scale(1);
    opacity: 1;
  }
  50% {
    box-shadow: 0 0 0 4px transparent;
    transform: scale(1.15);
    opacity: 0.8;
  }
}
</style>
