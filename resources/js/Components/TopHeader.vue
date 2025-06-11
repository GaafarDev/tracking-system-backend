<template>
  <header class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- Page Title -->
        <div class="flex items-center">
          <h1 class="text-2xl font-semibold text-gray-900">{{ title }}</h1>
          <div v-if="subtitle" class="ml-4 text-sm text-gray-500">{{ subtitle }}</div>
        </div>

        <!-- Right side actions -->
        <div class="flex items-center space-x-4">
          <!-- Notifications -->
          <div class="relative notification-dropdown-container">
            <button 
              @click="toggleNotifications" 
              class="p-2 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-full relative"
            >
              <BellIcon class="h-6 w-6" />
              <span v-if="unreadCount > 0" class="absolute -top-1 -right-1 h-5 w-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-medium">
                {{ unreadCount > 9 ? '9+' : unreadCount }}
              </span>
            </button>
            
            <!-- Notification Dropdown -->
            <div 
              v-show="showNotifications" 
              class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-200 z-50 max-h-96 overflow-y-auto"
            >
              <div class="p-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
              </div>
              
              <div v-if="notifications.length === 0" class="p-6 text-center text-gray-500">
                <BellIcon class="w-12 h-12 text-gray-300 mx-auto mb-3" />
                <p>No new notifications</p>
              </div>
              
              <div v-else class="divide-y divide-gray-100">
                <div 
                  v-for="notification in notifications" 
                  :key="notification.id"
                  :class="[
                    'p-4 hover:bg-gray-50 transition-colors duration-150 cursor-pointer',
                    !notification.is_read ? 'bg-blue-50' : ''
                  ]"
                  @click="markAsRead(notification)"
                >
                  <div class="flex items-start space-x-3">
                    <div :class="[
                      'w-2 h-2 rounded-full mt-2',
                      !notification.is_read ? 'bg-blue-500' : 'bg-gray-300'
                    ]"></div>
                    <div class="flex-1">
                      <h4 class="text-sm font-medium text-gray-900">{{ notification.title }}</h4>
                      <p class="text-sm text-gray-600 mt-1">{{ notification.message }}</p>
                      <p class="text-xs text-gray-400 mt-2">{{ formatDate(notification.created_at) }}</p>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="p-3 border-t border-gray-200 bg-gray-50">
                <button 
                  @click="markAllAsRead"
                  class="w-full text-center text-sm text-indigo-600 hover:text-indigo-800 font-medium"
                >
                  Mark all as read
                </button>
              </div>
            </div>
          </div>

          <!-- Search -->
          <div class="hidden md:block">
            <div class="relative">
              <MagnifyingGlassIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
              <input
                type="text"
                placeholder="Search..."
                class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm w-64"
              />
            </div>
          </div>

          <!-- User Menu -->
          <Dropdown align="right" width="48">
            <template #trigger>
              <button class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                  <span class="text-sm font-medium text-gray-700">
                    {{ user.name.charAt(0).toUpperCase() }}
                  </span>
                </div>
                <ChevronDownIcon class="ml-2 h-4 w-4 text-gray-400" />
              </button>
            </template>

            <template #content>
              <div class="px-4 py-2 text-xs text-gray-400 border-b border-gray-100">
                Manage Account
              </div>
              <DropdownLink :href="route('profile.show')">
                Profile
              </DropdownLink>
              <div class="border-t border-gray-100"></div>
              <form @submit.prevent="logout">
                <DropdownLink as="button">
                  Log Out
                </DropdownLink>
              </form>
            </template>
          </Dropdown>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'
import { BellIcon, MagnifyingGlassIcon, ChevronDownIcon } from '@heroicons/vue/24/outline'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'

const props = defineProps({
  title: String,
  subtitle: String,
  user: Object,
  notifications: {
    type: Array,
    default: () => []
  },
  unreadCount: {
    type: Number,
    default: 0
  }
})

const showNotifications = ref(false)

function toggleNotifications() {
  showNotifications.value = !showNotifications.value
}

function markAsRead(notification) {
  if (!notification.is_read) {
    router.post(`/api/notifications/${notification.id}/read`)
  }
}

function markAllAsRead() {
  router.post('/api/notifications/mark-all-read')
  showNotifications.value = false
}

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString()
}

const logout = () => {
  router.post(route('logout'))
}

// FIX: Properly handle click outside functionality
const handleClickOutside = (e) => {
  // Check if click is outside the notification dropdown
  const notificationElement = e.target.closest('.notification-dropdown-container')
  if (!notificationElement) {
    showNotifications.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>