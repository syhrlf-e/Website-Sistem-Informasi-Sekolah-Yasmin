<!--
  @component CroppedImage
  @description Renders an image with crop data applied via CSS
  @props {String} src - Image source URL
  @props {Object} crop - Crop data {x, y, width, height, imageWidth, imageHeight}
  @props {String} alt - Alt text
  @props {String} class - Additional CSS classes
  
  The crop data defines which area of the original image should be visible.
  Uses CSS object-fit and object-position to display only the cropped area.
-->

<template>
  <div 
    class="cropped-image-container overflow-hidden"
    :style="containerStyle"
  >
    <img 
      :src="src" 
      :alt="alt"
      class="cropped-image"
      :style="imageStyle"
      loading="lazy"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  src: {
    type: String,
    required: true
  },
  crop: {
    type: Object,
    default: null
    // Expected format: { x, y, width, height, imageWidth, imageHeight }
  },
  alt: {
    type: String,
    default: ''
  }
})

// Calculate the container and image styles based on crop data
const containerStyle = computed(() => {
  return {
    position: 'relative',
    width: '100%',
    height: '100%'
  }
})

const imageStyle = computed(() => {
  if (!props.crop || !props.crop.width || !props.crop.height) {
    // No crop data - show full image with cover
    return {
      width: '100%',
      height: '100%',
      objectFit: 'cover',
      objectPosition: 'center'
    }
  }

  const { x, y, width, height, imageWidth, imageHeight } = props.crop

  // Calculate the scale needed to fit the crop area to the container
  // We need to zoom in so only the crop area is visible
  const scaleX = imageWidth / width
  const scaleY = imageHeight / height
  const scale = Math.max(scaleX, scaleY)

  // Calculate object position to center the crop area
  // Position is based on the center of the crop area relative to the image
  const cropCenterX = x + width / 2
  const cropCenterY = y + height / 2
  
  // Convert to percentage
  const posX = (cropCenterX / imageWidth) * 100
  const posY = (cropCenterY / imageHeight) * 100

  return {
    width: '100%',
    height: '100%',
    objectFit: 'cover',
    objectPosition: `${posX}% ${posY}%`
  }
})
</script>

<style scoped>
.cropped-image-container {
  display: flex;
  align-items: center;
  justify-content: center;
}

.cropped-image {
  display: block;
}
</style>
