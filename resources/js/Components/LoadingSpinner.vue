<template>
  <div class="flex items-center justify-center" :class="containerClass">
    <div class="relative">
      <div class="w-8 h-8 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
      <div v-if="text" class="mt-2 text-sm text-gray-600 text-center">{{ text }}</div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  text: {
    type: String,
    default: ''
  },
  containerClass: {
    type: String,
    default: 'p-4'
  }
})
</script>