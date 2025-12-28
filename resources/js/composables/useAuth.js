/**
 * @composable useAuth
 * @description Authentication composable untuk admin panel
 * @provides login, logout, fetchUser, initAuth
 * @state user, token, isLoading, error, isAuthenticated, isAdmin, isSuperAdmin
 */

import api, { clearAuthToken } from '@/services/api'
import { computed, ref } from 'vue'

const user = ref(null)
const token = ref(sessionStorage.getItem('admin_token'))
const isLoading = ref(false)
const error = ref(null)

export function useAuth() {
  const isAuthenticated = computed(() => !!token.value)

  const isAdmin = computed(() => {
    if (!user.value) return false
    const role = user.value.role?.toLowerCase()
    return ['admin', 'super_admin', 'admin_ppdb'].includes(role)
  })

  const isSuperAdmin = computed(() => {
    if (!user.value) return false
    return user.value.role?.toLowerCase() === 'super_admin'
  })

  /**
   * Check if user is PPDB Admin (only PPDB access)
   */
  const isAdminPpdb = computed(() => {
    if (!user.value) return false
    return user.value.role?.toLowerCase() === 'admin_ppdb'
  })

  const login = async (credentials) => {
    isLoading.value = true
    error.value = null
    try {
      const response = await api.post('/login', credentials)
      token.value = response.data.token
      user.value = response.data.user
      sessionStorage.setItem('admin_token', response.data.token)
      sessionStorage.setItem('admin_user', JSON.stringify(response.data.user))
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Login gagal'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  const logout = async () => {
    isLoading.value = true
    error.value = null
    try {
      await api.post('/logout')
    } catch (err) {
      console.error('Logout API error:', err)
    } finally {
      token.value = null
      user.value = null
      sessionStorage.clear()
      localStorage.clear()
      clearAuthToken()
      document.cookie.split(';').forEach((c) => {
        document.cookie = c
          .replace(/^ +/, '')
          .replace(/=.*/, '=;expires=' + new Date().toUTCString() + ';path=/')
      })
      isLoading.value = false
    }
  }

  const fetchUser = async () => {
    if (!token.value) return
    try {
      const response = await api.get('/me')
      user.value = response.data.user
      sessionStorage.setItem('admin_user', JSON.stringify(response.data.user))
    } catch (err) {
      console.error('Fetch user error:', err)
      token.value = null
      user.value = null
      sessionStorage.removeItem('admin_token')
      sessionStorage.removeItem('admin_user')
    }
  }

  const initAuth = () => {
    const savedToken = sessionStorage.getItem('admin_token')
    const savedUser = sessionStorage.getItem('admin_user')
    if (savedToken && savedUser) {
      token.value = savedToken
      user.value = JSON.parse(savedUser)
    }
  }

  return {
    user,
    token,
    isLoading,
    error,
    isAuthenticated,
    isAdmin,
    isSuperAdmin,
    isAdminPpdb,
    login,
    logout,
    fetchUser,
    initAuth
  }
}
