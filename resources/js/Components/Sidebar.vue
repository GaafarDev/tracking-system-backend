<template>
  <div class="flex h-screen">
    <!-- Desktop Sidebar - Hover to expand -->
    <div class="hidden md:flex md:flex-col md:w-20 hover:md:w-72 transition-all duration-300 ease-in-out group">
      <div class="flex flex-col flex-grow pt-5 overflow-y-auto bg-white/80 backdrop-blur-xl border-r border-gray-200/80 shadow-elegant">
        <!-- Logo -->
        <div class="flex items-center flex-shrink-0 px-6 pb-4">
          <Link :href="route('dashboard')" class="flex items-center group-hover:group">
            <div class="relative">
              <div class="h-10 w-10 bg-gradient-to-r from-primary-600 to-secondary-600 rounded-xl flex items-center justify-center transform transition-transform hover:scale-105">
                <span class="text-white font-bold text-xl">ML</span>
              </div>
            </div>
            <div class="ml-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">
              <span class="text-2xl font-bold text-gradient-primary">MaraLinear</span>
              <div class="text-xs text-gray-500 font-medium">Transport Management</div>
            </div>
          </Link>
        </div>
        
        <!-- Navigation with hover labels -->
        <div class="mt-4 flex-1 flex flex-col px-2">
          <nav class="flex-1 space-y-2">
            <!-- Dashboard -->
            <div class="relative">
              <Link :href="route('dashboard')" 
                    :class="[route().current('dashboard') ? 'bg-gradient-to-r from-primary-500 to-primary-600 text-white shadow-lg shadow-primary-500/25' : 'text-gray-700 hover:bg-gray-50', 'group/item flex items-center px-2 py-3 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md']">
                <HomeIcon :class="[route().current('dashboard') ? 'text-white' : 'text-gray-400 group-hover/item:text-gray-600', 'h-6 w-6 transition-colors flex-shrink-0 group-hover:mr-3']" />
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
              <!-- Mobile navigation content (same as desktop but without collapse feature) -->
              <div class="pt-5 pb-4 overflow-y-auto">
                <!-- Logo -->
                <div class="flex items-center flex-shrink-0 px-6 pb-4">
                  <Link :href="route('dashboard')" class="flex items-center group">
                    <div class="relative">
                      <div class="h-10 w-10 bg-gradient-to-r from-primary-600 to-secondary-600 rounded-xl flex items-center justify-center transform transition-transform group-hover:scale-105">
                        <span class="text-white font-bold text-xl">ML</span>
                      </div>
                    </div>
                    <div class="ml-3">
                      <span class="text-2xl font-bold text-gradient-primary">MaraLinear</span>
                      <div class="text-xs text-gray-500 font-medium">Transport Management</div>
                    </div>
                  </Link>
                </div>
                
                <!-- Mobile Navigation Items -->
                <nav class="px-4 space-y-2">
                  <!-- Same navigation items as desktop but without collapse considerations -->
                  <Link :href="route('dashboard')" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md" :class="[route().current('dashboard') ? 'bg-gradient-to-r from-primary-500 to-primary-600 text-white shadow-lg shadow-primary-500/25' : 'text-gray-700 hover:bg-gray-50']">
                    <HomeIcon class="mr-3 h-5 w-5 transition-colors" :class="[route().current('dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600']" />
                    Dashboard
                  </Link>
                  <!-- Drivers -->
                  <div class="relative">
                    <Link :href="route('drivers.index')" 
                          :class="[route().current('drivers.*') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-lg shadow-blue-500/25' : 'text-gray-700 hover:bg-gray-50', 'group/item flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md']">
                      <UserGroupIcon :class="[route().current('drivers.*') ? 'text-white' : 'text-gray-400 group-hover:item:text-gray-600', 'h-5 w-5 transition-colors mx-auto group-hover:mx-0 group-hover:mr-3']" />
                      <span class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">Drivers</span>
                    </Link>
                  </div>

                  <!-- Vehicles -->
                  <div class="relative">
                    <Link :href="route('vehicles.index')" 
                          :class="[route().current('vehicles.*') ? 'bg-gradient-to-r from-emerald-500 to-emerald-600 text-white shadow-lg shadow-emerald-500/25' : 'text-gray-700 hover:bg-gray-50', 'group/item flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md']">
                      <TruckIcon :class="[route().current('vehicles.*') ? 'text-white' : 'text-gray-400 group-hover:item:text-gray-600', 'h-5 w-5 transition-colors mx-auto group-hover:mx-0 group-hover:mr-3']" />
                      <span class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">Vehicles</span>
                    </Link>
                  </div>

                  <!-- Routes -->
                  <div class="relative">
                    <Link :href="route('routes.index')" 
                          :class="[route().current('routes.*') ? 'bg-gradient-to-r from-purple-500 to-purple-600 text-white shadow-lg shadow-purple-500/25' : 'text-gray-700 hover:bg-gray-50', 'group/item flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md']">
                      <MapIcon :class="[route().current('routes.*') ? 'text-white' : 'text-gray-400 group-hover:item:text-gray-600', 'h-5 w-5 transition-colors mx-auto group-hover:mx-0 group-hover:mr-3']" />
                      <span class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">Routes</span>
                    </Link>
                  </div>

                  <!-- Schedules -->
                  <div class="relative">
                    <Link :href="route('schedules.index')" 
                          :class="[route().current('schedules.*') ? 'bg-gradient-to-r from-indigo-500 to-indigo-600 text-white shadow-lg shadow-indigo-500/25' : 'text-gray-700 hover:bg-gray-50', 'group/item flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md']">
                      <CalendarIcon :class="[route().current('schedules.*') ? 'text-white' : 'text-gray-400 group-hover:item:text-gray-600', 'h-5 w-5 transition-colors mx-auto group-hover:mx-0 group-hover:mr-3']" />
                      <span class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">Schedules</span>
                    </Link>
                  </div>

                  <!-- Incidents -->
                  <div class="relative">
                    <Link :href="route('incidents.index')" 
                          :class="[route().current('incidents.*') ? 'bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg shadow-orange-500/25' : 'text-gray-700 hover:bg-gray-50', 'group/item flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md']">
                      <ExclamationTriangleIcon :class="[route().current('incidents.*') ? 'text-white' : 'text-gray-400 group-hover:item:text-gray-600', 'h-5 w-5 transition-colors mx-auto group-hover:mx-0 group-hover:mr-3']" />
                      <span class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">Incidents</span>
                    </Link>
                  </div>

                  <!-- SOS Alerts -->
                  <div class="relative">
                    <Link :href="route('sos.index')" 
                          :class="[route().current('sos.*') ? 'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg shadow-red-500/25' : 'text-gray-700 hover:bg-gray-50', 'group/item flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md relative']">
                      <ShieldExclamationIcon :class="[route().current('sos.*') ? 'text-white' : 'text-gray-400 group-hover:item:text-gray-600', 'h-5 w-5 transition-colors mx-auto group-hover:mx-0 group-hover:mr-3']" />
                      <span class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">SOS Alerts</span>
                      <div v-if="activeSosCount > 0" class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold">{{ activeSosCount }}</div>
                    </Link>
                  </div>

                  <!-- Vendors -->
                  <div class="relative">
                    <Link :href="route('vendors.index')" 
                          :class="[route().current('vendors.*') ? 'bg-gradient-to-r from-teal-500 to-teal-600 text-white shadow-lg shadow-teal-500/25' : 'text-gray-700 hover:bg-gray-50', 'group/item flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md']">
                      <BuildingOfficeIcon :class="[route().current('vendors.*') ? 'text-white' : 'text-gray-400 group-hover:item:text-gray-600', 'h-5 w-5 transition-colors mx-auto group-hover:mx-0 group-hover:mr-3']" />
                      <span class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">Vendors</span>
                    </Link>
                  </div>
                </nav>
              </div>
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