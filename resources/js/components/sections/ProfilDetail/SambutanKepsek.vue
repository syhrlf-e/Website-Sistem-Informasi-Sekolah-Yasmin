<template>
  <section
    ref="sambutanSection"
    class="relative py-16 md:py-24 bg-white dark:bg-gray-900 transition-colors duration-300 overflow-hidden"
  >
    <div
      class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-blue-50 dark:bg-blue-900/10 rounded-full blur-3xl opacity-50"
    ></div>
    <div
      class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 bg-indigo-50 dark:bg-indigo-900/10 rounded-full blur-3xl opacity-50"
    ></div>
    <!-- OLD: container mx-auto px-6 -->
    <div class="container-content relative z-10">
      <div class="grid lg:grid-cols-12 gap-12 lg:gap-20 items-center">
        <div
          ref="photoRef"
          class="lg:col-span-5 relative order-2 lg:order-1 transition-all duration-1000 ease-out"
          :class="contentVisible ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-12'"
        >
          <div class="absolute -top-10 -left-10 w-40 h-40 text-blue-200 dark:text-gray-800">
            <svg width="100%" height="100%" fill="currentColor">
              <pattern id="dots" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                <circle cx="2" cy="2" r="2" />
              </pattern>
              <rect width="100%" height="100%" fill="url(#dots)" />
            </svg>
          </div>
          <div class="relative rounded-2xl overflow-hidden shadow-2xl group">
            <img
              src="/images/kepsek/Pak Fauzan.webp"
              alt="Kepala Sekolah"
              class="w-full h-auto object-cover transform transition-transform duration-700 group-hover:scale-105"
            />
            <div
              class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-60"
            ></div>
          </div>
          <div
            class="absolute -bottom-6 -right-6 md:-right-10 bg-white dark:bg-gray-800 p-4 md:p-6 rounded-xl shadow-xl max-w-[220px] md:max-w-[260px] border-l-4 border-blue-600 dark:border-blue-400"
          >
            <p class="text-sm text-gray-600 dark:text-gray-300 italic leading-relaxed">
              "Pendidikan bukan hanya tentang mengisi wadah, tapi menyalakan api."
            </p>
            <div class="mt-3 flex items-center gap-2">
              <div class="w-8 h-1 bg-blue-600 dark:bg-blue-400 rounded-full"></div>
              <span class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider"
                >Motto</span
              >
            </div>
          </div>
        </div>
        <div
          ref="textRef"
          class="lg:col-span-7 order-1 lg:order-2 transition-all duration-1000 ease-out delay-200"
          :class="contentVisible ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-12'"
        >
          <div class="flex items-center gap-3 mb-3 md:mb-6">
            <span
              class="px-3 py-1 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-sm md:text-md tracking-wider"
            >
              Sambutan Kepala Sekolah
            </span>
            <div class="h-px flex-1 bg-gray-200 dark:bg-gray-700"></div>
          </div>
          <h2
            class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white font-poppins leading-tight mb-6 md:mb-8"
          >
            Mewujudkan Generasi <br />
            <span
              class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400"
            >
              Cerdas & Berkarakter
            </span>
          </h2>
          <div
            class="space-y-5 text-gray-600 dark:text-gray-300 text-base md:text-lg leading-relaxed"
          >
            <p>
              Assalamu'alaikum Warahmatullahi Wabarakatuh. Puji syukur kehadirat Allah SWT. Saya
              menyambut hangat kehadiran Anda di website resmi SMAS Mutiara Insan Nusantara.
            </p>
            <p>
              Pendidikan adalah modal dasar untuk melanjutkan tongkat estafet kekhalifahan di bumi.
              Semakin baik pendidikan maka semakin baik kehidupan di bumi.
            </p>
            <p>
              SMA Mutiara Insan Nusantara hadir menyajikan pendidikan yang lebih baik untuk
              melanjutkannya. Dengan Visi dan Misi Mewujudkan Generasi Yang Berakhlak Berprestasi
              Berkarakter Berbudaya, Kami hadir untuk kehidupan dan pendidikan yang lebih baik.
            </p>
            <p>
              Guru guru kami berasal dari universitas terkemuka dan bersertifikat siap mendidik anak
              bangsa menjadi generasi yang unggul siap berkompetisi melawan tantangan zaman Dengan
              fasilitas yang memadai, gedung, sarana ibadah, olahraga, laboratorium, tempat parkir,
              kantin siap menjadi tempat yang nyaman untuk belajar dan beribadah.
            </p>
            <p>
              Harapan Kami, SMA Mutiara Insan Nusantara bisa mencetak para tokoh tokoh pembangun
              bangsa
            </p>
            <p>Waalaikumussalam_Wr Wb</p>
          </div>
          <div class="mt-8 md:mt-10 flex items-center gap-4 md:gap-6">
            <div class="flex flex-col">
              <span class="text-xl font-bold text-gray-900 dark:text-white font-poppins">
                AHMAD FAUZAN, S.Pd.I
              </span>
              <span class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                Kepala SMA Mutiara Insan Nusantara
              </span>
            </div>

            <div class="w-px h-10 md:h-12 bg-gray-300 dark:bg-gray-700"></div>

            <div
              class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-blue-600 dark:text-blue-400"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="w-6 h-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
<script setup>
import { onMounted, onUnmounted, ref } from 'vue'

const sambutanSection = ref(null)
const textRef = ref(null)
const photoRef = ref(null)
const contentVisible = ref(false)
let observer = null
const handleIntersection = (entries) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      contentVisible.value = true
      observer.disconnect()
    }
  })
}
onMounted(() => {
  observer = new IntersectionObserver(handleIntersection, {
    threshold: 0.2,
    rootMargin: '0px 0px -100px 0px'
  })
  if (sambutanSection.value) observer.observe(sambutanSection.value)
})
onUnmounted(() => {
  if (observer) observer.disconnect()
})
</script>
<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
