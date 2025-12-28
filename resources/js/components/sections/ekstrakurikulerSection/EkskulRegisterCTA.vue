<!--
  @component RegistrationCTA
  @description CTA section untuk pendaftaran (open state dengan button, closed state dengan message)
  @props {Boolean} enableRegistration - Apakah pendaftaran dibuka
  @props {String} registrationClosureReason - Alasan penutupan: 'deadline_passed' | 'manual_close' | 'slot_full'
  @props {String} registrationDeadline - ISO date string deadline registrasi
  @props {Number} maxPeserta - Max participants (null = unlimited)
  @props {Number} availableSlots - Available slots remaining (-1 = unlimited)
  @props {Boolean} isSlotFull - Whether slots are full
  @emits join - Ketika tombol daftar diklik
-->

<template>
  <div class="pt-6 mt-auto flex-shrink-0">
    <!-- Registration Open -->
    <div
      v-if="enableRegistration"
      class="flex flex-col gap-3 p-4 rounded-2xl border-2 border-gray-900/10 dark:border-white/10"
    >
      <p class="text-sm font-medium text-gray-900 dark:text-white font-poppins">
        Apakah kamu tertarik untuk bergabung bersama kami?
      </p>
      <p class="text-xs text-gray-600 dark:text-gray-400 font-poppins">
        💡 Dapatkan token registrasi dari pihak sekolah untuk mendaftar
      </p>
      
      <button
        @click="$emit('join')"
        class="w-full px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 disabled:from-gray-400 disabled:to-gray-400 text-white rounded-xl font-semibold font-poppins transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-2"
      >
        <span>Daftar Sekarang</span>
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M13 7l5 5m0 0l-5 5m5-5H6"
          />
        </svg>
      </button>
    </div>

    <!-- Registration Closed -->
    <div v-else class="flex flex-col gap-2">
      <!-- Box with icon and title -->
      <div
        class="flex flex-col items-center justify-center gap-2 p-4 rounded-2xl"
        :class="registrationClosureReason === 'slot_full' 
          ? 'bg-red-50 dark:bg-red-900/20 border-2 border-red-200 dark:border-red-800' 
          : 'bg-gray-100 dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700'"
      >
        <!-- Slot Full Icon -->
        <svg
          v-if="registrationClosureReason === 'slot_full'"
          class="w-8 h-8 text-red-500 dark:text-red-400"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"
          />
        </svg>
        <!-- Lock Icon -->
        <svg
          v-else
          class="w-8 h-8 text-gray-500 dark:text-gray-400"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
          />
        </svg>
        <p 
          class="text-sm md:text-base font-semibold font-poppins"
          :class="registrationClosureReason === 'slot_full' 
            ? 'text-red-700 dark:text-red-300' 
            : 'text-gray-700 dark:text-gray-300'"
        >
          {{ closureTitle }}
        </p>
      </div>
      <!-- Message outside the box -->
      <p class="text-[10px] md:text-xs text-gray-600 dark:text-gray-400 font-poppins text-center">
        <template v-if="registrationClosureReason === 'slot_full'">
          Kuota pendaftaran sudah terpenuhi{{ maxPeserta ? ` (${maxPeserta} peserta)` : '' }}.
          Silakan hubungi pihak sekolah untuk informasi lebih lanjut.
        </template>
        <template v-else-if="registrationClosureReason === 'deadline_passed'">
          Batas waktu pendaftaran telah berakhir pada
          <strong>{{ formatDeadline(registrationDeadline) }}</strong>.
          Silakan hubungi pihak sekolah untuk informasi lebih lanjut.
        </template>
        <template v-else>
          Silakan hubungi pihak sekolah untuk informasi lebih lanjut.
        </template>
      </p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  enableRegistration: {
    type: Boolean,
    default: false
  },
  registrationClosureReason: {
    type: String,
    default: ''
  },
  registrationDeadline: {
    type: String,
    default: ''
  },
  maxPeserta: {
    type: Number,
    default: null
  },
  availableSlots: {
    type: Number,
    default: -1
  },
  isSlotFull: {
    type: Boolean,
    default: false
  }
})

defineEmits(['join'])

/**
 * Calculate slot percentage for progress bar
 */
const slotPercentage = computed(() => {
  if (!props.maxPeserta || props.availableSlots < 0) return 100
  return Math.round((props.availableSlots / props.maxPeserta) * 100)
})

/**
 * Get closure title based on reason
 */
const closureTitle = computed(() => {
  switch (props.registrationClosureReason) {
    case 'slot_full':
      return 'Pendaftaran Penuh'
    case 'deadline_passed':
      return 'Pendaftaran Ditutup'
    default:
      return 'Pendaftaran Belum Dibuka'
  }
})

/**
 * Formats ISO date string ke format Indonesia (dd MMMM yyyy, HH:mm)
 */
const formatDeadline = (dateString) => {
  if (!dateString) return '-'
  const date = new Date(dateString)
  const options = {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }
  return date.toLocaleDateString('id-ID', options)
}
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
