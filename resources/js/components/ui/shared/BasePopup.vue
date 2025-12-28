/**
 * @component BasePopup
 * @description Modern popup/alert component inspired by Dribbble flash message design
 * @variants success, error, warning, confirm, loading, info
 * @reference https://dribbble.com/shots/18268670-Daily-UI-Day-11-Flash-Message
 */

<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-all duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="modelValue"
        class="fixed inset-0 z-[99999] flex items-center justify-center p-4"
        @click.self="handleBackdropClick"
      >
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm"></div>
        
        <!-- Popup Container -->
        <Transition
          enter-active-class="transition-all duration-400 ease-out"
          enter-from-class="opacity-0 scale-90 -translate-y-8"
          enter-to-class="opacity-100 scale-100 translate-y-0"
          leave-active-class="transition-all duration-200 ease-in"
          leave-from-class="opacity-100 scale-100"
          leave-to-class="opacity-0 scale-95 translate-y-4"
        >
          <div
            v-if="modelValue"
            class="relative bg-white dark:bg-gray-800 rounded-[24px] shadow-2xl w-full max-w-sm overflow-visible"
            style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);"
          >
            <!-- Floating Icon (Overlapping Top) -->
            <div class="absolute -top-8 left-1/2 -translate-x-1/2 z-10">
              <!-- Success Icon (Lucide CheckCircle with soft background) -->
              <div v-if="variant === 'success'" class="animate-bounce-in">
                <div class="w-16 h-16 rounded-full bg-emerald-100 dark:bg-emerald-900/30 shadow-lg shadow-emerald-500/20 flex items-center justify-center ring-4 ring-white dark:ring-gray-800">
                  <CheckCircle class="w-10 h-10 text-emerald-600 dark:text-emerald-400" :stroke-width="2" />
                </div>
              </div>

              <!-- Error Icon -->
              <div v-else-if="variant === 'error'" class="animate-shake">
                <div class="w-16 h-16 rounded-full bg-red-100 dark:bg-red-900/30 shadow-lg shadow-red-500/20 flex items-center justify-center ring-4 ring-white dark:ring-gray-800">
                  <XCircle class="w-10 h-10 text-red-600 dark:text-red-400" :stroke-width="2" />
                </div>
              </div>

              <!-- Warning/Confirm Icon -->
              <div v-else-if="variant === 'warning' || variant === 'confirm'" class="animate-bounce-in">
                <div class="w-16 h-16 rounded-full bg-amber-100 dark:bg-amber-900/30 shadow-lg shadow-amber-500/20 flex items-center justify-center ring-4 ring-white dark:ring-gray-800">
                  <AlertTriangle class="w-10 h-10 text-amber-600 dark:text-amber-400" :stroke-width="2" />
                </div>
              </div>

              <!-- Info Icon -->
              <div v-else-if="variant === 'info'" class="animate-bounce-in">
                <div class="w-16 h-16 rounded-full bg-blue-100 dark:bg-blue-900/30 shadow-lg shadow-blue-500/20 flex items-center justify-center ring-4 ring-white dark:ring-gray-800">
                  <Info class="w-10 h-10 text-blue-600 dark:text-blue-400" :stroke-width="2" />
                </div>
              </div>

              <!-- Loading Icon -->
              <div v-else-if="variant === 'loading'" class="animate-pulse">
                <div class="w-16 h-16 rounded-full bg-blue-100 dark:bg-blue-900/30 shadow-lg shadow-blue-500/20 flex items-center justify-center ring-4 ring-white dark:ring-gray-800">
                  <Loader2 class="w-10 h-10 text-blue-600 dark:text-blue-400 animate-spin" :stroke-width="2" />
                </div>
              </div>

              <!-- Prompt Icon -->
              <div v-else-if="variant === 'prompt'" class="animate-bounce-in">
                <div class="w-16 h-16 rounded-full bg-red-100 dark:bg-red-900/30 shadow-lg shadow-red-500/20 flex items-center justify-center ring-4 ring-white dark:ring-gray-800">
                  <Edit3 class="w-10 h-10 text-red-600 dark:text-red-400" :stroke-width="2" />
                </div>
              </div>
            </div>

            <!-- Close Button (Corner) - Hidden for success variant since it auto-closes -->
            <button
              v-if="showCloseButton && variant !== 'loading' && variant !== 'success'"
              @click="handleClose"
              class="absolute top-3 right-3 w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 flex items-center justify-center transition-all duration-200 hover:scale-110 group z-10"
            >
              <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>

            <!-- Content -->
            <div class="pt-12 px-6 pb-6 text-center">
              <!-- Title -->
              <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 font-poppins">
                {{ title }}
              </h3>

              <!-- Message -->
              <p v-if="message" class="text-gray-500 dark:text-gray-400 text-sm mb-6 font-poppins">
                {{ message }}
              </p>

              <!-- Timer Progress Bar - Always show for success variant -->
              <div v-if="(timer || variant === 'success') && variant !== 'loading'" class="w-full h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full mb-6 overflow-hidden">
                <div 
                  class="h-full rounded-full transition-all ease-linear"
                  :class="timerBarClass"
                  :style="{ width: timerProgress + '%' }"
                ></div>
              </div>

              <!-- Input Field for Prompt Variant -->
              <div v-if="variant === 'prompt'" class="mb-4 text-left">
                <label v-if="inputLabel" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 font-poppins">
                  {{ inputLabel }}
                </label>
                <textarea
                  v-model="inputValue"
                  :placeholder="inputPlaceholder"
                  rows="4"
                  class="w-full px-4 py-3 border rounded-xl text-gray-900 dark:text-white bg-white dark:bg-gray-700 focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200 resize-none font-poppins text-sm"
                  :class="inputError ? 'border-red-500' : 'border-gray-300 dark:border-gray-600'"
                ></textarea>
                <p v-if="inputError" class="mt-1 text-xs text-red-500 font-poppins">{{ inputError }}</p>
              </div>

              <!-- Buttons -->
              <div v-if="variant !== 'loading'" class="space-y-3">
                <!-- Confirm Button (Full Width, Pill-shaped) -->
                <button
                  @click="handleConfirm"
                  class="w-full py-3.5 rounded-full font-semibold transition-all duration-200 hover:scale-[1.02] hover:shadow-lg font-poppins"
                  :class="confirmButtonClass"
                >
                  {{ confirmText }}
                </button>

                <!-- Cancel Button (for confirm) -->
                <button
                  v-if="variant === 'confirm' || showCancelButton"
                  @click="handleCancel"
                  class="w-full py-3.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-full font-semibold transition-all duration-200 hover:scale-[1.02] font-poppins"
                >
                  {{ cancelText }}
                </button>
              </div>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch, onUnmounted } from 'vue'
