<template>
  <div class="flex flex-wrap justify-center gap-6">
    <slot></slot>
  </div>
</template>
<script setup>
// Dynamic grid wrapper with center alignment
// Supports 1-8 cards with automatic centering
</script>
<style scoped>
/* Base card styling */
.flex > * {
  /* Max 4 cards per row on desktop */
  width: 100%;
  animation: fadeInUp 0.6s ease-out backwards;
}
/* Mobile: 1 column */
@media (min-width: 640px) {
  .flex > * {
    width: calc(50% - 12px); /* 2 columns on tablet */
  }
}
/* Desktop: Max 4 columns */
@media (min-width: 1024px) {
  .flex > * {
    width: calc(25% - 18px); /* 4 columns on desktop */
    max-width: 300px; /* Prevent cards from being too wide */
  }
}
/* Stagger animation for all 8 cards */
.flex > *:nth-child(1) {
  animation-delay: 0.1s;
}
.flex > *:nth-child(2) {
  animation-delay: 0.2s;
}
.flex > *:nth-child(3) {
  animation-delay: 0.3s;
}
.flex > *:nth-child(4) {
  animation-delay: 0.4s;
}
.flex > *:nth-child(5) {
  animation-delay: 0.5s;
}
.flex > *:nth-child(6) {
  animation-delay: 0.6s;
}
.flex > *:nth-child(7) {
  animation-delay: 0.7s;
}
.flex > *:nth-child(8) {
  animation-delay: 0.8s;
}
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
