/**
 * @file stores/theme.js
 * @description Pinia store untuk dark mode dan theme colors
 * @provides useThemeStore
 */

import { defineStore } from 'pinia'
import { ref, watch } from 'vue'

export const useThemeStore = defineStore('theme', () => {
  const isDark = ref(false)
  const currentTheme = ref('default')

  const themes = {
    default: {
      primary: '#3b82f6',
      secondary: '#8b5cf6',
      accent: '#06b6d4'
    },
    yasmin: {
      primary: '#ec4899',
      secondary: '#a855f7',
      accent: '#f59e0b'
    },
    ocean: {
      primary: '#0ea5e9',
      secondary: '#06b6d4',
      accent: '#14b8a6'
    }
  }

  const initTheme = () => {
    const savedDarkMode = localStorage.getItem('darkMode')
    const savedTheme = localStorage.getItem('currentTheme')

    if (savedDarkMode !== null) {
      isDark.value = savedDarkMode === 'true'
    } else {
      isDark.value = false
    }

    if (savedTheme) {
      currentTheme.value = savedTheme
    }

    applyTheme()
  }

  const applyTheme = () => {
    if (isDark.value) {
      document.documentElement.classList.add('dark')
    } else {
      document.documentElement.classList.remove('dark')
    }

    const theme = themes[currentTheme.value]
    if (theme) {
      document.documentElement.style.setProperty('--color-primary', theme.primary)
      document.documentElement.style.setProperty('--color-secondary', theme.secondary)
      document.documentElement.style.setProperty('--color-accent', theme.accent)
    }
  }

  const toggleDark = () => {
    isDark.value = !isDark.value
  }

  const setTheme = (themeName) => {
    if (themes[themeName]) {
      currentTheme.value = themeName
      applyTheme()
    }
  }

  watch(isDark, (newValue) => {
    localStorage.setItem('darkMode', newValue.toString())
    applyTheme()
  })

  watch(currentTheme, (newValue) => {
    localStorage.setItem('currentTheme', newValue)
    applyTheme()
  })

  return {
    isDark,
    currentTheme,
    themes,
    initTheme,
    toggleDark,
    setTheme,
    applyTheme
  }
})
