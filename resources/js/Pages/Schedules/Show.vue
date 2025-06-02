<template>
    <AppLayout title="Schedule Details">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Schedule #{{ schedule.id }}
                </h2>
                <Link :href="route('schedules.edit', schedule.id)" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    Edit Schedule
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Schedule Details -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Schedule Information</h3>
                        
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-500">Status</span>
                                <span :class="getStatusBadgeClass(schedule.is_active)" class="px-2 py-1 text-xs font-semibold rounded-full">
                                    {{ schedule.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Day of Week</span>
                                <span class="text-sm text-gray-900 font-medium">{{ getDayName(schedule.day_of_week) }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Departure Time</span>
                                <span class="text-sm text-gray-900">{{ schedule.departure_time }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Arrival Time</span>
                                <span class="text-sm text-gray-900">{{ schedule.arrival_time }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Duration</span>
                                <span class="text-sm text-gray-900">{{ calculateDuration(schedule.departure_time, schedule.arrival_time) }}</span>
                            </div>
                            
                            <div class="pt-4 border-t">
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Created</span>
                                    <span class="text-sm text-gray-900">{{ formatDateTime(schedule.created_at) }}</span>
                                </div>
                                <div class="flex justify-between mt-2">
                                    <span class="text-sm font-medium text-gray-500">Last Updated</span>
                                    <span class="text-sm text-gray-900">{{ formatDateTime(schedule.updated_at) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Driver Information -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Assigned Driver</h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12 bg-gray-200 rounded-full flex items-center justify-center">
                                    <span class="text-gray-600 font-medium text-lg">
                                        {{ (schedule.driver?.user?.name || 'U').charAt(0).toUpperCase() }}
                                    </span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-lg font-medium text-gray-900">
                                        {{ schedule.driver?.user?.name || 'Unknown Driver' }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ schedule.driver?.user?.email || 'No email' }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">License Number</span>
                                    <span class="text-sm text-gray-900">{{ schedule.driver?.license_number || 'N/A' }}</span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Phone Number</span>
                                    <span class="text-sm text-gray-900">{{ schedule.driver?.phone_number || 'N/A' }}</span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Driver Status</span>
                                    <span :class="getDriverStatusClass(schedule.driver?.status)" class="text-sm px-2 py-1 rounded-full">
                                        {{ schedule.driver?.status?.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) || 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vehicle Information -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Assigned Vehicle</h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <div class="text-lg font-medium text-gray-900">
                                        {{ schedule.vehicle?.plate_number || 'Unknown Vehicle' }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ schedule.vehicle?.model || 'No model info' }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Vehicle Type</span>
                                    <span class="text-sm text-gray-900 capitalize">{{ schedule.vehicle?.type || 'N/A' }}</span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Capacity</span>
                                    <span class="text-sm text-gray-900">{{ schedule.vehicle?.capacity || 'N/A' }} seats</span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Vehicle Status</span>
                                    <span :class="getVehicleStatusClass(schedule.vehicle?.status)" class="text-sm px-2 py-1 rounded-full">
                                        {{ schedule.vehicle?.status?.replace(/\b\w/g, l => l.toUpperCase()) || 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Route Information -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Route Details</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <h4 class="text-lg font-medium text-gray-900">{{ schedule.route?.name || 'Unknown Route' }}</h4>
                                <p class="text-sm text-gray-500">{{ schedule.route?.description || 'No description available' }}</p>
                            </div>
                            
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Distance</span>
                                    <span class="text-sm text-gray-900">{{ schedule.route?.distance_km ? schedule.route.distance_km + ' km' : 'N/A' }}</span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Estimated Duration</span>
                                    <span class="text-sm text-gray-900">{{ formatRouteDuration(schedule.route?.estimated_duration_minutes) }}</span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Number of Stops</span>
                                    <span class="text-sm text-gray-900">{{ getStopCount(schedule.route?.stops) }} stops</span>
                                </div>
                            </div>
                            
                            <!-- Route Stops (if available) -->
                            <div v-if="schedule.route?.stops && schedule.route.stops.length > 0" class="pt-4 border-t">
                                <h5 class="text-sm font-medium text-gray-700 mb-2">Route Stops</h5>
                                <div class="space-y-1 max-h-32 overflow-y-auto">
                                    <div v-for="(stop, index) in schedule.route.stops" :key="index" class="text-xs text-gray-600 flex items-center">
                                        <span class="inline-block w-4 h-4 bg-blue-100 text-blue-600 rounded-full text-center mr-2 text-xs leading-4">{{ index + 1 }}</span>
                                        {{ stop.name || `Stop ${index + 1}` }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Route Map -->
                <div class="card-modern p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Route Map</h3>
                    <div id="route-detail-map" class="w-full h-96 rounded-xl border border-gray-200"></div>
                </div>

                <!-- Assigned Schedules -->
                <div v-if="routeData.schedules && routeData.schedules.length > 0" class="mt-6">
                    <div class="card-modern p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Schedule Information</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Time</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Duration</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr class="hover:bg-primary-50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ schedule.departure_time }} - {{ schedule.arrival_time }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ formatDuration(schedule.departure_time, schedule.arrival_time) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="getStatusBadgeClass(schedule.is_active)" class="px-3 py-1 text-xs font-semibold rounded-xl">
                                                {{ schedule.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 flex space-x-4">
                    <Link :href="route('schedules.index')" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        ← Back to Schedules
                    </Link>
                    
                    <Link :href="route('schedules.edit', schedule.id)" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        Edit Schedule
                    </Link>
                    
                    <button @click="toggleStatus" :class="schedule.is_active ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700'" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest">
                        {{ schedule.is_active ? 'Deactivate' : 'Activate' }} Schedule
                    </button>
                    
                    <button @click="confirmDelete" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                        Delete Schedule
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    schedule: Object,
});

function formatDateTime(dateString) {
    return new Date(dateString).toLocaleString();
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
        
        // Handle overnight schedules
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

function formatRouteDuration(minutes) {
    if (!minutes) return 'N/A';
    
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;
    
    if (hours > 0) {
        return `${hours}h ${mins > 0 ? mins + 'm' : ''}`;
    } else {
        return `${mins}m`;
    }
}

function getStopCount(stops) {
    if (!stops) return 0;
    return Array.isArray(stops) ? stops.length : 0;
}

function getStatusBadgeClass(isActive) {
    return isActive ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
}

function getDriverStatusClass(status) {
    const classMap = {
        'active': 'bg-green-100 text-green-800',
        'inactive': 'bg-gray-100 text-gray-800',
        'on_leave': 'bg-yellow-100 text-yellow-800'
    };
    return classMap[status] || 'bg-gray-100 text-gray-800';
}

function getVehicleStatusClass(status) {
    const classMap = {
        'active': 'bg-green-100 text-green-800',
        'maintenance': 'bg-yellow-100 text-yellow-800',
        'inactive': 'bg-gray-100 text-gray-800'
    };
    return classMap[status] || 'bg-gray-100 text-gray-800';
}

function toggleStatus() {
    router.put(route('schedules.update', props.schedule.id), {
        is_active: !props.schedule.is_active
    }, {
        preserveScroll: true
    });
}

function confirmDelete() {
    if (confirm(`Are you sure you want to delete schedule #${props.schedule.id}? This action cannot be undone.`)) {
        router.delete(route('schedules.destroy', props.schedule.id));
    }
}
</script>