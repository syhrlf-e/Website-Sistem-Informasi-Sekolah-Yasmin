/**
 * @component GaleriInfoBox
 * @description Info box dan alert untuk halaman galeri
 */

<template>
  <div class="space-y-4">
    <!-- Alert Messages -->
    <Transition
      enter-active-class="transition-all duration-300"
      enter-from-class="opacity-0 -translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition-all duration-200"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-2"
    >
      <div
        v-if="alert.show"
        :class="alert.type === 'success'
          ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800 text-green-800 dark:text-green-200'
          : 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800 text-red-800 dark:text-red-200'"
        class="border rounded-2xl p-4 flex items-center justify-between"
      >
        <p class="font-medium font-poppins">{{ alert.text }}</p>
        <button @click="$emit('close-alert')" class="text-current opacity-70 hover:opacity-100">
          <X class="w-5 h-5" />
        </button>
      </div>
    </Transition>

    <!-- Info Box -->
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-2xl p-4">
      <div class="flex items-start gap-3">
        <Info class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0" />
        <div class="text-sm text-blue-800 dark:text-blue-200">
          <p class="font-semibold mb-1">Aturan Upload Sequential:</p>
          <ul class="list-disc list-inside space-y-1 text-blue-700 dark:text-blue-300">
            <li>Baris 1 (Slot 1 & 2) harus diisi terlebih dahulu</li>
            <li>Baris 2 (Slot 3, 4, 5, 6) bisa diisi setelah Baris 1 penuh</li>
            <li>Baris 3 (Slot 7, 8, 9) bisa diisi setelah Baris 2 penuh</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Info, X } from 'lucide-vue-next'

defineProps({
  alert: {
    type: Object,
    default: () => ({ show: false, type: 'success', text: '' })
  }
})

defineEmits(['close-alert'])
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
