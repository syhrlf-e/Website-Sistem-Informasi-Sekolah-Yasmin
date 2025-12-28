<!--
  @component UserAvatar
  @description Avatar circle dengan initial letter dan optional user info (name/role)
  @props {Object} user - User data { name: String, role: 'admin'|'super_admin' }
  @props {Boolean} showInfo - Toggle name/role visibility (hidden di mobile)
-->

<template>
  <div class="flex items-center gap-3">
    <div v-if="showInfo" class="text-right hidden md:block">
      <p class="text-sm font-semibold text-gray-900 dark:text-white font-poppins">
        {{ user?.name || 'Admin' }}
      </p>
      <p class="text-xs text-gray-500 dark:text-gray-400">
        {{ user?.role === 'super_admin' ? 'Super Admin' : 'Admin' }}
      </p>
    </div>
    <div
      class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold"
    >
      {{ user?.name?.[0]?.toUpperCase() || 'A' }}
    </div>
  </div>
</template>

<script setup>
defineProps({
  user: {
    type: Object,
    default: null
  },
  showInfo: {
    type: Boolean,
    default: true
  }
})
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
