/**
 * @component DashboardPendaftar
 * @description Section pendaftar ekskul untuk dashboard dengan layout matching PPDB Overview
 */

<template>
  <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden h-full">
    <!-- Header -->
    <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center">
          <Target class="w-5 h-5 text-orange-600 dark:text-orange-400" />
        </div>
        <div>
          <h3 class="text-base font-bold text-gray-900 dark:text-white font-poppins">Pendaftar Ekskul</h3>
          <p class="text-xs text-gray-500 dark:text-gray-400 font-poppins">Ekstrakurikuler</p>
        </div>
      </div>
      <router-link 
        to="/yasmin-panel/pendaftar" 
        class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline font-poppins flex items-center gap-1"
      >
        Lihat Semua
        <ArrowRight class="w-4 h-4" />
      </router-link>
    </div>

    <!-- Content -->
    <div class="p-5">
      <!-- Mini Stats (matching main dashboard style) -->
      <div class="grid grid-cols-3 gap-3 mb-5">
        <!-- Pending -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
          <div class="flex items-center justify-between mb-2">
            <div class="w-8 h-8 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 flex items-center justify-center">
              <Clock class="w-4 h-4 text-yellow-600 dark:text-yellow-400" />
            </div>
            <span class="text-xs font-medium text-yellow-600 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-900/20 px-2 py-0.5 rounded-full">Pending</span>
          </div>
          <h3 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">{{ displayCount }}</h3>
        </div>
        <!-- Diterima -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
          <div class="flex items-center justify-between mb-2">
            <div class="w-8 h-8 rounded-lg bg-green-50 dark:bg-green-900/20 flex items-center justify-center">
              <CheckCircle class="w-4 h-4 text-green-600 dark:text-green-400" />
            </div>
            <span class="text-xs font-medium text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 px-2 py-0.5 rounded-full">Diterima</span>
          </div>
          <h3 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">{{ approvedCount }}</h3>
        </div>
        <!-- Ditolak -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
          <div class="flex items-center justify-between mb-2">
            <div class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/20 flex items-center justify-center">
              <XCircle class="w-4 h-4 text-red-600 dark:text-red-400" />
            </div>
            <span class="text-xs font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 px-2 py-0.5 rounded-full">Ditolak</span>
          </div>
          <h3 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">{{ rejectedCount }}</h3>
        </div>
      </div>

      <!-- Recent Registrants -->
      <div>
        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Pendaftar Terbaru</h4>
        <div v-if="items.length" class="space-y-2">
          <div 
            v-for="pendaftar in items.slice(0, 3)" 
            :key="pendaftar.id"
            class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg"
          >
            <div>
              <p class="text-sm font-medium text-gray-900 dark:text-white">{{ pendaftar.nama }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400">{{ pendaftar.ekskul }}</p>
            </div>
            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 text-xs font-medium rounded-full">
              Menunggu
            </span>
          </div>
          <!-- More registrants count -->
          <p v-if="remainingCount > 0" class="text-xs text-center text-gray-500 dark:text-gray-400 pt-2">
            dan {{ remainingCount }} pendaftar lainnya
          </p>
        </div>
        <div v-else class="text-center py-6">
          <ClipboardList class="w-8 h-8 text-gray-300 dark:text-gray-600 mx-auto mb-2" />
          <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada pendaftar baru hari ini</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ArrowRight, CheckCircle, ClipboardList, Clock, Target, XCircle } from 'lucide-vue-next'
import { computed, ref, watch } from 'vue'

const props = defineProps({
  items: { type: Array, required: true },
  pendingCount: { type: Number, default: 0 },
  approvedCount: { type: Number, default: 0 },
  rejectedCount: { type: Number, default: 0 }
})

// Computed remaining count
const remainingCount = computed(() => Math.max(0, props.items.length - 3))

// Animated display count
const displayCount = ref(props.pendingCount)

// Animate number when pendingCount changes
watch(() => props.pendingCount, (newVal) => {
  const oldVal = displayCount.value
  if (newVal === oldVal) return
  
  const diff = Math.abs(newVal - oldVal)
  
  // For small changes (1-2), update instantly - no flicker
  if (diff <= 2) {
    displayCount.value = newVal
    return
  }
  
  // For larger changes, animate smoothly
  const start = oldVal
  const end = newVal
  const duration = 400
  const steps = Math.min(diff, 10) // Max 10 steps
  const increment = (end - start) / steps
  let current = start
  let step = 0

  const interval = setInterval(() => {
    step++
    current += increment
    if (step >= steps) {
      displayCount.value = end
      clearInterval(interval)
    } else {
      displayCount.value = Math.round(current)
    }
  }, duration / steps)
}, { immediate: true })
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
