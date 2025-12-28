/**
 * @component GaleriGrid
 * @description Grid layout 9 slot untuk galeri homepage dengan shimmer skeleton
 */

<template>
  <!-- Loading State with Shimmer Effect -->
  <div v-if="loading" class="grid grid-cols-2 lg:grid-cols-4 gap-6">
    <div
      v-for="i in 9"
      :key="i"
      class="skeleton-shimmer rounded-2xl overflow-hidden"
      :class="[1, 2, 8].includes(i) ? 'col-span-2' : 'col-span-1'"
      style="height: 342px"
    >
      <div class="shimmer-effect"></div>
    </div>
  </div>

  <!-- Grid Layout -->
  <div v-else class="grid grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- BARIS 1 -->
    <GridSlot
      :slot-number="1"
      :gallery="galleries[0]"
      :is-available="availableSlots[1]"
      :is-uploading="uploadingSlot === 1"
      span-class="col-span-2"
      @upload="$emit('upload', 1)"
      @edit="$emit('edit', $event)"
      @delete="$emit('delete', $event)"
    />
    <GridSlot
      :slot-number="2"
      :gallery="galleries[1]"
      :is-available="availableSlots[2]"
      :is-uploading="uploadingSlot === 2"
      span-class="col-span-2"
      @upload="$emit('upload', 2)"
      @edit="$emit('edit', $event)"
      @delete="$emit('delete', $event)"
    />

    <!-- BARIS 2 -->
    <GridSlot
      v-for="i in [3, 4, 5, 6]"
      :key="i"
      :slot-number="i"
      :gallery="galleries[i - 1]"
      :is-available="availableSlots[i]"
      :is-uploading="uploadingSlot === i"
      span-class="col-span-1"
      @upload="$emit('upload', i)"
      @edit="$emit('edit', $event)"
      @delete="$emit('delete', $event)"
    />

    <!-- BARIS 3 -->
    <GridSlot
      :slot-number="7"
      :gallery="galleries[6]"
      :is-available="availableSlots[7]"
      :is-uploading="uploadingSlot === 7"
      span-class="col-span-1"
      @upload="$emit('upload', 7)"
      @edit="$emit('edit', $event)"
      @delete="$emit('delete', $event)"
    />
    <GridSlot
      :slot-number="8"
      :gallery="galleries[7]"
      :is-available="availableSlots[8]"
      :is-uploading="uploadingSlot === 8"
      span-class="col-span-2"
      @upload="$emit('upload', 8)"
      @edit="$emit('edit', $event)"
      @delete="$emit('delete', $event)"
    />
    <GridSlot
      :slot-number="9"
      :gallery="galleries[8]"
      :is-available="availableSlots[9]"
      :is-uploading="uploadingSlot === 9"
      span-class="col-span-1"
      @upload="$emit('upload', 9)"
      @edit="$emit('edit', $event)"
      @delete="$emit('delete', $event)"
    />
  </div>
</template>

<script setup>
import GridSlot from '../GridSlot.vue'

defineProps({
  loading: Boolean,
  galleries: { type: Array, required: true },
  availableSlots: { type: Object, required: true },
  uploadingSlot: { type: Number, default: null }
})

defineEmits(['upload', 'edit', 'delete'])
</script>

<style scoped>
/* Skeleton with Shimmer Effect */
.skeleton-shimmer {
  position: relative;
  background: linear-gradient(90deg, #e5e7eb 0%, #f3f4f6 50%, #e5e7eb 100%);
  background-size: 200% 100%;
}

:global(.dark) .skeleton-shimmer {
  background: linear-gradient(90deg, #374151 0%, #4b5563 50%, #374151 100%);
  background-size: 200% 100%;
}

/* Shimmer sweep effect */
.shimmer-effect {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(
    90deg,
    transparent 0%,
    rgba(255, 255, 255, 0.4) 50%,
    transparent 100%
  );
  animation: shimmer 1.5s infinite;
}

:global(.dark) .shimmer-effect {
  background: linear-gradient(
    90deg,
    transparent 0%,
    rgba(255, 255, 255, 0.1) 50%,
    transparent 100%
  );
}

@keyframes shimmer {
  0% {
    transform: translateX(-100%);
  }
  100% {
    transform: translateX(100%);
  }
}
</style>
