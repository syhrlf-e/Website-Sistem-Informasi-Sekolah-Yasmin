/**
 * @file stores/adminGuru.js
 * @description Pinia store untuk manajemen data guru di admin panel
 * @provides useAdminGuruStore
 */

import api from '@/services/api'
import { defineStore } from 'pinia'

export const useAdminGuruStore = defineStore('adminGuru', {
  state: () => ({
    items: [],
    currentGuru: null,
    currentPage: 1,
    perPage: 10,
    total: 0,
    lastPage: 1,
    from: 0,
    to: 0,
    searchQuery: '',
    loading: false,
    error: null
  }),

  getters: {
    hasNextPage: (state) => state.currentPage < state.lastPage,
    hasPrevPage: (state) => state.currentPage > 1
  },

  actions: {
    async fetchGuru(page = 1) {
      this.loading = true
      this.error = null
      this.currentPage = page

      try {
        const response = await api.get('/yasmin-panel/guru', {
          params: {
            page: page,
            per_page: this.perPage,
            search: this.searchQuery || undefined
          }
        })

        if (response.data.success) {
          this.items = response.data.data || []

          if (response.data.meta) {
            this.currentPage = response.data.meta.current_page
            this.lastPage = response.data.meta.last_page
            this.total = response.data.meta.total
            this.perPage = response.data.meta.per_page
            this.from = response.data.meta.from
            this.to = response.data.meta.to
          }
        }
      } catch (err) {
        this.error = err.response?.data?.message || 'Gagal memuat data guru'
      } finally {
        this.loading = false
      }
    },

    async fetchGuruById(id) {
      this.loading = true
      this.error = null

      try {
        const response = await api.get(`/yasmin-panel/guru/${id}`)

        if (response.data.success) {
          this.currentGuru = response.data.data
          return response.data.data
        }
      } catch (err) {
        this.error = err.response?.data?.message || 'Gagal memuat detail guru'
        throw err
      } finally {
        this.loading = false
      }
    },

    async createGuru(formData) {
      this.loading = true
      this.error = null

      try {
        const response = await api.post('/yasmin-panel/guru', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })

        if (response.data.success) {
          return response.data.data
        }
      } catch (err) {
        this.error = err.response?.data?.message || 'Gagal menambahkan guru'
        throw err
      } finally {
        this.loading = false
      }
    },

    async updateGuru(id, formData) {
      this.loading = true
      this.error = null

      try {
        const response = await api.post(`/yasmin-panel/guru/${id}`, formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })

        if (response.data.success) {
          return response.data.data
        }
      } catch (err) {
        this.error = err.response?.data?.message || 'Gagal mengupdate guru'
        throw err
      } finally {
        this.loading = false
      }
    },

    async deleteGuru(id) {
      this.loading = true
      this.error = null

      try {
        const response = await api.delete(`/yasmin-panel/guru/${id}`)

        if (response.data.success) {
          await this.fetchGuru()

          if (this.items.length === 0 && this.currentPage > 1) {
            await this.goToPage(this.currentPage - 1)
          }

          return true
        }
      } catch (err) {
        this.error = err.response?.data?.message || 'Gagal menghapus guru'
        throw err
      } finally {
        this.loading = false
      }
    },

    setSearch(query) {
      this.searchQuery = query
      this.fetchGuru(1)
    },

    goToPage(page) {
      if (page >= 1 && page <= this.lastPage) {
        this.fetchGuru(page)
      }
    },

    clearCurrentGuru() {
      this.currentGuru = null
    },

    resetFilters() {
      this.searchQuery = ''
      this.fetchGuru(1)
    }
  }
})
