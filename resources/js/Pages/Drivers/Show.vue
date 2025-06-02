<template>
    <AppLayout title="Driver Details">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Driver Details - {{ driver.user?.name }}
                </h2>
                <Link :href="route('drivers.edit', driver.id)" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    Edit Driver
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Personal Information -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-16 w-16 bg-gray-200 rounded-full flex items-center justify-center">
                                    <span class="text-gray-600 font-medium text-2xl">
                                        {{ (driver.user?.name || 'U').charAt(0).toUpperCase() }}
                                    </span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-xl font-medium text-gray-900">
                                        {{ driver.user?.name || 'Unknown Driver' }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ driver.user?.email || 'No email' }}
                                    </div>
                                    <span :class="getStatusBadgeClass(driver.status)" class="mt-1 px-2 py-1 text-xs font-semibold rounded-full">
                                        {{ driver.status?.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="space-y-3 pt-4">
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">License Number</span>
                                    <span class="text-sm text-gray-900">{{ driver.license_number || 'N/A' }}</span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Phone Number</span>
                                    <span class="text-sm text-gray-900">{{ driver.phone_number || 'N/A' }}</span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Status</span>
                                    <span class="text-sm text-gray-900 capitalize">{{ driver.status || 'N/A' }}</span>
                                </div>
                                
                                <div class="pt-2 border-t">
                                    <span class="text-sm font-medium text-gray-500">Address</span>
                                    <p class="text-sm text-gray-900 mt-1">{{ driver.address || 'No address provided' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Account Information -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Account Information</h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">User ID</span>
                                <span class="text-sm text-gray-900">#{{ driver.user?.id || 'N/A' }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Email Verified</span>
                                <span class="text-sm text-gray-900">
                                    {{ driver.user?.email_verified_at ? 'Yes' : 'No' }}
                                </span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Account Created</span>
                                <span class="text-sm text-gray-900">{{ formatDateTime(driver.created_at) }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Last Updated</span>
                                <span class="text-sm text-gray-900">{{ formatDateTime(driver.updated_at) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Statistics</h3>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">{{ driver.schedules_count || 0 }}</div>
                                <div class="text-sm text-blue-600">Schedules</div>
                            </div>
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">{{ driver.incidents_count || 0 }}</div>
                                <div class="text-sm text-green-600">Incidents</div>
                            </div>
                            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                                <div class="text-2xl font-bold text-yellow-600">{{ driver.sos_alerts_count || 0 }}</div>
                                <div class="text-sm text-yellow-600">SOS Alerts</div>
                            </div>
                            <div class="text-center p-4 bg-purple-50 rounded-lg">
                                <div class="text-2xl font-bold text-purple-600">{{ driver.locations_count || 0 }}</div>
                                <div class="text-sm text-purple-600">Locations</div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h3>
                        
                        <div class="space-y-3">
                            <div v-if="driver.recent_locations && driver.recent_locations.length > 0">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Latest Location</h4>
                                <div class="text-sm text-gray-600">
                                    Last seen: {{ formatDateTime(driver.recent_locations[0].recorded_at) }}
                                </div>
                            </div>
                            
                            <div v-if="driver.recent_incidents && driver.recent_incidents.length > 0">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Recent Incidents</h4>
                                <div class="space-y-1">
                                    <div v-for="incident in driver.recent_incidents.slice(0, 3)" :key="incident.id" class="text-sm text-gray-600">
                                        {{ formatIncidentType(incident.type) }} - {{ formatDateTime(incident.created_at) }}
                                    </div>
                                </div>
                            </div>
                            
                            <div v-if="!driver.recent_locations?.length && !driver.recent_incidents?.length" class="text-sm text-gray-500 text-center py-4">
                                No recent activity
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 flex space-x-4">
                    <Link :href="route('drivers.index')" class="btn-secondary-modern">
                        ← Back to Drivers
                    </Link>
                    
                    <Link :href="route('drivers.edit', driver.id)" class="btn-modern">
                        Edit Driver
                    </Link>
                    
                    <button @click="confirmDelete" class="btn-danger">
                        Delete Driver
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
    driver: Object,
});

function formatDateTime(dateString) {
    return new Date(dateString).toLocaleString();
}

function getStatusBadgeClass(status) {
    const classMap = {
        'active': 'bg-green-100 text-green-800',
        'inactive': 'bg-gray-100 text-gray-800',
        'on_leave': 'bg-yellow-100 text-yellow-800'
    };
    return classMap[status] || 'bg-gray-100 text-gray-800';
}

function formatIncidentType(type) {
    const typeMap = {
        'accident': 'Accident',
        'breakdown': 'Vehicle Breakdown',
        'road_obstruction': 'Road Obstruction',
        'weather': 'Weather Issue',
        'other': 'Other'
    };
    return typeMap[type] || type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
}

function confirmDelete() {
    if (confirm(`Are you sure you want to delete driver "${props.driver.user?.name}"? This action cannot be undone.`)) {
        router.delete(route('drivers.destroy', props.driver.id));
    }
}
</script>