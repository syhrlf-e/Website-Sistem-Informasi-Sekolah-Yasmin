/**
 * @component BeritaFormSettings
 * @description Settings form untuk berita: status publikasi, featured, dan action buttons
 * @redesign 27 Des 2024 - Button-style toggles, Indonesian labels
 */

<template>
  <div class="space-y-6">
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
      <!-- Status Row: Publikasi (left) + Berita Unggulan (right) -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <!-- Status Publikasi -->
        <div>
          <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">
            Status Publikasi
          </label>
          <div class="flex items-center gap-2">
            <button
              type="button"
              @click="updateField('published', true)"
              :class="[
                'px-4 py-2 rounded-full font-medium text-sm transition-all duration-200 font-poppins',
                modelValue.published === true
                  ? 'bg-green-500 text-white shadow-md'
                  : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600'
              ]"
            >
              Terbit
            </button>
            <button
              type="button"
              @click="updateField('published', false)"
              :class="[
                'px-4 py-2 rounded-full font-medium text-sm transition-all duration-200 font-poppins',
                modelValue.published === false
                  ? 'bg-yellow-500 text-white shadow-md'
                  : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600'
              ]"
            >
              Draf
            </button>
          </div>
        </div>

        <!-- Berita Unggulan -->
        <div>
          <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">
            Sorotan
          </label>
          <button
            type="button"
            @click="updateField('is_featured', !modelValue.is_featured)"
            :class="[
              'px-4 py-2 rounded-full font-medium text-sm transition-all duration-200 font-poppins flex items-center gap-2',
              modelValue.is_featured
                ? 'bg-blue-500 text-white shadow-md'
                : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600'
            ]"
          >
            <Star :class="['w-4 h-4', modelValue.is_featured ? 'fill-current' : '']" />
            Berita Unggulan
          </button>
        </div>
      </div>
    </div>

    <div class="flex items-center gap-4">
      <button
        type="submit"
        :disabled="loading"
        class="flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white rounded-xl font-semibold transition-colors duration-200 shadow-lg hover:shadow-xl font-poppins"
      >
        <Save class="w-5 h-5" />
        {{ loading ? 'Menyimpan...' : isEdit ? 'Update Berita' : 'Simpan Berita' }}
      </button>
      <router-link
        to="/yasmin-panel/berita"
        class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl font-semibold transition-colors duration-200 font-poppins"
      >
        Batal
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { Save, Star } from 'lucide-vue-next'

const props = defineProps({
  modelValue: {
    type: Object,
    required: true
  },
  loading: {
    type: Boolean,
    default: false
  },
  isEdit: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue'])

const updateField = (field, value) => {
  emit('update:modelValue', { ...props.modelValue, [field]: value })
}
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
