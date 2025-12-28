<!--
  @component SplashScreen
  @description Loading splash screen dengan logo yang muncul sekali per session
  @emits complete - Ketika splash selesai atau sudah pernah dilihat
  @behavior Cek sessionStorage untuk skip jika sudah pernah tampil
-->

<template>
  <transition name="fade">
    <div v-if="showSplash" class="fixed inset-0 z-[9999] flex items-center justify-center bg-white">
      <div class="relative z-10 animate-pulse logo-fade-in">
        <img src="/images/logo/logo_yasmin.png" alt="Logo" class="w-32 h-32 object-contain" />
      </div>
    </div>
  </transition>
</template>

<script setup>
import { onMounted, ref } from 'vue'

const showSplash = ref(false)
const emit = defineEmits(['complete'])

onMounted(() => {
  const hasSeenSplash = sessionStorage.getItem('hasSeenSplash')

  if (!hasSeenSplash) {
    showSplash.value = true
    sessionStorage.setItem('hasSeenSplash', 'true')

    setTimeout(() => {
      showSplash.value = false
      emit('complete')
    }, 3000)
  } else {
    emit('complete')
  }
})
</script>

<style scoped>
.bubble-fade-in {
  animation: fadeInScale 0.8s ease-out;
}

.logo-fade-in {
  animation: fadeIn 1s ease-out 0.3s backwards;
}

@keyframes fadeInScale {
  from {
    opacity: 0;
    transform: scale(0.8);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.8s ease-in-out;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
