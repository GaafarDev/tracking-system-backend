<template>
    <AppLayout title="Vehicle Details">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Vehicle Details - {{ vehicle.plate_number }}
                </h2>
                <Link :href="route('vehicles.edit', vehicle.id)" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    Edit Vehicle
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Vehicle Information -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Vehicle Information</h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-16 w-16 bg-gray-200 rounded-full flex items-center justify-center text-2xl">
                                    {{ getVehicleTypeIcon(vehicle.type) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-xl font-medium text-gray-900">
                                        {{ vehicle.plate_number }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ vehicle.model }}
                                    </div>
                                    <span :class="getStatusBadgeClass(vehicle.status)" class="mt-1 px-2 py-1 text-xs font-semibold rounded-full">
                                        {{ vehicle.status?.replace(/\b\w/g, l => l.toUpperCase()) }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="space-y-3 pt-4">
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Vehicle Type</span>
                                    <span class="text-sm text-gray-900 capitalize">{{ vehicle.type || 'N/A' }}</span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Capacity</span>
                                    <span class="text-sm text-gray-900">{{ vehicle.capacity || 'N/A' }} seats</span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Year</span>
                                    <span class="text-sm text-gray-900">{{ vehicle.year || 'N/A' }}</span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Color</span>
                                    <span class="text-sm text-gray-900">{{ vehicle.color || 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Technical Details -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Technical Details</h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Vehicle ID</span>
                                <span class="text-sm text-gray-900">#{{ vehicle.id }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Registration Date</span>
                                <span class="text-sm text-gray-900">{{ formatDateTime(vehicle.created_at) }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Last Updated</span>
                                <span class="text-sm text-gray-900">{{ formatDateTime(vehicle.updated_at) }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Fuel Type</span>
                                <span class="text-sm text-gray-900">{{ vehicle.fuel_type || 'N/A' }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Engine Number</span>
                                <span class="text-sm text-gray-900">{{ vehicle.engine_number || 'N/A' }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Chassis Number</span>
                                <span class="text-sm text-gray-900">{{ vehicle.chassis_number || 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Usage Statistics</h3>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">{{ vehicle.schedules_count || 0 }}</div>
                                <div class="text-sm text-blue-600">Schedules</div>
                            </div>
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">{{ vehicle.total_distance || 0 }} km</div>
                                <div class="text-sm text-green-600">Total Distance</div>
                            </div>
                            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                                <div class="text-2xl font-bold text-yellow-600">{{ vehicle.maintenance_count || 0 }}</div>
                                <div class="text-sm text-yellow-600">Maintenance</div>
                            </div>
                            <div class="text-center p-4 bg-purple-50 rounded-lg">
                                <div class="text-2xl font-bold text-purple-600">{{ vehicle.incident_count || 0 }}</div>
                                <div class="text-sm text-purple-600">Incidents</div>
                            </div>
                        </div>
                    </div>

                    <!-- Current Assignment -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Current Assignment</h3>
                        
                        <div v-if="vehicle.current_driver" class="space-y-3">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center">
                                    <span class="text-gray-600 font-medium">
                                        {{ (vehicle.current_driver.user?.name || 'U').charAt(0).toUpperCase() }}
                                    </span>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ vehicle.current_driver.user?.name || 'Unknown Driver' }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ vehicle.current_driver.user?.email }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="pt-3 border-t">
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">License Number</span>
                                    <span class="text-sm text-gray-900">{{ vehicle.current_driver.license_number }}</span>
                                </div>
                                <div class="flex justify-between mt-1">
                                    <span class="text-sm font-medium text-gray-500">Phone</span>
                                    <span class="text-sm text-gray-900">{{ vehicle.current_driver.phone_number }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div v-else class="text-sm text-gray-500 text-center py-4">
                            No driver currently assigned
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 flex space-x-4">
                    <Link :href="route('vehicles.index')" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        ← Back to Vehicles
                    </Link>
                    
                    <Link :href="route('vehicles.edit', vehicle.id)" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        Edit Vehicle
                    </Link>
                    
                    <button @click="confirmDelete" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                        Delete Vehicle
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
    vehicle: Object,
});

function formatDateTime(dateString) {
    return new Date(dateString).toLocaleString();
}

function getVehicleTypeIcon(type) {
    const iconMap = {
        'bus': '🚌',
        'van': '🚐',
        'car': '🚗',
        'boat': '⛵',
        'truck': '🚛'
    };
    return iconMap[type] || '🚗';
}

function getStatusBadgeClass(status) {
    const classMap = {
        'active': 'bg-green-100 text-green-800',
        'maintenance': 'bg-yellow-100 text-yellow-800',
        'inactive': 'bg-gray-100 text-gray-800'
    };
    return classMap[status] || 'bg-gray-100 text-gray-800';
}

function confirmDelete() {
    if (confirm(`Are you sure you want to delete vehicle "${props.vehicle.plate_number}"? This action cannot be undone.`)) {
        router.delete(route('vehicles.destroy', props.vehicle.id));
    }
}
</script>