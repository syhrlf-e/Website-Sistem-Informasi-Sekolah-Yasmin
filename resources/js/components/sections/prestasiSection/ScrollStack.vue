<script setup>
import Lenis from 'lenis'
import { defineComponent, h, nextTick, onBeforeUnmount, onMounted, ref, useTemplateRef } from 'vue'

const props = defineProps({
  className: {
    type: String,
    default: ''
  },
  itemDistance: {
    type: Number,
    default: 200
  },
  itemScale: {
    type: Number,
    default: 0.04
  },
  itemStackDistance: {
    type: Number,
    default: 15
  },
  stackPosition: {
    type: String,
    default: '250px'
  },
  scaleEndPosition: {
    type: String,
    default: '35vh'
  },
  baseScale: {
    type: Number,
    default: 0.88
  }
})

const emit = defineEmits(['stackComplete'])

const scrollerRef = useTemplateRef('scrollerRef')
const stackCompletedRef = ref(false)
const animationFrameRef = ref(null)
const lenisRef = ref(null)
const cardsRef = ref([])
const lastTransformsRef = ref(new Map())
const isUpdatingRef = ref(false)

const calculateProgress = (scrollTop, start, end) => {
  if (scrollTop < start) return 0
  if (scrollTop > end) return 1
  return (scrollTop - start) / (end - start)
}

const parsePercentage = (value, containerHeight) => {
  if (typeof value === 'string' && value.includes('%')) {
    return (parseFloat(value) / 100) * containerHeight
  }
  if (typeof value === 'string' && value.includes('px')) {
    return parseFloat(value)
  }
  return parseFloat(value)
}

const updateCardTransforms = () => {
  const scroller = scrollerRef.value
  if (!scroller || !cardsRef.value.length || isUpdatingRef.value) return

  isUpdatingRef.value = true

  const scrollTop = scroller.scrollTop
  const containerHeight = scroller.clientHeight
  const stackPositionPx = parsePercentage(props.stackPosition, containerHeight)
  const scaleEndPositionPx = parsePercentage(props.scaleEndPosition, containerHeight)
  const endElement = scroller.querySelector('.scroll-stack-end')
  const endElementTop = endElement ? endElement.offsetTop : 0

  cardsRef.value.forEach((card, i) => {
    if (!card) return

    const cardTop = card.offsetTop
    const triggerStart = cardTop - stackPositionPx - props.itemStackDistance * i
    const triggerEnd = cardTop - scaleEndPositionPx
    const pinStart = cardTop - stackPositionPx - props.itemStackDistance * i
    const pinEnd = endElementTop - containerHeight / 2

    const scaleProgress = calculateProgress(scrollTop, triggerStart, triggerEnd)
    const targetScale = props.baseScale + i * props.itemScale
    const scale = 1 - scaleProgress * (1 - targetScale)

    let translateY = 0
    const isPinned = scrollTop >= pinStart && scrollTop <= pinEnd

    if (isPinned) {
      translateY = scrollTop - cardTop + stackPositionPx + props.itemStackDistance * i
    } else if (scrollTop > pinEnd) {
      translateY = pinEnd - cardTop + stackPositionPx + props.itemStackDistance * i
    }

    const newTransform = {
      translateY: Math.round(translateY * 100) / 100,
      scale: Math.round(scale * 1000) / 1000
    }

    const lastTransform = lastTransformsRef.value.get(i)
    const hasChanged =
      !lastTransform ||
      Math.abs(lastTransform.translateY - newTransform.translateY) > 0.1 ||
      Math.abs(lastTransform.scale - newTransform.scale) > 0.001

    if (hasChanged) {
      const transform = `translate3d(0, ${newTransform.translateY}px, 0) scale(${newTransform.scale})`
      card.style.transform = transform
      lastTransformsRef.value.set(i, newTransform)
    }

    // Detect when last card is stacked
    if (i === cardsRef.value.length - 1) {
      const isInView = scrollTop >= pinStart && scrollTop <= pinEnd
      if (isInView && !stackCompletedRef.value) {
        stackCompletedRef.value = true
        emit('stackComplete')
      } else if (!isInView && stackCompletedRef.value) {
        stackCompletedRef.value = false
      }
    }
  })

  isUpdatingRef.value = false
}

const handleScroll = () => {
  updateCardTransforms()
}

const setupLenis = () => {
  const scroller = scrollerRef.value
  if (!scroller) return

  const lenis = new Lenis({
    wrapper: scroller,
    content: scroller.querySelector('.scroll-stack-inner'),
    duration: 1.2,
    easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
    smoothWheel: true,
    touchMultiplier: 2,
    infinite: false,
    gestureOrientation: 'vertical',
    wheelMultiplier: 1,
    lerp: 0.1,
    syncTouch: true,
    syncTouchLerp: 0.075
  })

  lenis.on('scroll', handleScroll)

  const raf = (time) => {
    lenis.raf(time)
    animationFrameRef.value = requestAnimationFrame(raf)
  }
  animationFrameRef.value = requestAnimationFrame(raf)

  lenisRef.value = lenis
  return lenis
}

let cleanup = null
const setup = () => {
  const scroller = scrollerRef.value
  if (!scroller) return

  const cards = Array.from(scroller.querySelectorAll('.scroll-stack-card'))
  cardsRef.value = cards
  const transformsCache = lastTransformsRef.value

  cards.forEach((card, i) => {
    if (i < cards.length - 1) {
      card.style.marginBottom = `${props.itemDistance}px`
    }
    card.style.willChange = 'transform'
    card.style.transformOrigin = 'top center'
    card.style.backfaceVisibility = 'hidden'
    card.style.transform = 'translateZ(0)'
    card.style.webkitTransform = 'translateZ(0)'
    card.style.perspective = '1000px'
    card.style.webkitPerspective = '1000px'
  })

  setupLenis()
  updateCardTransforms()

  cleanup = () => {
    if (animationFrameRef.value) {
      cancelAnimationFrame(animationFrameRef.value)
    }
    if (lenisRef.value) {
      lenisRef.value.destroy()
    }
    stackCompletedRef.value = false
    cardsRef.value = []
    transformsCache.clear()
    isUpdatingRef.value = false
  }
}

onMounted(async () => {
  await nextTick()
  setup()
})

onBeforeUnmount(() => {
  cleanup?.()
})
</script>

<script>
import { defineComponent, h } from 'vue'

export const ScrollStackItem = defineComponent({
  name: 'ScrollStackItem',
  props: {
    itemClassName: {
      type: String,
      default: ''
    }
  },
  setup(props, { slots }) {
    return () =>
      h(
        'div',
        {
          class: `scroll-stack-card ${props.itemClassName}`.trim(),
          style: {
            backfaceVisibility: 'hidden',
            transformStyle: 'preserve-3d'
          }
        },
        slots.default?.()
      )
  }
})
</script>

<template>
  <div
    ref="scrollerRef"
    :class="['relative w-full overflow-y-auto overflow-x-visible', className]"
    :style="{
      height: '100%',
      minHeight: '100vh',
      overscrollBehavior: 'contain',
      WebkitOverflowScrolling: 'touch',
      scrollBehavior: 'smooth',
      WebkitTransform: 'translateZ(0)',
      transform: 'translateZ(0)',
      willChange: 'scroll-position'
    }"
  >
    <div class="scroll-stack-inner pb-[100vh]">
      <slot />
      <!-- Spacer for smooth release -->
      <div class="w-full h-[50vh] scroll-stack-end" />
    </div>
  </div>
</template>
