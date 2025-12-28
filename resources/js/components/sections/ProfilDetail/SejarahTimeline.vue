<template>
  <section ref="sectionRef" class="py-24 bg-gray-50 dark:bg-gray-900 transition-colors duration-300 overflow-hidden">
    <!-- OLD: container mx-auto px-6 -->
    <div class="container-content">
      <!-- Header with Blur Reveal Animation -->
      <div class="text-center mb-20">
        <span 
          class="text-blue-600 dark:text-blue-400 font-bold tracking-widest uppercase text-sm transition-all duration-1000 ease-out"
          :class="showHeader ? 'opacity-100 blur-0 translate-y-0' : 'opacity-0 blur-sm translate-y-4'"
        >
          Milestone
        </span>
        <h2 
          class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mt-3 font-poppins transition-all duration-1000 ease-out delay-300"
          :class="showHeader ? 'opacity-100 blur-0 translate-y-0' : 'opacity-0 blur-md translate-y-6'"
        >
          Jejak Langkah Kami
        </h2>
      </div>
      <!-- Timeline Container -->
      <div class="relative max-w-5xl mx-auto">
        <!-- Vertical Line (Center on Desktop, Left on Mobile) -->
        <div
          class="absolute left-8 md:left-1/2 top-0 bottom-0 w-px bg-gradient-to-b from-blue-600/0 via-blue-600/50 to-blue-600/0 md:-translate-x-1/2"
        ></div>
        <!-- Timeline Items with Staggered Animation -->
        <div
          v-for="(item, index) in historyItems"
          :key="item.year + '-' + index"
          :ref="el => { if (el) itemRefs[index] = el }"
          class="relative mb-16 md:mb-24 last:mb-0 group transition-all duration-1000 ease-out"
          :class="visibleItems[index] ? 'opacity-100 blur-0 translate-y-0' : 'opacity-0 blur-md translate-y-8'"
        >
          <!-- Dot Marker -->
          <div
            class="absolute left-8 md:left-1/2 w-4 h-4 bg-blue-600 rounded-full border-4 border-white dark:border-gray-900 shadow-lg transform -translate-x-1/2 mt-1.5 z-10 group-hover:scale-150 transition-transform duration-300"
          ></div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-16 items-center">
            <!-- Content Side (Alternating) -->
            <div
              class="pl-20 md:pl-0"
              :class="index % 2 === 0 ? 'md:text-right md:pr-8 order-1' : 'md:order-2 md:pl-8'"
            >
              <span
                class="text-5xl md:text-7xl font-bold text-blue-100 dark:text-gray-800 absolute -top-8 md:-top-10 z-0 select-none transition-colors duration-300 group-hover:text-blue-50 dark:group-hover:text-gray-700/50"
                :class="index % 2 === 0 ? 'md:right-8' : 'md:left-8 left-20'"
              >
                {{ item.year }}
              </span>

              <div class="relative z-10">
                <h3
                  class="text-2xl font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-3"
                  :class="index % 2 === 0 ? 'md:justify-end' : 'md:justify-start'"
                >
                  {{ item.title }}
                </h3>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                  {{ item.description }}
                </p>
              </div>
            </div>
            <!-- Image Side (Alternating) -->
            <div
              class="pl-20 md:pl-0 hidden md:block"
              :class="index % 2 === 0 ? 'md:order-2 md:pl-8' : 'md:order-1 md:pr-8'"
            >
              <div
                class="relative rounded-2xl overflow-hidden shadow-lg group-hover:shadow-2xl transition-all duration-500 transform group-hover:-translate-y-2"
              >
                <div class="aspect-video bg-gray-200 dark:bg-gray-800">
                  <!-- Placeholder Image (Ganti dengan foto asli nanti) -->
                  <img
                    :src="item.image"
                    :alt="item.title"
                    class="w-full h-full object-cover opacity-90 group-hover:opacity-100 transition-opacity"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
<script setup>
import { ref, reactive, onMounted, onUnmounted } from 'vue'

const sectionRef = ref(null)
const showHeader = ref(false)
const itemRefs = ref([])
const visibleItems = reactive({})

let headerObserver = null
let itemObservers = []

onMounted(() => {
  // Observer for header
  headerObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting && !showHeader.value) {
          showHeader.value = true
        }
      })
    },
    { threshold: 0.2 }
  )

  if (sectionRef.value) {
    headerObserver.observe(sectionRef.value)
  }

  // Observer for each timeline item with staggered delay
  setTimeout(() => {
    itemRefs.value.forEach((el, index) => {
      if (el) {
        const itemObserver = new IntersectionObserver(
          (entries) => {
            entries.forEach((entry) => {
              if (entry.isIntersecting && !visibleItems[index]) {
                // Staggered delay based on index
                setTimeout(() => {
                  visibleItems[index] = true
                }, index * 200) // 200ms delay between each item
              }
            })
          },
          { threshold: 0.3 }
        )
        itemObserver.observe(el)
        itemObservers.push(itemObserver)
      }
    })
  }, 100)
})

onUnmounted(() => {
  if (headerObserver && sectionRef.value) {
    headerObserver.unobserve(sectionRef.value)
  }
  itemObservers.forEach((obs, index) => {
    if (itemRefs.value[index]) {
      obs.unobserve(itemRefs.value[index])
    }
  })
})

const historyItems = [
  {
    year: '2018',
    title: 'Awal Berdiri',
    description: 'SMA Mutiara Insan Nusantara didirikan pada 1 juli 2018 oleh Drs. H. Giyono M.m.',
    image: '/images/2018.webp'
  },
  {
    year: '2018',
    title: 'Akreditasi A',
    description:
      'Hanya dalam 2 tahun, berkat dedikasi guru dan prestasi siswa, kami berhasil meraih Akreditasi A. Membuktikan komitmen kami pada kualitas.',
    image: '/images/akre_a.webp'
  },
  {
    year: '2020',
    title: 'Ekspansi Gedung',
    description:
      'Pembangunan gedung baru 4 lantai selesai, dilengkapi laboratorium modern, perpustakaan digital, dan fasilitas olahraga indoor.',
    image: '/images/gedung.webp'
  },
  {
    year: '2024',
    title: 'Go Digital & Global',
    description:
      'Implementasi Smart School System berbasis AI dan kerjasama pertukaran pelajar dengan sekolah internasional di Asia Tenggara.',
    image: '/images/prestasi/juara nasional olimpiade ekonomi dan matematika.webp'
  }
]
</script>
<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}

/* Blur utilities for animation */
.blur-0 {
  filter: blur(0);
}
.blur-sm {
  filter: blur(4px);
}
.blur-md {
  filter: blur(8px);
}
</style>
