/**
 * @component CalendarSidebar
 * @description Slide-in calendar sidebar with event management for admin dashboard
 */

<template>
  <!-- Sidebar (no overlay, positioned below header) -->
  <Transition name="slide">
    <div 
      v-if="isOpen"
      class="fixed right-0 top-[40px] h-[calc(100vh-40px)] w-[400px] bg-white dark:bg-gray-800 shadow-2xl z-40 flex flex-col border-l border-gray-200 dark:border-gray-700"
    >
      <!-- Header -->
      <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
            <CalendarDays class="w-5 h-5 text-blue-600 dark:text-blue-400" />
          </div>
          <h2 class="text-lg font-bold text-gray-900 dark:text-white font-poppins">Kalender</h2>
        </div>
        <button 
          @click="$emit('close')"
          class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
        >
          <X class="w-5 h-5 text-gray-500" />
        </button>
      </div>

      <!-- Calendar Grid -->
      <div class="p-5 border-b border-gray-200 dark:border-gray-700">
        <!-- Month Navigation -->
        <div class="flex items-center justify-between mb-4">
          <button @click="prevMonth" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <ChevronLeft class="w-5 h-5 text-gray-600 dark:text-gray-300" />
          </button>
          <h3 class="text-sm font-semibold text-gray-900 dark:text-white font-poppins">
            {{ monthYearLabel }}
          </h3>
          <button @click="nextMonth" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <ChevronRight class="w-5 h-5 text-gray-600 dark:text-gray-300" />
          </button>
        </div>

        <!-- Day Headers -->
        <div class="grid grid-cols-7 gap-1 mb-2">
          <div v-for="day in ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min']" :key="day"
            class="text-center text-xs font-medium text-gray-500 dark:text-gray-400 py-1">
            {{ day }}
          </div>
        </div>

        <!-- Calendar Days -->
        <div class="grid grid-cols-7 gap-1">
          <button 
            v-for="(day, index) in calendarDays" 
            :key="index"
            @click="day.date && selectDate(day.date)"
            :class="[
              'relative h-9 rounded-lg text-sm font-medium transition-colors',
              day.date ? 'hover:bg-blue-50 dark:hover:bg-blue-900/20 cursor-pointer' : 'cursor-default',
              day.isToday ? 'bg-blue-600 text-white hover:bg-blue-700' : 'text-gray-700 dark:text-gray-300',
              day.isSelected ? 'ring-2 ring-blue-500' : ''
            ]"
            :disabled="!day.date"
          >
            {{ day.day }}
            <!-- Event Dot -->
            <span 
              v-if="day.hasEvent" 
              class="absolute bottom-1 left-1/2 -translate-x-1/2 w-1.5 h-1.5 rounded-full bg-red-500"
            />
          </button>
        </div>
      </div>

      <!-- Agenda Section -->
      <div class="flex-1 overflow-y-auto p-5">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-sm font-semibold text-gray-900 dark:text-white font-poppins flex items-center gap-2">
            <Pin class="w-4 h-4" /> Agenda
          </h3>
          <button 
            @click="showAddModal = true"
            class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors flex items-center gap-1"
          >
            <Plus class="w-3 h-3" /> Tambah
          </button>
        </div>

        <!-- Event List -->
        <div v-if="events.length" class="space-y-2">
          <div 
            v-for="event in events" 
            :key="event.id"
            class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg group"
          >
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <div class="flex items-center gap-2">
                  <span :class="['w-2 h-2 rounded-full', colorClasses[event.color] || 'bg-blue-500']" />
                  <p class="text-sm font-medium text-gray-900 dark:text-white">{{ event.title }}</p>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                  {{ formatEventDate(event.event_date) }}
                  <span v-if="event.event_time"> • {{ event.event_time }}</span>
                </p>
              </div>
              <button 
                @click="deleteEvent(event.id)"
                class="p-1 opacity-0 group-hover:opacity-100 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition-all"
              >
                <Trash2 class="w-4 h-4 text-red-500" />
              </button>
            </div>
          </div>
        </div>
        <div v-else class="text-center py-8">
          <CalendarDays class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-2" />
          <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada agenda bulan ini</p>
        </div>
      </div>

      <!-- Add Event Modal -->
      <AddEventModal 
        v-if="showAddModal"
        :selected-date="selectedDate"
        @close="showAddModal = false"
        @saved="handleEventSaved"
      />
    </div>
  </Transition>
