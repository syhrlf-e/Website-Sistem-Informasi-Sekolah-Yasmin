/**
 * @component EkskulListModal
 * @description Modal form untuk tambah/edit ekstrakurikuler dengan side-by-side registration panel
 */

<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-opacity duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="show"
        class="fixed inset-0 z-[9999] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
      >
        <!-- Side-by-Side Container -->
        <div class="flex items-stretch gap-4 my-auto">
          <!-- Main Modal -->
          <div
            class="bg-white dark:bg-gray-800 rounded-2xl w-[600px] flex flex-col border border-gray-200 dark:border-gray-700 shadow-2xl transition-all duration-300"
            style="max-height: calc(100vh - 2rem)"
          >
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
              <h2 class="text-2xl font-bold text-gray-900 dark:text-white font-poppins">
                {{ editMode ? 'Edit Ekstrakurikuler' : 'Tambah Ekstrakurikuler Baru' }}
              </h2>
              <button @click="$emit('close')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <X class="w-5 h-5 text-gray-500" />
              </button>
            </div>

            <!-- Form Body -->
            <div class="overflow-y-auto flex-1 p-6 modal-scrollbar">
              <div class="space-y-4">
                <!-- Nama -->
                <div>
                  <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">
                    Nama Ekstrakurikuler <span class="text-red-500">*</span>
                  </label>
                  <input
                    v-model="localForm.nama"
                    type="text"
                    required
                    placeholder="Contoh: English Club"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white font-poppins"
                  />
                </div>

                <!-- Tagline -->
                <div>
                  <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">Tagline</label>
                  <input
                    v-model="localForm.tagline"
                    type="text"
                    placeholder="Contoh: Speak English with Confidence!"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white font-poppins"
                  />
                </div>

                <!-- Pembina & Badge -->
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">
                      Pembina <span class="text-red-500">*</span>
                    </label>
                    <input
                      v-model="localForm.pembina"
                      type="text"
                      required
                      placeholder="Nama Pembina"
                      class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white font-poppins"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">
                      Badge/Kategori <span class="text-red-500">*</span>
                    </label>
                    <select
                      v-model="localForm.badge"
                      required
                      class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-900 dark:text-white font-poppins"
                    >
                      <option value="">Pilih Badge</option>
                      <option value="Akademik">Akademik</option>
                      <option value="Olahraga">Olahraga</option>
                      <option value="Seni">Seni</option>
                      <option value="Kepemimpinan">Kepemimpinan</option>
                    </select>
                  </div>
                </div>

                <!-- Jadwal & Lokasi -->
                <div class="grid grid-cols-2 gap-4">
                  <div class="grid grid-cols-2 gap-2">
                    <!-- Hari Picker -->
                    <div class="relative">
                      <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">
                        Hari <span class="text-red-500">*</span>
                      </label>
                      <button
                        type="button"
                        @click="showDayPicker = !showDayPicker"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-left flex items-center justify-between"
                      >
                        <span v-if="localForm.selectedDays.length > 0" class="text-gray-900 dark:text-white text-sm truncate">
                          {{ localForm.selectedDays.join(', ') }}
                        </span>
                        <span v-else class="text-gray-400 text-sm">Pilih Hari</span>
                        <Calendar class="w-4 h-4 text-gray-400 flex-shrink-0" />
                      </button>
                      <div v-if="showDayPicker" class="absolute z-10 mt-2 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg p-3">
                        <div class="space-y-2">
                          <label v-for="day in availableDays" :key="day" class="flex items-center gap-2 p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg cursor-pointer">
                            <input type="checkbox" :value="day" v-model="localForm.selectedDays" class="w-4 h-4 text-blue-600" />
                            <span class="text-sm text-gray-900 dark:text-white">{{ day }}</span>
                          </label>
                        </div>
                        <button type="button" @click="showDayPicker = false" class="mt-3 w-full px-3 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold">Selesai</button>
                      </div>
                    </div>

                    <!-- Jam Picker -->
                    <div class="relative">
                      <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">
                        Jam <span class="text-red-500">*</span>
                      </label>
                      <button
                        type="button"
                        @click="showTimePicker = !showTimePicker"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-left flex items-center justify-between"
                      >
                        <span v-if="localForm.scheduleStartTime && localForm.scheduleEndTime" class="text-gray-900 dark:text-white text-sm">
                          {{ localForm.scheduleStartTime }}-{{ localForm.scheduleEndTime }}
                        </span>
                        <span v-else class="text-gray-400 text-sm">Pilih Jam</span>
                      </button>
                      <div v-if="showTimePicker" class="absolute z-10 mt-2 w-64 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg p-4">
                        <div class="grid grid-cols-2 gap-3">
                          <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2 text-center">Jam Mulai</label>
                            <div class="h-32 overflow-y-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                              <button v-for="hour in timeOptions" :key="'start-' + hour" type="button" @click="localForm.scheduleStartTime = hour"
                                :class="['w-full px-3 py-2 text-sm text-gray-900 dark:text-white', localForm.scheduleStartTime === hour ? 'bg-blue-600 !text-white font-semibold' : 'hover:bg-gray-100 dark:hover:bg-gray-700']">
                                {{ hour }}
                              </button>
                            </div>
                          </div>
                          <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2 text-center">Jam Selesai</label>
                            <div class="h-32 overflow-y-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                              <button v-for="hour in timeOptions" :key="'end-' + hour" type="button" @click="localForm.scheduleEndTime = hour"
                                :class="['w-full px-3 py-2 text-sm text-gray-900 dark:text-white', localForm.scheduleEndTime === hour ? 'bg-blue-600 !text-white font-semibold' : 'hover:bg-gray-100 dark:hover:bg-gray-700']">
                                {{ hour }}
                              </button>
                            </div>
                          </div>
                        </div>
                        <button type="button" @click="showTimePicker = false" class="mt-3 w-full px-3 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold">Selesai</button>
                      </div>
                    </div>
                  </div>

                  <div>
                    <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">
                      Lokasi <span class="text-red-500">*</span>
                    </label>
                    <input
                      v-model="localForm.lokasi"
                      type="text"
                      required
                      placeholder="Contoh: Ruang Lab Bahasa"
                      class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white font-poppins"
                    />
                  </div>
                </div>

                <!-- Image Upload with Cropper -->
                <div>
                  <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">Gambar</label>
                  
                  <!-- Preview Cropped Image -->
                  <div v-if="croppedPreview || imagePreview || (editMode && localForm.gambar)" class="mb-3">
                    <div class="relative group">
                      <img 
                        :src="croppedPreview || imagePreview || getImageUrl(localForm.gambar)" 
                        alt="Preview" 
                        class="w-full h-48 object-cover rounded-xl border border-gray-200 dark:border-gray-700" 
                      />
                      <!-- Overlay dengan badge -->
                      <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity rounded-xl flex items-center justify-center gap-3">
                        <button
                          type="button"
                          @click="triggerFileInput"
                          class="px-4 py-2 bg-white/90 text-gray-900 rounded-lg font-medium text-sm flex items-center gap-2 hover:bg-white transition-colors"
                        >
                          <ImageIcon class="w-4 h-4" />
                          Ganti
                        </button>
                        <button
                          type="button"
                          @click="handleRemoveImage"
                          class="px-4 py-2 bg-red-500 text-white rounded-lg font-medium text-sm flex items-center gap-2 hover:bg-red-600 transition-colors"
                        >
                          <Trash2 class="w-4 h-4" />
                          Hapus
                        </button>
                      </div>
                      <!-- Cropped Badge -->
                      <div v-if="croppedPreview" class="absolute top-2 left-2 px-2 py-1 bg-green-500 text-white text-xs rounded-full font-medium flex items-center gap-1">
                        <Check class="w-3 h-3" />
                        Gambar dikrop (16:9)
                      </div>
                    </div>
                  </div>
                  
                  <!-- File Input Area -->
                  <div
                    v-if="!croppedPreview && !imagePreview && !(editMode && localForm.gambar)"
                    class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 text-center hover:border-blue-500 dark:hover:border-blue-400 transition-colors duration-200 cursor-pointer"
                    @click="triggerFileInput"
                  >
                    <Upload class="w-10 h-10 text-gray-400 mx-auto mb-2" />
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 font-poppins">
                      Klik untuk upload gambar
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, WEBP maks 2MB. Akan di-crop ke rasio 16:9.</p>
                  </div>
                  
                  <input 
                    ref="fileInput" 
                    type="file" 
                    accept="image/*" 
                    class="hidden"
                    @change="handleFileSelect" 
                  />
                </div>

                <!-- Deskripsi -->
                <div>
                  <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">
                    Deskripsi <span class="text-red-500">*</span>
                  </label>
                  <textarea v-model="localForm.deskripsi" rows="4" required placeholder="Deskripsi lengkap..."
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl resize-none text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <!-- Benefits -->
                <div>
                  <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2 font-poppins">Yang Kamu Dapatkan</label>
                  <div class="space-y-2">
                    <div v-for="(benefit, index) in localForm.benefits" :key="'benefit-' + index" class="flex items-center gap-2">
                      <input v-model="localForm.benefits[index]" type="text" placeholder="Contoh: Meningkatkan kemampuan..."
                        class="flex-1 px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" />
                      <button v-if="localForm.benefits.length > 1" type="button" @click="removeBenefit(index)"
                        class="p-3 bg-red-50 dark:bg-red-900/20 text-red-600 rounded-xl">
                        <Trash2 class="w-4 h-4" />
                      </button>
                    </div>
                    <button type="button" @click="addBenefit"
                      class="w-full px-4 py-3 bg-gray-50 border border-dashed border-gray-300 rounded-xl text-gray-600">+ Tambah Benefit</button>
                  </div>
                </div>

                <!-- Toggle for Registration Panel -->
                <div class="border-t pt-4 mt-4">
                  <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 rounded-xl">
                    <div>
                      <p class="font-semibold text-gray-900 dark:text-white">
                        Pengaturan Pendaftaran
                      </p>
                      <p class="text-sm text-gray-600 dark:text-gray-400">Aktifkan untuk menampilkan panel pengaturan</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                      <input v-model="localForm.enableRegistration" type="checkbox" class="sr-only peer" />
                      <div class="w-11 h-6 bg-gray-200 peer-checked:bg-blue-600 rounded-full peer after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                    </label>
                  </div>
                </div>

                <!-- Status Active -->
                <div class="flex items-center gap-3">
                  <input v-model="localForm.is_active" type="checkbox" id="is_active" class="w-5 h-5 text-blue-600" />
                  <label for="is_active" class="text-sm font-semibold text-gray-900 dark:text-white">Aktifkan Ekstrakurikuler</label>
                </div>
              </div>
            </div>

            <!-- Footer -->
            <div class="p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50/50 flex-shrink-0">
              <div class="flex items-center gap-3">
                <button @click="handleSubmit" :disabled="submitting"
                  class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 disabled:from-gray-400 disabled:to-gray-400 text-white rounded-xl font-semibold">
                  {{ submitting ? 'Menyimpan...' : editMode ? 'Update' : 'Simpan' }}
                </button>
                <button type="button" @click="$emit('close')" :disabled="submitting"
                  class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold">Batal</button>
              </div>
            </div>
          </div>

          <!-- Secondary Panel - Registration Settings -->
          <Transition
            enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0 translate-x-4 w-0"
            enter-to-class="opacity-100 translate-x-0 w-[350px]"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100 translate-x-0 w-[350px]"
            leave-to-class="opacity-0 translate-x-4 w-0"
          >
            <div
              v-if="localForm.enableRegistration"
              class="bg-white dark:bg-gray-800 rounded-2xl w-[350px] flex flex-col border border-gray-200 dark:border-gray-700 shadow-2xl overflow-hidden"
              style="max-height: calc(100vh - 2rem)"
            >
              <!-- Panel Header -->
              <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex-shrink-0">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white font-poppins">
                  Pengaturan Pendaftaran
                </h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Konfigurasi pendaftaran siswa</p>
              </div>

              <!-- Panel Body -->
              <div class="overflow-y-auto flex-1 p-6 modal-scrollbar space-y-6">
                <!-- Kode Generation -->
                <div>
                  <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Kode Registrasi</label>
                  <input :value="form.registrationToken" type="text" readonly placeholder="Klik 'Generate' untuk membuat kode"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl font-mono text-sm text-gray-900 dark:text-white mb-3" />
                  <!-- Generate Button -->
                  <button v-if="canGenerateToken && !generatingToken" @click="$emit('generate-token', localForm.nama)" type="button"
                    class="w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold flex items-center justify-center gap-2 transition-colors">
                    <RefreshCw class="w-4 h-4" />
                    Generate Kode
                  </button>
                  <!-- Loading Button -->
                  <button v-else-if="generatingToken" type="button" disabled
                    class="w-full px-4 py-3 bg-blue-400 text-white rounded-xl font-semibold flex items-center justify-center gap-2 cursor-wait">
                    <RefreshCw class="w-4 h-4 animate-spin" />
                    Generating...
                  </button>
                  <!-- Copy Button -->
                  <button v-else @click="$emit('copy-token')" type="button"
                    :class="['w-full px-4 py-3 rounded-xl font-semibold flex items-center justify-center gap-2 transition-colors', tokenCopied ? 'bg-green-600 text-white' : 'bg-blue-600 hover:bg-blue-700 text-white']">
                    <Copy v-if="!tokenCopied" class="w-4 h-4" />
                    {{ tokenCopied ? '✓ Copied!' : 'Copy Kode' }}
                  </button>
                  <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">💡 Kode berlaku 24 jam</p>
                </div>

                <!-- Max Participants -->
                <div>
                  <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Maksimal Peserta</label>
                  <input v-model.number="localForm.maxParticipants" type="number" min="1" placeholder="30"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white" />
                </div>

                <!-- Deadline -->
                <div>
                  <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Batas Waktu Pendaftaran</label>
                  <div class="space-y-2">
                    <input v-model="localForm.deadlineDate" type="date" 
                      class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white" />
                    <input v-model="localForm.deadlineTime" type="time" 
                      class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white" />
                  </div>
                </div>

                <!-- WhatsApp Admin -->
                <div>
                  <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">WhatsApp Admin</label>
                  <input v-model="localForm.whatsappAdmin" type="text" placeholder="628xxxxxxxxxx"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white" />
                  <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">📱 Nomor WhatsApp untuk notifikasi ke siswa saat approved</p>
                </div>
              </div>
            </div>
          </Transition>
        </div>
      </div>
    </Transition>

    <!-- Image Cropper Modal -->
    <ImageCropperModal
      :show="showCropper"
      :image-src="rawImageSrc"
      :aspect-ratio="16/9"
      @close="showCropper = false"
      @crop="handleCrop"
      @change-image="handleChangeImage"
    />
  </Teleport>
