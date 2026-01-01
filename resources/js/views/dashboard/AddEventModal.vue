/**
 * @component AddEventModal
 * @description Modal form to add new calendar event
 */

<template>
  <div class="fixed inset-0 bg-black/50 z-[60] flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-md shadow-xl">
      <!-- Header -->
      <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white font-poppins">Tambah Event</h3>
        <button @click="$emit('close')" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
          <X class="w-5 h-5 text-gray-500" />
        </button>
      </div>

      <!-- Form -->
      <form @submit.prevent="handleSubmit" class="p-5 space-y-4">
        <!-- Title -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Judul Event *</label>
          <input 
            v-model="form.title"
            type="text"
            required
            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
            placeholder="Contoh: Deadline PPDB Gel 1"
          />
        </div>

        <!-- Date -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal *</label>
          <input 
            v-model="form.event_date"
            type="date"
            required
            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
          />
        </div>

        <!-- Time (Optional) -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Waktu (Opsional)</label>
          <input 
            v-model="form.event_time"
            type="time"
            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
          />
        </div>

        <!-- Color -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Warna</label>
          <div class="flex gap-2">
            <button 
              v-for="color in colors" 
              :key="color.value"
              type="button"
              @click="form.color = color.value"
              :class="[
                'w-8 h-8 rounded-full transition-all',
                color.class,
                form.color === color.value ? 'ring-2 ring-offset-2 ring-gray-400 scale-110' : ''
              ]"
            />
          </div>
        </div>

        <!-- Description (Optional) -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi (Opsional)</label>
          <textarea 
            v-model="form.description"
            rows="2"
            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none"
            placeholder="Catatan tambahan..."
          />
        </div>

        <!-- Actions -->
        <div class="flex gap-3 pt-2">
          <button 
            type="button"
            @click="$emit('close')"
            class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
          >
            Batal
          </button>
          <button 
            type="submit"
            :disabled="isSubmitting"
            class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition-colors disabled:opacity-50"
          >
            {{ isSubmitting ? 'Menyimpan...' : 'Simpan' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import api from '@/services/api'
import { X } from 'lucide-vue-next'
import { reactive, ref } from 'vue'

const props = defineProps({
  selectedDate: { type: String, default: null }
})

const emit = defineEmits(['close', 'saved'])

const isSubmitting = ref(false)

const form = reactive({
  title: '',
  event_date: props.selectedDate || new Date().toISOString().split('T')[0],
  event_time: '',
  color: 'blue',
  description: ''
})

const colors = [
  { value: 'blue', class: 'bg-blue-500' },
  { value: 'green', class: 'bg-green-500' },
  { value: 'red', class: 'bg-red-500' },
  { value: 'yellow', class: 'bg-yellow-500' },
  { value: 'purple', class: 'bg-purple-500' },
  { value: 'orange', class: 'bg-orange-500' }
]

const handleSubmit = async () => {
  isSubmitting.value = true
  try {
    const payload = {
      title: form.title,
      event_date: form.event_date,
      color: form.color,
      description: form.description || null,
      event_time: form.event_time || null
    }
    
    const response = await api.post('/yasmin-panel/calendar/events', payload)
    if (response.data.success) {
      emit('saved', response.data.data)
    }
  } catch (error) {
    console.error('Failed to save event:', error)
    alert('Gagal menyimpan event')
  } finally {
    isSubmitting.value = false
  }
}
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
</style>
