/**
 * @file services/ekskulRegistrationService.js
 * @description Service untuk API calls pendaftaran ekstrakurikuler
 * @provides registerUser, getRegistrations, approveRegistration, rejectRegistration, deleteRegistration, getDashboardStats, getRecentPendaftar, formatStatus, validateTokenFormat
 */

import api from './api'

export const registerUser = async (data) => {
  try {
    const response = await api.post('/ekstrakurikuler/register', data)
    return response.data
  } catch (error) {
    throw error
  }
}

export const getRegistrations = async (filters = {}) => {
  try {
    const response = await api.get('/admin/ekstrakurikuler/registrations', {
      params: filters
    })
    return response.data
  } catch (error) {
    throw error
  }
}

export const getRegistrationDetail = async (id) => {
  try {
    const response = await api.get(`/admin/ekstrakurikuler/registrations/${id}`)
    return response.data
  } catch (error) {
    throw error
  }
}

export const approveRegistration = async (id) => {
  try {
    const response = await api.post(`/admin/ekstrakurikuler/registrations/${id}/approve`)
    return response.data
  } catch (error) {
    throw error
  }
}

export const rejectRegistration = async (id, reason = null) => {
  try {
    const response = await api.post(`/admin/ekstrakurikuler/registrations/${id}/reject`, {
      rejection_reason: reason
    })
    return response.data
  } catch (error) {
    throw error
  }
}

export const deleteRegistration = async (id) => {
  try {
    const response = await api.delete(`/admin/ekstrakurikuler/registrations/${id}`)
    return response.data
  } catch (error) {
    throw error
  }
}

export const getDashboardStats = async () => {
  try {
    const response = await api.get('/admin/dashboard/registrations/stats')
    return response.data
  } catch (error) {
    throw error
  }
}

export const getRecentPendaftar = async (limit = 5) => {
  try {
    const response = await api.get('/admin/dashboard/registrations/pendaftar', {
      params: { limit }
    })
    return response.data
  } catch (error) {
    throw error
  }
}

export const formatStatus = (status) => {
  const statusMap = {
    pending: {
      label: 'Menunggu',
      color: 'yellow',
      bgClass: 'bg-yellow-100 dark:bg-yellow-900/30',
      textClass: 'text-yellow-800 dark:text-yellow-300'
    },
    approved: {
      label: 'Diterima',
      color: 'green',
      bgClass: 'bg-green-100 dark:bg-green-900/30',
      textClass: 'text-green-800 dark:text-green-300'
    },
    rejected: {
      label: 'Ditolak',
      color: 'red',
      bgClass: 'bg-red-100 dark:bg-red-900/30',
      textClass: 'text-red-800 dark:text-red-300'
    }
  }
  return statusMap[status] || statusMap.pending
}

export const validateTokenFormat = (token) => {
  const tokenRegex = /^[A-Za-z0-9]{8,32}$/
  return tokenRegex.test(token)
}

export default {
  registerUser,
  getRegistrations,
  getRegistrationDetail,
  approveRegistration,
  rejectRegistration,
  deleteRegistration,
  getDashboardStats,
  getRecentPendaftar,
  formatStatus,
  validateTokenFormat
}
