<template>
  <div>
    <!-- Loading overlay -->
    <div v-if="$page.props.loading" class="fixed inset-0 bg-black bg-opacity-25 z-50 flex items-center justify-center">
      <div class="bg-white p-6 rounded-xl shadow-lg">
        <div class="flex items-center space-x-3">
          <svg class="animate-spin h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span class="text-gray-700">Loading...</span>
        </div>
      </div>
    </div>
    
    <!-- Your existing layout content -->
    <div class="flex h-screen">
      <!-- Desktop Sidebar - Hover to expand -->
      <div class="hidden md:flex md:flex-col md:w-20 hover:md:w-72 transition-all duration-300 ease-in-out group">
        <div class="flex flex-col flex-grow pt-5 overflow-y-auto bg-white/80 backdrop-blur-xl border-r border-gray-200/80 shadow-elegant">
          <!-- Logo -->
          <div class="flex items-center flex-shrink-0">
            <Link :href="route('dashboard')" class="flex items-center w-full">
              <div class="relative w-full h-16 overflow-hidden">
                <!-- Short Logo - shown when collapsed -->
                <div class="absolute left-0 top-0 w-16 h-16 flex items-center justify-center transition-all duration-300 ease-in-out group-hover:opacity-0 group-hover:scale-95">
                  <img 
                    src="/images/Maraliner_logo_short.png" 
                    alt="MaraLinear Short Logo" 
                    class="h-12 w-12 object-contain"
                  />
                </div>
                
                <!-- Full Logo - shown when expanded -->
                <div class="absolute left-0 top-0 w-full h-16 flex items-center justify-start opacity-0 scale-95 transition-all duration-300 ease-in-out group-hover:opacity-100 group-hover:scale-100">
                  <img 
                    src="/images/Maraliner_logo_Full.png" 
                    alt="MaraLinear Full Logo" 
                    class="h-12 w-auto object-contain max-w-none"
                  />
                </div>
              </div>
            </Link>
          </div>
          
          <!-- Navigation with hover labels -->
          <div class="mt-4 flex-1 flex flex-col px-2">
            <nav class="flex-1 space-y-2">
              <!-- Dashboard -->
              <div class="relative">
                <Link :href="route('dashboard')" 
                      :class="[route().current('dashboard') ? 'bg-gradient-to-r from-primary-500 to-primary-600 text-white shadow-lg shadow-primary-500/25' : 'text-gray-700 hover:bg-gray-50', 'group/item flex items-center px-2 py-3 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md']"
                      preserve-state
                      preserve-scroll
                >
                  <HomeIcon :class="[route().current('dashboard') ? 'text-white' : 'text-gray-400 group-hover:item:text-gray-600', 'h-6 w-6 transition-colors flex-shrink-0 group-hover:mr-3']" />
                  <span class="ml-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">Dashboard</span>
                </Link>
              </div>

              <!-- Drivers -->
              <div class="relative">
                <Link :href="route('drivers.index')" 
                      :class="[route().current('drivers.*') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-lg shadow-blue-500/25' : 'text-gray-700 hover:bg-gray-50', 'group/item flex items-center px-2 py-3 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md']">
                  <UserGroupIcon :class="[route().current('drivers.*') ? 'text-white' : 'text-gray-400 group-hover:item:text-gray-600', 'h-6 w-6 transition-colors flex-shrink-0 group-hover:mr-3']" />
                  <span class="ml-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">Drivers</span>
                </Link>
              </div>

              <!-- Vehicles -->
              <div class="relative">
                <Link :href="route('vehicles.index')" 
                      :class="[route().current('vehicles.*') ? 'bg-gradient-to-r from-emerald-500 to-emerald-600 text-white shadow-lg shadow-emerald-500/25' : 'text-gray-700 hover:bg-gray-50', 'group/item flex items-center px-2 py-3 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md']">
                  <TruckIcon :class="[route().current('vehicles.*') ? 'text-white' : 'text-gray-400 group-hover:item:text-gray-600', 'h-6 w-6 transition-colors flex-shrink-0 group-hover:mr-3']" />
                  <span class="ml-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">Vehicles</span>
                </Link>
              </div>

              <!-- Routes -->
              <div class="relative">
                <Link :href="route('routes.index')" 
                      :class="[route().current('routes.*') ? 'bg-gradient-to-r from-purple-500 to-purple-600 text-white shadow-lg shadow-purple-500/25' : 'text-gray-700 hover:bg-gray-50', 'group/item flex items-center px-2 py-3 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md']">
                  <MapIcon :class="[route().current('routes.*') ? 'text-white' : 'text-gray-400 group-hover:item:text-gray-600', 'h-6 w-6 transition-colors flex-shrink-0 group-hover:mr-3']" />
                  <span class="ml-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">Routes</span>
                </Link>
              </div>

              <!-- Schedules -->
              <div class="relative">
                <Link :href="route('schedules.index')" 
                      :class="[route().current('schedules.*') ? 'bg-gradient-to-r from-indigo-500 to-indigo-600 text-white shadow-lg shadow-indigo-500/25' : 'text-gray-700 hover:bg-gray-50', 'group/item flex items-center px-2 py-3 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md']">
                  <CalendarIcon :class="[route().current('schedules.*') ? 'text-white' : 'text-gray-400 group-hover:item:text-gray-600', 'h-6 w-6 transition-colors flex-shrink-0 group-hover:mr-3']" />
                  <span class="ml-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">Schedules</span>
                </Link>
              </div>

              <!-- Incidents -->
              <div class="relative">
                <Link :href="route('incidents.index')" 
                      :class="[route().current('incidents.*') ? 'bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg shadow-orange-500/25' : 'text-gray-700 hover:bg-gray-50', 'group/item flex items-center px-2 py-3 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md']">
                  <ExclamationTriangleIcon :class="[route().current('incidents.*') ? 'text-white' : 'text-gray-400 group-hover:item:text-gray-600', 'h-6 w-6 transition-colors flex-shrink-0 group-hover:mr-3']" />
                  <span class="ml-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">Incidents</span>
                </Link>
              </div>

              <!-- SOS Alerts -->
              <div class="relative">
                <Link :href="route('sos.index')" 
                      :class="[route().current('sos.*') ? 'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg shadow-red-500/25' : 'text-gray-700 hover:bg-gray-50', 'group/item flex items-center px-2 py-3 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md relative']">
                  <ShieldExclamationIcon :class="[route().current('sos.*') ? 'text-white' : 'text-gray-400 group-hover:item:text-gray-600', 'h-6 w-6 transition-colors flex-shrink-0 group-hover:mr-3']" />
                  <span class="ml-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">SOS Alerts</span>
                  <div v-if="activeSosCount > 0" class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold">{{ activeSosCount }}</div>
                </Link>
              </div>

              <!-- Vendors -->
              <div class="relative">
                <Link :href="route('vendors.index')" 
                      :class="[route().current('vendors.*') ? 'bg-gradient-to-r from-teal-500 to-teal-600 text-white shadow-lg shadow-teal-500/25' : 'text-gray-700 hover:bg-gray-50', 'group/item flex items-center px-2 py-3 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md']">
                  <BuildingOfficeIcon :class="[route().current('vendors.*') ? 'text-white' : 'text-gray-400 group-hover:item:text-gray-600', 'h-6 w-6 transition-colors flex-shrink-0 group-hover:mr-3']" />
                  <span class="ml-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">Vendors</span>
                </Link>
              </div>
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
                <!-- Mobile navigation content -->
                <div class="pt-5 pb-4 overflow-y-auto">
                  <!-- Mobile Logo - Always show full logo -->
                  <div class="flex items-center flex-shrink-0 px-6 pb-4">
                    <Link :href="route('dashboard')" class="flex items-center w-full">
                      <img 
                        src="/images/Maraliner_logo_Full.png" 
                        alt="MaraLinear Full Logo" 
                        class="h-12 w-auto object-contain"
                      />
                    </Link>
                  </div>
                  
                  <!-- Mobile Navigation Items -->
                  <nav class="px-4 space-y-2">
                    <Link :href="route('dashboard')" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md" :class="[route().current('dashboard') ? 'bg-gradient-to-r from-primary-500 to-primary-600 text-white shadow-lg shadow-primary-500/25' : 'text-gray-700 hover:bg-gray-50']">
                      <HomeIcon class="mr-3 h-5 w-5 transition-colors" :class="[route().current('dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600']" />
                      Dashboard
                    </Link>
                    
                    <!-- Drivers -->
                    <Link :href="route('drivers.index')" 
                          :class="[route().current('drivers.*') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-lg shadow-blue-500/25' : 'text-gray-700 hover:bg-gray-50', 'group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md']">
                      <UserGroupIcon :class="[route().current('drivers.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600', 'h-5 w-5 transition-colors mr-3']" />
                      Drivers
                    </Link>

                    <!-- Vehicles -->
                    <Link :href="route('vehicles.index')" 
                          :class="[route().current('vehicles.*') ? 'bg-gradient-to-r from-emerald-500 to-emerald-600 text-white shadow-lg shadow-emerald-500/25' : 'text-gray-700 hover:bg-gray-50', 'group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md']">
                      <TruckIcon :class="[route().current('vehicles.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600', 'h-5 w-5 transition-colors mr-3']" />
                      Vehicles
                    </Link>

                    <!-- Routes -->
                    <Link :href="route('routes.index')" 
                          :class="[route().current('routes.*') ? 'bg-gradient-to-r from-purple-500 to-purple-600 text-white shadow-lg shadow-purple-500/25' : 'text-gray-700 hover:bg-gray-50', 'group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md']">
                      <MapIcon :class="[route().current('routes.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600', 'h-5 w-5 transition-colors mr-3']" />
                      Routes
                    </Link>

                    <!-- Schedules -->
                    <Link :href="route('schedules.index')" 
                          :class="[route().current('schedules.*') ? 'bg-gradient-to-r from-indigo-500 to-indigo-600 text-white shadow-lg shadow-indigo-500/25' : 'text-gray-700 hover:bg-gray-50', 'group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md']">
                      <CalendarIcon :class="[route().current('schedules.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600', 'h-5 w-5 transition-colors mr-3']" />
                      Schedules
                    </Link>

                    <!-- Incidents -->
                    <Link :href="route('incidents.index')" 
                          :class="[route().current('incidents.*') ? 'bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg shadow-orange-500/25' : 'text-gray-700 hover:bg-gray-50', 'group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md']">
                      <ExclamationTriangleIcon :class="[route().current('incidents.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600', 'h-5 w-5 transition-colors mr-3']" />
                      Incidents
                    </Link>

                    <!-- SOS Alerts -->
                    <Link :href="route('sos.index')" 
                          :class="[route().current('sos.*') ? 'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg shadow-red-500/25' : 'text-gray-700 hover:bg-gray-50', 'group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md relative']">
                      <ShieldExclamationIcon :class="[route().current('sos.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600', 'h-5 w-5 transition-colors mr-3']" />
                      SOS Alerts
                      <div v-if="activeSosCount > 0" class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold">{{ activeSosCount }}</div>
                    </Link>

                    <!-- Vendors -->
                    <Link :href="route('vendors.index')" 
                          :class="[route().current('vendors.*') ? 'bg-gradient-to-r from-teal-500 to-teal-600 text-white shadow-lg shadow-teal-500/25' : 'text-gray-700 hover:bg-gray-50', 'group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md']">
                      <BuildingOfficeIcon :class="[route().current('vendors.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600', 'h-5 w-5 transition-colors mr-3']" />
                      Vendors
                    </Link>
                  </nav>
                </div>
              </DialogPanel>
            </TransitionChild>
          </div>
        </Dialog>
      </TransitionRoot>
    </div>
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
  ChevronLeftIcon,
  ChevronRightIcon,
} from '@heroicons/vue/24/outline'

defineProps({
  activeSosCount: {
    type: Number,
    default: 0
  }
})

const sidebarOpen = ref(false)
const sidebarCollapsed = ref(false)

const toggleSidebar = () => {
  sidebarCollapsed.value = !sidebarCollapsed.value
}
</script>