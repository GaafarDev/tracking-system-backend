<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DashboardCard from '@/Components/DashboardCard.vue';
import { PlusIcon, BuildingOfficeIcon, UserGroupIcon, TruckIcon, MapIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    vendors: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');

// Watch for changes in filters and update URL
let searchTimeout;
watch([search], ([newSearch]) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('vendors.index'), {
            search: newSearch || undefined,
        }, {
            preserveState: true,
            replace: true,
        });
    }, 300);
});

function clearFilters() {
    search.value = '';
    
    router.get(route('vendors.index'), {}, {
        preserveState: true,
        replace: true,
    });
}

function confirmDelete(vendor) {
    if (confirm(`Are you sure you want to delete vendor "${vendor.name}"?`)) {
        router.delete(route('vendors.destroy', vendor.id));
    }
}

function getStatusBadgeClass(status) {
    switch (status) {
        case 'active':
            return 'bg-gradient-to-r from-green-100 to-green-200 text-green-800 border border-green-300 shadow-sm';
        case 'inactive':
            return 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 border border-gray-300 shadow-sm';
        case 'suspended':
            return 'bg-gradient-to-r from-red-100 to-red-200 text-red-800 border border-red-300 shadow-sm';
        default:
            return 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 border border-gray-300 shadow-sm';
    }
}

// Calculate stats
const stats = {
    total: props.vendors.total || 0,
    totalDrivers: props.vendors.data.reduce((sum, v) => sum + (v.drivers_count || 0), 0),
    totalVehicles: props.vendors.data.reduce((sum, v) => sum + (v.vehicles_count || 0), 0),
    avgPerVendor: props.vendors.data.length > 0 ? 
        Math.round((props.vendors.data.reduce((sum, v) => sum + (v.drivers_count || 0) + (v.vehicles_count || 0), 0)) / props.vendors.data.length) : 0,
};
</script>

