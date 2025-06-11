<template>
  <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden hover:shadow-2xl transition-all duration-300">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <div class="p-2 bg-indigo-100 rounded-xl">
            <CalendarIcon class="w-5 h-5 text-indigo-600" />
          </div>
          <div>
            <h3 class="text-lg font-semibold text-gray-900">Today's Schedules</h3>
            <p class="text-sm text-gray-600">{{ getCurrentDate() }}</p>
          </div>
        </div>
        <Link :href="route('schedules.index')" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
          View All →
        </Link>
      </div>
    </div>

    <!-- Schedule List -->
    <div class="p-6">
      <div v-if="todaySchedules && todaySchedules.length > 0" class="space-y-4">
        <div 
          v-for="schedule in todaySchedules.slice(0, 5)" 
          :key="schedule.id"
          class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors duration-200 cursor-pointer"
          @click="viewSchedule(schedule)"
        >
          <div class="flex items-center space-x-4">
            <!-- Driver Avatar -->
            <div class="w-10 h-10 bg-gradient-to-br from-indigo-400 to-purple-600 rounded-full flex items-center justify-center">
              <span class="text-white font-semibold text-sm">
                {{ (schedule.driver?.user?.name || 'D').charAt(0).toUpperCase() }}
              </span>
            </div>
            
            <!-- Schedule Info -->
            <div>
              <div class="font-semibold text-gray-900">
                {{ schedule.driver?.user?.name || 'Unknown Driver' }}
              </div>
              <div class="text-sm text-gray-600">
                {{ schedule.route?.name || 'No Route' }}
              </div>
              <div class="text-xs text-gray-500">
                {{ schedule.vehicle?.plate_number || 'No Vehicle' }}
              </div>
            </div>
          </div>
          
          <!-- Time and Status -->
          <div class="text-right">
            <div class="text-sm font-medium text-gray-900">
              {{ formatTime(schedule.departure_time) }} - {{ formatTime(schedule.arrival_time) }}
            </div>
            <div class="flex items-center justify-end mt-1 space-x-2">
              <div 
                :class="[
                  'w-2 h-2 rounded-full',
                  schedule.is_active ? 'bg-green-500' : 'bg-gray-400'
                ]"
              ></div>
              <span 
                :class="[
                  'text-xs font-medium',
                  schedule.is_active ? 'text-green-600' : 'text-gray-500'
                ]"
              >
                {{ schedule.is_active ? 'Active' : 'Inactive' }}
              </span>
            </div>
          </div>
        </div>
        
        <!-- Show more link if there are more schedules -->
        <div v-if="todaySchedules.length > 5" class="text-center pt-2">
          <Link 
            :href="route('schedules.index')" 
            class="text-sm text-indigo-600 hover:text-indigo-800 font-medium"
          >
            + {{ todaySchedules.length - 5 }} more schedules
          </Link>
        </div>
      </div>
      
      <!-- Empty State -->
      <div v-else class="text-center py-8">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <CalendarIcon class="w-8 h-8 text-gray-400" />
        </div>
        <h4 class="text-lg font-medium text-gray-900 mb-2">No Schedules Today</h4>
        <p class="text-gray-500 mb-4">There are no schedules for today.</p>
        <Link :href="route('schedules.create')" class="btn-primary text-sm">
          Create Schedule
        </Link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { router, Link } from '@inertiajs/vue3'
import { CalendarIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  todaySchedules: {
    type: Array,
    default: () => []
  }
})

function getCurrentDate() {
  const today = new Date()
  return today.toLocaleDateString('en-US', { 
    weekday: 'long', 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  })
}

function formatTime(time) {
  if (!time) return 'N/A'
  return time
}

function viewSchedule(schedule) {
  router.visit(route('schedules.show', schedule.id))
}
</script>