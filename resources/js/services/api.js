/**
 * @file services/api.js
 * @description Axios instance dengan interceptors untuk auth dan error handling
 * @provides api (default), clearAuthToken
 */

import axios from 'axios'

const api = axios.create({
  baseURL: '/api',
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
  }
})

export const clearAuthToken = () => {
  delete api.defaults.headers.common['Authorization']
}

api.interceptors.request.use(
  (config) => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')
    if (csrfToken) {
      config.headers['X-CSRF-TOKEN'] = csrfToken.content
    }

    const token = sessionStorage.getItem('admin_token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

api.interceptors.response.use(
  (response) => {
    return response
  },
  (error) => {
    if (error.response) {
      if (error.response.status === 401) {
        sessionStorage.removeItem('admin_token')
        sessionStorage.removeItem('admin_user')
        clearAuthToken()
      }
    }
    return Promise.reject(error)
  }
)

export default api
