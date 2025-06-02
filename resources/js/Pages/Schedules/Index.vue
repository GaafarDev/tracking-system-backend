<template>
    <AppLayout title="Schedules">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header with View Toggle -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gradient-primary">Schedule Management</h1>
                        <p class="text-gray-600 mt-1">Manage driver schedules and assignments</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- View Toggle -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-1">
                            <button 
                                @click="viewMode = 'calendar'"
                                :class="[viewMode === 'calendar' ? 'bg-primary-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-900', 'px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200']"
                            >
                                Calendar
                            </button>
                            <button 
                                @click="viewMode = 'list'"
                                :class="[viewMode === 'list' ? 'bg-primary-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-900', 'px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200']"
                            >
                                List
                            </button>
                        </div>
                        <Link :href="route('schedules.create')" class="btn-primary">
                            <PlusIcon class="w-5 h-5 mr-2" />
                            Add Schedule
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Calendar View -->
            <div v-if="viewMode === 'calendar'" class="card-modern">
                <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-primary-50 to-secondary-50">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Weekly Schedule</h2>
                    
                    <!-- Week Navigation -->
                    <div class="flex items-center justify-between mb-6">
                        <button class="p-2 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-200">
                            <ChevronLeftIcon class="w-5 h-5 text-gray-600" />
                        </button>
                        <span class="text-lg font-medium text-gray-900">Week 1 - Week 5</span>
                        <button class="p-2 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-200">
                            <ChevronRightIcon class="w-5 h-5 text-gray-600" />
                        </button>
                    </div>
                </div>

                <!-- Calendar Grid -->
                <div class="p-6">
                    <div class="grid grid-cols-6 gap-4">
                        <!-- Cars Column -->
                        <div class="font-semibold text-gray-900 border-r border-gray-200 pr-4">
                            <h3 class="text-sm font-medium text-gray-500 mb-4">Vehicles</h3>
                            <div class="space-y-4">
                                <div v-for="vehicle in uniqueVehicles" :key="vehicle.id" class="p-3 bg-gray-50 rounded-lg">
                                    <div class="text-sm font-medium text-gray-900">{{ vehicle.plate_number }}</div>
                                    <div class="text-xs text-gray-500">{{ vehicle.model }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Week Days -->
                        <div v-for="day in weekDays" :key="day" class="relative">
                            <h3 class="text-sm font-medium text-gray-500 mb-4 text-center">{{ day }}</h3>
                            <div class="space-y-4">
                                <div v-for="vehicle in uniqueVehicles" :key="`${vehicle.id}-${day}`" class="h-16 relative">
                                    <!-- Schedule Block -->
                                    <div 
                                        v-for="schedule in getSchedulesForVehicleAndDay(vehicle.id, day.toLowerCase())" 
                                        :key="schedule.id"
                                        :class="getScheduleColorClass(schedule)"
                                        class="absolute inset-0 rounded-lg p-2 text-white text-xs font-medium shadow-sm hover:shadow-md transition-all duration-200 cursor-pointer"
                                        @click="viewSchedule(schedule)"
                                    >
                                        <div class="flex items-center space-x-2">
                                            <div class="w-6 h-6 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                                <span class="text-xs font-semibold">{{ schedule.driver?.user?.name?.charAt(0) }}</span>
                                            </div>
                                            <span class="truncate">{{ schedule.driver?.user?.name }}</span>
                                        </div>
                                        <div class="mt-1 text-xs opacity-90">
                                            {{ schedule.departure_time }} - {{ schedule.arrival_time }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- List View (existing table) -->
            <div v-else class="card-modern">
                <!-- Filters -->
                <div class="flex flex-col md:flex-row gap-4 mb-6">
                    <div class="flex-1">
                        <input 
                            v-model="search" 
                            type="text" 
                            placeholder="Search schedules..." 
                            class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            @input="handleSearchInput"
                        />
                    </div>
                    
                    <!-- Driver Filter -->
                    <div class="w-full md:w-48">
                        <select 
                            v-model="filterDriverId"
                            @change="applyFiltersImmediately"
                            class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">All Drivers</option>
                            <option v-for="driver in props.drivers" :key="driver.id" :value="driver.id">
                                {{ driver.user.name }}
                            </option>
                        </select>
                    </div>
                    
                    <!-- Route Filter -->
                    <div class="w-full md:w-48">
                        <select 
                            v-model="filterRouteId"
                            @change="applyFiltersImmediately"
                            class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">All Routes</option>
                            <option v-for="routeItem in props.routes" :key="routeItem.id" :value="routeItem.id">
                                {{ routeItem.name }}
                            </option>
                        </select>
                    </div>
                    
                    <!-- Day Filter -->
                    <div class="w-full md:w-48">
                        <select 
                            v-model="filterDayOfWeek"
                            @change="applyFiltersImmediately"
                            class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">All Days</option>
                            <option value="monday">Monday</option>
                            <option value="tuesday">Tuesday</option>
                            <option value="wednesday">Wednesday</option>
                            <option value="thursday">Thursday</option>
                            <option value="friday">Friday</option>
                            <option value="saturday">Saturday</option>
                            <option value="sunday">Sunday</option>
                        </select>
                    </div>
                    
                    <!-- Active Status Filter -->
                    <div class="w-full md:w-48">
                        <select 
                            v-model="filterActive"
                            @change="applyFiltersImmediately"
                            class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">All Statuses</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <!-- Clear Filters Button -->
                    <div class="w-full md:w-auto">
                        <button 
                            @click="clearFilters"
                            class="w-full md:w-auto px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400"
                        >
                            Clear Filters
                        </button>
                    </div>
                </div>
                
                <!-- Schedules Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Route</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Driver</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="schedule in props.schedules.data" :key="schedule.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ schedule.route?.name || 'Unknown' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ schedule.driver?.user?.name || 'Unknown' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ schedule.vehicle?.plate_number || 'Unknown' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ getDayName(schedule.day_of_week) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ formatTime(schedule.departure_time) }} - {{ formatTime(schedule.arrival_time) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', schedule.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800']">
                                        {{ schedule.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <Link :href="route('schedules.show', schedule.id)" class="text-blue-600 hover:text-blue-900 mr-3">
                                        View
                                    </Link>
                                    <Link :href="route('schedules.edit', schedule.id)" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        Edit
                                    </Link>
                                    <button @click="confirmDelete(schedule)" class="text-red-600 hover:text-red-900">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            
                            <!-- Empty state -->
                            <tr v-if="props.schedules.data.length === 0">
                                <td colspan="7" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                    No schedules found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="mt-6 flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Showing {{ props.schedules.from || 0 }} to {{ props.schedules.to || 0 }} of {{ props.schedules.total || 0 }} schedules
                    </div>
                    
                    <div class="flex-1 flex justify-end">
                        <Link 
                            v-if="props.schedules.prev_page_url" 
                            :href="props.schedules.prev_page_url" 
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Previous
                        </Link>
                        <Link 
                            v-if="props.schedules.next_page_url" 
                            :href="props.schedules.next_page_url" 
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Next
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { PlusIcon, ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    schedules: Object,
    drivers: Array,
    routes: Array,
    vehicles: Array,
    filters: Object
});

const viewMode = ref('calendar');
const weekDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

// Initialize filters with proper defaults - Fix the active filter initialization
const search = ref(props.filters?.search || '');
const filterDriverId = ref(props.filters?.driver_id || '');
const filterRouteId = ref(props.filters?.route_id || '');
const filterDayOfWeek = ref(props.filters?.day_of_week || '');
const filterActive = ref(props.filters?.is_active !== undefined ? String(props.filters.is_active) : '');

let searchTimeout;

const uniqueVehicles = computed(() => {
    const vehicles = new Map();
    props.schedules.data.forEach(schedule => {
        if (schedule.vehicle) {
            vehicles.set(schedule.vehicle.id, schedule.vehicle);
        }
    });
    return Array.from(vehicles.values());
});

function buildFilters() {
    const filters = {};
    
    if (search.value) filters.search = search.value;
    if (filterDriverId.value) filters.driver_id = filterDriverId.value;
    if (filterRouteId.value) filters.route_id = filterRouteId.value;
    if (filterDayOfWeek.value) filters.day_of_week = filterDayOfWeek.value;
    
    // Properly handle boolean values for is_active filter
    if (filterActive.value !== '') {
        // Convert string to proper boolean/integer for backend
        if (filterActive.value === '1' || filterActive.value === 'true') {
            filters.is_active = true;
        } else if (filterActive.value === '0' || filterActive.value === 'false') {
            filters.is_active = false;
        }
    }
    
    return filters;
}

function applyFiltersImmediately() {
    const filters = buildFilters();
    
    router.get(route('schedules.index'), filters, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        only: ['schedules', 'drivers', 'routes', 'vehicles', 'filters']
    });
}

function handleSearchInput() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFiltersImmediately();
    }, 250);
}