<template>
    <AppLayout title="Vendors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gradient-primary">Vendor Management</h1>
                        <p class="text-gray-600 mt-2">Manage transportation service providers and their resources</p>
                    </div>
                    <div class="mt-4 sm:mt-0">
                        <Link :href="route('vendors.create')" class="btn-primary">
                            <PlusIcon class="w-4 h-4 mr-2" />
                            Add Vendor
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Enhanced Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <DashboardCard
                    title="Total Vendors"
                    :value="stats.total"
                    :icon="BuildingOfficeIcon"
                    icon-color="blue"
                    :animation-delay="0"
                    subtitle="Service providers"
                />
                <DashboardCard
                    title="Total Drivers"
                    :value="stats.totalDrivers"
                    :icon="UserGroupIcon"
                    icon-color="green"
                    :animation-delay="100"
                    subtitle="Across all vendors"
                />
                <DashboardCard
                    title="Total Vehicles"
                    :value="stats.totalVehicles"
                    :icon="TruckIcon"
                    icon-color="orange"
                    :animation-delay="200"
                    subtitle="Fleet capacity"
                />
                <DashboardCard
                    title="Avg Resources"
                    :value="stats.avgPerVendor"
                    :icon="MapIcon"
                    icon-color="purple"
                    :animation-delay="300"
                    subtitle="Per vendor"
                />
            </div>

            <!-- Main Content Card -->
            <div class="card-modern">
                <!-- Enhanced Header -->
                <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-indigo-50">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Vendor Directory</h3>
                            <p class="text-sm text-gray-600">Complete overview of all transportation service providers</p>
                        </div>
                        <div class="flex items-center space-x-4 mt-4 sm:mt-0">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                <span class="text-sm text-gray-600">Active</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-gray-500 rounded-full"></div>
                                <span class="text-sm text-gray-600">Inactive</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                <span class="text-sm text-gray-600">Suspended</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Filters -->
                <div class="px-6 py-5 bg-gradient-to-r from-gray-50 to-slate-50 border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input
                                    v-model="search"
                                    type="text"
                                    placeholder="Search vendors by name, contact person, email..."
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white"
                                />
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button 
                                @click="clearFilters"
                                class="px-4 py-2 bg-gradient-to-r from-gray-500 to-gray-600 text-white rounded-xl shadow-sm hover:from-gray-600 hover:to-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200 font-medium"
                            >
                                Clear All
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Table -->
                <div class="overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Vendor</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Contact Info</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Resources</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="vendor in props.vendors.data" :key="vendor.id" 
                                    class="hover:bg-gradient-to-r hover:from-purple-50 hover:to-indigo-50 transition-all duration-200 group">
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-12 w-12 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-full flex items-center justify-center shadow-sm group-hover:shadow-md transition-shadow duration-200">
                                                <BuildingOfficeIcon class="h-6 w-6 text-purple-600" />
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-semibold text-gray-900">{{ vendor.name }}</div>
                                                <div class="text-sm text-gray-500">{{ vendor.company_registration_number }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ vendor.contact_person }}</div>
                                        <div class="text-sm text-gray-500">{{ vendor.email }}</div>
                                        <div class="text-sm text-gray-500">{{ vendor.phone_number }}</div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="flex space-x-4">
                                            <div class="flex items-center text-sm text-gray-900">
                                                <UserGroupIcon class="h-4 w-4 text-gray-400 mr-1" />
                                                {{ vendor.drivers_count || 0 }} drivers
                                            </div>
                                            <div class="flex items-center text-sm text-gray-900">
                                                <TruckIcon class="h-4 w-4 text-gray-400 mr-1" />
                                                {{ vendor.vehicles_count || 0 }} vehicles
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <span :class="getStatusBadgeClass(vendor.status)" class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-semibold shadow-sm">
                                            <div class="w-2 h-2 rounded-full mr-2" 
                                                 :class="{
                                                     'bg-green-500': vendor.status === 'active',
                                                     'bg-red-500': vendor.status === 'suspended',
                                                     'bg-gray-500': vendor.status === 'inactive'
                                                 }">
                                            </div>
                                            {{ vendor.status.charAt(0).toUpperCase() + vendor.status.slice(1) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <Link :href="route('vendors.show', vendor.id)" 
                                                  class="inline-flex items-center px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-200 text-xs font-medium">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 616 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                View
                                            </Link>
                                            <Link :href="route('vendors.edit', vendor.id)" 
                                                  class="inline-flex items-center px-3 py-2 bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 transition-colors duration-200 text-xs font-medium">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Edit
                                            </Link>
                                            <button @click="confirmDelete(vendor)" 
                                                    class="inline-flex items-center px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors duration-200 text-xs font-medium">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Enhanced Empty state -->
                                <tr v-if="props.vendors.data.length === 0">
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-20 h-20 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-full flex items-center justify-center mb-4">
                                                <BuildingOfficeIcon class="h-10 w-10 text-purple-500" />
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900 mb-2">No vendors found</h3>
                                            <p class="text-gray-500 mb-4 max-w-sm">There are currently no vendors matching your search criteria.</p>
                                            <button @click="clearFilters" class="btn-primary">
                                                Clear Filters
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Enhanced Pagination -->
                <div v-if="props.vendors.data.length > 0" class="px-6 py-4 border-t border-gray-200 bg-gradient-to-r from-gray-50 to-slate-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center text-sm text-gray-700">
                            <span class="font-medium">
                                Showing {{ props.vendors.from || 0 }} to {{ props.vendors.to || 0 }} 
                            </span>
                            <span class="mx-2">of</span>
                            <span class="font-medium">{{ props.vendors.total || 0 }} vendors</span>
                        </div>
                        <div class="flex space-x-2">
                            <Link 
                                v-if="props.vendors.prev_page_url" 
                                :href="props.vendors.prev_page_url" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm">
                                ← Previous
                            </Link>
                            <Link 
                                v-if="props.vendors.next_page_url" 
                                :href="props.vendors.next_page_url" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm">
                                Next →
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>