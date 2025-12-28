/**
 * @file stores/news.js
 * @description Pinia store untuk berita di public pages
 * @provides useNewsStore
 */

import api from '@/services/api'
import { defineStore } from 'pinia'

export const useNewsStore = defineStore('news', {
  state: () => ({
    items: [],
    currentNews: null,
    featuredNews: [],
    currentPage: 1,
    perPage: 12,
    total: 0,
    lastPage: 1,
    searchQuery: '',
    filterCategory: 'all',
    filterDate: null,
    loading: false,
    detailLoading: false,
    error: null
  }),

  getters: {
    hasNextPage: (state) => state.currentPage < state.lastPage,
    hasPrevPage: (state) => state.currentPage > 1,
    totalFiltered: (state) => state.items.length,
    totalFeaturedNews: (state) => state.featuredNews.length,
    shouldShowViewMore: (state) => state.total > 8 || state.featuredNews.length >= 8
  },

  actions: {
    async fetchFeaturedNews() {
      this.loading = true
      this.error = null

      try {
        const response = await api.get('/berita/featured', {
          params: { limit: 8 }
        })

        this.featuredNews = response.data.data || []
      } catch (err) {
        this.error = err.response?.data?.message || 'Gagal memuat berita'
      } finally {
        this.loading = false
      }
    },

    async fetchNews(page = 1) {
      this.loading = true
      this.error = null
      this.currentPage = page

      try {
        const response = await api.get('/berita', {
          params: {
            page: page,
            per_page: this.perPage,
            search: this.searchQuery || null,
            category: this.filterCategory !== 'all' ? this.filterCategory : null,
            date: this.filterDate || null
          }
        })

        this.items = response.data.data || []

        if (response.data.meta) {
          this.currentPage = response.data.meta.current_page
          this.lastPage = response.data.meta.last_page
          this.total = response.data.meta.total
          this.perPage = response.data.meta.per_page
        }
      } catch (err) {
        this.error = err.response?.data?.message || 'Gagal memuat berita'
      } finally {
        this.loading = false
      }
    },

    async fetchNewsDetail(slug) {
      this.detailLoading = true
      this.error = null

      try {
        const response = await api.get(`/berita/${slug}`)
        this.currentNews = response.data.data
        return response.data.data
      } catch (err) {
        this.error = err.response?.data?.message || 'Gagal memuat detail berita'
        throw err
      } finally {
        this.detailLoading = false
      }
    },

    setSearchQuery(query) {
      this.searchQuery = query
      this.currentPage = 1
      this.fetchNews(1)
    },

    setFilterCategory(category) {
      this.filterCategory = category
      this.currentPage = 1
      this.fetchNews(1)
    },

    setFilterDate(date) {
      this.filterDate = date
      this.currentPage = 1
      this.fetchNews(1)
    },

    clearFilters() {
      this.searchQuery = ''
      this.filterCategory = 'all'
      this.filterDate = null
      this.currentPage = 1
      this.fetchNews(1)
    },

    nextPage() {
      if (this.hasNextPage) {
        this.fetchNews(this.currentPage + 1)
      }
    },

    prevPage() {
      if (this.hasPrevPage) {
        this.fetchNews(this.currentPage - 1)
      }
    },

    goToPage(page) {
      if (page >= 1 && page <= this.lastPage) {
        this.fetchNews(page)
      }
    },

    clearCurrentNews() {
      this.currentNews = null
    },

    resetState() {
      this.$reset()
    }
  }
})
