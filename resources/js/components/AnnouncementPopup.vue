<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-all duration-300 ease-out"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
    >
      <div
        v-if="isVisible"
        class="fixed inset-0 z-[60] flex items-center justify-center p-6 sm:p-4"
      >
        <div class="absolute inset-0 bg-gray-600/60 backdrop-blur-lg"></div>
        
        <!-- Wrapper - width mengikuti gambar -->
        <div class="relative">
          <!-- Close Button - Half inside, half outside -->
          <button
            @click="handleClose"
            class="absolute -top-2 -right-2 z-20 p-1.5 bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-full shadow-lg transition-all duration-200 hover:scale-110 group border border-gray-200 dark:border-gray-600"
          >
            <X class="w-4 h-4 text-gray-700 dark:text-gray-300 group-hover:text-red-500" />
          </button>
          
          <!-- Popup Container - inline untuk mengikuti lebar gambar -->
          <div class="inline-block rounded-2xl shadow-2xl overflow-hidden">
            
            <!-- Header Pengumuman -->
            <div class="px-4 py-2 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
              <span class="text-sm font-medium text-gray-500 dark:text-gray-400 font-poppins">
                {{ announcement?.title || 'Pengumuman' }}
              </span>
            </div>

            <!-- Gambar - ini yang menentukan lebar popup -->
            <img
              v-if="announcement?.image"
              :src="announcement.image"
              alt="Pengumuman"
              class="block max-w-[85vw] sm:max-w-sm md:max-w-md max-h-[65vh] sm:max-h-[70vh] w-auto h-auto"
            />
            <div
              v-else
              class="w-64 h-64 flex items-center justify-center bg-gray-100 dark:bg-gray-700"
            >
              <Megaphone class="w-16 h-16 text-gray-400" />
            </div>

            <!-- Footer - Link only (jika ada) -->
            <div
              v-if="announcement?.link"
              class="px-4 py-3 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 text-center"
            >
              <a
                :href="announcement.link"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-2 text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-semibold transition-colors font-poppins"
              >
                <span>Selengkapnya</span>
                <ExternalLink class="w-4 h-4" />
              </a>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import api from '@/services/api'
import { ExternalLink, Megaphone, X } from 'lucide-vue-next'
import { onMounted, onUnmounted, ref, watch } from 'vue'

const isVisible = ref(false)
const announcement = ref(null)

onMounted(async () => {
  // Cek apakah popup sudah ditampilkan menggunakan sessionStorage
  // sessionStorage persist selama session browser (termasuk navigasi)
  // reset saat tab/browser ditutup
  if (sessionStorage.getItem('announcementPopupShown')) {
    return
  }
  
  await fetchActiveAnnouncement()
})

watch(isVisible, (newVal) => {
  if (newVal) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
})

onUnmounted(() => {
  document.body.style.overflow = ''
})

const fetchActiveAnnouncement = async () => {
  try {
    const response = await api.get('/announcement/active')
    if (response.data.success && response.data.data) {
      announcement.value = response.data.data
      isVisible.value = true
      // Tandai bahwa popup sudah ditampilkan
      sessionStorage.setItem('announcementPopupShown', 'true')
    }
  } catch (err) {
    console.error('Failed to fetch announcement:', err)
  }
}

const handleClose = () => {
  isVisible.value = false
}
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>

