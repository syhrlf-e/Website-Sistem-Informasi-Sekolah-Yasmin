/** @type {import('tailwindcss').Config} */
export default {
  content: ['./resources/**/*.blade.php', './resources/**/*.js', './resources/**/*.vue'],
  darkMode: 'class',
  theme: {
    extend: {
      fontFamily: {
        sans: ['Poppins', 'sans-serif']
      },
      colors: {
        // Light Mode
        light: {
          bg: {
            primary: '#ffffff',
            secondary: '#f8fafc', // slate-50
            tertiary: '#f1f5f9' // slate-100
          },
          text: {
            primary: '#0f172a', // slate-900
            secondary: '#475569', // slate-600
            muted: '#94a3b8' // slate-400
          }
        },

        // Dark Mode
        dark: {
          bg: {
            primary: '#0f172a', // slate-900
            secondary: '#1e293b', // slate-800
            tertiary: '#334155', // slate-700
            elevated: '#475569' // slate-600 (hover)
          },
          text: {
            primary: '#f1f5f9', // slate-100
            secondary: '#cbd5e1', // slate-300
            muted: '#94a3b8' // slate-400
          },
          border: {
            primary: '#334155', // slate-700
            secondary: '#475569' // slate-600
          }
        },

        brand: {
          // Primary Blue
          'primary-light': '#2563eb', // blue-600
          'primary-dark': '#3b82f6', // blue-500

          // Secondary Purple/Cyan
          'secondary-light': '#7c3aed', // violet-600
          'secondary-dark': '#06b6d4' // cyan-500
        }
      }
    }
  },
  plugins: []
}