</template>

<script setup>
import api from '@/services/api'
import { CalendarDays, ChevronLeft, ChevronRight, Pin, Plus, Trash2, X } from 'lucide-vue-next'
import { computed, onMounted, ref, watch } from 'vue'
import AddEventModal from './AddEventModal.vue'

const props = defineProps({
  isOpen: { type: Boolean, default: false }
})

const emit = defineEmits(['close'])

const currentMonth = ref(new Date().getMonth())
const currentYear = ref(new Date().getFullYear())
const events = ref([])
const selectedDate = ref(null)
const showAddModal = ref(false)

const colorClasses = {
  blue: 'bg-blue-500',
  green: 'bg-green-500',
  red: 'bg-red-500',
  yellow: 'bg-yellow-500',
  purple: 'bg-purple-500',
  orange: 'bg-orange-500'
}

const monthYearLabel = computed(() => {
  const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                  'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
  return `${months[currentMonth.value]} ${currentYear.value}`
})

const calendarDays = computed(() => {
  const days = []
  const firstDay = new Date(currentYear.value, currentMonth.value, 1)
  const lastDay = new Date(currentYear.value, currentMonth.value + 1, 0)
  const today = new Date()
  
  // Adjust for Monday start (getDay() returns 0 for Sunday)
  let startPadding = firstDay.getDay() - 1
  if (startPadding < 0) startPadding = 6
  
  // Empty cells for days before month starts
  for (let i = 0; i < startPadding; i++) {
    days.push({ day: '', date: null })
  }
  
  // Actual days of the month
  for (let d = 1; d <= lastDay.getDate(); d++) {
    const date = new Date(currentYear.value, currentMonth.value, d)
    const dateStr = date.toISOString().split('T')[0]
    days.push({
      day: d,
      date: dateStr,
      isToday: date.toDateString() === today.toDateString(),
      isSelected: dateStr === selectedDate.value,
      hasEvent: events.value.some(e => e.event_date === dateStr)
    })
  }
  
  return days
})

const prevMonth = () => {
  if (currentMonth.value === 0) {
    currentMonth.value = 11
    currentYear.value--
  } else {
    currentMonth.value--
  }
}

const nextMonth = () => {
  if (currentMonth.value === 11) {
    currentMonth.value = 0
    currentYear.value++
  } else {
    currentMonth.value++
  }
}

const selectDate = (date) => {
  selectedDate.value = date
  showAddModal.value = true
}

const fetchEvents = async () => {
  try {
    const response = await api.get('/yasmin-panel/calendar/events', {
      params: { month: currentMonth.value + 1, year: currentYear.value }
    })
    if (response.data.success) {
      events.value = response.data.data
    }
  } catch (error) {
    console.error('Failed to fetch events:', error)
  }
}

const deleteEvent = async (id) => {
  if (!confirm('Hapus event ini?')) return
  try {
    await api.delete(`/yasmin-panel/calendar/events/${id}`)
    events.value = events.value.filter(e => e.id !== id)
  } catch (error) {
    console.error('Failed to delete event:', error)
  }
}

const handleEventSaved = (newEvent) => {
  events.value.push(newEvent)
  showAddModal.value = false
}

const formatEventDate = (dateStr) => {
  const date = new Date(dateStr)
  return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })
}

watch([currentMonth, currentYear], fetchEvents)
watch(() => props.isOpen, (isOpen) => {
  if (isOpen) fetchEvents()
})

onMounted(() => {
  if (props.isOpen) fetchEvents()
})
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }

.slide-enter-active, .slide-leave-active {
  transition: transform 0.3s ease;
}
.slide-enter-from, .slide-leave-to {
  transform: translateX(100%);
}
.slide-enter-to, .slide-leave-from {
  transform: translateX(0);
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
