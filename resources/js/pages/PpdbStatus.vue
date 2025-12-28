<!--
  @component PpdbStatus
  @description Check PPDB registration status page
  @route /ppdb/status
-->

<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-12 px-4 flex items-center justify-center">
    <div class="max-w-lg w-full">

      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-2 font-poppins">
          Cek Status Pendaftaran
        </h1>
        <p class="text-gray-600 dark:text-gray-300 font-poppins">
          Masukkan Nomor Registrasi dan Token Anda
        </p>
      </div>

      <!-- Form -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-gray-700 mb-6">
        <form @submit.prevent="checkStatus" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nomor Registrasi</label>
            <input
              v-model="form.registration_number"
              type="text"
              required
              class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-blue-500"
              placeholder="PPDB-2024-0001"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Token</label>
            <input
              v-model="form.token"
              type="text"
              required
              maxlength="6"
              class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-mono text-center text-2xl tracking-widest uppercase focus:ring-2 focus:ring-blue-500"
              placeholder="ABC123"
            />
          </div>
          <button
            type="submit"
            :disabled="isLoading"
            class="w-full py-3 px-6 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 disabled:from-gray-400 disabled:to-gray-500 text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2"
          >
            <div v-if="isLoading" class="animate-spin w-5 h-5 border-2 border-white border-t-transparent rounded-full"></div>
            <Search v-else class="w-5 h-5" />
            {{ isLoading ? 'Mencari...' : 'Cek Status' }}
          </button>
        </form>

        <!-- Error -->
        <div v-if="error" class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
          <p class="text-red-700 dark:text-red-300 text-sm">{{ error }}</p>
        </div>
      </div>

      <!-- Result -->
      <Transition name="fade">
        <div v-if="result" class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-gray-700 mb-6">
          <div class="text-center mb-6">
            <div
              :class="statusColors[result.status]?.bg"
              class="w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4"
            >
              <component :is="statusIcons[result.status]" class="w-8 h-8" :class="statusColors[result.status]?.text" />
            </div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ result.nama_lengkap }}</h2>
            <p class="text-gray-500 font-mono text-sm">{{ result.registration_number }}</p>
          </div>

          <div class="space-y-4">
            <!-- Status -->
            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
              <span class="text-gray-600 dark:text-gray-400">Status</span>
              <span
                :class="statusColors[result.status]?.badge"
                class="px-4 py-1.5 rounded-full font-semibold"
              >
                {{ result.status_label }}
              </span>
            </div>

            <!-- Info -->
            <div class="grid grid-cols-2 gap-4">
              <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                <p class="text-xs text-gray-500 mb-1">Jurusan</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ result.jurusan_pilihan }}</p>
              </div>
              <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                <p class="text-xs text-gray-500 mb-1">Asal Sekolah</p>
                <p class="font-semibold text-gray-900 dark:text-white text-sm">{{ result.asal_sekolah }}</p>
              </div>
            </div>

            <!-- Catatan -->
            <div v-if="result.catatan_admin" class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
              <p class="text-xs text-blue-600 mb-1">Catatan dari Admin</p>
              <p class="text-blue-800 dark:text-blue-200">{{ result.catatan_admin }}</p>
            </div>

            <!-- Date -->
            <p class="text-center text-sm text-gray-500">
              Terdaftar pada: {{ result.registered_at }}
            </p>
          </div>
        </div>
      </Transition>

      <!-- Back Link -->
      <div class="text-center">
        <a href="/ppdb/landing" class="inline-flex items-center gap-2 text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 text-sm transition-colors">
          ← Kembali ke halaman PPDB
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
import BackButton from '@/components/ui/BackButton.vue'
import { useHead } from '@vueuse/head'
import { CheckCircle, Clock, Search, UserCheck, XCircle } from 'lucide-vue-next'
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

const form = reactive({
  registration_number: '',
  token: ''
})

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
    const response = await fetch('/api/ppdb/check-status', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(form)
    })
    
    const data = await response.json()
    
    if (data.success) {
      result.value = data.data
    } else {
      error.value = data.message || 'Data tidak ditemukan'
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
