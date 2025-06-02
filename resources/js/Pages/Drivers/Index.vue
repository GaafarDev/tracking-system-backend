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

// Update status badges function
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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <DashboardCard
                    title="Total Drivers"
                    :value="stats.total"
                    :icon="UserGroupIcon"
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
                    title="On Leave"
                    :value="stats.onLeave"
                    :icon="ClockIcon"
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
                            <h3 class="text-lg font-semibold text-gray-900">Drivers Management</h3>
                            <p class="text-sm text-gray-600 mt-1">Manage and monitor all registered drivers</p>
                        </div>
                        <div class="mt-4 sm:mt-0">
                            <Link :href="route('drivers.create')" class="btn-primary">
                                <PlusIcon class="w-4 h-4 mr-2" />
                                Add Driver
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
                                    placeholder="Search drivers by name, email, license..."
                                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                />
                            </div>
                        </div>
                        <div class="w-full sm:w-48">
                            <select 
                                v-model="filterStatus"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                                <option value="all">All Statuses</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="on_leave">On Leave</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Driver</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">License</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="driver in drivers.data" :key="driver.id" class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center">
                                            <span class="text-white font-semibold text-sm">{{ driver.user.name.charAt(0) }}</span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-semibold text-gray-900">{{ driver.user.name }}</div>
                                            <div class="text-sm text-gray-500">{{ driver.user.email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ driver.phone_number }}</div>
                                    <div class="text-sm text-gray-500">{{ driver.address || 'No address' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ driver.license_number }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="getStatusColor(driver.status)" class="inline-flex items-center px-2.5 py-1.5 rounded-full text-xs font-semibold">
                                        {{ driver.status.charAt(0).toUpperCase() + driver.status.slice(1).replace('_', ' ') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <Link :href="route('drivers.show', driver.id)" class="text-blue-600 hover:text-blue-900 px-3 py-1.5 rounded-lg hover:bg-blue-50 transition-colors duration-150">
                                            View
                                        </Link>
                                        <Link :href="route('drivers.edit', driver.id)" class="text-indigo-600 hover:text-indigo-900 px-3 py-1.5 rounded-lg hover:bg-indigo-50 transition-colors duration-150">
                                            Edit
                                        </Link>
                                        <button @click="confirmDelete(driver)" class="text-red-600 hover:text-red-900 px-3 py-1.5 rounded-lg hover:bg-red-50 transition-colors duration-150">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Empty state -->
                            <tr v-if="drivers.data.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <UserGroupIcon class="h-12 w-12 text-gray-300 mb-4" />
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">No drivers found</h3>
                                        <p class="text-gray-500">Try adjusting your search or filter criteria.</p>
                                        <Link :href="route('drivers.create')" class="mt-4 btn-primary">
                                            <PlusIcon class="w-4 h-4 mr-2" />
                                            Add Your First Driver
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="drivers.data.length > 0" class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing {{ drivers.from || 0 }} to {{ drivers.to || 0 }} of {{ drivers.total || 0 }} drivers
                        </div>
                        <div class="flex space-x-2">
                            <Link 
                                v-if="drivers.prev_page_url" 
                                :href="drivers.prev_page_url" 
                                class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                                Previous
                            </Link>
                            <Link 
                                v-if="drivers.next_page_url" 
                                :href="drivers.next_page_url" 
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