<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    vehicles: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const filterType = ref(props.filters.type || 'all');
const filterStatus = ref(props.filters.status || 'all');

// Watch for changes in filters and update URL
let searchTimeout;
watch([search, filterType, filterStatus], ([newSearch, newType, newStatus]) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('vehicles.index'), {
            search: newSearch || undefined,
            type: newType !== 'all' ? newType : undefined,
            status: newStatus !== 'all' ? newStatus : undefined,
        }, {
            preserveState: true,
            replace: true,
        });
    }, 300);
});

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

function confirmDelete(vehicle) {
    if (confirm(`Are you sure you want to delete vehicle "${vehicle.plate_number}"?`)) {
        router.delete(route('vehicles.destroy', vehicle.id));
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
                                placeholder="Search vehicles by plate number, model..." 
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
                    
                    <!-- Results summary -->
                    <div class="mb-4">
                        <p class="text-sm text-gray-600">
                            Showing {{ props.vehicles.data.length }} of {{ props.vehicles.total }} vehicles
                        </p>
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
                                <tr v-for="vehicle in props.vehicles.data" :key="vehicle.id" class="hover:bg-gray-50">
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
                                        <div class="flex justify-end space-x-2">
                                            <Link :href="route('vehicles.show', vehicle.id)" class="text-blue-600 hover:text-blue-900 px-2 py-1 rounded">
                                                View
                                            </Link>
                                            <Link :href="route('vehicles.edit', vehicle.id)" class="text-indigo-600 hover:text-indigo-900 px-2 py-1 rounded">
                                                Edit
                                            </Link>
                                            <button @click="confirmDelete(vehicle)" class="text-red-600 hover:text-red-900 px-2 py-1 rounded">
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Empty state -->
                                <tr v-if="props.vehicles.data.length === 0">
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="h-12 w-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                            </svg>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">No vehicles found</h3>
                                            <p class="text-gray-500">Try adjusting your search or filter criteria.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-6 flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing {{ props.vehicles.from || 0 }} to {{ props.vehicles.to || 0 }} of {{ props.vehicles.total || 0 }} vehicles
                        </div>
                        
                        <div class="flex-1 flex justify-end space-x-2">
                            <Link 
                                v-if="props.vehicles.prev_page_url" 
                                :href="props.vehicles.prev_page_url" 
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition duration-150 ease-in-out"
                            >
                                Previous
                            </Link>
                            <Link 
                                v-if="props.vehicles.next_page_url" 
                                :href="props.vehicles.next_page_url" 
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition duration-150 ease-in-out"
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