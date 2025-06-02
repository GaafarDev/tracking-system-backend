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
          <div class="relative">
            <button class="p-2 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-full">
              <BellIcon class="h-6 w-6" />
              <span v-if="notificationCount > 0" class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
                {{ notificationCount }}
              </span>
            </button>
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
import { router } from '@inertiajs/vue3'
import { BellIcon, MagnifyingGlassIcon, ChevronDownIcon } from '@heroicons/vue/24/outline'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'

defineProps({
  title: String,
  subtitle: String,
  user: Object,
  notificationCount: {
    type: Number,
    default: 0
  }
})

const logout = () => {
  router.post(route('logout'))
}
</script>