<template>
  <Transition name="modal">
    <div
      v-if="show"
      class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
    >
      <div
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full p-6 border border-gray-200 dark:border-gray-700"
      >
        <!-- Icon -->
        <div class="flex justify-center mb-4">
          <div
            class="w-16 h-16 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center"
          >
            <Clock class="w-8 h-8 text-yellow-600 dark:text-yellow-400" />
          </div>
        </div>
        <!-- Title -->
        <h3 class="text-xl font-bold text-gray-900 dark:text-white text-center mb-2">
          Sesi Akan Berakhir
        </h3>
        <!-- Message -->
        <p class="text-gray-600 dark:text-gray-400 text-center mb-6">
          Anda tidak aktif selama beberapa waktu. Sesi akan otomatis logout dalam
          <span class="font-bold text-yellow-600 dark:text-yellow-400">
            {{ formatTime(remainingSeconds) }}
          </span>
        </p>
        <!-- Progress Bar -->
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mb-6 overflow-hidden">
          <div
            class="bg-gradient-to-r from-yellow-500 to-orange-500 h-2 rounded-full transition-all duration-1000"
            :style="{ width: progressPercentage + '%' }"
          ></div>
        </div>
        <!-- Actions -->
        <div class="flex gap-3">
          <button
            @click="$emit('extend')"
            class="flex-1 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center gap-2"
          >
            <RefreshCw class="w-5 h-5" />
            Tetap Login
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>
<script setup>
import { Clock, RefreshCw } from 'lucide-vue-next'
import { computed } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  remainingSeconds: {
    type: Number,
    default: 300
  }
})
defineEmits(['extend'])
// Format seconds to MM:SS
const formatTime = (seconds) => {
  const mins = Math.floor(seconds / 60)
  const secs = seconds % 60
  return `${mins}:${secs.toString().padStart(2, '0')}`
}
// Progress percentage (300 seconds = 100%)
const progressPercentage = computed(() => {
  return (props.remainingSeconds / 300) * 100
})
</script>
<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}
.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
.modal-enter-active .bg-white,
.modal-leave-active .bg-white {
  transition: transform 0.3s ease;
}
.modal-enter-from .bg-white,
.modal-leave-to .bg-white {
  transform: scale(0.9);
}
</style>
