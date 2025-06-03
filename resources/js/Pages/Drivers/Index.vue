<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DashboardCard from '@/Components/DashboardCard.vue';
import { PlusIcon, UserGroupIcon, CheckCircleIcon, ClockIcon, XCircleIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    drivers: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const filterStatus = ref(props.filters.status || 'all');

// Watch for changes in filters and update URL
let searchTimeout;
watch([search, filterStatus], ([newSearch, newStatus]) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('drivers.index'), {
            search: newSearch || undefined,
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
            return 'bg-gradient-to-r from-green-100 to-green-200 text-green-800 border border-green-300 shadow-sm';
        case 'inactive':
            return 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 border border-gray-300 shadow-sm';
        case 'on_leave':
            return 'bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-800 border border-yellow-300 shadow-sm';
        default:
            return 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 border border-gray-300 shadow-sm';
    }
}

function clearFilters() {
    search.value = '';
    filterStatus.value = 'all';
    
    router.get(route('drivers.index'), {}, {
        preserveState: true,
        replace: true,
    });
}

function confirmDelete(driver) {
    if (confirm(`Are you sure you want to delete driver "${driver.user.name}"?`)) {
        router.delete(route('drivers.destroy', driver.id));
    }
}

// Calculate stats
const stats = {
    total: props.drivers.total || 0,
    active: props.drivers.data.filter(d => d.status === 'active').length,
    inactive: props.drivers.data.filter(d => d.status === 'inactive').length,
    onLeave: props.drivers.data.filter(d => d.status === 'on_leave').length,
};
</script>

<template>
    <AppLayout title="Drivers">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gradient-primary">Driver Management</h1>
                        <p class="text-gray-600 mt-2">Manage and monitor all registered drivers in your fleet</p>
                    </div>
                    <div class="mt-4 sm:mt-0">
                        <Link :href="route('drivers.create')" class="btn-primary">
                            <PlusIcon class="w-4 h-4 mr-2" />
                            Add Driver
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Enhanced Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <DashboardCard
                    title="Total Drivers"
                    :value="stats.total"
                    :icon="UserGroupIcon"
                    icon-color="blue"
                    :animation-delay="0"
                    subtitle="Registered drivers"
                />
                <DashboardCard
                    title="Active"
                    :value="stats.active"
                    :icon="CheckCircleIcon"
                    icon-color="green"
                    :animation-delay="100"
                    subtitle="Currently available"
                />
                <DashboardCard
                    title="On Leave"
                    :value="stats.onLeave"
                    :icon="ClockIcon"
                    icon-color="orange"
                    :animation-delay="200"
                    subtitle="Temporarily unavailable"
                />
                <DashboardCard
                    title="Inactive"
                    :value="stats.inactive"
                    :icon="XCircleIcon"
                    icon-color="gray"
                    :animation-delay="300"
                    subtitle="Not in service"
                />
            </div>

            <!-- Main Content Card -->
            <div class="card-modern">
                <!-- Enhanced Header -->
                <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Driver Directory</h3>
                            <p class="text-sm text-gray-600">Complete overview of all drivers and their current status</p>
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
                                    placeholder="Search drivers by name, email, license..."
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white"
                                />
                            </div>
                        </div>
                        <div>
                            <select 
                                v-model="filterStatus"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white">
                                <option value="all">All Statuses</option>
                                <option value="active">✅ Active</option>
                                <option value="inactive">❌ Inactive</option>
                                <option value="on_leave">🏖️ On Leave</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button 
                            @click="clearFilters"
                            class="px-4 py-2 bg-gradient-to-r from-gray-500 to-gray-600 text-white rounded-xl shadow-sm hover:from-gray-600 hover:to-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200 font-medium"
                        >
                            Clear All
                        </button>
                    </div>
                </div>

                <!-- Enhanced Table -->
                <div class="overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Driver</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Contact</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">License</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="driver in drivers.data" :key="driver.id" 
                                    class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-200 group">
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-12 w-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center shadow-sm group-hover:shadow-md transition-shadow duration-200">
                                                <span class="text-white font-semibold text-sm">{{ driver.user.name.charAt(0) }}</span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-semibold text-gray-900">{{ driver.user.name }}</div>
                                                <div class="text-sm text-gray-500">{{ driver.user.email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ driver.phone_number }}</div>
                                        <div class="text-sm text-gray-500">{{ driver.address || 'No address' }}</div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ driver.license_number }}</div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <span :class="getStatusColor(driver.status)" class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-semibold shadow-sm">
                                            <div class="w-2 h-2 rounded-full mr-2" 
                                                 :class="{
                                                     'bg-green-500': driver.status === 'active',
                                                     'bg-gray-500': driver.status === 'inactive',
                                                     'bg-yellow-500': driver.status === 'on_leave'
                                                 }">
                                            </div>
                                            {{ driver.status.charAt(0).toUpperCase() + driver.status.slice(1).replace('_', ' ') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <Link :href="route('drivers.show', driver.id)" 
                                                  class="inline-flex items-center px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-200 text-xs font-medium">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                View
                                            </Link>
                                            <Link :href="route('drivers.edit', driver.id)" 
                                                  class="inline-flex items-center px-3 py-2 bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 transition-colors duration-200 text-xs font-medium">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Edit
                                            </Link>
                                            <button @click="confirmDelete(driver)" 
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
                                <tr v-if="drivers.data.length === 0">
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-full flex items-center justify-center mb-4">
                                                <UserGroupIcon class="h-10 w-10 text-blue-500" />
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900 mb-2">No drivers found</h3>
                                            <p class="text-gray-500 mb-4 max-w-sm">There are currently no drivers matching your search criteria.</p>
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
                <div v-if="drivers.data.length > 0" class="px-6 py-4 border-t border-gray-200 bg-gradient-to-r from-gray-50 to-slate-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center text-sm text-gray-700">
                            <span class="font-medium">
                                Showing {{ drivers.from || 0 }} to {{ drivers.to || 0 }} 
                            </span>
                            <span class="mx-2">of</span>
                            <span class="font-medium">{{ drivers.total || 0 }} drivers</span>
                        </div>
                        <div class="flex space-x-2">
                            <Link 
                                v-if="drivers.prev_page_url" 
                                :href="drivers.prev_page_url" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm">
                                ← Previous
                            </Link>
                            <Link 
                                v-if="drivers.next_page_url" 
                                :href="drivers.next_page_url" 
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