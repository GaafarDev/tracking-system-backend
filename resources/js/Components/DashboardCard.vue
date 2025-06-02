<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
    <div class="p-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center">
          <div :class="[iconBgColor, 'rounded-lg p-3']">
            <component :is="icon" :class="[iconColor, 'h-6 w-6']" />
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">{{ title }}</p>
            <p class="text-2xl font-semibold text-gray-900">{{ value }}</p>
          </div>
        </div>
        <div v-if="trend" class="flex items-center">
          <component :is="trend.direction === 'up' ? ArrowUpIcon : ArrowDownIcon" 
                     :class="[trend.direction === 'up' ? 'text-green-500' : 'text-red-500', 'h-4 w-4 mr-1']" />
          <span :class="[trend.direction === 'up' ? 'text-green-600' : 'text-red-600', 'text-sm font-medium']">
            {{ trend.value }}%
          </span>
        </div>
      </div>
      <div v-if="description" class="mt-4">
        <p class="text-sm text-gray-500">{{ description }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ArrowUpIcon, ArrowDownIcon } from '@heroicons/vue/24/outline'

defineProps({
  title: {
    type: String,
    required: true
  },
  value: {
    type: [String, Number],
    required: true
  },
  icon: {
    type: Object,
    required: true
  },
  iconColor: {
    type: String,
    default: 'text-blue-600'
  },
  iconBgColor: {
    type: String,
    default: 'bg-blue-100'
  },
  trend: {
    type: Object,
    default: null
  },
  description: {
    type: String,
    default: null
  }
})
</script>