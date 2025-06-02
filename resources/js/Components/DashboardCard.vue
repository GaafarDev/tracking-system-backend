<template>
  <div class="group relative overflow-hidden rounded-2xl bg-white/70 backdrop-blur-sm border border-gray-200/50 shadow-soft hover:shadow-elegant transition-all duration-300 hover:-translate-y-1"
       v-motion
       :initial="{ opacity: 0, y: 20 }"
       :enter="{ opacity: 1, y: 0, transition: { delay: animationDelay } }">
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
          <span class="text-xs font-medium text-gray-700">{{ progressPercentage }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
          <div class="h-2 rounded-full transition-all duration-1000 ease-out" 
               :class="progressBarClass" 
               :style="{ width: progressPercentage + '%' }"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { ArrowUpIcon, ArrowDownIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  value: {
    type: [String, Number],
    required: true
  },
  subtitle: {
    type: String,
    default: ''
  },
  subtitleType: {
    type: String,
    default: 'neutral',
    validator: (value) => ['success', 'warning', 'danger', 'neutral'].includes(value)
  },
  subtitleIcon: {
    type: Object,
    default: null
  },
  icon: {
    type: Object,
    required: true
  },
  iconColor: {
    type: String,
    default: 'blue'
  },
  animationDelay: {
    type: Number,
    default: 0
  },
  showProgress: {
    type: Boolean,
    default: false
  },
  progressValue: {
    type: Number,
    default: 0
  },
  progressMax: {
    type: Number,
    default: 100
  }
})

const formatValue = (value) => {
  if (typeof value === 'number' && value >= 1000) {
    return (value / 1000).toFixed(1) + 'k'
  }
  return value
}

const progressPercentage = computed(() => {
  if (!props.showProgress) return 0
  return Math.min(Math.round((props.progressValue / props.progressMax) * 100), 100)
})

const subtitleClass = computed(() => {
  const classes = {
    success: 'text-emerald-600',
    warning: 'text-amber-600',
    danger: 'text-red-600',
    neutral: 'text-gray-600'
  }
  return classes[props.subtitleType]
})

const iconBgClass = computed(() => {
  const classes = {
    blue: 'bg-blue-100 group-hover:bg-blue-200',
    green: 'bg-emerald-100 group-hover:bg-emerald-200',
    yellow: 'bg-amber-100 group-hover:bg-amber-200',
    red: 'bg-red-100 group-hover:bg-red-200',
    gray: 'bg-gray-100 group-hover:bg-gray-200',
    purple: 'bg-purple-100 group-hover:bg-purple-200',
    indigo: 'bg-indigo-100 group-hover:bg-indigo-200',
    teal: 'bg-teal-100 group-hover:bg-teal-200'
  }
  return classes[props.iconColor]
})

const iconClass = computed(() => {
  const classes = {
    blue: 'text-blue-600 group-hover:text-blue-700',
    green: 'text-emerald-600 group-hover:text-emerald-700',
    yellow: 'text-amber-600 group-hover:text-amber-700',
    red: 'text-red-600 group-hover:text-red-700',
    gray: 'text-gray-600 group-hover:text-gray-700',
    purple: 'text-purple-600 group-hover:text-purple-700',
    indigo: 'text-indigo-600 group-hover:text-indigo-700',
    teal: 'text-teal-600 group-hover:text-teal-700'
  }
  return classes[props.iconColor]
})

const progressBarClass = computed(() => {
  const classes = {
    blue: 'bg-gradient-to-r from-blue-500 to-blue-600',
    green: 'bg-gradient-to-r from-emerald-500 to-emerald-600',
    yellow: 'bg-gradient-to-r from-amber-500 to-amber-600',
    red: 'bg-gradient-to-r from-red-500 to-red-600',
    gray: 'bg-gradient-to-r from-gray-500 to-gray-600',
    purple: 'bg-gradient-to-r from-purple-500 to-purple-600',
    indigo: 'bg-gradient-to-r from-indigo-500 to-indigo-600',
    teal: 'bg-gradient-to-r from-teal-500 to-teal-600'
  }
  return classes[props.iconColor]
})
</script>