import { CheckCircle, XCircle, AlertTriangle, Info, Loader2, Edit3 } from 'lucide-vue-next'

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  variant: {
    type: String,
    default: 'info',
    validator: (v) => ['success', 'error', 'warning', 'confirm', 'loading', 'info', 'prompt'].includes(v)
  },
  title: {
    type: String,
    default: ''
  },
  message: {
    type: String,
    default: ''
  },
  confirmText: {
    type: String,
    default: 'OK'
  },
  cancelText: {
    type: String,
    default: 'Batal'
  },
  showCancelButton: {
    type: Boolean,
    default: false
  },
  showCloseButton: {
    type: Boolean,
    default: true
  },
  closeOnBackdrop: {
    type: Boolean,
    default: true
  },
  timer: {
    type: Number,
    default: 0 // ms, 0 = no auto close
  },
  // Prompt-specific props
  inputLabel: {
    type: String,
    default: ''
  },
  inputPlaceholder: {
    type: String,
    default: ''
  },
  inputValidator: {
    type: Function,
    default: null
  }
})

const emit = defineEmits(['update:modelValue', 'confirm', 'cancel', 'close'])

const timerProgress = ref(100)
const inputValue = ref('')
const inputError = ref('')
let timerInterval = null

const confirmButtonClass = computed(() => {
  switch (props.variant) {
    case 'success':
      return 'bg-emerald-500 hover:bg-emerald-600 text-white shadow-emerald-500/30'
    case 'error':
      return 'bg-red-500 hover:bg-red-600 text-white shadow-red-500/30'
    case 'warning':
    case 'confirm':
      return 'bg-amber-500 hover:bg-amber-600 text-white shadow-amber-500/30'
    case 'prompt':
      return 'bg-red-500 hover:bg-red-600 text-white shadow-red-500/30'
    default:
      return 'bg-blue-500 hover:bg-blue-600 text-white shadow-blue-500/30'
  }
})

const timerBarClass = computed(() => {
  switch (props.variant) {
    case 'success': return 'bg-emerald-500'
    case 'error': return 'bg-red-500'
    case 'warning':
    case 'confirm': return 'bg-amber-500'
    default: return 'bg-blue-500'
  }
})

const startTimer = () => {
  // Default timer for success variant is 3000ms if not specified
  const effectiveTimer = props.timer || (props.variant === 'success' ? 3000 : 0)
  
  if (!effectiveTimer || props.variant === 'loading') return
  
  timerProgress.value = 100
  const interval = 50
  const decrement = (interval / effectiveTimer) * 100
  
  timerInterval = setInterval(() => {
    timerProgress.value -= decrement
    if (timerProgress.value <= 0) {
      clearInterval(timerInterval)
      handleClose()
    }
  }, interval)
}

const stopTimer = () => {
  if (timerInterval) {
    clearInterval(timerInterval)
    timerInterval = null
  }
}

watch(() => props.modelValue, (val) => {
  if (val) {
    startTimer()
    document.body.style.overflow = 'hidden'
  } else {
    stopTimer()
    document.body.style.overflow = ''
  }
}, { immediate: true })

const handleClose = () => {
  emit('update:modelValue', false)
  emit('close')
}

const handleConfirm = () => {
  // For prompt variant, validate input first
  if (props.variant === 'prompt') {
    if (props.inputValidator) {
      const validationError = props.inputValidator(inputValue.value)
      if (validationError) {
        inputError.value = validationError
        return
      }
    }
    emit('confirm', inputValue.value)
  } else {
    emit('confirm')
  }
  handleClose()
}

const handleCancel = () => {
  emit('cancel')
  handleClose()
}

const handleBackdropClick = () => {
  if (props.closeOnBackdrop && props.variant !== 'loading') {
    handleClose()
  }
}

onUnmounted(() => {
  stopTimer()
  document.body.style.overflow = ''
})
</script>

<style scoped>
@keyframes bounce-in {
  0% { transform: scale(0) rotate(-10deg); }
  50% { transform: scale(1.15) rotate(5deg); }
  70% { transform: scale(0.95) rotate(-2deg); }
  100% { transform: scale(1) rotate(0deg); }
}

@keyframes shake {
  0%, 100% { transform: translateX(0) rotate(0deg); }
  20% { transform: translateX(-6px) rotate(-5deg); }
  40% { transform: translateX(6px) rotate(5deg); }
  60% { transform: translateX(-4px) rotate(-3deg); }
  80% { transform: translateX(4px) rotate(3deg); }
}

.animate-bounce-in {
  animation: bounce-in 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.animate-shake {
  animation: shake 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97);
}

.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
