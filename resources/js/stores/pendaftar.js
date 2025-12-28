/**
 * @store pendaftar
 * @description Pinia store for managing pendaftar ekskul data with caching and realtime polling
 */

import api from '@/services/api'
import { defineStore } from 'pinia'
import { computed, ref } from 'vue'

export const usePendaftarStore = defineStore('pendaftar', () => {
    // State
    const stats = ref({
        berita: 0,
        berita_published: 0,
        berita_draft: 0,
        berita_featured: 0,
        ekskul: 0,
        galeri: 0,
        prestasi: 0,
        pendaftar: 0,
        pendaftar_pending: 0,
        pendaftar_approved: 0,
        pendaftar_rejected: 0
    })
    const recentPendaftar = ref([])
    const isInitialLoading = ref(true) // True on first load only
    const isLoaded = ref(false) // True after first successful fetch
    const error = ref(null)

    // Polling interval reference
    let pollingInterval = null

    // Getters
    const pendingCount = computed(() => stats.value.pendaftar_pending || 0)
    const hasData = computed(() => isLoaded.value)

    // Actions
    const fetchStats = async () => {
        try {
            const response = await api.get('/yasmin-panel/dashboard/stats')
            if (response.data.success) {
                stats.value = response.data.data
                isLoaded.value = true
                error.value = null
            }
        } catch (err) {
            console.error('Error fetching stats:', err)
            error.value = 'Gagal memuat statistik'
        } finally {
            isInitialLoading.value = false
        }
    }

    const fetchRecentPendaftar = async () => {
        try {
            const response = await api.get('/yasmin-panel/dashboard/pendaftar')
            if (response.data.success) {
                recentPendaftar.value = response.data.data
            }
        } catch (err) {
            console.error('Error fetching pendaftar:', err)
        }
    }

    const fetchAll = async () => {
        await Promise.all([fetchStats(), fetchRecentPendaftar()])
    }

    // Start polling for realtime updates
    const startPolling = () => {
        if (pollingInterval) return // Already polling

        // Fetch immediately if not loaded
        if (!isLoaded.value) {
            fetchAll()
        }

        // Poll every 15 seconds for realtime updates (optimized from 3s)
        pollingInterval = setInterval(() => {
            fetchAll()
        }, 15000)

        // Setup visibility API to pause polling when tab is inactive
        setupVisibilityListener()
    }

    // Stop polling
    const stopPolling = () => {
        if (pollingInterval) {
            clearInterval(pollingInterval)
            pollingInterval = null
        }
    }

    // Visibility API: pause/resume polling based on tab visibility
    let visibilityListenerAdded = false
    const handleVisibilityChange = () => {
        if (document.hidden) {
            // Tab is hidden - stop polling to save resources
            stopPolling()
        } else {
            // Tab is visible again - resume polling
            if (!pollingInterval) {
                fetchAll() // Fetch immediately when returning
                pollingInterval = setInterval(() => fetchAll(), 15000)
            }
        }
    }

    const setupVisibilityListener = () => {
        if (!visibilityListenerAdded) {
            document.addEventListener('visibilitychange', handleVisibilityChange)
            visibilityListenerAdded = true
        }
    }

    // Reset store (on logout)
    const reset = () => {
        stopPolling()
        stats.value = {
            berita: 0,
            berita_published: 0,
            berita_draft: 0,
            berita_featured: 0,
            ekskul: 0,
            galeri: 0,
            prestasi: 0,
            pendaftar: 0,
            pendaftar_pending: 0,
            pendaftar_approved: 0,
            pendaftar_rejected: 0
        }
        recentPendaftar.value = []
        isInitialLoading.value = true
        isLoaded.value = false
        error.value = null
    }

    return {
        // State
        stats,
        recentPendaftar,
        isInitialLoading,
        isLoaded,
        error,

        // Getters
        pendingCount,
        hasData,

        // Actions
        fetchStats,
        fetchRecentPendaftar,
        fetchAll,
        startPolling,
        stopPolling,
        reset
    }
})
