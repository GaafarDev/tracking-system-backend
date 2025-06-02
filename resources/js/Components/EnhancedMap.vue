<template>
  <div class="enhanced-map-container">
    <div class="map-header bg-white rounded-t-2xl p-4 border-b border-gray-200">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <div class="p-2 bg-blue-100 rounded-lg">
            <MapPinIcon class="w-5 h-5 text-blue-600" />
          </div>
          <div>
            <h3 class="text-lg font-semibold text-gray-900">{{ title }}</h3>
            <p class="text-sm text-gray-600">{{ subtitle }}</p>
          </div>
        </div>
        <div class="flex items-center space-x-2">
          <button @click="refreshMap" class="p-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
            <ArrowPathIcon class="w-4 h-4 text-gray-600" />
          </button>
          <button @click="toggleFullscreen" class="p-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
            <ArrowsPointingOutIcon class="w-4 h-4 text-gray-600" />
          </button>
        </div>
      </div>
    </div>
    <div :id="mapId" :class="mapClasses" class="rounded-b-2xl border border-gray-200"></div>
  </div>
</template>

<script setup>
import { onMounted, computed } from 'vue'
import { MapPinIcon, ArrowPathIcon, ArrowsPointingOutIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  mapId: {
    type: String,
    default: 'enhanced-map'
  },
  title: {
    type: String,
    default: 'Live Tracking'
  },
  subtitle: {
    type: String,
    default: 'Real-time vehicle locations'
  },
  height: {
    type: String,
    default: 'h-96'
  }
})

const emit = defineEmits(['map-initialized', 'refresh-requested'])

const mapClasses = computed(() => `w-full ${props.height}`)

function refreshMap() {
  emit('refresh-requested')
}

function toggleFullscreen() {
  // Implement fullscreen toggle logic
}

onMounted(() => {
  emit('map-initialized', props.mapId)
})
</script>

<style scoped>
.enhanced-map-container {
  @apply bg-white rounded-2xl shadow-xl overflow-hidden;
}
</style>