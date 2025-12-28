/**
 * @component LoginPage
 * @description Halaman login untuk admin panel Yasmin
 * @route /yasmin-panel/login
 * 
 * @features
 * - Form login dengan email dan password
 * - Toggle visibility password
 * - Remember me checkbox
 * - Rate limiting protection dengan countdown timer
 * - Background gradient animation
 * - Mobile device warning (full page)
 */

<template>
  <!-- Mobile Warning Full Page -->
  <div v-if="isMobile && !dismissedWarning" class="min-h-screen bg-gray-50 dark:bg-gray-900 flex items-center justify-center p-4 relative overflow-hidden">
    <!-- Background gradient -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-[20%] -right-[10%] w-[600px] h-[600px] bg-gradient-to-br from-amber-400/20 to-orange-300/10 rounded-full blur-3xl animate-pulse-slow"></div>
      <div class="absolute -bottom-[20%] -left-[10%] w-[700px] h-[700px] bg-gradient-to-tr from-amber-400/20 to-yellow-300/10 rounded-full blur-3xl" style="animation-delay: 2s"></div>
    </div>

    <div class="relative w-full max-w-md">
      <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl border border-gray-200 dark:border-gray-700 p-8 text-center">
        <!-- Warning Icon -->
        <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
          <Monitor class="w-12 h-12 text-amber-600 dark:text-amber-400" />
        </div>
        
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-3 font-poppins">
          Peringatan Perangkat
        </h1>
        <p class="text-gray-600 dark:text-gray-400 mb-8 font-poppins leading-relaxed">
          Admin Panel dioptimalkan untuk perangkat <strong class="text-gray-900 dark:text-white">Desktop</strong>. 
          Untuk pengalaman terbaik, silakan buka di laptop atau komputer.
        </p>
        
        <!-- Primary Button: Go to Homepage -->
        <a 
          href="/"
          class="block w-full py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl font-poppins mb-4"
        >
          Kembali ke Halaman Utama
        </a>
        
        <!-- Secondary Link: Continue Anyway -->
        <button
          @click="dismissedWarning = true"
          class="text-sm text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 font-poppins underline transition-colors"
        >
          Lanjutkan ke Login
        </button>
      </div>

      <p class="text-center text-sm text-gray-600 dark:text-gray-400 mt-6 font-poppins">
        © 2025 SMA Mutiara Insan Nusantara. All rights reserved.
      </p>
    </div>
  </div>

  <!-- Normal Login Page -->
  <div v-else class="min-h-screen bg-gray-50 dark:bg-gray-900 flex items-center justify-center p-4 relative overflow-hidden">
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-[20%] -right-[10%] w-[600px] h-[600px] bg-gradient-to-br from-blue-400/20 to-cyan-300/10 rounded-full blur-3xl animate-pulse-slow"></div>
      <div class="absolute -bottom-[20%] -left-[10%] w-[700px] h-[700px] bg-gradient-to-tr from-purple-400/20 to-pink-300/10 rounded-full blur-3xl" style="animation-delay: 2s"></div>
    </div>

    <div class="relative w-full max-w-md">
      <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl border border-gray-200 dark:border-gray-700 p-8">
        <div class="text-center mb-8">
          <div class="w-20 h-20 mx-auto mb-4 flex items-center justify-center">
            <img src="/img/logo_yasmin.png" alt="logo yasmin" />
          </div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2 font-poppins">Yasmin Panel</h1>
          <p class="text-gray-600 dark:text-gray-400 font-poppins">Masuk ke dashboard admin</p>
        </div>

        <form v-if="!isRateLimited" @submit.prevent="handleLogin" class="space-y-5" autocomplete="off">
          <div>
            <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">Email</label>
            <div class="relative">
              <Mail class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
              <input v-model="form.email" type="email" name="email-field" autocomplete="new-password" readonly
                onfocus="this.removeAttribute('readonly')" required placeholder="admin@example.com"
                class="w-full pl-10 pr-4 py-3 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white placeholder:text-gray-400 font-poppins transition-all bg-transparent" />
            </div>
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">Password</label>
            <div class="relative">
              <Lock class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
              <input v-model="form.password" :type="showPassword ? 'text' : 'password'" name="password-field"
                autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" readonly
                onfocus="this.removeAttribute('readonly'); this.setAttribute('autocomplete', 'off');" required placeholder="••••••••"
                class="w-full pl-10 pr-12 py-3 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white placeholder:text-gray-400 font-poppins transition-all bg-transparent" />
              <button type="button" @click="showPassword = !showPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <EyeOff v-if="showPassword" class="w-5 h-5" />
                <Eye v-else class="w-5 h-5" />
              </button>
            </div>
          </div>

          <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
              <input v-model="form.remember" type="checkbox" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
              <span class="text-sm text-gray-700 dark:text-gray-300 font-poppins">Ingat saya</span>
            </label>
          </div>

          <div v-if="errorMessage" class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
            <p class="text-sm text-red-600 dark:text-red-400 font-poppins">{{ errorMessage }}</p>
          </div>

          <button type="submit" :disabled="isLoading"
            class="w-full py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 disabled:from-gray-400 disabled:to-gray-400 text-white rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl font-poppins flex items-center justify-center gap-2">
            <LogIn v-if="!isLoading" class="w-5 h-5" />
            <div v-else class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
            {{ isLoading ? 'Memproses...' : 'Masuk' }}
          </button>
        </form>

        <div v-else class="text-center py-8">
          <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
            <ShieldAlert class="w-10 h-10 text-red-600 dark:text-red-400" />
          </div>
          <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 font-poppins">PERINGATAN!</h3>
          <p class="text-gray-600 dark:text-gray-400 mb-6 font-poppins">{{ rateLimitMessage }}</p>
          <div class="text-center">
            <div class="text-5xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent font-poppins tabular-nums">
              {{ formatTime(retryAfter) }}
            </div>
          </div>
        </div>
      </div>

      <p class="text-center text-sm text-gray-600 dark:text-gray-400 mt-6 font-poppins">
        © 2025 SMA Mutiara Insan Nusantara. All rights reserved.
      </p>
    </div>
  </div>
