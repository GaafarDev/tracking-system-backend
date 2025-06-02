<template>
  <div class="flex h-screen">
    <!-- Desktop Sidebar -->
    <div class="hidden md:flex md:w-72 md:flex-col">
      <div class="flex flex-col flex-grow pt-5 overflow-y-auto bg-white/80 backdrop-blur-xl border-r border-gray-200/80 shadow-elegant">
        <!-- Logo -->
        <div class="flex items-center flex-shrink-0 px-6 pb-4">
          <Link :href="route('dashboard')" class="flex items-center group">
            <div class="relative">
              <div class="h-10 w-10 bg-gradient-to-r from-primary-600 to-secondary-600 rounded-xl flex items-center justify-center transform transition-transform group-hover:scale-105">
                <span class="text-white font-bold text-xl">ML</span>
              </div>
              <div class="absolute -inset-2 bg-primary-500/20 rounded-xl blur opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </div>
            <div class="ml-3">
              <span class="text-2xl font-bold text-gradient-primary">MaraLinear</span>
              <div class="text-xs text-gray-500 font-medium">Transport Management</div>
            </div>
          </Link>
        </div>
        
        <!-- Navigation -->
        <div class="mt-4 flex-1 flex flex-col px-4">
          <nav class="flex-1 space-y-2">
            <!-- Dashboard -->
            <Link :href="route('dashboard')" 
                  :class="[route().current('dashboard') ? 'bg-gradient-to-r from-primary-500 to-primary-600 text-white shadow-lg shadow-primary-500/25' : 'text-gray-700 hover:bg-gray-50', 'group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md']">
              <HomeIcon :class="[route().current('dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600', 'mr-3 h-5 w-5 transition-colors']" />
              Dashboard
              <div v-if="route().current('dashboard')" class="ml-auto w-2 h-2 bg-white rounded-full"></div>
            </Link>

            <!-- Drivers -->
            <Link :href="route('drivers.index')" 
                  :class="[route().current('drivers.*') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-lg shadow-blue-500/25' : 'text-gray-700 hover:bg-gray-50', 'group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md']">
              <UserGroupIcon :class="[route().current('drivers.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600', 'mr-3 h-5 w-5 transition-colors']" />
              Drivers
              <div v-if="route().current('drivers.*')" class="ml-auto w-2 h-2 bg-white rounded-full"></div>
            </Link>

            <!-- Vehicles -->
            <Link :href="route('vehicles.index')" 
                  :class="[route().current('vehicles.*') ? 'bg-gradient-to-r from-emerald-500 to-emerald-600 text-white shadow-lg shadow-emerald-500/25' : 'text-gray-700 hover:bg-gray-50', 'group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md']">
              <TruckIcon :class="[route().current('vehicles.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600', 'mr-3 h-5 w-5 transition-colors']" />
              Vehicles
              <div v-if="route().current('vehicles.*')" class="ml-auto w-2 h-2 bg-white rounded-full"></div>
            </Link>

            <!-- Routes -->
            <Link :href="route('routes.index')" 
                  :class="[route().current('routes.*') ? 'bg-gradient-to-r from-purple-500 to-purple-600 text-white shadow-lg shadow-purple-500/25' : 'text-gray-700 hover:bg-gray-50', 'group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md']">
              <MapIcon :class="[route().current('routes.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600', 'mr-3 h-5 w-5 transition-colors']" />
              Routes
              <div v-if="route().current('routes.*')" class="ml-auto w-2 h-2 bg-white rounded-full"></div>
            </Link>

            <!-- Schedules -->
            <Link :href="route('schedules.index')" 
                  :class="[route().current('schedules.*') ? 'bg-gradient-to-r from-indigo-500 to-indigo-600 text-white shadow-lg shadow-indigo-500/25' : 'text-gray-700 hover:bg-gray-50', 'group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md']">
              <CalendarIcon :class="[route().current('schedules.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600', 'mr-3 h-5 w-5 transition-colors']" />
              Schedules
              <div v-if="route().current('schedules.*')" class="ml-auto w-2 h-2 bg-white rounded-full"></div>
            </Link>

            <!-- Incidents -->
            <Link :href="route('incidents.index')" 
                  :class="[route().current('incidents.*') ? 'bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg shadow-orange-500/25' : 'text-gray-700 hover:bg-gray-50', 'group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md']">
              <ExclamationTriangleIcon :class="[route().current('incidents.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600', 'mr-3 h-5 w-5 transition-colors']" />
              Incidents
              <div v-if="route().current('incidents.*')" class="ml-auto w-2 h-2 bg-white rounded-full"></div>
            </Link>

            <!-- SOS Alerts -->
            <Link :href="route('sos.index')" 
                  :class="[route().current('sos.*') ? 'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg shadow-red-500/25' : 'text-gray-700 hover:bg-gray-50', 'group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md relative']">
              <ShieldExclamationIcon :class="[route().current('sos.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600', 'mr-3 h-5 w-5 transition-colors']" />
              SOS Alerts
              <div v-if="route().current('sos.*')" class="ml-auto w-2 h-2 bg-white rounded-full"></div>
              <span v-if="activeSosCount > 0" class="absolute -top-1 -right-1 h-6 w-6 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold animate-pulse-gentle">{{ activeSosCount }}</span>
            </Link>

            <!-- Vendors -->
            <Link :href="route('vendors.index')" 
                  :class="[route().current('vendors.*') ? 'bg-gradient-to-r from-teal-500 to-teal-600 text-white shadow-lg shadow-teal-500/25' : 'text-gray-700 hover:bg-gray-50', 'group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md']">
              <BuildingOfficeIcon :class="[route().current('vendors.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600', 'mr-3 h-5 w-5 transition-colors']" />
              Vendors
              <div v-if="route().current('vendors.*')" class="ml-auto w-2 h-2 bg-white rounded-full"></div>
            </Link>
          </nav>
        </div>
      </div>
    </div>

    <!-- Mobile menu button -->
    <div class="md:hidden">
      <button @click="sidebarOpen = !sidebarOpen" class="fixed top-4 left-4 z-50 p-3 rounded-xl text-gray-600 hover:text-gray-900 bg-white/80 backdrop-blur-sm border border-gray-200/80 hover:bg-white shadow-elegant focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all">
        <span class="sr-only">Open sidebar</span>
        <Bars3Icon class="h-6 w-6" />
      </button>
    </div>

    <!-- Mobile sidebar with better animations -->
    <TransitionRoot as="template" :show="sidebarOpen">
      <Dialog as="div" class="relative z-40 md:hidden" @close="sidebarOpen = false">
        <TransitionChild as="template" enter="transition-opacity ease-linear duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="transition-opacity ease-linear duration-300" leave-from="opacity-100" leave-to="opacity-0">
          <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" />
        </TransitionChild>

        <div class="fixed inset-0 flex z-40">
          <TransitionChild as="template" enter="transition ease-in-out duration-300 transform" enter-from="-translate-x-full" enter-to="translate-x-0" leave="transition ease-in-out duration-300 transform" leave-from="translate-x-0" leave-to="-translate-x-full">
            <DialogPanel class="relative flex-1 flex flex-col max-w-xs w-full bg-white/95 backdrop-blur-xl shadow-2xl">
              <TransitionChild as="template" enter="ease-in-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in-out duration-300" leave-from="opacity-100" leave-to="opacity-0">
                <div class="absolute top-0 right-0 -mr-12 pt-2">
                  <button type="button" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white bg-white/20 backdrop-blur-sm" @click="sidebarOpen = false">
                    <span class="sr-only">Close sidebar</span>
                    <XMarkIcon class="h-6 w-6 text-white" />
                  </button>
                </div>
              </TransitionChild>
              <!-- Same navigation content as desktop but optimized for mobile -->
            </DialogPanel>
          </TransitionChild>
        </div>
      </Dialog>
    </TransitionRoot>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue'
import {
  Bars3Icon,
  XMarkIcon,
  HomeIcon,
  UserGroupIcon,
  TruckIcon,
  MapIcon,
  CalendarIcon,
  ExclamationTriangleIcon,
  ShieldExclamationIcon,
  BuildingOfficeIcon,
} from '@heroicons/vue/24/outline'
import ApplicationMark from '@/Components/ApplicationMark.vue'

defineProps({
  activeSosCount: {
    type: Number,
    default: 0
  }
})

const sidebarOpen = ref(false)
</script>