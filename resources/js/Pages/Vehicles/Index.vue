<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DashboardCard from '@/Components/DashboardCard.vue';
import { PlusIcon, TruckIcon, CheckCircleIcon, WrenchScrewdriverIcon, XCircleIcon } from '@heroicons/vue/24/outline';

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
            return 'bg-green-100 text-green-800 border border-green-200';
        case 'maintenance':
            return 'bg-yellow-100 text-yellow-800 border border-yellow-200';
        case 'inactive':
            return 'bg-gray-100 text-gray-800 border border-gray-200';
        default:
            return 'bg-gray-100 text-gray-800 border border-gray-200';
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

// Calculate stats
const stats = {
    total: props.vehicles.total || 0,
    active: props.vehicles.data.filter(v => v.status === 'active').length,
    maintenance: props.vehicles.data.filter(v => v.status === 'maintenance').length,
    inactive: props.vehicles.data.filter(v => v.status === 'inactive').length,
};
</script>

<template>
    <AppLayout title="Vehicles">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <DashboardCard
                    title="Total Vehicles"
                    :value="stats.total"
                    :icon="TruckIcon"
                    icon-color="text-blue-600"
                    icon-bg-color="bg-blue-100"
                />
                <DashboardCard
                    title="Active"
                    :value="stats.active"
                    :icon="CheckCircleIcon"
                    icon-color="text-green-600"
                    icon-bg-color="bg-green-100"
                />
                <DashboardCard
                    title="Maintenance"
                    :value="stats.maintenance"
                    :icon="WrenchScrewdriverIcon"
                    icon-color="text-yellow-600"
                    icon-bg-color="bg-yellow-100"
                />
                <DashboardCard
                    title="Inactive"
                    :value="stats.inactive"
                    :icon="XCircleIcon"
                    icon-color="text-gray-600"
                    icon-bg-color="bg-gray-100"
                />
            </div>

            <!-- Main Content Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Vehicle Fleet</h3>
                            <p class="text-sm text-gray-600 mt-1">Manage and monitor your vehicle fleet</p>
                        </div>
                        <div class="mt-4 sm:mt-0">
                            <Link :href="route('vehicles.create')" class="btn-primary">
                                <PlusIcon class="w-4 h-4 mr-2" />
                                Add Vehicle
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <input
                                    v-model="search"
                                    type="text"
                                    placeholder="Search vehicles by plate number, model..."
                                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                />
                            </div>
                        </div>
                        <div class="w-full sm:w-48">
                            <select 
                                v-model="filterType"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                                <option value="all">All Types</option>
                                <option value="bus">Bus</option>
                                <option value="van">Van</option>
                                <option value="car">Car</option>
                                <option value="boat">Boat</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="w-full sm:w-48">
                            <select 
                                v-model="filterStatus"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                                <option value="all">All Statuses</option>
                                <option value="active">Active</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Table -->
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
                            <tr v-for="vehicle in props.vehicles.data" :key="vehicle.id" class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-12 bg-gray-100 rounded-full flex items-center justify-center text-2xl">
                                            {{ getVehicleTypeIcon(vehicle.type) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-semibold text-gray-900">{{ vehicle.plate_number }}</div>
                                            <div class="text-sm text-gray-500">{{ vehicle.model }}</div>
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
                                    <span :class="getStatusColor(vehicle.status)" class="inline-flex items-center px-2.5 py-1.5 rounded-full text-xs font-semibold">
                                        {{ vehicle.status.charAt(0).toUpperCase() + vehicle.status.slice(1) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <Link :href="route('vehicles.show', vehicle.id)" class="btn-action-view">
                                            View
                                        </Link>
                                        <Link :href="route('vehicles.edit', vehicle.id)" class="btn-action-edit">
                                            Edit
                                        </Link>
                                        <button @click="confirmDelete(vehicle)" class="btn-action-delete">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Empty state -->
                            <tr v-if="props.vehicles.data.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <TruckIcon class="h-12 w-12 text-gray-300 mb-4" />
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">No vehicles found</h3>
                                        <p class="text-gray-500">Try adjusting your search or filter criteria.</p>
                                        <Link :href="route('vehicles.create')" class="mt-4 btn-primary">
                                            <PlusIcon class="w-4 h-4 mr-2" />
                                            Add Your First Vehicle
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="props.vehicles.data.length > 0" class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing {{ props.vehicles.from || 0 }} to {{ props.vehicles.to || 0 }} of {{ props.vehicles.total || 0 }} vehicles
                        </div>
                        <div class="flex space-x-2">
                            <Link 
                                v-if="props.vehicles.prev_page_url" 
                                :href="props.vehicles.prev_page_url" 
                                class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                                Previous
                            </Link>
                            <Link 
                                v-if="props.vehicles.next_page_url" 
                                :href="props.vehicles.next_page_url" 
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