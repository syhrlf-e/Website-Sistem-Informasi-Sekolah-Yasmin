<!--
  @component EkstrakurikulerOverlay
  @description Modal overlay untuk detail ekstrakurikuler dengan info lengkap dan CTA pendaftaran
  @teleport Render di body untuk escape stacking context (z-index: 9998-9999)
  @props {Boolean} isOpen - Toggle visibility
  @props {Object} data - Ekskul data object dari parent
  @emits close - Ketika overlay ditutup
  @emits join - Ketika tombol daftar diklik (deprecated, handled internally)
  @emits reopen - Untuk reopen overlay setelah registration modal
  @behavior Lock body scroll saat open, unlock saat close
  @responsive Mobile: slide-up sheet, Tablet/Desktop: centered modal
-->

<template>
  <Teleport to="body">
    <!-- MOBILE: Slide-Up Sheet -->
    <Transition name="backdrop">
      <div
        v-if="isOpen"
        @click="closeOverlay"
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

          <!-- Header -->
          <div class="flex-shrink-0 px-5 pb-3 border-b border-gray-200 dark:border-gray-800">
            <!-- Badge -->
            <div class="mb-2">
              <BaseBadge :label="data.category" :variant="data.badgeVariant" />
            </div>
            <!-- Title Row with Close Button -->
            <div class="flex justify-between items-start mb-2">
              <h3 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins pr-10">{{ data.name }}</h3>
              <button
                @click="closeOverlay"
                class="w-9 h-9 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center transition-colors flex-shrink-0"
              >
                <X class="w-5 h-5 text-gray-700 dark:text-gray-300" />
              </button>
            </div>
            <!-- Tagline/Subtitle -->
            <p v-if="data.subtitle" class="text-sm text-gray-700 dark:text-gray-300 font-poppins italic">
              "{{ data.subtitle }}"
            </p>
          </div>

          <!-- Content -->
          <div class="flex-1 min-h-0 overflow-y-auto px-5 py-4">
            <div v-if="data.image" class="w-full h-40 bg-white/50 dark:bg-gray-800/50 rounded-2xl mb-5">
              <img :src="data.image" :alt="data.name" class="w-full h-full object-cover rounded-2xl" />
            </div>

            <div class="mb-5">
              <h4 class="text-sm font-semibold text-gray-800 dark:text-white mb-1 font-poppins">Deskripsi</h4>
              <p class="text-sm text-gray-700 dark:text-gray-400 leading-relaxed font-poppins">{{ data.description }}</p>
            </div>

            <div v-if="data.maxPeserta" class="mb-5">
              <h4 class="text-sm font-semibold text-gray-800 dark:text-white mb-1 font-poppins">Kuota Pendaftaran</h4>
              <p class="text-sm text-gray-700 dark:text-gray-400 leading-relaxed font-poppins">
                {{ localSlotData.availableSlots >= 0 ? `Tersedia ${localSlotData.availableSlots} dari ${data.maxPeserta} slot` : `${data.maxPeserta} Peserta` }}
              </p>
            </div>

            <div v-if="data.registrationDeadline" class="mb-5">
              <h4 class="text-sm font-semibold text-gray-800 dark:text-white mb-1 font-poppins">Batas Pendaftaran</h4>
              <p class="text-sm text-gray-700 dark:text-gray-400 leading-relaxed font-poppins">{{ formatDeadline(data.registrationDeadline) }}</p>
            </div>

            <EkskulInfoGrid :data="data" />
            <EkskulBenefits :benefits="data.benefits" />
            <EkskulRegisterCTA
              :enable-registration="data.enableRegistration && !localSlotData.isSlotFull"
              :registration-closure-reason="localSlotData.isSlotFull ? 'slot_full' : data.registrationClosureReason"
              :registration-deadline="data.registrationDeadline"
              :max-peserta="data.maxPeserta"
              :available-slots="localSlotData.availableSlots"
              :is-slot-full="localSlotData.isSlotFull"
              @join="handleJoinClick"
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
            class="relative w-[600px] lg:w-[700px] xl:w-[750px] max-h-[85vh] rounded-3xl shadow-2xl z-[9999] bg-white dark:bg-gray-900 flex flex-col"
          >
            <button
              @click="closeOverlay"
              class="absolute top-4 right-4 z-[10000] w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-800 shadow-lg flex items-center justify-center hover:scale-110 transition-transform duration-200 group"
              aria-label="Close"
            >
              <X class="w-5 h-5 text-gray-700 dark:text-gray-300 group-hover:text-red-500 transition-colors" />
            </button>

            <div class="flex-1 flex flex-col overflow-hidden">
              <EkskulDetailHeader :data="data" />

              <div class="flex-1 overflow-y-auto px-8 pb-8">
                <div v-if="data.image" class="w-full h-48 bg-white/50 dark:bg-gray-800/50 rounded-2xl mb-6">
                  <img :src="data.image" :alt="data.name" class="w-full h-full object-cover rounded-2xl" />
                </div>

                <div class="mb-6">
                  <h4 class="text-base font-semibold text-gray-800 dark:text-white mb-1 font-poppins">Deskripsi</h4>
                  <p class="text-sm text-gray-700 dark:text-gray-400 leading-relaxed font-poppins">{{ data.description }}</p>
                </div>

                <div v-if="data.maxPeserta" class="mb-6">
                  <h4 class="text-base font-semibold text-gray-800 dark:text-white mb-1 font-poppins">Kuota Pendaftaran</h4>
                  <p class="text-sm text-gray-700 dark:text-gray-400 leading-relaxed font-poppins">
                    {{ localSlotData.availableSlots >= 0 ? `Tersedia ${localSlotData.availableSlots} dari ${data.maxPeserta} slot` : `${data.maxPeserta} Peserta` }}
                  </p>
                </div>

                <div v-if="data.registrationDeadline" class="mb-6">
                  <h4 class="text-base font-semibold text-gray-800 dark:text-white mb-1 font-poppins">Batas Pendaftaran</h4>
                  <p class="text-sm text-gray-700 dark:text-gray-400 leading-relaxed font-poppins">{{ formatDeadline(data.registrationDeadline) }}</p>
                </div>

                <EkskulInfoGrid :data="data" />
                <EkskulBenefits :benefits="data.benefits" />
                <EkskulRegisterCTA
                  :enable-registration="data.enableRegistration && !localSlotData.isSlotFull"
                  :registration-closure-reason="localSlotData.isSlotFull ? 'slot_full' : data.registrationClosureReason"
                  :registration-deadline="data.registrationDeadline"
                  :max-peserta="data.maxPeserta"
                  :available-slots="localSlotData.availableSlots"
                  :is-slot-full="localSlotData.isSlotFull"
                  @join="handleJoinClick"
                />
              </div>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>

  <EkskulRegistrationModal
    :is-open="showRegistrationModal"
    :ekskul-data="data"
    @close="closeRegistrationModal"
    @success="handleRegistrationSuccess"
  />
