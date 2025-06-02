<template>
  <div class="space-y-1">
    <label v-if="label" :for="id" class="block text-sm font-medium text-gray-700">
      {{ label }}
      <span v-if="required" class="text-red-500 ml-1">*</span>
    </label>
    
    <div class="relative">
      <!-- Input Field -->
      <input
        v-if="type !== 'textarea' && type !== 'select'"
        :id="id"
        :type="type"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
        :class="inputClasses"
        :autocomplete="autocomplete"
      />
      
      <!-- Textarea -->
      <textarea
        v-else-if="type === 'textarea'"
        :id="id"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
        :rows="rows"
        :class="inputClasses"
      ></textarea>
      
      <!-- Select -->
      <select
        v-else-if="type === 'select'"
        :id="id"
        :value="modelValue"
        @change="$emit('update:modelValue', $event.target.value)"
        :required="required"
        :disabled="disabled"
        :class="inputClasses"
      >
        <option v-if="placeholder" value="">{{ placeholder }}</option>
        <option v-for="option in options" :key="option.value" :value="option.value">
          {{ option.label }}
        </option>
      </select>
      
      <!-- Icon -->
      <div v-if="icon" class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <component :is="icon" class="h-5 w-5 text-gray-400" />
      </div>
    </div>
    
    <!-- Help Text -->
    <p v-if="help" class="text-sm text-gray-600">{{ help }}</p>
    
    <!-- Error Message -->
    <p v-if="error" class="text-sm text-red-600 flex items-center">
      <ExclamationCircleIcon class="w-4 h-4 mr-1" />
      {{ error }}
    </p>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { ExclamationCircleIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  id: String,
  label: String,
  type: {
    type: String,
    default: 'text'
  },
  modelValue: [String, Number],
  placeholder: String,
  required: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  error: String,
  help: String,
  icon: Object,
  autocomplete: String,
  rows: {
    type: Number,
    default: 3
  },
  options: {
    type: Array,
    default: () => []
  }
})

defineEmits(['update:modelValue'])

const inputClasses = computed(() => {
  const baseClasses = 'block w-full rounded-xl border-gray-300 shadow-sm transition-colors duration-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500'
  const iconClasses = props.icon ? 'pl-10' : ''
  const errorClasses = props.error ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : ''
  const disabledClasses = props.disabled ? 'bg-gray-50 text-gray-500 cursor-not-allowed' : ''
  
  return [baseClasses, iconClasses, errorClasses, disabledClasses].filter(Boolean).join(' ')
})
</script>