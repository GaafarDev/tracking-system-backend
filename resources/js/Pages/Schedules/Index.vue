<template>
    <AppLayout title="Schedules">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Schedules
                </h2>
                <Link :href="route('schedules.create')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 active:bg-blue-600 transition">
                    Add Schedule
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
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
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    schedules: Object,
    drivers: Array,
    routes: Array,
    vehicles: Array,
    filters: Object
});

// Initialize filters with proper defaults - Fix the active filter initialization
const search = ref(props.filters?.search || '');
const filterDriverId = ref(props.filters?.driver_id || '');
const filterRouteId = ref(props.filters?.route_id || '');
const filterDayOfWeek = ref(props.filters?.day_of_week || '');
const filterActive = ref(props.filters?.is_active !== undefined ? String(props.filters.is_active) : '');

// Debounce timer for search
let searchTimeout;

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
</script>