<template>
  <div
    :class="[
      spanClass,
      'relative rounded-2xl overflow-hidden border-2 transition-all duration-300',
      'h-[342px]', // FIXED HEIGHT 342px - persis seperti FE!
      gallery
        ? 'border-gray-200 dark:border-gray-700 hover:shadow-xl'
        : isAvailable
          ? 'border-dashed border-purple-400 dark:border-purple-600 hover:border-purple-500 dark:hover:border-purple-500 cursor-pointer'
          : 'border-dashed border-gray-300 dark:border-gray-600 opacity-50 cursor-not-allowed'
    ]"
    @click="handleClick"
  >
    <!-- Filled Slot -->
    <div v-if="gallery" class="group relative w-full h-full">
      <!-- Image -->
      <img :src="gallery.image_url" :alt="gallery.title" class="w-full h-full object-cover" />
      <!-- Slot Badge -->
      <div class="absolute top-2 left-2 z-10">
        <span
          class="px-2 py-1 text-xs font-bold text-white bg-purple-600 rounded-lg font-poppins shadow-lg"
        >
          Slot {{ slotNumber }}
        </span>
      </div>
      <!-- Active Badge -->
      <div v-if="!gallery.is_active" class="absolute top-2 right-2 z-10">
        <span
          class="px-2 py-1 text-xs font-bold text-white bg-red-500 rounded-lg font-poppins shadow-lg"
        >
          Inactive
        </span>
      </div>
      <!-- Overlay with Actions -->
      <div
        class="absolute inset-0 bg-black/0 group-hover:bg-black/60 transition-all duration-300 flex items-center justify-center gap-2"
      >
        <!-- Edit Button -->
        <button
          @click.stop="$emit('edit', gallery)"
          class="opacity-0 group-hover:opacity-100 scale-0 group-hover:scale-100 transition-all duration-300 p-3 bg-blue-500/90 rounded-xl hover:bg-blue-600 backdrop-blur-sm"
          title="Edit"
        >
          <Pencil class="w-5 h-5 text-white" />
        </button>
        <!-- Delete Button -->
        <button
          @click.stop="$emit('delete', gallery)"
          class="opacity-0 group-hover:opacity-100 scale-0 group-hover:scale-100 transition-all duration-300 delay-50 p-3 bg-red-500/90 rounded-xl hover:bg-red-600 backdrop-blur-sm"
          title="Hapus"
        >
          <Trash2 class="w-5 h-5 text-white" />
        </button>
      </div>
      <!-- Caption Overlay -->
      <div
        class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/80 to-transparent"
      >
        <p class="text-white font-bold text-sm font-poppins line-clamp-1">
          {{ gallery.title }}
        </p>
        <p v-if="gallery.description" class="text-gray-200 text-xs font-poppins line-clamp-1 mt-1">
          {{ gallery.description }}
        </p>
      </div>
    </div>
    <!-- Empty Slot (Available) -->
    <div
      v-else-if="isAvailable"
      class="flex flex-col items-center justify-center h-full bg-purple-50 dark:bg-purple-900/10 hover:bg-purple-100 dark:hover:bg-purple-900/20 transition-colors"
    >
      <div v-if="isUploading" class="flex flex-col items-center">
        <LoadingSpinner size="lg" color="purple" text="Uploading..." />
      </div>
      <div v-else class="flex flex-col items-center">
        <div
          class="w-16 h-16 rounded-full bg-purple-100 dark:bg-purple-800 flex items-center justify-center mb-3"
        >
          <Upload class="w-8 h-8 text-purple-600 dark:text-purple-400" />
        </div>
        <p class="text-sm font-bold text-purple-700 dark:text-purple-300 font-poppins mb-1">
          Slot {{ slotNumber }}
        </p>
        <p class="text-xs text-purple-600 dark:text-purple-400 font-poppins">Klik untuk upload</p>
      </div>
    </div>
    <!-- Empty Slot (Locked) -->
    <div
      v-else
      class="flex flex-col items-center justify-center h-full bg-gray-50 dark:bg-gray-800/50"
    >
      <div
        class="w-16 h-16 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center mb-3"
      >
        <Lock class="w-8 h-8 text-gray-400" />
      </div>
      <p class="text-sm font-bold text-gray-500 dark:text-gray-400 font-poppins mb-1">
        Slot {{ slotNumber }}
      </p>
      <p class="text-xs text-gray-400 dark:text-gray-500 font-poppins text-center px-4">
        Lengkapi baris sebelumnya
      </p>
    </div>
  </div>
</template>
<script setup>
import LoadingSpinner from '@/components/ui/shared/LoadingSpinner.vue'
import { Lock, Pencil, Trash2, Upload } from 'lucide-vue-next'

// Props
const props = defineProps({
  slotNumber: {
    type: Number,
    required: true
  },
  gallery: {
    type: Object,
    default: null
  },
  isAvailable: {
    type: Boolean,
    default: false
  },
  isUploading: {
    type: Boolean,
    default: false
  },
  spanClass: {
    type: String,
    default: 'col-span-1'
  }
})
// Emits
const emit = defineEmits(['upload', 'edit', 'delete'])
// Handle click
const handleClick = () => {
  if (!props.gallery && props.isAvailable && !props.isUploading) {
    emit('upload')
  }
}
</script>
<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
