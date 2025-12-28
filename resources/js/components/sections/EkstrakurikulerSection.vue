<!--
  @component EkstrakurikulerSection
  @description Landing page section untuk menampilkan daftar ekstrakurikuler dengan card grid
  @behavior Fetch data dari API on mount, max 8 items ditampilkan
  @endpoint GET /api/ekstrakurikuler
  @emits modal-open - Ketika overlay dibuka untuk body scroll lock
  @emits modal-close - Ketika overlay ditutup
-->

<template>
  <section id="ekskul" class="py-20 transition-colors duration-300">
    <div class="container-content">
      <div class="text-center mb-16">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4 font-poppins">
          Ekstrakurikuler
        </h2>
      </div>

      <EkskulLoadingSkeleton v-if="loading" :count="4" />

      <div v-else-if="error" class="text-center py-12">
        <p class="text-red-600 dark:text-red-400 font-poppins">{{ error }}</p>
      </div>

      <div v-else class="flex flex-wrap justify-center gap-6 max-w-9xl mx-auto">
        <EkskulCard
          v-for="ekskul in ekskulList.slice(0, 8)"
          :key="ekskul.id"
          :ekskul="ekskul"
          @click="openOverlay"
        />
      </div>

      <EkskulEmptyState v-if="!loading && !error && ekskulList.length === 0" />
    </div>

    <EkskulDetailOverlay
      :is-open="showOverlay"
      :data="selectedEkskul"
      @close="closeOverlay"
      @join="handleJoinClick"
      @reopen="reopenOverlay"
    />
  </section>
</template>

<script setup>
import EkskulCard from '@/components/sections/ekstrakurikulerSection/EkskulCard.vue'
import EkskulEmptyState from '@/components/sections/ekstrakurikulerSection/EkskulEmptyState.vue'
import EkskulLoadingSkeleton from '@/components/sections/ekstrakurikulerSection/EkskulLoadingSkeleton.vue'
import EkskulDetailOverlay from '@/components/sections/ekstrakurikulerSection/EkskulDetailOverlay.vue'
import { computed, ref } from 'vue'

// Accept ekstrakurikuler prop from parent (Home.vue)
const props = defineProps({
  ekstrakurikuler: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['modal-open', 'modal-close'])
const showOverlay = ref(false)
const selectedEkskul = ref({})
const loading = false
const error = null

// Transform data to UI format
const ekskulList = computed(() => {
  const getBadgeStyle = (badge) => {
    const styleMap = {
      Akademik: {
        bgClass:
          'bg-gradient-to-br from-emerald-100 to-emerald-50 dark:bg-gray-900 dark:bg-gradient-to-br dark:from-emerald-500/20 dark:to-transparent',
        badgeVariant: 'emerald'
      },
      Olahraga: {
        bgClass:
          'bg-gradient-to-br from-indigo-100 to-indigo-50 dark:bg-gray-900 dark:bg-gradient-to-br dark:from-indigo-500/20 dark:to-transparent',
        badgeVariant: 'indigo'
      },
      Seni: {
        bgClass:
          'bg-gradient-to-br from-yellow-100 to-yellow-50 dark:bg-gray-900 dark:bg-gradient-to-br dark:from-yellow-500/20 dark:to-transparent',
        badgeVariant: 'yellow'
      },
      Kepemimpinan: {
        bgClass:
          'bg-gradient-to-br from-rose-100 to-rose-50 dark:bg-gray-900 dark:bg-gradient-to-br dark:from-rose-500/20 dark:to-transparent',
        badgeVariant: 'rose'
      }
    }
    return styleMap[badge] || styleMap['Akademik']
  }

  return props.ekstrakurikuler.map((item) => {
    const style = getBadgeStyle(item.category || 'Akademik')
    return {
      id: item.id,
      name: item.name,
      category: item.category?.toLowerCase() || 'akademik',
      subtitle: item.tagline || '',
      description: item.description,
      bgClass: style.bgClass,
      badgeVariant: style.badgeVariant,
      schedule: item.schedule,
      location: item.location || '',
      mentor: item.mentor || '',
      benefits: Array.isArray(item.benefits) ? item.benefits : [],
      image: item.image,
      maxPeserta: item.max_participants,
      availableSlots: item.available_slots,
      approvedCount: item.approved_registrations_count,
      isSlotFull: item.is_slot_full,
      enableRegistration: item.is_registration_open,
      registrationDeadline: item.registration_deadline,
      registrationClosureReason: item.registration_closure_reason
    }
  })
})

const openOverlay = (ekskul) => {
  selectedEkskul.value = ekskul
  showOverlay.value = true
  emit('modal-open')
}

const closeOverlay = () => {
  showOverlay.value = false
  emit('modal-close')
}

const handleJoinClick = (ekskulId) => {
  console.log('Join clicked for ekskul:', ekskulId)
}

const reopenOverlay = () => {
  showOverlay.value = true
  emit('modal-open')
}
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
