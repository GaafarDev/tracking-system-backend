<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    vehicles: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const filterType = ref(props.filters.type || 'all');
const filterStatus = ref(props.filters.status || 'all');

function getStatusColor(status) {
    switch (status) {
        case 'active':
            return 'bg-green-100 text-green-800';
        case 'maintenance':
            return 'bg-yellow-100 text-yellow-800';
        case 'inactive':
            return 'bg-gray-100 text-gray-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

function getVehicleTypeIcon(type) {
    switch (type) {
        case 'bus':
            return '🚌';
        case 'van':
            return '🚐';
        case 'car':
            return '🚗';
        case 'boat':
            return '⛵';
        default:
            return '🚗';
    }
}
</script>

<template>
    <AppLayout title="Vehicles">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Vehicles
                </h2>
                <Link :href="route('vehicles.create')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 active:bg-blue-600 transition">
                    Add Vehicle
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <!-- Search and Filters -->
                    <div class="flex flex-col md:flex-row gap-4 mb-6">
                        <div class="flex-1">
                            <input 
                                v-model="search" 
                                type="text" 
                                placeholder="Search vehicles..." 
                                class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                        </div>
                        <div class="w-full md:w-48">
                            <select 
                                v-model="filterType"
                                class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="all">All Types</option>
                                <option value="bus">Bus</option>
                                <option value="van">Van</option>
                                <option value="car">Car</option>
                                <option value="boat">Boat</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="w-full md:w-48">
                            <select 
                                v-model="filterStatus"
                                class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="all">All Statuses</option>
                                <option value="active">Active</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Vehicles Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="vehicle in props.vehicles.data" :key="vehicle.id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center text-xl">
                                                {{ getVehicleTypeIcon(vehicle.type) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ vehicle.plate_number }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ vehicle.model }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 capitalize">{{ vehicle.type }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ vehicle.capacity }} seats</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusColor(vehicle.status)]">
                                            {{ vehicle.status.charAt(0).toUpperCase() + vehicle.status.slice(1) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Link :href="route('vehicles.show', vehicle.id)" class="text-blue-600 hover:text-blue-900 mr-3">
                                            View
                                        </Link>
                                        <Link :href="route('vehicles.edit', vehicle.id)" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                            Edit
                                        </Link>
                                        <button @click="confirmDelete(vehicle)" class="text-red-600 hover:text-red-900">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                                
                                <!-- Empty state -->
                                <tr v-if="props.vehicles.data.length === 0">
                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                        No vehicles found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-6 flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing {{ props.vehicles.from }} to {{ props.vehicles.to }} of {{ props.vehicles.total }} vehicles
                        </div>
                        
                        <div class="flex-1 flex justify-end">
                            <Link 
                                v-if="props.vehicles.prev_page_url" 
                                :href="props.vehicles.prev_page_url" 
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                Previous
                            </Link>
                            <Link 
                                v-if="props.vehicles.next_page_url" 
                                :href="props.vehicles.next_page_url" 
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