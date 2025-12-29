<!--
  @component DateWheelPicker
  @description iOS-style wheel date picker with scrollable day, month, year columns
-->

<template>
  <!-- Trigger Button -->
  <div 
    @click="openPicker" 
    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white cursor-pointer flex items-center justify-between hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
  >
    <span :class="modelValue ? 'text-gray-900 dark:text-white' : 'text-gray-400'">
      {{ displayValue }}
    </span>
    <Calendar class="w-4 h-4 text-gray-400" />
  </div>

  <!-- Modal Backdrop -->
  <Teleport to="body">
    <Transition name="fade">
      <div 
        v-if="isOpen" 
        class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
        @click.self="cancel"
      >
        <!-- Picker Modal -->
        <div class="bg-white dark:bg-gray-800 w-full max-w-sm rounded-2xl shadow-2xl overflow-hidden">
          <!-- Header -->
          <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 text-center">
            <span class="font-semibold text-gray-900 dark:text-white">Tanggal Lahir</span>
          </div>

          <!-- Picker Wheels -->
          <div class="flex items-center justify-center py-4 relative">
            <!-- Selection Highlight -->
            <div class="absolute left-4 right-4 h-10 bg-blue-50 dark:bg-blue-900/30 rounded-lg pointer-events-none"></div>
            
            <!-- Day Column -->
            <div class="relative w-20 h-48 overflow-hidden">
              <div 
                ref="dayScroll"
                class="h-full overflow-y-scroll snap-y snap-mandatory scroll-smooth hide-scrollbar"
                @scroll="onScroll('day', $event)"
              >
                <div class="h-[76px]"></div>
                <div 
                  v-for="d in daysInMonth" 
                  :key="d"
                  class="h-10 flex items-center justify-center snap-center text-lg font-medium transition-all"
                  :class="d === selectedDay ? 'text-gray-900 dark:text-white scale-110' : 'text-gray-400 scale-90'"
                >
                  {{ d }}
                </div>
                <div class="h-[76px]"></div>
              </div>
            </div>

            <!-- Month Column -->
            <div class="relative w-28 h-48 overflow-hidden">
              <div 
                ref="monthScroll"
                class="h-full overflow-y-scroll snap-y snap-mandatory scroll-smooth hide-scrollbar"
                @scroll="onScroll('month', $event)"
              >
                <div class="h-[76px]"></div>
                <div 
                  v-for="(m, idx) in months" 
                  :key="idx"
                  class="h-10 flex items-center justify-center snap-center text-lg font-medium transition-all"
                  :class="idx === selectedMonth ? 'text-gray-900 dark:text-white scale-110' : 'text-gray-400 scale-90'"
                >
                  {{ m }}
                </div>
                <div class="h-[76px]"></div>
              </div>
            </div>

            <!-- Year Column -->
            <div class="relative w-24 h-48 overflow-hidden">
              <div 
                ref="yearScroll"
                class="h-full overflow-y-scroll snap-y snap-mandatory scroll-smooth hide-scrollbar"
                @scroll="onScroll('year', $event)"
              >
                <div class="h-[76px]"></div>
                <div 
                  v-for="y in years" 
                  :key="y"
                  class="h-10 flex items-center justify-center snap-center text-lg font-medium transition-all"
                  :class="y === selectedYear ? 'text-gray-900 dark:text-white scale-110' : 'text-gray-400 scale-90'"
                >
                  {{ y }}
                </div>
                <div class="h-[76px]"></div>
              </div>
            </div>
          </div>

          <!-- Footer Buttons -->
          <div class="flex border-t border-gray-200 dark:border-gray-700">
            <button @click="cancel" class="flex-1 py-3 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition-colors">
              Batal
            </button>
            <div class="w-px bg-gray-200 dark:bg-gray-700"></div>
            <button @click="confirm" class="flex-1 py-3 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 font-semibold transition-colors">
              OK
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { Calendar } from 'lucide-vue-next'
import { computed, nextTick, ref, watch } from 'vue'

const props = defineProps({
  modelValue: { type: String, default: '' }, // Format: YYYY-MM-DD
  minYear: { type: Number, default: 1990 },
  maxYear: { type: Number, default: () => new Date().getFullYear() - 12 }
})

const emit = defineEmits(['update:modelValue'])

const isOpen = ref(false)
const dayScroll = ref(null)
const monthScroll = ref(null)
const yearScroll = ref(null)

const selectedDay = ref(1)
const selectedMonth = ref(0) // 0-indexed
const selectedYear = ref(2007)

const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']

const years = computed(() => {
  const result = []
  for (let y = props.minYear; y <= props.maxYear; y++) {
    result.push(y)
  }
  return result
})

const daysInMonth = computed(() => {
  const days = new Date(selectedYear.value, selectedMonth.value + 1, 0).getDate()
  return Array.from({ length: days }, (_, i) => i + 1)
})

const displayValue = computed(() => {
  if (!props.modelValue) return 'Pilih tanggal lahir'
  const d = new Date(props.modelValue)
  return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`
})

const openPicker = () => {
  // Parse current value
  if (props.modelValue) {
    const d = new Date(props.modelValue)
    selectedDay.value = d.getDate()
    selectedMonth.value = d.getMonth()
    selectedYear.value = d.getFullYear()
  } else {
    // Default: 15 years ago
    selectedDay.value = 1
    selectedMonth.value = 0
    selectedYear.value = new Date().getFullYear() - 15
  }
  
  isOpen.value = true
  
  nextTick(() => {
    scrollToSelected()
  })
}

const scrollToSelected = () => {
  const itemHeight = 40 // h-10 = 40px
  
  if (dayScroll.value) {
    dayScroll.value.scrollTop = (selectedDay.value - 1) * itemHeight
  }
  if (monthScroll.value) {
    monthScroll.value.scrollTop = selectedMonth.value * itemHeight
  }
  if (yearScroll.value) {
    const yearIndex = years.value.indexOf(selectedYear.value)
    yearScroll.value.scrollTop = yearIndex * itemHeight
  }
}

const onScroll = (type, event) => {
  const scrollTop = event.target.scrollTop
  const itemHeight = 40
  const index = Math.round(scrollTop / itemHeight)
  
  if (type === 'day') {
    selectedDay.value = Math.min(index + 1, daysInMonth.value.length)
  } else if (type === 'month') {
    selectedMonth.value = Math.min(index, 11)
  } else if (type === 'year') {
    selectedYear.value = years.value[Math.min(index, years.value.length - 1)]
  }
}

const cancel = () => {
  isOpen.value = false
}

const confirm = () => {
  // Clamp day if month changed
  const maxDay = daysInMonth.value.length
  const day = Math.min(selectedDay.value, maxDay)
  
  const month = String(selectedMonth.value + 1).padStart(2, '0')
  const dayStr = String(day).padStart(2, '0')
  
  emit('update:modelValue', `${selectedYear.value}-${month}-${dayStr}`)
  isOpen.value = false
}

// Watch for day overflow when month/year changes
watch([selectedMonth, selectedYear], () => {
  const maxDay = daysInMonth.value.length
  if (selectedDay.value > maxDay) {
    selectedDay.value = maxDay
  }
})
</script>

<style scoped>
.hide-scrollbar {
  scrollbar-width: none;
  -ms-overflow-style: none;
}
.hide-scrollbar::-webkit-scrollbar {
  display: none;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
