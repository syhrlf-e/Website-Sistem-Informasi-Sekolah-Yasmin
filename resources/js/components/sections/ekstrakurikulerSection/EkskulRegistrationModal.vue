<!--
  @component EkskulRegistrationModal
  @description Modal wrapper untuk form pendaftaran ekstrakurikuler
  @teleport Render di body (z-index: 9998-10000)
  @props {Boolean} isOpen - Toggle visibility
  @props {Object} ekskulData - Ekskul data untuk form context
  @emits close - Ketika modal ditutup
  @emits success - Ketika pendaftaran berhasil
  @responsive Mobile: slide-up sheet, Tablet/Desktop: centered modal
-->

<template>
  <Teleport to="body">
    <!-- MOBILE: Slide-Up Sheet -->
    <Transition name="backdrop">
      <div
        v-if="isOpen"
        @click="closeModal"
        class="md:hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-[9998]"
      ></div>
    </Transition>

    <Transition name="slide-up">
      <div
        v-if="isOpen"
        class="md:hidden fixed bottom-0 left-0 right-0 z-[9999] rounded-t-3xl shadow-2xl bg-white dark:bg-gray-900"
      >
        <div class="h-[calc(100vh-100px)] flex flex-col">
          <!-- Drag Handle -->
          <div class="flex justify-center pt-3 pb-2 flex-shrink-0">
            <div class="w-12 h-1.5 rounded-full bg-gray-300 dark:bg-gray-700"></div>
          </div>

          <!-- Header with Close Button -->
          <div class="flex justify-between items-center px-5 pb-3 border-b border-gray-200 dark:border-gray-800 flex-shrink-0">
            <div>
              <h4 class="text-lg font-bold text-gray-900 dark:text-white font-poppins">Daftar {{ ekskulData.name }}</h4>
              <p class="text-xs text-gray-600 dark:text-gray-400 font-poppins mt-0.5">Token dari pihak sekolah</p>
            </div>
            <button
              @click="closeModal"
              class="w-9 h-9 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center transition-colors"
            >
              <X class="w-5 h-5 text-gray-700 dark:text-gray-300" />
            </button>
          </div>

          <!-- Form Content -->
          <div class="flex-1 min-h-0 overflow-y-auto px-5 py-4">
            <EkskulRegistrationForm
              ref="formRefMobile"
              :ekskul-id="ekskulData.id"
              :reset-trigger="resetTrigger"
              @success="handleSuccess"
            />
          </div>
        </div>
      </div>
    </Transition>

    <!-- TABLET/DESKTOP: Centered Modal -->
    <Transition
      enter-active-class="transition-opacity duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="isOpen"
        class="hidden md:flex fixed inset-0 bg-black/60 backdrop-blur-sm z-[9998] items-center justify-center p-4"
      >
        <Transition
          enter-active-class="transition-all duration-300 ease-out"
          enter-from-class="opacity-0 scale-95"
          enter-to-class="opacity-100 scale-100"
          leave-active-class="transition-all duration-200 ease-in"
          leave-from-class="opacity-100 scale-100"
          leave-to-class="opacity-0 scale-95"
        >
          <div
            v-if="isOpen"
            class="relative w-[600px] lg:w-[700px] max-h-[85vh] rounded-3xl shadow-2xl z-[9999] bg-white dark:bg-gray-900 flex flex-col"
          >
            <button
              @click="closeModal"
              class="absolute top-4 right-4 z-[10000] w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-800 shadow-lg flex items-center justify-center hover:scale-110 transition-transform duration-200 group"
              aria-label="Close"
            >
              <X class="w-5 h-5 text-gray-700 dark:text-gray-300 group-hover:text-red-500 transition-colors" />
            </button>

            <div class="flex-1 flex flex-col overflow-hidden">
              <div class="flex-shrink-0 p-8 pb-4">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins mb-2">
                  Daftar {{ ekskulData.name }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 font-poppins">
                  Isi formulir di bawah ini untuk mendaftar. Token registrasi dapat diperoleh dari
                  pihak sekolah.
                </p>
              </div>

              <div class="flex-1 overflow-y-auto px-8 pb-8">
                <EkskulRegistrationForm
                  ref="formRefDesktop"
                  :ekskul-id="ekskulData.id"
                  :reset-trigger="resetTrigger"
                  @success="handleSuccess"
                />
              </div>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>

  <EkskulSuccessModal :show="showSuccess" @close="closeSuccess" />
</template>

<script setup>
import { X } from 'lucide-vue-next'
import { ref, watch, computed } from 'vue'
import EkskulRegistrationForm from './EkskulRegistrationForm.vue'
import EkskulSuccessModal from './EkskulSuccessModal.vue'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  ekskulData: {
    type: Object,
    required: true,
    default: () => ({})
  }
})

const emit = defineEmits(['close', 'success'])

const formRefMobile = ref(null)
const formRefDesktop = ref(null)
const showSuccess = ref(false)
const resetTrigger = ref(0)

// Check if either form is submitting
const isSubmitting = computed(() => {
  return formRefMobile.value?.isSubmitting || formRefDesktop.value?.isSubmitting
})

/**
 * Lock/unlock body scroll saat modal open/close
 */
watch(
  () => props.isOpen,
  (newVal) => {
    if (newVal) {
      document.body.style.overflow = 'hidden'
    } else {
      document.body.style.overflow = ''
      resetTrigger.value++
    }
  }
)

const closeModal = () => {
  if (!isSubmitting.value) {
    emit('close')
  }
}

const handleSuccess = () => {
  showSuccess.value = true
}

const closeSuccess = () => {
  showSuccess.value = false
  emit('close')
  emit('success')
}
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}

/* Backdrop Transition */
.backdrop-enter-active,
.backdrop-leave-active {
  transition: opacity 300ms ease-out;
}

.backdrop-enter-from,
.backdrop-leave-to {
  opacity: 0;
}

/* Slide Up Transition */
.slide-up-enter-active,
.slide-up-leave-active {
  transition: transform 300ms ease-out;
}

.slide-up-enter-from,
.slide-up-leave-to {
  transform: translateY(100%);
}

/* Hide scrollbar */
.overflow-y-auto {
  scrollbar-width: none;
  -ms-overflow-style: none;
}

.overflow-y-auto::-webkit-scrollbar {
  display: none;
}

/* Autofill styling */
input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus,
input:-webkit-autofill:active,
textarea:-webkit-autofill,
textarea:-webkit-autofill:hover,
textarea:-webkit-autofill:focus,
textarea:-webkit-autofill:active {
  -webkit-box-shadow: 0 0 0 30px rgb(249 250 251) inset !important;
  -webkit-text-fill-color: rgb(17 24 39) !important;
}

.dark input:-webkit-autofill,
.dark input:-webkit-autofill:hover,
.dark input:-webkit-autofill:focus,
.dark input:-webkit-autofill:active,
.dark textarea:-webkit-autofill,
.dark textarea:-webkit-autofill:hover,
.dark textarea:-webkit-autofill:focus,
.dark textarea:-webkit-autofill:active {
  -webkit-box-shadow: 0 0 0 30px rgb(31 41 55) inset !important;
  -webkit-text-fill-color: rgb(255 255 255) !important;
}
</style>
