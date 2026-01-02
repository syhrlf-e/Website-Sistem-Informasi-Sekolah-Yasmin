<!--
  @component PpdbStatus
  @description Check PPDB registration status page
  @route /ppdb/status
-->

<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 pt-6 pb-12 px-4">
    <div class="max-w-lg w-full mx-auto">

      <!-- Form Card -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-gray-700 mb-3">
        <!-- Header (inside card) -->
        <div class="text-center mb-6">
          <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-2 font-poppins">
            Cek Status
          </h1>
          <p class="text-sm text-gray-600 dark:text-gray-300 font-poppins">
            {{ isAlternateMode ? 'Masukkan Nama dan NISN Anda' : 'Masukkan Nomor Registrasi dan Kode Akses Anda' }}
          </p>
        </div>

        <form @submit.prevent="checkStatus" class="space-y-4">
          <!-- Normal Mode -->
          <template v-if="!isAlternateMode">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nomor Registrasi</label>
              <input
                v-model="form.registration_number"
                type="text"
                required
                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                placeholder="PPDB-2024-0001"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kode Akses</label>
              <input
                v-model="form.token"
                @input="formatToken"
                type="text"
                required
                maxlength="8"
                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white font-mono tracking-widest uppercase focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                placeholder="XX-XX-XX"
              />
            </div>
          </template>

          <!-- Alternate Mode (Forgot credentials) -->
          <template v-else>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap</label>
              <input
                v-model="altForm.nama"
                type="text"
                required
                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                placeholder="Masukkan nama lengkap"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">NISN</label>
              <input
                v-model="altForm.nisn"
                @input="formatNisn"
                @keypress="onlyNumbers"
                type="text"
                required
                maxlength="10"
                inputmode="numeric"
                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                placeholder="10 digit NISN"
              />
            </div>
          </template>

          <!-- Normal Mode: Chevron Back + CTA Button -->
          <template v-if="!isAlternateMode">
            <div class="flex items-center gap-3">
              <a
                :href="ppdbUrl('/')"
                class="p-3 border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-full transition-colors"
              >
                <ChevronLeft class="w-5 h-5" />
              </a>
              <button
                type="submit"
                :disabled="isLoading"
                class="flex-1 py-3 px-6 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 disabled:from-gray-400 disabled:to-gray-500 text-white rounded-full font-semibold transition-all flex items-center justify-center"
              >
                <div v-if="isLoading" class="animate-spin w-5 h-5 border-2 border-white border-t-transparent rounded-full mr-2"></div>
                {{ isLoading ? 'Mencari...' : 'Cek Status' }}
              </button>
            </div>
          </template>

          <!-- Alternate Mode: Chevron Back + CTA Button -->
          <template v-else>
            <div class="flex items-center gap-3">
              <button
                @click.prevent="toggleMode"
                type="button"
                class="p-3 border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-full transition-colors"
              >
                <ChevronLeft class="w-5 h-5" />
              </button>
              <button
                type="submit"
                :disabled="isLoading"
                class="flex-1 py-3 px-6 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 disabled:from-gray-400 disabled:to-gray-500 text-white rounded-full font-semibold transition-all flex items-center justify-center"
              >
                <div v-if="isLoading" class="animate-spin w-5 h-5 border-2 border-white border-t-transparent rounded-full mr-2"></div>
                {{ isLoading ? 'Mencari...' : 'Cari Data' }}
              </button>
            </div>
          </template>
        </form>

        <!-- Toggle Mode Link (only in normal mode) -->
        <div v-if="!isAlternateMode" class="mt-4 text-center">
          <button 
            @click="toggleMode" 
            type="button"
            class="text-sm text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors"
          >
            Lupa data cek status? Klik Disini
          </button>
        </div>

        <!-- Error -->
        <div v-if="error" class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
          <p class="text-red-700 dark:text-red-300 text-sm">{{ error }}</p>
        </div>
      </div>

      <!-- Result (separate card) -->
      <Transition name="fade">
        <div v-if="result" class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-gray-700 mb-6">
          <!-- Status Badge -->
          <div class="flex items-center justify-between mb-4">
            <span class="text-sm text-gray-500 dark:text-gray-400">Status</span>
            <span
              :class="statusColors[result.status]?.badge"
              class="px-3 py-1 rounded-full text-xs font-medium"
            >
              {{ result.status_label }}
            </span>
          </div>

          <!-- Info List -->
          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-sm text-gray-500 dark:text-gray-400">Nama</span>
              <span class="text-sm font-medium text-gray-900 dark:text-white">{{ result.nama_lengkap }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500 dark:text-gray-400">No. Registrasi</span>
              <span class="text-sm font-medium text-gray-900 dark:text-white">{{ result.registration_number }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500 dark:text-gray-400">Jurusan</span>
              <span class="text-sm font-medium text-gray-900 dark:text-white">{{ result.jurusan_pilihan }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500 dark:text-gray-400">Asal Sekolah</span>
              <span class="text-sm font-medium text-gray-900 dark:text-white text-right max-w-[60%]">{{ result.asal_sekolah }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500 dark:text-gray-400">Terdaftar</span>
              <span class="text-sm font-medium text-gray-900 dark:text-white">{{ result.registered_at }}</span>
            </div>
          </div>

          <!-- Catatan -->
          <div v-if="result.catatan_admin" class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
            <p class="text-xs text-blue-600 dark:text-blue-400 mb-1">Catatan Admin</p>
            <p class="text-sm text-blue-800 dark:text-blue-200">{{ result.catatan_admin }}</p>
          </div>
        </div>
      </Transition>

    </div>
  </div>
</template>

<script setup>
import { useHead } from '@vueuse/head'
import { CheckCircle, ChevronLeft, Clock, UserCheck, XCircle } from 'lucide-vue-next'
import { reactive, ref, shallowRef } from 'vue'

useHead({
  title: 'Cek Status PPDB - SMA Mutiara Insan Nusantara',
  meta: [
    { name: 'description', content: 'Cek status pendaftaran PPDB SMA Mutiara Insan Nusantara' }
  ]
})

const isLoading = ref(false)
const error = ref('')
const result = ref(null)
const isAlternateMode = ref(false)

// Helper to generate PPDB URLs based on current domain
const ppdbUrl = (path) => {
  const isSubdomain = window.location.hostname.startsWith('ppdb.')
  if (isSubdomain) return path
  return '/ppdb' + (path === '/' ? '/landing' : path)
}

const form = reactive({
  registration_number: '',
  token: ''
})

const altForm = reactive({
  nama: '',
  nisn: ''
})

// Toggle between normal and alternate mode
const toggleMode = () => {
  isAlternateMode.value = !isAlternateMode.value
  error.value = ''
  result.value = null
}

// Block non-numeric keypress
const onlyNumbers = (event) => {
  const char = String.fromCharCode(event.which || event.keyCode)
  if (!/[0-9]/.test(char)) {
    event.preventDefault()
  }
}

// Format NISN - only numbers
const formatNisn = (event) => {
  altForm.nisn = event.target.value.replace(/\D/g, '').slice(0, 10)
}

// Auto-format token with dashes: XX-XX-XX
const formatToken = (event) => {
  let value = event.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '')
  if (value.length > 6) value = value.slice(0, 6)
  
  // Add dashes
  if (value.length > 4) {
    value = value.slice(0, 2) + '-' + value.slice(2, 4) + '-' + value.slice(4)
  } else if (value.length > 2) {
    value = value.slice(0, 2) + '-' + value.slice(2)
  }
  
  form.token = value
}

const statusColors = {
  pending: { bg: 'bg-yellow-100 dark:bg-yellow-900/30', text: 'text-yellow-600', badge: 'bg-yellow-100 text-yellow-800' },
  verified: { bg: 'bg-blue-100 dark:bg-blue-900/30', text: 'text-blue-600', badge: 'bg-blue-100 text-blue-800' },
  selection: { bg: 'bg-purple-100 dark:bg-purple-900/30', text: 'text-purple-600', badge: 'bg-purple-100 text-purple-800' },
  accepted: { bg: 'bg-green-100 dark:bg-green-900/30', text: 'text-green-600', badge: 'bg-green-100 text-green-800' },
  rejected: { bg: 'bg-red-100 dark:bg-red-900/30', text: 'text-red-600', badge: 'bg-red-100 text-red-800' },
}

const statusIcons = shallowRef({
  pending: Clock,
  verified: UserCheck,
  selection: UserCheck,
  accepted: CheckCircle,
  rejected: XCircle,
})

const checkStatus = async () => {
  isLoading.value = true
  error.value = ''
  result.value = null

  try {
    let response
    
    if (isAlternateMode.value) {
      // Search by name + NISN
      response = await fetch('/api/ppdb/find-registration', {
        method: 'POST',
        headers: { 
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify(altForm)
      })
      
      const data = await response.json()
      
      if (data.success) {
        // Redirect to success page with credentials
        const params = new URLSearchParams({
          reg: data.data.registration_number,
          token: data.data.token,
          nama: data.data.nama_lengkap
        })
        window.location.href = ppdbUrl('/sukses') + `?${params.toString()}`
      } else {
        error.value = data.message || 'Data tidak valid'
      }
    } else {
      // Normal check by reg number + token
      response = await fetch('/api/ppdb/check-status', {
        method: 'POST',
        headers: { 
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify(form)
      })
      
      const data = await response.json()
      
      if (data.success) {
        result.value = data.data
      } else {
        error.value = data.message || 'Data tidak ditemukan'
      }
    }
  } catch (err) {
    error.value = 'Terjadi kesalahan. Silakan coba lagi.'
    console.error('Error:', err)
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }

.fade-enter-active, .fade-leave-active {
  transition: all 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
  transform: translateY(10px);
}
</style>
