<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ModernTable from '@/Components/ModernTable.vue';
import { PlusIcon } from '@heroicons/vue/24/outline';

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
            return 'bg-green-100 text-green-800';
        case 'inactive':
            return 'bg-gray-100 text-gray-800';
        case 'on_leave':
            return 'bg-yellow-100 text-yellow-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

function confirmDelete(driver) {
    if (confirm(`Are you sure you want to delete driver "${driver.user.name}"?`)) {
        router.delete(route('drivers.destroy', driver.id));
    }
}
</script>

<template>
    <AppLayout title="Drivers">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Drivers
                </h2>
                <Link :href="route('drivers.create')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 active:bg-blue-600 transition">
                    Add Driver
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <ModernTable
                        title="Drivers"
                        subtitle="Manage and monitor all registered drivers"
                        :data="drivers.data"
                        :columns="[
                            { key: 'user.name', label: 'Name' },
                            { key: 'phone_number', label: 'Contact' },
                            { key: 'license_number', label: 'License' },
                            { key: 'status', label: 'Status' }
                        ]"
                        search-placeholder="Search drivers by name, email, license..."
                        empty-title="No drivers found"
                        empty-message="Try adjusting your search or filter criteria.">
                        
                        <template #header-actions>
                            <Link :href="route('drivers.create')" class="btn-primary">
                                <PlusIcon class="w-4 h-4 mr-2" />
                                Add Driver
                            </Link>
                        </template>

                        <template #filters>
                            <select 
                                v-model="filterStatus"
                                class="px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                                <option value="all">All Statuses</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="on_leave">On Leave</option>
                            </select>
                        </template>

                        <template #cell-user.name="{ item }">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center">
                                    <span class="text-white font-medium">{{ item.user.name.charAt(0) }}</span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ item.user.name }}</div>
                                    <div class="text-sm text-gray-500">{{ item.user.email }}</div>
                                </div>
                            </div>
                        </template>

                        <template #cell-phone_number="{ item }">
                            <div>
                                <div class="text-sm text-gray-900">{{ item.phone_number }}</div>
                                <div class="text-sm text-gray-500">{{ item.address || 'No address' }}</div>
                            </div>
                        </template>

                        <template #cell-status="{ item }">
                            <span :class="['px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusColor(item.status)]">
                                {{ item.status.charAt(0).toUpperCase() + item.status.slice(1).replace('_', ' ') }}
                            </span>
                        </template>

                        <template #actions="{ item }">
                            <div class="flex space-x-2">
                                <Link :href="route('drivers.show', item.id)" class="text-blue-600 hover:text-blue-900 px-2 py-1 rounded">
                                    View
                                </Link>
                                <Link :href="route('drivers.edit', item.id)" class="text-indigo-600 hover:text-indigo-900 px-2 py-1 rounded">
                                    Edit
                                </Link>
                                <button @click="confirmDelete(item)" class="text-red-600 hover:text-red-900 px-2 py-1 rounded">
                                    Delete
                                </button>
                            </div>
                        </template>
                    </ModernTable>
                </div>
            </div>
        </div>
    </AppLayout>
</template>