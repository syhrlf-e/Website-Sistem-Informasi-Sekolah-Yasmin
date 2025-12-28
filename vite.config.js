import vue from '@vitejs/plugin-vue'
import laravel from 'laravel-vite-plugin'
import { defineConfig } from 'vite'

export default defineConfig({
  // base: '/', // Root domain deployment
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false
        }
      }
    })
  ],

  resolve: { alias: { '@': '/resources/js' } },

  // Development-only optimizations (tidak mempengaruhi production)
  server: {
    allowedHosts: ['lions-minimize-runner-britannica.trycloudflare.com'],
    // Faster HMR
    hmr: {
      overlay: true
    },
    // Warmup frequently used files
    warmup: {
      clientFiles: [
        './resources/js/app.js',
        './resources/js/pages/Home.vue',
        './resources/js/components/ui/Navbar.vue'
      ]
    }
  },

  // Pre-bundle dependencies for faster dev startup
  optimizeDeps: {
    include: ['vue', 'vue-router', 'pinia', 'axios', 'lodash']
  },

  // Faster CSS handling in dev
  css: {
    devSourcemap: false // Disable CSS sourcemaps in dev for speed
  },
  build: {
    rollupOptions: {
      output: {
        manualChunks: {
          vendor: ['vue', 'vue-router', 'pinia'],
          ui: ['lucide-vue-next', 'sweetalert2', 'vue-lazyload']
        }
      }
    }
  }
})
