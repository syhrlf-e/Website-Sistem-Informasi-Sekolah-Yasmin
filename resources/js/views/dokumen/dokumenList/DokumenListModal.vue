/**
 * @component DokumenListModal
 * @description Modal form untuk upload/edit dokumen PPDB
 */

<template>
  <Teleport to="body">
    <Transition>
      <div
        v-if="show"
        class="fixed inset-0 z-[9999] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
        @click.self="$emit('close')"
      >
        <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-2xl w-full border border-gray-200 dark:border-gray-700 shadow-2xl">
          <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">
              {{ editMode ? 'Edit Dokumen' : 'Upload Dokumen Baru' }}
            </h2>
            <button
              @click="$emit('close')"
              class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
            >
              <X class="w-5 h-5 text-gray-500" />
            </button>
          </div>
          <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                Nama Dokumen <span class="text-red-500">*</span>
              </label>
              <input
                v-model="localForm.title"
                type="text"
                required
                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white"
              />
            </div>
            <div v-if="!editMode">
              <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                File <span class="text-red-500">*</span>
              </label>
              <input
                ref="fileInput"
                type="file"
                accept=".pdf,.doc,.docx,.xls,.xlsx"
                @change="handleFileUpload"
                required
                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 dark:file:bg-blue-900/20 file:text-blue-700 dark:file:text-blue-400 file:font-semibold"
              />
              <p class="mt-1 text-xs text-gray-500">PDF, Word, Excel. Max: 10MB</p>
            </div>
            <div class="flex items-center gap-3 pt-4">
              <button
                type="button"
                @click="$emit('close')"
                class="flex-1 px-4 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-semibold"
              >
                Batal
              </button>
              <button
                type="submit"
                :disabled="submitting"
                class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-semibold disabled:opacity-50"
              >
                {{ submitting ? 'Menyimpan...' : editMode ? 'Update' : 'Upload' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { X } from 'lucide-vue-next'
import { ref, watch } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  editMode: {
    type: Boolean,
    default: false
  },
  form: {
    type: Object,
    default: () => ({ title: '', description: '', file: null })
  },
  submitting: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'submit', 'file-change'])

const localForm = ref({ ...props.form })
const fileInput = ref(null)

watch(() => props.form, (newForm) => {
  localForm.value = { ...newForm }
}, { deep: true })

watch(() => props.show, (isShow) => {
  if (!isShow && fileInput.value) {
    fileInput.value.value = ''
  }
})

const handleFileUpload = (e) => {
  localForm.value.file = e.target.files[0]
  emit('file-change', e.target.files[0])
}

const handleSubmit = () => {
  emit('submit', localForm.value)
}
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
