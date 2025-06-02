<template>
  <span :class="[badgeClasses, 'px-3 py-1.5 inline-flex items-center text-xs leading-5 font-semibold rounded-full transition-all duration-200 shadow-sm']">
    <div v-if="showDot" :class="[dotClasses, 'w-2 h-2 rounded-full mr-2 animate-pulse']"></div>
    <component v-if="icon" :is="icon" class="w-3 h-3 mr-1" />
    {{ displayText }}
  </span>
</template>

<script setup>
import { computed } from 'vue'
import { 
  CheckCircleIcon, 
  ExclamationTriangleIcon, 
  XCircleIcon, 
  ClockIcon,
  PlayIcon,
  PauseIcon
} from '@heroicons/vue/24/solid'

const props = defineProps({
  status: {
    type: String,
    required: true
  },
  type: {
    type: String,
    default: 'default', // 'default', 'driver', 'vehicle', 'incident', 'sos'
    validator: (value) => ['default', 'driver', 'vehicle', 'incident', 'sos'].includes(value)
  },
  showDot: {
    type: Boolean,
    default: false
  },
  showIcon: {
    type: Boolean,
    default: false
  }
})

const statusConfig = {
  driver: {
    active: { 
      classes: 'bg-green-100 text-green-800 border border-green-200', 
      text: 'Active',
      icon: CheckCircleIcon,
      dot: 'bg-green-500'
    },
    inactive: { 
      classes: 'bg-gray-100 text-gray-800 border border-gray-200', 
      text: 'Inactive',
      icon: PauseIcon,
      dot: 'bg-gray-500'
    },
    on_leave: { 
      classes: 'bg-yellow-100 text-yellow-800 border border-yellow-200', 
      text: 'On Leave',
      icon: ClockIcon,
      dot: 'bg-yellow-500'
    }
  },
  vehicle: {
    active: { 
      classes: 'bg-green-100 text-green-800 border border-green-200', 
      text: 'Active',
      icon: CheckCircleIcon,
      dot: 'bg-green-500'
    },
    maintenance: { 
      classes: 'bg-yellow-100 text-yellow-800 border border-yellow-200', 
      text: 'Maintenance',
      icon: ExclamationTriangleIcon,
      dot: 'bg-yellow-500'
    },
    inactive: { 
      classes: 'bg-gray-100 text-gray-800 border border-gray-200', 
      text: 'Inactive',
      icon: PauseIcon,
      dot: 'bg-gray-500'
    }
  },
  incident: {
    reported: { 
      classes: 'bg-red-100 text-red-800 border border-red-200', 
      text: 'Reported',
      icon: ExclamationTriangleIcon,
      dot: 'bg-red-500 animate-pulse'
    },
    in_progress: { 
      classes: 'bg-yellow-100 text-yellow-800 border border-yellow-200', 
      text: 'In Progress',
      icon: PlayIcon,
      dot: 'bg-yellow-500'
    },
    resolved: { 
      classes: 'bg-blue-100 text-blue-800 border border-blue-200', 
      text: 'Resolved',
      icon: CheckCircleIcon,
      dot: 'bg-blue-500'
    },
    closed: { 
      classes: 'bg-green-100 text-green-800 border border-green-200', 
      text: 'Closed',
      icon: CheckCircleIcon,
      dot: 'bg-green-500'
    }
  },
  sos: {
    active: { 
      classes: 'bg-red-100 text-red-800 border border-red-200 animate-pulse', 
      text: 'URGENT',
      icon: ExclamationTriangleIcon,
      dot: 'bg-red-500 animate-pulse'
    },
    responded: { 
      classes: 'bg-yellow-100 text-yellow-800 border border-yellow-200', 
      text: 'Responded',
      icon: PlayIcon,
      dot: 'bg-yellow-500'
    },
    resolved: { 
      classes: 'bg-green-100 text-green-800 border border-green-200', 
      text: 'Resolved',
      icon: CheckCircleIcon,
      dot: 'bg-green-500'
    }
  },
  default: {
    active: { 
      classes: 'bg-green-100 text-green-800 border border-green-200', 
      text: 'Active',
      icon: CheckCircleIcon,
      dot: 'bg-green-500'
    },
    inactive: { 
      classes: 'bg-gray-100 text-gray-800 border border-gray-200', 
      text: 'Inactive',
      icon: XCircleIcon,
      dot: 'bg-gray-500'
    }
  }
}

const config = computed(() => {
  const typeConfig = statusConfig[props.type] || statusConfig.default
  return typeConfig[props.status] || typeConfig.active || statusConfig.default.active
})

const badgeClasses = computed(() => config.value.classes)
const displayText = computed(() => config.value.text)
const icon = computed(() => props.showIcon ? config.value.icon : null)
const dotClasses = computed(() => config.value.dot)
</script>