<template>
    <AppLayout title="Schedules">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <!-- Header with View Toggle -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gradient-primary">Schedule Management</h1>
                        <p class="text-gray-600 mt-2">Manage driver schedules and assignments across your fleet</p>
                    </div>
                    <div class="flex items-center space-x-4 mt-4 sm:mt-0">
                        <!-- View Toggle -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-1">
                            <button 
                                @click="viewMode = 'calendar'"
                                :class="[viewMode === 'calendar' ? 'bg-primary-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-900', 'px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200']"
                            >
                                <CalendarIcon class="w-4 h-4 mr-2 inline" />
                                Calendar
                            </button>
                            <button 
                                @click="viewMode = 'list'"
                                :class="[viewMode === 'list' ? 'bg-primary-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-900', 'px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200']"
                            >
                                <ListBulletIcon class="w-4 h-4 mr-2 inline" />
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

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <DashboardCard
                    title="Total Schedules"
                    :value="stats.total"
                    :icon="CalendarIcon"
                    icon-color="blue"
                    :animation-delay="0"
                />
                <DashboardCard
                    title="Active Today"
                    :value="stats.activeToday"
                    :icon="ClockIcon"
                    icon-color="green"
                    :animation-delay="100"
                />
                <DashboardCard
                    title="This Week"
                    :value="stats.thisWeek"
                    :icon="CalendarDaysIcon"
                    icon-color="purple"
                    :animation-delay="200"
                />
                <DashboardCard
                    title="Drivers Scheduled"
                    :value="stats.driversScheduled"
                    :icon="UserGroupIcon"
                    icon-color="teal"
                    :animation-delay="300"
                />
            </div>

            <!-- Calendar View - Enhanced -->
            <div v-if="viewMode === 'calendar'" class="card-modern">
                <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-primary-50 to-secondary-50">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 mb-2">Weekly Schedule Calendar</h2>
                            <p class="text-sm text-gray-600">Visual overview of all scheduled routes and assignments</p>
                        </div>
                        
                        <!-- Week Navigation -->
                        <div class="flex items-center space-x-4 mt-4 sm:mt-0">
                            <button @click="previousWeek" class="p-2 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-200 text-gray-600 hover:text-gray-900">
                                <ChevronLeftIcon class="w-5 h-5" />
                            </button>
                            <div class="text-center">
                                <div class="text-lg font-semibold text-gray-900">Week {{ currentWeek + 1 }}</div>
                                <div class="text-xs text-gray-500">{{ getCurrentWeekDateRange() }}</div>
                            </div>
                            <button @click="nextWeek" class="p-2 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-200 text-gray-600 hover:text-gray-900">
                                <ChevronRightIcon class="w-5 h-5" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Calendar Grid -->
                <div class="p-6">
                    <div class="grid grid-cols-6 gap-4">
                        <!-- Vehicles Column -->
                        <div class="border-r border-gray-200 pr-4">
                            <h3 class="text-sm font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">Vehicles</h3>
                            <div class="space-y-4">
                                <div v-for="vehicle in uniqueVehicles" :key="vehicle.id" class="card-modern p-3 hover-lift">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 bg-blue-100 rounded-lg">
                                            <TruckIcon class="w-4 h-4 text-blue-600" />
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ vehicle.plate_number }}</div>
                                            <div class="text-xs text-gray-500">{{ vehicle.model }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="uniqueVehicles.length === 0" class="text-sm text-gray-400 italic p-4 text-center">
                                    No vehicles scheduled
                                </div>
                            </div>
                        </div>

                        <!-- Week Days -->
                        <div v-for="day in weekDays" :key="day" class="relative">
                            <h3 class="text-sm font-semibold text-gray-900 mb-4 text-center pb-2 border-b border-gray-200">
                                {{ day }}
                                <div class="text-xs text-gray-500 font-normal">{{ getDayDate(day) }}</div>
                            </h3>
                            <div class="space-y-3">
                                <div v-for="vehicle in uniqueVehicles" :key="`${vehicle.id}-${day}`" class="min-h-[80px] relative">
                                    <!-- Schedule Blocks -->
                                    <div 
                                        v-for="(schedule, index) in getSchedulesForVehicleAndDay(vehicle.id, day.toLowerCase())" 
                                        :key="schedule.id"
                                        :class="[getScheduleColorClass(schedule), 'rounded-xl p-3 text-white text-xs font-medium shadow-md hover:shadow-lg transition-all duration-200 cursor-pointer mb-2']"
                                        @click="viewSchedule(schedule)"
                                    >
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-6 h-6 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                                    <span class="text-xs font-bold">{{ schedule.driver?.user?.name?.charAt(0) || 'D' }}</span>
                                                </div>
                                                <span class="font-semibold truncate">{{ schedule.driver?.user?.name || 'Unknown' }}</span>
                                            </div>
                                            <div class="text-xs opacity-75">
                                                {{ schedule.is_active ? '●' : '○' }}
                                            </div>
                                        </div>
                                        <div class="space-y-1">
                                            <div class="text-xs opacity-90">
                                                🕐 {{ schedule.departure_time }} - {{ schedule.arrival_time }}
                                            </div>
                                            <div class="text-xs opacity-75 truncate">
                                                📍 {{ schedule.route?.name || 'No route' }}
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Empty slot indicator -->
                                    <div v-if="getSchedulesForVehicleAndDay(vehicle.id, day.toLowerCase()).length === 0" 
                                         class="h-16 border-2 border-dashed border-gray-200 rounded-xl flex items-center justify-center text-gray-400 text-xs">
                                        No schedule
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Calendar Legend -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            <span class="font-medium">Legend:</span>
                            <span class="ml-2">● Active Schedule</span>
                            <span class="ml-4">○ Inactive Schedule</span>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center space-x-2">
                                <div class="w-4 h-4 bg-gradient-to-r from-primary-500 to-primary-600 rounded"></div>
                                <span class="text-xs text-gray-600">Morning Shift</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-4 h-4 bg-gradient-to-r from-blue-500 to-blue-600 rounded"></div>
                                <span class="text-xs text-gray-600">Afternoon Shift</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-4 h-4 bg-gradient-to-r from-purple-500 to-purple-600 rounded"></div>
                                <span class="text-xs text-gray-600">Evening Shift</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- List View (Enhanced existing table) -->
            <div v-else class="card-modern">
                <!-- Filters -->
                <div class="px-6 py-4 bg-gradient-to-r from-primary-50 to-secondary-50 border-b border-gray-200">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <input 
                                v-model="search" 
                                type="text" 
                                placeholder="Search schedules..." 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200"
                                @input="handleSearchInput"
                            />
                        </div>
                        
                        <!-- Driver Filter -->
                        <div class="w-full md:w-48">
                            <select 
                                v-model="filterDriverId"
                                @change="applyFiltersImmediately"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200"
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
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200"
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
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200"
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
                        
                        <!-- Clear Filters Button -->
                        <div class="w-full md:w-auto">
                            <button 
                                @click="clearFilters"
                                class="btn-secondary-modern w-full"
                            >
                                Clear Filters
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Schedules Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Route</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Driver</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Vehicle</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Day</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Time</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="schedule in props.schedules.data" :key="schedule.id" class="hover:bg-primary-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                            <MapIcon class="w-4 h-4 text-blue-600" />
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ schedule.route?.name || 'Unknown' }}</div>
                                            <div class="text-xs text-gray-500">{{ schedule.route?.description || 'No description' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-white font-medium text-xs">
                                                {{ (schedule.driver?.user?.name || 'U').charAt(0).toUpperCase() }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ schedule.driver?.user?.name || 'Unknown' }}</div>
                                            <div class="text-xs text-gray-500">{{ schedule.driver?.license_number || 'No license' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="p-2 bg-gray-100 rounded-lg mr-3">
                                            <TruckIcon class="w-4 h-4 text-gray-600" />
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ schedule.vehicle?.plate_number || 'Unknown' }}</div>
                                            <div class="text-xs text-gray-500">{{ schedule.vehicle?.model || 'No model' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 capitalize">{{ getDayName(schedule.day_of_week) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <ClockIcon class="w-4 h-4 text-gray-400" />
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ formatTime(schedule.departure_time) }} - {{ formatTime(schedule.arrival_time) }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                Duration: {{ calculateDuration(schedule.departure_time, schedule.arrival_time) }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="['inline-flex items-center px-3 py-1 rounded-xl text-xs font-semibold', schedule.is_active ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-gray-100 text-gray-800 border border-gray-200']">
                                        <div :class="['w-2 h-2 rounded-full mr-2', schedule.is_active ? 'bg-green-500' : 'bg-gray-500']"></div>
                                        {{ schedule.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <Link :href="route('schedules.show', schedule.id)" class="btn-action-view">
                                            View
                                        </Link>
                                        <Link :href="route('schedules.edit', schedule.id)" class="btn-action-edit">
                                            Edit
                                        </Link>
                                        <button @click="confirmDelete(schedule)" class="btn-action-delete">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Empty state -->
                            <tr v-if="props.schedules.data.length === 0">
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <CalendarIcon class="h-12 w-12 text-gray-300 mb-4" />
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">No schedules found</h3>
                                        <p class="text-gray-500 mb-4">Create your first schedule to get started.</p>
                                        <Link :href="route('schedules.create')" class="btn-primary">
                                            <PlusIcon class="w-4 h-4 mr-2" />
                                            Create Schedule
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing {{ props.schedules.from || 0 }} to {{ props.schedules.to || 0 }} of {{ props.schedules.total || 0 }} schedules
                        </div>
                        
                        <div class="flex space-x-2">
                            <Link 
                                v-if="props.schedules.prev_page_url" 
                                :href="props.schedules.prev_page_url" 
                                class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                                Previous
                            </Link>
                            <Link 
                                v-if="props.schedules.next_page_url" 
                                :href="props.schedules.next_page_url" 
                                class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-150">
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
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DashboardCard from '@/Components/DashboardCard.vue';
import { 
    PlusIcon, 
    ChevronLeftIcon, 
    ChevronRightIcon,
    CalendarIcon,
    ListBulletIcon,
    ClockIcon,
    CalendarDaysIcon,
    UserGroupIcon,
    TruckIcon,
    MapIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    schedules: Object,
    drivers: Array,
    routes: Array,
    vehicles: Array,
    filters: Object
});

const viewMode = ref('calendar');
const weekDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
const currentWeek = ref(0);

// Initialize filters
const search = ref(props.filters?.search || '');
const filterDriverId = ref(props.filters?.driver_id || '');
const filterRouteId = ref(props.filters?.route_id || '');
const filterDayOfWeek = ref(props.filters?.day_of_week || '');

let searchTimeout;

// Calculate statistics
const stats = computed(() => {
    const total = props.schedules.total || 0;
    const activeToday = props.schedules.data.filter(s => s.is_active && isToday(s.day_of_week)).length;
    const thisWeek = props.schedules.data.filter(s => s.is_active).length;
    const driversScheduled = new Set(props.schedules.data.map(s => s.driver_id)).size;
    
    return { total, activeToday, thisWeek, driversScheduled };
});

// Add the week filtering
const currentWeekSchedules = computed(() => {
    if (!props.schedules.data) return [];
    
    // For now, return all schedules since we don't have date-based filtering
    // In a real implementation, you'd filter based on currentWeek.value
    return props.schedules.data;
});

const uniqueVehicles = computed(() => {
    const vehicles = new Map();
    currentWeekSchedules.value.forEach(schedule => {
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

function calculateDuration(departureTime, arrivalTime) {
    try {
        const [depHour, depMin] = departureTime.split(':').map(Number);
        const [arrHour, arrMin] = arrivalTime.split(':').map(Number);
        
        let depMinutes = depHour * 60 + depMin;
        let arrMinutes = arrHour * 60 + arrMin;
        
        if (arrMinutes < depMinutes) {
            arrMinutes += 24 * 60;
        }
        
        const durationMinutes = arrMinutes - depMinutes;
        const hours = Math.floor(durationMinutes / 60);
        const minutes = durationMinutes % 60;
        
        if (hours > 0) {
            return `${hours}h ${minutes > 0 ? minutes + 'm' : ''}`;
        } else {
            return `${minutes}m`;
        }
    } catch (error) {
        return 'N/A';
    }
}

// Update the getSchedulesForVehicleAndDay function
function getSchedulesForVehicleAndDay(vehicleId, day) {
    return currentWeekSchedules.value.filter(schedule => 
        schedule.vehicle?.id === vehicleId && 
        schedule.day_of_week === day.toLowerCase() &&
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

// Update week navigation functions
function previousWeek() {
    currentWeek.value = Math.max(0, currentWeek.value - 1);
    // Add API call here to fetch data for the new week
    router.get(route('schedules.index'), { week: currentWeek.value }, { preserveState: true });
}

function nextWeek() {
    currentWeek.value = currentWeek.value + 1;
    // Add API call here to fetch data for the new week
    router.get(route('schedules.index'), { week: currentWeek.value }, { preserveState: true });
}

function getCurrentWeekDateRange() {
    const today = new Date();
    const startOfWeek = new Date(today.setDate(today.getDate() - today.getDay() + 1 + (currentWeek.value * 7)));
    const endOfWeek = new Date(startOfWeek);
    endOfWeek.setDate(startOfWeek.getDate() + 4);
    
    return `${startOfWeek.toLocaleDateString()} - ${endOfWeek.toLocaleDateString()}`;
}

function getDayDate(dayName) {
    const today = new Date();
    const dayIndex = weekDays.indexOf(dayName);
    const startOfWeek = new Date(today.setDate(today.getDate() - today.getDay() + 1 + (currentWeek.value * 7)));
    const dayDate = new Date(startOfWeek);
    dayDate.setDate(startOfWeek.getDate() + dayIndex);
    
    return dayDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
}

function isToday(dayOfWeek) {
    const today = new Date().getDay();
    const dayMap = {
        sunday: 0, monday: 1, tuesday: 2, wednesday: 3, 
        thursday: 4, friday: 5, saturday: 6
    };
    return dayMap[dayOfWeek] === today;
}

function viewSchedule(schedule) {
    router.visit(route('schedules.show', schedule.id));
}
</script>