function clearFilters() {
    search.value = '';
    filterDriverId.value = '';
    filterRouteId.value = '';
    filterDayOfWeek.value = '';
    filterActive.value = '';
    
    router.get(route('schedules.index'), {}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        only: ['schedules', 'drivers', 'routes', 'vehicles', 'filters']
    });
}

function confirmDelete(schedule) {
    if (confirm(`Are you sure you want to delete this schedule?`)) {
        router.delete(route('schedules.destroy', schedule.id), {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                // Maintain current filters after deletion
                const filters = buildFilters();
                router.get(route('schedules.index'), filters, {
                    preserveState: true,
                    preserveScroll: true,
                    replace: true,
                    only: ['schedules', 'drivers', 'routes', 'vehicles', 'filters']
                });
            }
        });
    }
}

function formatTime(time) {
    if (!time) return 'N/A';
    return time;
}

function getDayName(day) {
    const days = {
        monday: 'Monday',
        tuesday: 'Tuesday',
        wednesday: 'Wednesday',
        thursday: 'Thursday',
        friday: 'Friday',
        saturday: 'Saturday',
        sunday: 'Sunday'
    };
    return days[day] || day;
}

function getSchedulesForVehicleAndDay(vehicleId, day) {
    return props.schedules.data.filter(schedule => 
        schedule.vehicle?.id === vehicleId && 
        schedule.day_of_week === day &&
        schedule.is_active
    );
}

function getScheduleColorClass(schedule) {
    const colors = [
        'bg-gradient-to-r from-primary-500 to-primary-600',
        'bg-gradient-to-r from-blue-500 to-blue-600',
        'bg-gradient-to-r from-green-500 to-green-600',
        'bg-gradient-to-r from-purple-500 to-purple-600',
        'bg-gradient-to-r from-secondary-500 to-secondary-600',
    ];
    return colors[schedule.id % colors.length];
}

function viewSchedule(schedule) {
    router.visit(route('schedules.show', schedule.id));
}
</script>