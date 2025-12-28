/**
 * Inertia.js + Vue 3 Application Entry Point
 * 
 * This setup uses Inertia for public pages (SEO optimized)
 * and keeps reactive features for the entire application.
 */

import './bootstrap'
import '../css/app.css'

import { createApp, h } from 'vue'
import { createInertiaApp, Head, Link } from '@inertiajs/vue3'
import { createPinia } from 'pinia'
import { createHead } from '@vueuse/head'
import VueLazyload from 'vue-lazyload'
import Swal from 'sweetalert2'
import { useAuth } from '@/composables/useAuth'
import { useThemeStore } from '@/stores/theme'

createInertiaApp({
  // Resolve page components
  resolve: name => {
    const pages = import.meta.glob('./pages/**/*.vue', { eager: true })
    return pages[`./pages/${name}.vue`]
  },

  // Setup application
  setup({ el, App, props, plugin }) {
    const pinia = createPinia()
    const head = createHead()

    const app = createApp({ render: () => h(App, props) })

    // Use Inertia plugin
    app.use(plugin)

    // Use Pinia for state management
    app.use(pinia)

    // Use head for additional meta management
    app.use(head)

    // Install Vue Router ONLY for admin panel pages
    // Admin panel uses Vue Router SPA, public pages use Inertia
    if (props.initialPage.component.startsWith('Admin/')) {
      import('./router/index.js').then((module) => {
        const router = module.default
        app.use(router)

        // Mount application after router is ready
        router.isReady().then(() => {
          app.mount(el)

          // Initialize auth and theme after mount
          const { initAuth } = useAuth()
          initAuth()

          const themeStore = useThemeStore()
          themeStore.initTheme()
        })
      })
    } else {
      // For public Inertia pages, mount directly without Vue Router
      // Vue Lazyload
      app.use(VueLazyload, {
        preLoad: 1.3,
        // Removed error and loading placeholders to avoid 404s
        // Images will fade in directly when loaded
        attempt: 2,
        lazyComponent: true,
        observerOptions: {
          rootMargin: '50px',
          threshold: 0.1
        }
      })

      // Register Inertia components globally
      app.component('Head', Head)
      app.component('InertiaLink', Link)

      // Mount application
      app.mount(el)

      // Initialize auth and theme after mount  
      const { initAuth } = useAuth()
      initAuth()

      const themeStore = useThemeStore()
      themeStore.initTheme()
    }

    // Global Swal
    window.Swal = Swal
  },

  // Progress bar configuration
  progress: {
    color: '#2563eb', // blue-600
    showSpinner: true,
  },
})