</template>

<script setup>
import { Calendar, Check, Copy, Image as ImageIcon, RefreshCw, Trash2, Upload, X } from 'lucide-vue-next'
import { ref, watch } from 'vue'
import ImageCropperModal from '@/components/ui/shared/ImageCropperModal.vue'

const props = defineProps({
  show: Boolean,
  editMode: Boolean,
  form: Object,
  submitting: Boolean,
  canGenerateToken: Boolean,
  generatingToken: Boolean,
  tokenCopied: Boolean,
  imagePreview: String
})

const emit = defineEmits(['close', 'submit', 'generate-token', 'copy-token', 'image-crop', 'remove-image'])

const localForm = ref({ ...props.form })
const showDayPicker = ref(false)
const showTimePicker = ref(false)
const fileInput = ref(null)

const availableDays = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']
const timeOptions = []
for (let hour = 6; hour <= 22; hour++) {
  for (let minute = 0; minute < 60; minute += 30) {
    timeOptions.push(`${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')}`)
  }
}

watch(() => props.form, (newForm) => {
  localForm.value = { 
    ...newForm,
    benefits: [...(newForm.benefits || [''])]
  }
}, { immediate: true })

const getImageUrl = (path) => {
  if (!path) return null
  if (path.startsWith('http')) return path
  if (path.startsWith('/storage')) return path
  return `/storage/${path}`
}

