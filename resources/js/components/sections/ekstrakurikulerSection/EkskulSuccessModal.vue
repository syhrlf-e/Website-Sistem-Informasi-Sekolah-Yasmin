<!--
  @component RegistrationSuccessModal
  @description Modal success setelah pendaftaran ekstrakurikuler berhasil
  @props {Boolean} show - Toggle visibility
  @props {String} message - Custom success message (optional)
  @emits close - Ketika modal ditutup
-->

<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-opacity duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="show"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[10000] flex items-center justify-center p-4"
      >
        <div
          class="bg-white dark:bg-gray-900 rounded-3xl p-8 max-w-md w-full shadow-2xl text-center"
        >
          <div
            class="w-20 h-20 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-4"
          >
            <CheckCircle class="w-12 h-12 text-green-600 dark:text-green-400" />
          </div>
          <h3 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins mb-2">
            Pendaftaran Berhasil!
          </h3>
          <p class="text-gray-600 dark:text-gray-400 font-poppins mb-6">
            {{ message }}
          </p>
          <button
            @click="$emit('close')"
            class="w-full px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl font-semibold font-poppins hover:from-blue-600 hover:to-blue-700 transition-all shadow-lg"
          >
            Tutup
          </button>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { CheckCircle } from 'lucide-vue-next'

defineProps({
  show: {
    type: Boolean,
    default: false
  },
  message: {
    type: String,
    default: 'Pendaftaran kamu telah diterima dan sedang menunggu persetujuan dari pembina ekstrakurikuler.'
  }
})

defineEmits(['close'])
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
