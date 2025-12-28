/**
 * @composable useIntersectionObserver
 * @description Lazy load content when element enters viewport using IntersectionObserver
 * @param {Object} options - Configuration options
 * @param {Number} options.threshold - Trigger when % of element is visible (default: 0.1)
 * @param {String} options.rootMargin - Margin around root (default: '0px')
 * @param {Boolean} options.once - Only trigger once (default: true)
 * @param {Function} options.onIntersect - Callback when element intersects
 * @returns {Object} { elementRef, isVisible }
 * 
 * @example
 * const { elementRef, isVisible } = useIntersectionObserver({
 *   threshold: 0.1,
 *   rootMargin: '100px',
 *   once: true,
 *   onIntersect: () => fetchData()
 * })
 */

import { onBeforeUnmount, onMounted, ref } from 'vue'

export function useIntersectionObserver(options = {}) {
  const {
    threshold = 0.1,
    rootMargin = '0px',
    once = true,
    onIntersect = null
  } = options

  const elementRef = ref(null)
  const isVisible = ref(false)
  let observer = null
  let hasIntersected = false

  const handleIntersection = (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        isVisible.value = true

        if (onIntersect && typeof onIntersect === 'function') {
          onIntersect()
        }

        if (once && !hasIntersected) {
          hasIntersected = true
          if (observer) {
            observer.disconnect()
          }
        }
      } else {
        isVisible.value = false
      }
    })
  }

  const initObserver = () => {
    if (!elementRef.value) {
      console.warn('useIntersectionObserver: elementRef is not set')
      return
    }

    if (!('IntersectionObserver' in window)) {
      console.warn('IntersectionObserver is not supported in this browser')
      isVisible.value = true
      if (onIntersect && typeof onIntersect === 'function') {
        onIntersect()
      }
      return
    }

    observer = new IntersectionObserver(handleIntersection, {
      threshold,
      rootMargin
    })

    observer.observe(elementRef.value)
  }

  const cleanup = () => {
    if (observer) {
      observer.disconnect()
      observer = null
    }
  }

  onMounted(() => {
    initObserver()
  })

  onBeforeUnmount(() => {
    cleanup()
  })

  return {
    elementRef,
    isVisible
  }
}
