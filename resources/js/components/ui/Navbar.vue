<!--
  @component Navbar
  @description Fixed navigation bar dengan desktop menu, mobile hamburger, dark mode toggle
  @behavior Smooth scroll ke section, route navigation, responsive design
  @store useThemeStore - Dark mode toggle
-->

<template>
  <nav class="fixed top-0 left-0 right-0 pt-5 transition-all duration-300 z-[60]">
    <div
      class="lg:hidden fixed inset-0 bg-gray-900/50 dark:bg-black/50 backdrop-blur-md transition-opacity duration-300 z-40"
      :class="mobileMenuOpen ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'"
    ></div>
    <div class="container-content">
      <div
        class="bg-white dark:bg-gray-900 backdrop-blur-2xl backdrop-saturate-150 shadow-2xl shadow-black/10 rounded-3xl px-6 transition-all duration-500 ease-[cubic-bezier(0.32,0.72,0,1)] overflow-hidden relative z-50"
        :class="mobileMenuOpen ? 'py-6' : 'py-2 hover:bg-white dark:hover:bg-gray-900'"
      >
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3 cursor-pointer group" @click="goToHome">
            <img
              src="/img/logo_yasmin.png"
              alt="Logo SMAS Mutiara"
              class="h-12 w-12 object-contain transition-transform duration-300 group-hover:drop-shadow-lg group-hover:scale-110"
              fetchpriority="high"
              loading="eager"
            />
          </div>

          <div
            class="hidden lg:flex items-center gap-1 absolute left-1/2 transform -translate-x-1/2"
          >
            <a
              v-for="item in menuItems"
              :key="item.id"
              @click="handleNavClick(item, $event)"
              class="relative px-4 py-2 text-sm font-medium cursor-pointer transition-all duration-300 rounded-lg hover:bg-white/30 dark:hover:bg-white/5"
              :class="
                activeMenu === item.label
                  ? 'text-blue-600 dark:text-white'
                  : 'text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-white'
              "
            >
              <span class="relative z-10">{{ item.label }}</span>
              <span
                v-if="activeMenu === item.label"
                class="absolute bottom-1.5 left-4 right-4 h-0.5 bg-blue-600 dark:bg-white animate-slide-up rounded-full"
              ></span>
            </a>
          </div>

          <div class="flex items-center gap-3">
            <button
              @click="handleDokumenPPDB"
              class="hidden lg:flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white text-sm font-medium transition-all duration-300 shadow-lg shadow-orange-500/20 hover:shadow-orange-500/40 hover:-translate-y-0.5"
            >
              <svg
                class="w-4 h-4"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                viewBox="0 0 24 24"
              >
                <circle cx="12" cy="12" r="10" />
                <path d="M12 16v-4" />
                <path d="M12 8h.01" />
              </svg>
              <span>Info PPDB</span>
            </button>

            <div class="hidden lg:block w-px h-6 bg-gray-400/30 dark:bg-gray-600/50"></div>

            <button
              @click="themeStore.toggleDark()"
              class="hidden lg:block p-2 hover:scale-110 transition-all duration-200 rounded-xl hover:bg-white/50 dark:hover:bg-white/10"
              aria-label="Toggle Dark Mode"
            >
              <svg
                v-if="!themeStore.isDark"
                class="w-5 h-5 text-gray-600 dark:text-gray-400 hover:text-blue-600 transition-colors"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                viewBox="0 0 24 24"
              >
                <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z" />
              </svg>
              <svg
                v-else
                class="w-5 h-5 text-yellow-400 drop-shadow-[0_0_8px_rgba(250,204,21,0.5)]"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                viewBox="0 0 24 24"
              >
                <circle cx="12" cy="12" r="4" />
                <path d="M12 2v2" />
                <path d="M12 20v2" />
                <path d="m4.93 4.93 1.41 1.41" />
                <path d="m17.66 17.66 1.41 1.41" />
                <path d="M2 12h2" />
                <path d="M20 12h2" />
                <path d="m6.34 17.66-1.41 1.41" />
                <path d="m19.07 4.93-1.41 1.41" />
              </svg>
            </button>

            <div class="lg:hidden flex items-center gap-3">
              <button
                @click="themeStore.toggleDark()"
                class="p-2 hover:scale-110 transition-transform duration-200 rounded-lg hover:bg-black/5 dark:hover:bg-white/5"
                aria-label="Toggle Dark Mode"
              >
                <svg
                  v-if="!themeStore.isDark"
                  class="w-5 h-5 text-gray-600 dark:text-gray-400"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  viewBox="0 0 24 24"
                >
                  <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z" />
                </svg>
                <svg
                  v-else
                  class="w-5 h-5 text-yellow-500"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  viewBox="0 0 24 24"
                >
                  <circle cx="12" cy="12" r="4" />
                  <path d="M12 2v2" />
                  <path d="M12 20v2" />
                  <path d="m4.93 4.93 1.41 1.41" />
                  <path d="m17.66 17.66 1.41 1.41" />
                  <path d="M2 12h2" />
                  <path d="M20 12h2" />
                  <path d="m6.34 17.66-1.41 1.41" />
                  <path d="m19.07 4.93-1.41 1.41" />
                </svg>
              </button>
              <div class="w-px h-6 bg-gray-300 dark:bg-gray-600"></div>
              <button
                @click="mobileMenuOpen = !mobileMenuOpen"
                class="p-2 hover:scale-110 transition-transform duration-200 rounded-lg hover:bg-black/5 dark:hover:bg-white/5"
                aria-label="Toggle Menu"
              >
                <Menu v-if="!mobileMenuOpen" class="w-6 h-6 text-gray-700 dark:text-gray-300" />
                <X v-else class="w-6 h-6 text-gray-700 dark:text-gray-300" />
              </button>
            </div>
          </div>
        </div>

        <div
          v-show="mobileMenuOpen"
          class="lg:hidden border-t border-gray-200/30 dark:border-gray-700/30 mt-4"
        >
          <div class="pt-4 pb-4 space-y-2">
            <a
              v-for="(item, index) in menuItems"
              :key="item.id"
              @click="handleNavClick(item, $event)"
              :style="{ transitionDelay: mobileMenuOpen ? `${index * 50}ms` : '0ms' }"
              class="flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-300 cursor-pointer group opacity-0 translate-y-2"
              :class="[
                activeMenu === item.label
                  ? 'bg-blue-50/50 dark:bg-blue-900/20 text-blue-600 dark:text-white'
                  : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50/50 dark:hover:bg-white/5',
                mobileMenuOpen ? 'animate-slide-in' : ''
              ]"
            >
              <span class="font-medium group-hover:translate-x-1 transition-transform">{{
                item.label
              }}</span>
              <ArrowLeft
                v-if="activeMenu === item.label"
                class="w-5 h-5 text-blue-600 dark:text-white animate-bounce-x"
              />
            </a>
          </div>
          <div class="pt-4 pb-2 border-t border-gray-200/30 dark:border-gray-700/30">
            <button
              @click="handleDokumenPPDB"
              class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-medium transition-all duration-300 shadow-lg shadow-orange-500/20 opacity-0 translate-y-2"
              :class="mobileMenuOpen ? 'animate-slide-in' : ''"
              style="transition-delay: 300ms"
            >
              <svg
                class="w-5 h-5"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                viewBox="0 0 24 24"
              >
                <circle cx="12" cy="12" r="10" />
                <path d="M12 16v-4" />
                <path d="M12 8h.01" />
              </svg>
              <span>Info PPDB</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ArrowLeft, Menu, X } from 'lucide-vue-next'
