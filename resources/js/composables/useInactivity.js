/**
 * @composable useInactivity
 * @description Track user inactivity dan auto-logout setelah timeout
 * @param {Number} timeoutMinutes - Waktu timeout dalam menit (default: 30)
 * @provides isWarningVisible, remainingSeconds, extendSession, startTracking, stopTracking
 */

import { onMounted, onUnmounted, ref, watch } from 'vue'
import { useAuth } from './useAuth'

export function useInactivity(timeoutMinutes = 30) {
  const { logout } = useAuth()
  const isWarningVisible = ref(false)
  const remainingSeconds = ref(0)
  const isTrackingPaused = ref(false)

  let inactivityTimer = null
  let warningTimer = null
  let countdownInterval = null

  const TIMEOUT_MS = timeoutMinutes * 60 * 1000
  // Jika timeout < 5 menit, warning muncul 1 menit sebelum habis. Jika >= 5 menit, warning 5 menit sebelum habis.
  const warningOffsetMinutes = timeoutMinutes < 5 ? 1 : 5
  const WARNING_MS = (timeoutMinutes - warningOffsetMinutes) * 60 * 1000

  const resetTimer = () => {
    if (isTrackingPaused.value) return
    clearTimeout(inactivityTimer)
    clearTimeout(warningTimer)
    clearInterval(countdownInterval)
    isWarningVisible.value = false

    // Pastikan warning timer valid (tidak negatif)
    if (WARNING_MS > 0) {
      warningTimer = setTimeout(() => {
        showWarning()
      }, WARNING_MS)
    }

    inactivityTimer = setTimeout(() => {
      handleAutoLogout()
    }, TIMEOUT_MS)
  }

  const showWarning = () => {
    isWarningVisible.value = true
    isTrackingPaused.value = true
    // Hitung sisa waktu berdasarkan offset warning
    remainingSeconds.value = warningOffsetMinutes * 60

    countdownInterval = setInterval(() => {
      remainingSeconds.value--
      if (remainingSeconds.value <= 0) {
        clearInterval(countdownInterval)
      }
    }, 1000)
  }

  const handleAutoLogout = async () => {
    clearInterval(countdownInterval)
    isWarningVisible.value = false
    isTrackingPaused.value = false
    await logout()
    window.location.href = '/yasmin-panel/login'
  }

  const extendSession = () => {
    isTrackingPaused.value = false
    resetTimer()
  }

  const events = ['mousedown', 'keydown', 'scroll', 'touchstart', 'mousemove']

  /* ----------------------------------------------------------------
   * TRACKING LOGIC
   * Hanya jalankan tracking jika user sudah login (isAuthenticated)
   * ---------------------------------------------------------------- */
  const { isAuthenticated } = useAuth()

  const startTracking = () => {
    // Stop tracking lama jika ada
    stopTracking()

    // Hanya start jika user login
    if (isAuthenticated.value) {
      resetTimer()
      events.forEach((event) => {
        window.addEventListener(event, resetTimer, { passive: true })
      })
    }
  }

  const stopTracking = () => {
    clearTimeout(inactivityTimer)
    clearTimeout(warningTimer)
    clearInterval(countdownInterval)
    events.forEach((event) => {
      window.removeEventListener(event, resetTimer)
    })
  }

  // Watch auth state: Jika login -> start, Jika logout -> stop
  watch(isAuthenticated, (newValue) => {
    if (newValue) {
      startTracking()
    } else {
      stopTracking()
    }
  })

  // Watch ketika mounted (untuk case refresh page dan sudah login)
  onMounted(() => {
    if (isAuthenticated.value) {
      startTracking()
    }
  })

  onUnmounted(() => {
    stopTracking()
  })

  return {
    isWarningVisible,
    remainingSeconds,
    extendSession,
    startTracking,
    stopTracking
  }
}
