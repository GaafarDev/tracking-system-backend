<template>
  <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all duration-200 card-hover"
       v-motion
       :initial="{ opacity: 0, y: 20 }"
       :enter="{ opacity: 1, y: 0, transition: { delay: animationDelay } }">
    <div class="flex items-center justify-between">
      <div class="flex-1">
        <p class="text-sm font-medium text-gray-600">{{ title }}</p>
        <p class="text-3xl font-bold text-gray-900 mt-2">{{ value }}</p>
        <p v-if="subtitle" class="text-sm mt-1" :class="subtitleClass">
          <component :is="subtitleIcon" v-if="subtitleIcon" class="w-4 h-4 inline mr-1" />
          {{ subtitle }}
        </p>
      </div>
      <div class="h-12 w-12 rounded-xl flex items-center justify-center" :class="iconBgClass">
        <component :is="icon" class="h-6 w-6" :class="iconClass" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

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
    default: 'neutral', // 'success', 'warning', 'danger', 'neutral'
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
  }
})

const subtitleClass = computed(() => {
  const classes = {
    success: 'text-green-600',
    warning: 'text-yellow-600',
    danger: 'text-red-600',
    neutral: 'text-gray-600'
  }
  return classes[props.subtitleType]
})

const iconBgClass = computed(() => {
  const classes = {
    blue: 'bg-blue-100',
    green: 'bg-green-100',
    yellow: 'bg-yellow-100',
    red: 'bg-red-100',
    gray: 'bg-gray-100'
  }
  return classes[props.iconColor]
})

const iconClass = computed(() => {
  const classes = {
    blue: 'text-blue-600',
    green: 'text-green-600',
    yellow: 'text-yellow-600',
    red: 'text-red-600',
    gray: 'text-gray-600'
  }
  return classes[props.iconColor]
})
</script>