import { computed, ref, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { useThemeStore } from '../../stores/theme'

const props = defineProps({
  isModalOpen: {
    type: Boolean,
    default: false
  }
})

// Use Inertia's usePage instead of Vue Router
const page = usePage()
const currentUrl = computed(() => page.url)
const themeStore = useThemeStore()
const mobileMenuOpen = ref(false)
const activeMenu = ref('Beranda')

// Lock body scroll when mobile menu is open
watch(
  () => mobileMenuOpen.value,
  (isOpen) => {
    if (isOpen) {
      document.body.style.overflow = 'hidden'
    } else {
      document.body.style.overflow = ''
    }
  }
)

const menuItems = [
  { id: 1, label: 'Beranda', type: 'scroll', href: '#hero', route: '/' },
  { id: 2, label: 'Profil', type: 'route', href: '/profil' },
  { id: 3, label: 'Prestasi', type: 'scroll', href: '#prestasi', route: '/' },
  { id: 4, label: 'Galeri', type: 'scroll', href: '#galeri', route: '/' },
  { id: 5, label: 'Berita', type: 'scroll', href: '#berita', route: '/' },
  { id: 6, label: 'Kontak', type: 'scroll', href: '#kontak', route: '/' }
]

const handleNavClick = (item, event) => {
  event?.preventDefault()
  activeMenu.value = item.label
  mobileMenuOpen.value = false

  if (item.type === 'route') {
    setTimeout(() => {
      router.visit(item.href)
    }, 300)
  } else if (item.type === 'scroll') {
    const currentPath = currentUrl.value
    const isOnHomepage =
      currentPath === '/sma-yasmin' ||
      currentPath === '/sma-yasmin/' ||
      currentPath === '/' ||
      currentPath === ''

    if (isOnHomepage) {
      setTimeout(
        () => {
          scrollToSection(item.href)
        },
        mobileMenuOpen.value ? 400 : 0
      )
    } else {
      router.visit('/', {
        onSuccess: () => {
          setTimeout(() => {
            scrollToSection(item.href)
          }, 600)
        }
      })
    }
  }
}

const scrollToSection = (href) => {
  const targetId = href.replace('#', '')
  const target = document.getElementById(targetId)

  if (target) {
    const targetPosition = target.getBoundingClientRect().top + window.pageYOffset
    const startPosition = window.pageYOffset
    const distance = targetPosition - startPosition
    const duration = 1000
    let start = null

    const animation = (currentTime) => {
      if (start === null) start = currentTime
      const timeElapsed = currentTime - start
      const run = ease(timeElapsed, startPosition, distance, duration)
      window.scrollTo(0, run)
      if (timeElapsed < duration) requestAnimationFrame(animation)
    }

    const ease = (t, b, c, d) => {
      t /= d / 2
      if (t < 1) return (c / 2) * t * t + b
      t--
      return (-c / 2) * (t * (t - 2) - 1) + b
    }

    requestAnimationFrame(animation)
  }
}

const goToHome = () => {
  router.visit('/')
  activeMenu.value = 'Beranda'
}

watch(
  currentUrl,
  (newPath) => {
    if (newPath === '/sma-yasmin' || newPath === '/sma-yasmin/' || newPath === '/' || newPath === '') {
      activeMenu.value = 'Beranda'
    } else if (newPath.includes('/profil')) {
      activeMenu.value = 'Profil'
    } else if (newPath.includes('/ppdb')) {
      activeMenu.value = 'Info PPDB'
    }
  },
  { immediate: true }
)

const handleDokumenPPDB = () => {
  mobileMenuOpen.value = false
  router.visit('/ppdb')
}
</script>

<style scoped>
@keyframes slide-up {
  from {
    transform: translateY(8px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.animate-slide-up {
  animation: slide-up 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes slide-in {
  from {
    opacity: 0;
    transform: translateY(8px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-slide-in {
  animation: slide-in 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes bounce-x {
  0%,
  100% {
    transform: translateX(0);
  }
  50% {
    transform: translateX(-4px);
  }
}

.animate-bounce-x {
  animation: bounce-x 1s ease-in-out infinite;
}
</style>
