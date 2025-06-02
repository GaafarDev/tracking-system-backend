<template>
  <div class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    <div class="hidden md:flex md:w-64 md:flex-col">
      <div class="flex flex-col flex-grow pt-5 overflow-y-auto bg-white border-r border-gray-200">
        <!-- Logo -->
        <div class="flex items-center flex-shrink-0 px-6">
          <Link :href="route('dashboard')" class="flex items-center">
            <ApplicationMark class="h-8 w-auto text-red-600" />
            <span class="ml-3 text-xl font-bold text-gray-900">MaraLinear</span>
          </Link>
        </div>
        
        <!-- Navigation -->
        <div class="mt-8 flex-1 flex flex-col">
          <nav class="flex-1 px-4 pb-4 space-y-1">
            <!-- Dashboard -->
            <Link :href="route('dashboard')" 
                  :class="[route().current('dashboard') ? 'bg-red-50 border-red-500 text-red-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900', 'group flex items-center px-3 py-2 text-sm font-medium border-l-4 rounded-r-md transition-colors duration-200']">
              <HomeIcon :class="[route().current('dashboard') ? 'text-red-500' : 'text-gray-400 group-hover:text-gray-500', 'mr-3 h-5 w-5']" />
              Dashboard
            </Link>

            <!-- Drivers -->
            <Link :href="route('drivers.index')" 
                  :class="[route().current('drivers.*') ? 'bg-blue-50 border-blue-500 text-blue-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900', 'group flex items-center px-3 py-2 text-sm font-medium border-l-4 rounded-r-md transition-colors duration-200']">
              <UserGroupIcon :class="[route().current('drivers.*') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500', 'mr-3 h-5 w-5']" />
              Drivers
            </Link>

            <!-- Vehicles -->
            <Link :href="route('vehicles.index')" 
                  :class="[route().current('vehicles.*') ? 'bg-emerald-50 border-emerald-500 text-emerald-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900', 'group flex items-center px-3 py-2 text-sm font-medium border-l-4 rounded-r-md transition-colors duration-200']">
              <TruckIcon :class="[route().current('vehicles.*') ? 'text-emerald-500' : 'text-gray-400 group-hover:text-gray-500', 'mr-3 h-5 w-5']" />
              Vehicles
            </Link>

            <!-- Routes -->
            <Link :href="route('routes.index')" 
                  :class="[route().current('routes.*') ? 'bg-purple-50 border-purple-500 text-purple-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900', 'group flex items-center px-3 py-2 text-sm font-medium border-l-4 rounded-r-md transition-colors duration-200']">
              <MapIcon :class="[route().current('routes.*') ? 'text-purple-500' : 'text-gray-400 group-hover:text-gray-500', 'mr-3 h-5 w-5']" />
              Routes
            </Link>

            <!-- Schedules -->
            <Link :href="route('schedules.index')" 
                  :class="[route().current('schedules.*') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900', 'group flex items-center px-3 py-2 text-sm font-medium border-l-4 rounded-r-md transition-colors duration-200']">
              <CalendarIcon :class="[route().current('schedules.*') ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500', 'mr-3 h-5 w-5']" />
              Schedules
            </Link>

            <!-- Incidents -->
            <Link :href="route('incidents.index')" 
                  :class="[route().current('incidents.*') ? 'bg-orange-50 border-orange-500 text-orange-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900', 'group flex items-center px-3 py-2 text-sm font-medium border-l-4 rounded-r-md transition-colors duration-200']">
              <ExclamationTriangleIcon :class="[route().current('incidents.*') ? 'text-orange-500' : 'text-gray-400 group-hover:text-gray-500', 'mr-3 h-5 w-5']" />
              Incidents
            </Link>

            <!-- SOS Alerts -->
            <Link :href="route('sos.index')" 
                  :class="[route().current('sos.*') ? 'bg-red-50 border-red-500 text-red-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900', 'group flex items-center px-3 py-2 text-sm font-medium border-l-4 rounded-r-md transition-colors duration-200 relative']">
              <ShieldExclamationIcon :class="[route().current('sos.*') ? 'text-red-500' : 'text-gray-400 group-hover:text-gray-500', 'mr-3 h-5 w-5']" />
              SOS Alerts
              <span v-if="activeSosCount > 0" class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-400 ring-2 ring-white"></span>
            </Link>

            <!-- Vendors -->
            <Link :href="route('vendors.index')" 
                  :class="[route().current('vendors.*') ? 'bg-teal-50 border-teal-500 text-teal-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900', 'group flex items-center px-3 py-2 text-sm font-medium border-l-4 rounded-r-md transition-colors duration-200']">
              <BuildingOfficeIcon :class="[route().current('vendors.*') ? 'text-teal-500' : 'text-gray-400 group-hover:text-gray-500', 'mr-3 h-5 w-5']" />
              Vendors
            </Link>
          </nav>
        </div>
      </div>
    </div>

    <!-- Mobile menu button -->
    <div class="md:hidden">
      <button @click="sidebarOpen = !sidebarOpen" class="fixed top-4 left-4 z-50 p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
        <span class="sr-only">Open sidebar</span>
        <Bars3Icon class="h-6 w-6" />
      </button>
    </div>

    <!-- Mobile sidebar -->
    <TransitionRoot as="template" :show="sidebarOpen">
      <Dialog as="div" class="relative z-40 md:hidden" @close="sidebarOpen = false">
        <TransitionChild as="template" enter="transition-opacity ease-linear duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="transition-opacity ease-linear duration-300" leave-from="opacity-100" leave-to="opacity-0">
          <div class="fixed inset-0 bg-gray-600 bg-opacity-75" />
        </TransitionChild>

        <div class="fixed inset-0 flex z-40">
          <TransitionChild as="template" enter="transition ease-in-out duration-300 transform" enter-from="-translate-x-full" enter-to="translate-x-0" leave="transition ease-in-out duration-300 transform" leave-from="translate-x-0" leave-to="-translate-x-full">
            <DialogPanel class="relative flex-1 flex flex-col max-w-xs w-full bg-white">
              <TransitionChild as="template" enter="ease-in-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in-out duration-300" leave-from="opacity-100" leave-to="opacity-0">
                <div class="absolute top-0 right-0 -mr-12 pt-2">
                  <button type="button" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" @click="sidebarOpen = false">
                    <span class="sr-only">Close sidebar</span>
                    <XMarkIcon class="h-6 w-6 text-white" />
                  </button>
                </div>
              </TransitionChild>
              <!-- Mobile navigation content (same as desktop) -->
              <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                <div class="flex-shrink-0 flex items-center px-4">
                  <ApplicationMark class="h-8 w-auto text-red-600" />
                  <span class="ml-3 text-xl font-bold text-gray-900">MaraLinear</span>
                </div>
                <nav class="mt-5 px-2 space-y-1">
                  <!-- Same navigation items as desktop -->
                </nav>
              </div>
            </DialogPanel>
          </TransitionChild>
          <div class="flex-shrink-0 w-14"></div>
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