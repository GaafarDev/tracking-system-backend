<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    vendors: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const filterStatus = ref(props.filters?.status || 'all');

// Watch for filter changes
let searchTimeout;
watch([search, filterStatus], ([newSearch, newStatus]) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('vendors.index'), {
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
            return 'bg-green-100 text-green-800';
        case 'inactive':
            return 'bg-gray-100 text-gray-800';
        case 'suspended':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

function confirmDelete(vendor) {
    if (confirm(`Are you sure you want to delete vendor "${vendor.name}"?`)) {
        router.delete(route('vendors.destroy', vendor.id));
    }
}
</script>

<template>
    <AppLayout title="Vendors">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Vendors
                </h2>
                <Link :href="route('vendors.create')" class="btn-primary">
                    Add Vendor
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
                                placeholder="Search vendors..." 
                                class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>
                        <div class="w-full md:w-48">
                            <select 
                                v-model="filterStatus"
                                class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="all">All Statuses</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="suspended">Suspended</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Vendors Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stats</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="vendor in props.vendors.data" :key="vendor.id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ vendor.name }}</div>
                                            <div class="text-sm text-gray-500">{{ vendor.company_registration_number }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ vendor.contact_person }}</div>
                                        <div class="text-sm text-gray-500">{{ vendor.email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ vendor.drivers_count || 0 }} drivers, {{ vendor.vehicles_count || 0 }} vehicles
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusColor(vendor.status)]">
                                            {{ vendor.status.charAt(0).toUpperCase() + vendor.status.slice(1) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Link :href="route('vendors.show', vendor.id)" class="btn-action-view">
                                            View
                                        </Link>
                                        <Link :href="route('vendors.edit', vendor.id)" class="btn-action-edit">
                                            Edit
                                        </Link>
                                        <button @click="confirmDelete(vendor)" class="btn-action-delete">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                                
                                <tr v-if="props.vendors.data.length === 0">
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        No vendors found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-6 flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing {{ props.vendors.from || 0 }} to {{ props.vendors.to || 0 }} of {{ props.vendors.total || 0 }} vendors
                        </div>
                        
                        <div class="flex-1 flex justify-end">
                            <Link 
                                v-if="props.vendors.prev_page_url" 
                                :href="props.vendors.prev_page_url" 
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                Previous
                            </Link>
                            <Link 
                                v-if="props.vendors.next_page_url" 
                                :href="props.vendors.next_page_url" 
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