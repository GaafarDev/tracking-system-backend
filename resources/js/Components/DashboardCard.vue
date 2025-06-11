<template>
  <div class="group relative overflow-hidden rounded-2xl bg-white/70 backdrop-blur-sm border border-gray-200/50 shadow-soft hover:shadow-elegant transition-all duration-300 hover:-translate-y-1"
       :style="{ animationDelay: `${animationDelay}ms` }"
       :class="{ 'animate-fade-in-up': shouldAnimate }">
    <!-- Gradient overlay -->
    <div class="absolute inset-0 bg-gradient-to-br from-transparent via-transparent to-gray-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
    
    <div class="relative p-6">
      <div class="flex items-center justify-between">
        <div class="flex-1">
          <p class="text-sm font-medium text-gray-600 mb-1">{{ title }}</p>
          <p class="text-3xl font-bold text-gray-900 mb-2 tracking-tight">{{ formatValue(value) }}</p>
          <p v-if="subtitle" class="text-sm flex items-center" :class="subtitleClass">
            <component :is="subtitleIcon" v-if="subtitleIcon" class="w-4 h-4 mr-1.5" />
            {{ subtitle }}
          </p>
        </div>
        <div class="relative">
          <div class="h-14 w-14 rounded-2xl flex items-center justify-center transition-all duration-300 group-hover:scale-110" :class="iconBgClass">
            <component :is="icon" class="h-7 w-7 transition-colors duration-300" :class="iconClass" />
          </div>
          <!-- Floating effect -->
          <div class="absolute inset-0 rounded-2xl bg-gradient-to-br opacity-0 group-hover:opacity-20 transition-opacity duration-300" :class="iconBgClass"></div>
        </div>
      </div>
      
      <!-- Progress bar for certain metrics -->
      <div v-if="showProgress" class="mt-4">
        <div class="flex justify-between items-center mb-1">
          <span class="text-xs text-gray-500">Progress</span>
          <span class="text-xs text-gray-500">{{ progress }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
          <div 
            class="h-2 rounded-full transition-all duration-1000 ease-out" 
            :class="progressBarClass"
            :style="{ width: `${progress}%` }"
          ></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'

const props = defineProps({
  title: String,
  value: [Number, String],
  icon: Object,
  iconColor: {
    type: String,
    default: 'blue'
  },
  subtitle: String,
  subtitleIcon: Object,
  subtitleType: {
    type: String,
    default: 'neutral'
  },
  animationDelay: {
    type: Number,
    default: 0
  },
  showProgress: {
    type: Boolean,
    default: false
  },
  progress: {
    type: Number,
    default: 0
  }
})

const shouldAnimate = ref(false)

onMounted(() => {
  // Trigger animation after component mounts
  setTimeout(() => {
    shouldAnimate.value = true
  }, 50)
})

const formatValue = (value) => {
  if (typeof value === 'number') {
    return value.toLocaleString()
  }
  return value
}

const iconBgClass = computed(() => {
  const colors = {
    blue: 'bg-blue-100 group-hover:bg-blue-200',
    green: 'bg-green-100 group-hover:bg-green-200',
    red: 'bg-red-100 group-hover:bg-red-200',
    yellow: 'bg-yellow-100 group-hover:bg-yellow-200',
    purple: 'bg-purple-100 group-hover:bg-purple-200',
    indigo: 'bg-indigo-100 group-hover:bg-indigo-200',
    pink: 'bg-pink-100 group-hover:bg-pink-200',
    gray: 'bg-gray-100 group-hover:bg-gray-200',
    orange: 'bg-orange-100 group-hover:bg-orange-200',
    teal: 'bg-teal-100 group-hover:bg-teal-200'
  }
  return colors[props.iconColor] || colors.blue
})

const iconClass = computed(() => {
  const colors = {
    blue: 'text-blue-600',
    green: 'text-green-600',
    red: 'text-red-600',
    yellow: 'text-yellow-600',
    purple: 'text-purple-600',
    indigo: 'text-indigo-600',
    pink: 'text-pink-600',
    gray: 'text-gray-600',
    orange: 'text-orange-600',
    teal: 'text-teal-600'
  }
  return colors[props.iconColor] || colors.blue
})

const subtitleClass = computed(() => {
  const types = {
    success: 'text-green-600',
    danger: 'text-red-600',
    warning: 'text-yellow-600',
    neutral: 'text-gray-500'
  }
  return types[props.subtitleType] || types.neutral
})

const progressBarClass = computed(() => {
  const colors = {
    blue: 'bg-blue-500',
    green: 'bg-green-500',
    red: 'bg-red-500',
    yellow: 'bg-yellow-500',
    purple: 'bg-purple-500',
    indigo: 'bg-indigo-500',
    pink: 'bg-pink-500',
    gray: 'bg-gray-500',
    orange: 'bg-orange-500',
    teal: 'bg-teal-500'
  }
  return colors[props.iconColor] || colors.blue
})
</script>