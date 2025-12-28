/**
 * @component DashboardPendaftar
 * @description Section pendaftar baru untuk dashboard dengan animated counter
 */

<template>
  <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <!-- Header -->
    <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center">
          <Bell class="w-5 h-5 text-orange-600 dark:text-orange-400" />
        </div>
        <div>
          <h3 class="text-lg font-bold text-gray-900 dark:text-white font-poppins">Pendaftar Baru</h3>
          <p class="text-sm text-gray-600 dark:text-gray-400">
            <span class="animated-number">{{ displayCount }}</span> Pendaftar Menunggu Persetujuan
          </p>
        </div>
      </div>
      <router-link to="/yasmin-panel/pendaftar" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline font-poppins flex items-center gap-1">
        Lihat Semua
        <ArrowRight class="w-4 h-4" />
      </router-link>
    </div>

    <!-- List Pendaftar -->
    <TransitionGroup name="list" tag="div" class="divide-y divide-gray-200 dark:divide-gray-700">
      <div v-for="pendaftar in items" :key="pendaftar.id" class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
        <div class="flex items-start justify-between gap-4">
          <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
              <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white font-bold">
                {{ pendaftar.nama[0].toUpperCase() }}
              </div>
              <div>
                <h4 class="font-semibold text-gray-900 dark:text-white font-poppins">{{ pendaftar.nama }}</h4>
                <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                  <GraduationCap class="w-4 h-4" />
                  <span>{{ pendaftar.kelas }}</span>
                </div>
              </div>
            </div>
            <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400 ml-13">
              <div class="flex items-center gap-1">
                <Target class="w-4 h-4" />
                <span>{{ pendaftar.ekskul }}</span>
              </div>
              <div class="flex items-center gap-1">
                <Clock class="w-4 h-4" />
                <span>{{ pendaftar.waktu }}</span>
              </div>
            </div>
          </div>
          <span class="px-3 py-1.5 bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-300 text-sm font-medium rounded-full font-poppins">
            Menunggu Persetujuan
          </span>
        </div>
      </div>
    </TransitionGroup>

    <!-- Empty State -->
    <div v-if="items.length === 0" class="p-12 text-center">
      <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
        <ClipboardList class="w-8 h-8 text-gray-400" />
      </div>
      <p class="text-gray-600 dark:text-gray-400 font-poppins">Tidak ada pendaftar baru</p>
    </div>
  </div>
</template>

<script setup>
import { ArrowRight, Bell, ClipboardList, Clock, GraduationCap, Target } from 'lucide-vue-next'
import { ref, watch } from 'vue'

const props = defineProps({
  items: { type: Array, required: true },
  pendingCount: { type: Number, default: 0 }
})

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

/* Animated number pop effect when value changes */
.animated-number {
  display: inline-block;
  transition: transform 0.3s ease-out;
}

/* List transition animations */
.list-enter-active {
  animation: slide-in 0.4s ease-out;
}

.list-leave-active {
  animation: slide-out 0.3s ease-in;
}

.list-move {
  transition: transform 0.3s ease;
}

@keyframes slide-in {
  0% {
    opacity: 0;
    transform: translateX(-20px);
  }
  100% {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes slide-out {
  0% {
    opacity: 1;
    transform: translateX(0);
  }
  100% {
    opacity: 0;
    transform: translateX(20px);
  }
}
</style>
