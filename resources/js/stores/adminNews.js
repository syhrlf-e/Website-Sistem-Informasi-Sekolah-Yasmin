/**
 * @file stores/adminNews.js
 * @description Pinia store untuk manajemen berita di admin panel (CRUD + Trash)
 * @provides useAdminNewsStore
 */

import api from '@/services/api'
import { defineStore } from 'pinia'

export const useAdminNewsStore = defineStore('adminNews', {
  state: () => ({
    items: [],
    currentNews: null,
    currentPage: 1,
    perPage: 10,
    total: 0,
    lastPage: 1,
    from: 0,
    to: 0,
    searchQuery: '',
    filterCategory: '',
    filterStatus: '',
    loading: false,
    error: null
  }),

  getters: {
    hasNextPage: (state) => state.currentPage < state.lastPage,
    hasPrevPage: (state) => state.currentPage > 1
  },

  actions: {
    async fetchNews(page = 1) {
      this.loading = true
      this.error = null
      this.currentPage = page

      try {
        const response = await api.get('/yasmin-panel/news', {
          params: {
            page: page,
            per_page: this.perPage,
            search: this.searchQuery || undefined,
            category: this.filterCategory || undefined,
            status: this.filterStatus || undefined
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
        this.error = err.response?.data?.message || 'Gagal memuat data berita'
      } finally {
        this.loading = false
      }
    },

    async fetchNewsById(id) {
      this.loading = true
      this.error = null

      try {
        const response = await api.get(`/yasmin-panel/news/${id}`)

        if (response.data.success) {
          this.currentNews = response.data.data
          return response.data.data
        }
      } catch (err) {
        this.error = err.response?.data?.message || 'Gagal memuat detail berita'
        throw err
      } finally {
        this.loading = false
      }
    },

    async createNews(formData) {
      this.loading = true
      this.error = null

      try {
        const response = await api.post('/yasmin-panel/news', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })

        if (response.data.success) {
          return response.data.data
        }
      } catch (err) {
        this.error = err.response?.data?.message || 'Gagal membuat berita'
        throw err
      } finally {
        this.loading = false
      }
    },

    async updateNews(id, formData) {
      this.loading = true
      this.error = null

      try {
        const response = await api.post(`/yasmin-panel/news/${id}`, formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })

        if (response.data.success) {
          return response.data.data
        }
      } catch (err) {
        this.error = err.response?.data?.message || 'Gagal mengupdate berita'
        throw err
      } finally {
        this.loading = false
      }
    },

    async deleteNews(id) {
      this.loading = true
      this.error = null

      try {
        const response = await api.delete(`/yasmin-panel/news/${id}`)

        if (response.data.success) {
          await this.fetchNews()

          if (this.items.length === 0 && this.currentPage > 1) {
            await this.goToPage(this.currentPage - 1)
          }

          return true
        }
      } catch (err) {
        this.error = err.response?.data?.message || 'Gagal menghapus berita'
        throw err
      } finally {
        this.loading = false
      }
    },

    async fetchTrashedNews() {
      this.loading = true
      this.error = null

      try {
        const params = {
          page: this.currentPage,
          per_page: this.perPage
        }

        const response = await api.get('/yasmin-panel/news/trash', { params })

        if (response.data.success) {
          this.items = response.data.data
          this.currentPage = response.data.meta.current_page
          this.lastPage = response.data.meta.last_page
          this.total = response.data.meta.total
          this.from = response.data.meta.from
          this.to = response.data.meta.to
        }
      } catch (err) {
        this.error = err.response?.data?.message || 'Gagal memuat data trash'
      } finally {
        this.loading = false
      }
    },

    async restoreNews(id) {
      this.loading = true
      this.error = null

      try {
        const response = await api.post(`/yasmin-panel/news/${id}/restore`)

        if (response.data.success) {
          await this.fetchTrashedNews()

          if (this.items.length === 0 && this.currentPage > 1) {
            await this.goToPage(this.currentPage - 1)
          }

          return true
        }
      } catch (err) {
        this.error = err.response?.data?.message || 'Gagal memulihkan berita'
        throw err
      } finally {
        this.loading = false
      }
    },

    async forceDeleteNews(id) {
      this.loading = true
      this.error = null

      try {
        const response = await api.delete(`/yasmin-panel/news/${id}/force`)

        if (response.data.success) {
          await this.fetchTrashedNews()

          if (this.items.length === 0 && this.currentPage > 1) {
            await this.goToPage(this.currentPage - 1)
          }

          return true
        }
      } catch (err) {
        this.error = err.response?.data?.message || 'Gagal menghapus berita permanen'
        throw err
      } finally {
        this.loading = false
      }
    },

    setSearch(query) {
      this.searchQuery = query
      this.fetchNews(1)
    },

    setCategory(category) {
      this.filterCategory = category
      this.fetchNews(1)
    },

    setStatus(status) {
      this.filterStatus = status
      this.fetchNews(1)
    },

    goToPage(page) {
      if (page >= 1 && page <= this.lastPage) {
        this.fetchNews(page)
      }
    },

    clearCurrentNews() {
      this.currentNews = null
    },

    resetFilters() {
      this.searchQuery = ''
      this.filterCategory = ''
      this.filterStatus = ''
      this.fetchNews(1)
    }
  }
})
