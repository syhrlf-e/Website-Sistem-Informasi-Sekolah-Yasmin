/**
 * @composable useSwal
 * @description Lazy-loaded SweetAlert2 wrapper dengan helper methods
 * @provides fire, getSwal, close, showSuccess, showError, showWarning, showInfo, confirmDelete, confirm, showLoading
 * 
 * @example
 * const { fire, showSuccess, confirmDelete } = useSwal()
 * await showSuccess('Data berhasil disimpan!')
 * const result = await confirmDelete('Hapus data ini?')
 */

let SwalInstance = null
let loadingPromise = null

export function useSwal() {
  const getSwal = async () => {
    if (SwalInstance) {
      return SwalInstance
    }

    if (loadingPromise) {
      return loadingPromise
    }

    loadingPromise = import('sweetalert2')
      .then((module) => {
        SwalInstance = module.default
        loadingPromise = null
        return SwalInstance
      })
      .catch((error) => {
        console.error('Failed to load SweetAlert2:', error)
        loadingPromise = null
        throw error
      })

    return loadingPromise
  }

  const fire = async (options) => {
    const Swal = await getSwal()
    return Swal.fire(options)
  }

  const showSuccess = async (title, text = '') => {
    return fire({
      icon: 'success',
      title,
      text,
      confirmButtonColor: '#0d9488',
      timer: 3000,
      timerProgressBar: true
    })
  }

  const showError = async (title, text = '') => {
    return fire({
      icon: 'error',
      title,
      text,
      confirmButtonColor: '#ef4444'
    })
  }

  const showWarning = async (title, text = '') => {
    return fire({
      icon: 'warning',
      title,
      text,
      confirmButtonColor: '#f59e0b'
    })
  }

  const showInfo = async (title, text = '') => {
    return fire({
      icon: 'info',
      title,
      text,
      confirmButtonColor: '#3b82f6'
    })
  }

  const confirmDelete = async (
    title = 'Apakah Anda yakin?',
    text = 'Data yang dihapus tidak dapat dikembalikan!'
  ) => {
    return fire({
      icon: 'warning',
      title,
      text,
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Ya, Hapus!',
      cancelButtonText: 'Batal',
      reverseButtons: true
    })
  }

  const confirm = async (title, text = '', confirmText = 'Ya') => {
    return fire({
      icon: 'question',
      title,
      text,
      showCancelButton: true,
      confirmButtonColor: '#0d9488',
      cancelButtonColor: '#6b7280',
      confirmButtonText: confirmText,
      cancelButtonText: 'Batal',
      reverseButtons: true
    })
  }

  const showLoading = async (title = 'Memproses...') => {
    const Swal = await getSwal()
    return Swal.fire({
      title,
      allowOutsideClick: false,
      allowEscapeKey: false,
      didOpen: () => {
        Swal.showLoading()
      }
    })
  }

  const close = async () => {
    if (SwalInstance) {
      SwalInstance.close()
    }
  }

  return {
    fire,
    getSwal,
    close,
    showSuccess,
    showError,
    showWarning,
    showInfo,
    confirmDelete,
    confirm,
    showLoading
  }
}