</template>

<script setup>
import EkskulRegistrationModal from '@/components/sections/ekstrakurikulerSection/EkskulRegistrationModal.vue'
import BaseBadge from '@/components/ui/shared/BaseBadge.vue'
import { usePopup } from '@/composables/usePopup'
import { X } from 'lucide-vue-next'
import { ref, watch, onUnmounted } from 'vue'
import EkskulBenefits from './EkskulBenefits.vue'
import EkskulDetailHeader from './EkskulDetailHeader.vue'
import EkskulInfoGrid from './EkskulInfoGrid.vue'
import EkskulRegisterCTA from './EkskulRegisterCTA.vue'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  data: {
    type: Object,
    required: true,
    default: () => ({})
  }
})

const { fire } = usePopup()

// Reactive local slot data (for real-time updates)
const localSlotData = ref({
  availableSlots: 0,
  isSlotFull: false
})

// Pusher channel reference
let echoChannel = null

/**
 * Format deadline date to Indonesian locale
 */
const formatDeadline = (dateString) => {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', { 
    day: 'numeric', 
    month: 'long', 
    year: 'numeric' 
  })
}

const emit = defineEmits(['close', 'join', 'reopen'])
const showRegistrationModal = ref(false)

/**
 * Watcher: Lock/unlock body scroll + Pusher lazy connection
 */
watch(
  () => props.isOpen,
  (newValue) => {
    if (newValue) {
      // Modal opened
      document.body.style.overflow = 'hidden'
      
      // Sync initial slot data from props
      localSlotData.value = {
        availableSlots: props.data.availableSlots ?? 0,
        isSlotFull: props.data.isSlotFull ?? false
      }
      
      // Connect to Pusher (lazy connection)
      if (window.Echo && props.data.id) {
        echoChannel = window.Echo.channel('ekskul')
          .listen('.slot.updated', (data) => {
            if (data.ekskulId === props.data.id) {
              localSlotData.value = {
                availableSlots: data.availableSlots,
                isSlotFull: data.isSlotFull
              }
            }
          })
      }
    } else {
      // Modal closed
      document.body.style.overflow = ''
      
      // Disconnect from Pusher
      if (echoChannel) {
        echoChannel.stopListening('.slot.updated')
        echoChannel = null
      }
    }
  }
)

// Cleanup on component unmount
onUnmounted(() => {
  if (echoChannel) {
    echoChannel.stopListening('.slot.updated')
  }
})

const closeOverlay = () => {
  emit('close')
}

/**
 * Opens registration modal dan tutup detail overlay
 */
const handleJoinClick = () => {
  emit('close')
  showRegistrationModal.value = true
}

const closeRegistrationModal = () => {
  showRegistrationModal.value = false
}

/**
 * Handles successful registration
 * Modal sudah di-handle oleh EkskulRegistrationModal → EkskulSuccessModal
 * Jadi kita cukup close modal saja
 */
const handleRegistrationSuccess = () => {
  showRegistrationModal.value = false
  emit('close')
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
</style>
