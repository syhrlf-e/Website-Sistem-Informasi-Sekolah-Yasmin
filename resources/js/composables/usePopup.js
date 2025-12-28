/**
 * @composable usePopup
 * @description Modern popup composable to replace SweetAlert2
 * @provides showSuccess, showError, showWarning, showInfo, confirmDelete, confirm, showLoading, close
 * 
 * @example
 * const { showSuccess, confirmDelete } = usePopup()
 * await showSuccess('Data berhasil disimpan!')
 * const result = await confirmDelete('Hapus data ini?')
 */

import { createApp, ref, h } from 'vue'
import BasePopup from '@/components/ui/shared/BasePopup.vue'

// Global state for managing popup instances
let currentPopupApp = null
let currentPopupContainer = null

export function usePopup() {

    const createPopup = (options) => {
        return new Promise((resolve) => {
            // Clean up any existing popup
            if (currentPopupApp) {
                currentPopupApp.unmount()
                currentPopupContainer?.remove()
            }

            // Create container
            currentPopupContainer = document.createElement('div')
            currentPopupContainer.id = 'popup-container'
            document.body.appendChild(currentPopupContainer)

            const isOpen = ref(true)
            let result = { isConfirmed: false, isDismissed: false }

            // Create app with popup component
            currentPopupApp = createApp({
                setup() {
                    const handleConfirm = (inputValue) => {
                        result = { isConfirmed: true, isDismissed: false, value: inputValue }
                    }

                    const handleCancel = () => {
                        result = { isConfirmed: false, isDismissed: true }
                    }

                    const handleClose = () => {
                        isOpen.value = false
                        setTimeout(() => {
                            currentPopupApp?.unmount()
                            currentPopupContainer?.remove()
                            currentPopupApp = null
                            currentPopupContainer = null
                            resolve(result)
                        }, 100) // Fast transition for instant popup chaining
                    }

                    return () => h(BasePopup, {
                        modelValue: isOpen.value,
                        'onUpdate:modelValue': (val) => { isOpen.value = val },
                        variant: options.variant || 'info',
                        title: options.title || '',
                        message: options.message || options.text || '',
                        confirmText: options.confirmText || options.confirmButtonText || 'OK',
                        cancelText: options.cancelText || options.cancelButtonText || 'Batal',
                        showCancelButton: options.showCancelButton || false,
                        showCloseButton: options.showCloseButton !== false,
                        closeOnBackdrop: options.closeOnBackdrop !== false,
                        timer: options.timer || 0,
                        // Prompt-specific props
                        inputLabel: options.inputLabel || '',
                        inputPlaceholder: options.inputPlaceholder || '',
                        inputValidator: options.inputValidator || null,
                        onConfirm: handleConfirm,
                        onCancel: handleCancel,
                        onClose: handleClose
                    })
                }
            })

            currentPopupApp.mount(currentPopupContainer)
        })
    }

    const showSuccess = async (title, text = '') => {
        return createPopup({
            variant: 'success',
            title,
            text,
            timer: 3000
        })
    }

    const showError = async (title, text = '') => {
        return createPopup({
            variant: 'error',
            title,
            text
        })
    }

    const showWarning = async (title, text = '') => {
        return createPopup({
            variant: 'warning',
            title,
            text
        })
    }

    const showInfo = async (title, text = '') => {
        return createPopup({
            variant: 'info',
            title,
            text
        })
    }

    const confirmDelete = async (
        title = 'Apakah Anda yakin?',
        text = 'Data yang dihapus tidak dapat dikembalikan!'
    ) => {
        return createPopup({
            variant: 'confirm',
            title,
            text,
            showCancelButton: true,
            confirmText: 'Ya, Hapus!',
            cancelText: 'Batal',
            closeOnBackdrop: false
        })
    }

    const confirm = async (title, text = '', confirmText = 'Ya') => {
        return createPopup({
            variant: 'confirm',
            title,
            text,
            showCancelButton: true,
            confirmText,
            cancelText: 'Batal',
            closeOnBackdrop: false
        })
    }

    const showLoading = async (title = 'Memproses...') => {
        return createPopup({
            variant: 'loading',
            title,
            closeOnBackdrop: false,
            showCloseButton: false
        })
    }

    const showPrompt = async (title, options = {}) => {
        return createPopup({
            variant: 'prompt',
            title,
            text: options.text || '',
            inputLabel: options.inputLabel || '',
            inputPlaceholder: options.inputPlaceholder || '',
            inputValidator: options.inputValidator || null,
            confirmText: options.confirmText || 'Kirim',
            cancelText: options.cancelText || 'Batal',
            showCancelButton: true,
            closeOnBackdrop: false
        })
    }

    const close = () => {
        if (currentPopupApp) {
            currentPopupApp.unmount()
            currentPopupContainer?.remove()
            currentPopupApp = null
            currentPopupContainer = null
        }
    }

    // Fire method for advanced usage (SweetAlert-like API)
    const fire = async (options) => {
        return createPopup({
            variant: options.icon || 'info',
            title: options.title || '',
            text: options.text || options.html || '',
            confirmText: options.confirmButtonText || 'OK',
            cancelText: options.cancelButtonText || 'Batal',
            showCancelButton: options.showCancelButton || false,
            timer: options.timer || 0,
            closeOnBackdrop: options.allowOutsideClick !== false
        })
    }

    return {
        fire,
        close,
        showSuccess,
        showError,
        showWarning,
        showInfo,
        confirmDelete,
        confirm,
        showLoading,
        showPrompt
    }
}