const addBenefit = () => localForm.value.benefits.push('')
const removeBenefit = (index) => localForm.value.benefits.splice(index, 1)

// Cropper state
const showCropper = ref(false)
const rawImageSrc = ref('')
const croppedPreview = ref('')

const triggerFileInput = () => {
  fileInput.value?.click()
}

// Handle file selection - open cropper
const handleFileSelect = (event) => {
  const file = event.target.files?.[0]
  if (!file) return

  if (!file.type.startsWith('image/')) {
    alert('Pilih file gambar yang valid!')
    return
  }

  if (file.size > 2 * 1024 * 1024) {
    alert('Ukuran file maksimal 2MB!')
    return
  }

  // Read file and open cropper
  const reader = new FileReader()
  reader.onload = (e) => {
    rawImageSrc.value = e.target.result
    showCropper.value = true
  }
  reader.readAsDataURL(file)
  
  // Reset input
  event.target.value = ''
}

// Handle cropped result from cropper modal
const handleCrop = (blob) => {
  croppedPreview.value = URL.createObjectURL(blob)
  emit('image-crop', blob)
}

// Handle image change from cropper modal
const handleChangeImage = (newImageSrc) => {
  rawImageSrc.value = newImageSrc
}

// Handle remove image
const handleRemoveImage = () => {
  croppedPreview.value = ''
  rawImageSrc.value = ''
  localForm.value.gambar = null
  if (fileInput.value) {
    fileInput.value.value = ''
  }
  emit('remove-image')
}

const handleSubmit = () => emit('submit', localForm.value)

// Reset cropper state when modal closes
watch(() => props.show, (isVisible) => {
  if (!isVisible) {
    croppedPreview.value = ''
    rawImageSrc.value = ''
  }
})
</script>

<style scoped>
.font-poppins { font-family: 'Poppins', sans-serif; }
.modal-scrollbar { scrollbar-width: thin; scrollbar-color: rgba(37, 99, 235, 0.3) transparent; }
.modal-scrollbar::-webkit-scrollbar { width: 8px; }
.modal-scrollbar::-webkit-scrollbar-thumb { background: linear-gradient(180deg, rgba(37, 99, 235, 0.4), rgba(37, 99, 235, 0.6)); border-radius: 10px; }
</style>