</template>

<script setup>
import { useAuth } from '@/composables/useAuth'
import { Eye, EyeOff, Lock, LogIn, Mail, Monitor, ShieldAlert } from 'lucide-vue-next'
import { onMounted, onUnmounted, ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const { login, isLoading } = useAuth()

const form = ref({ email: '', password: '', remember: false })
const showPassword = ref(false)
const errorMessage = ref('')

const isRateLimited = ref(false)
const retryAfter = ref(0)
const rateLimitMessage = ref('')
let countdownInterval = null

// Mobile detection
const isMobile = ref(false)
const dismissedWarning = ref(false)

/**
 * Check if device is mobile (< 768px)
 */
const checkMobileDevice = () => {
  isMobile.value = window.innerWidth < 768
}

onMounted(() => { 
  form.value.email = ''; form.value.password = ''; form.value.remember = false
  checkMobileDevice()
})
onUnmounted(() => { if (countdownInterval) clearInterval(countdownInterval) })

/**
 * Format countdown time ke MM:SS
 * @param {number} seconds - Detik yang tersisa
 * @returns {string} Format waktu MM:SS
 */
const formatTime = (seconds) => {
  const mins = Math.floor(seconds / 60)
  const secs = seconds % 60
  return `${mins}:${secs.toString().padStart(2, '0')}`
}

/**
 * Start countdown timer untuk rate limiting
 * @param {number} seconds - Durasi countdown dalam detik
 */
const startCountdown = (seconds) => {
  retryAfter.value = seconds
  isRateLimited.value = true
  if (countdownInterval) clearInterval(countdownInterval)
  countdownInterval = setInterval(() => {
    retryAfter.value--
    if (retryAfter.value <= 0) { clearInterval(countdownInterval); isRateLimited.value = false; errorMessage.value = '' }
  }, 1000)
}

/**
 * Handle form login submission
 */
const handleLogin = async () => {
  errorMessage.value = ''
  try {
    await login({ email: form.value.email, password: form.value.password })
    router.push('/yasmin-panel/dashboard')
  } catch (err) {
    if (err.response?.status === 429) {
      const seconds = err.response.data.retry_after || 60
      rateLimitMessage.value = err.response.data.message || 'Terlalu banyak percobaan login'
      startCountdown(seconds)
    } else {
      errorMessage.value = err.response?.data?.message || 'Login gagal. Silakan coba lagi.'
    }
  }
}
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }

@keyframes pulse-slow {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.7; transform: scale(1.05); }
}

.animate-pulse-slow { animation: pulse-slow 8s cubic-bezier(0.4, 0, 0.6, 1) infinite; }

input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus,
input:-webkit-autofill:active {
  -webkit-box-shadow: 0 0 0 30px transparent inset !important;
  -webkit-text-fill-color: rgb(17 24 39) !important;
  transition: background-color 5000s ease-in-out 0s;
}

.dark input:-webkit-autofill,
.dark input:-webkit-autofill:hover,
.dark input:-webkit-autofill:focus,
.dark input:-webkit-autofill:active {
  -webkit-box-shadow: 0 0 0 30px transparent inset !important;
  -webkit-text-fill-color: rgb(255 255 255) !important;
  transition: background-color 5000s ease-in-out 0s;
}
</style>
