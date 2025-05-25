<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    incidents: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const filterType = ref(props.filters.type || '');
const filterStatus = ref(props.filters.status || '');
const dateRange = ref({
    from: props.filters.from_date || '',
    to: props.filters.to_date || ''
});

// Debounce timer for search
let searchTimeout;

function applyFilters() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        const filters = {
            search: search.value,
            type: filterType.value,
            status: filterStatus.value,
            from_date: dateRange.value.from,
            to_date: dateRange.value.to
        };

        // Remove empty filters
        Object.keys(filters).forEach(key => {
            if (!filters[key]) {
                delete filters[key];
            }
        });

        router.get(route('incidents.index'), filters, {
            preserveState: true,
            replace: true,
        });
    }, 300);
}

function clearFilters() {
    search.value = '';
    filterType.value = '';
    filterStatus.value = '';
    dateRange.value = { from: '', to: '' };
    
    router.get(route('incidents.index'), {}, {
        preserveState: true,
        replace: true,
    });
}

function getStatusColor(status) {
    switch (status) {
        case 'reported':
            return 'bg-red-100 text-red-800';
        case 'in_progress':
            return 'bg-yellow-100 text-yellow-800';
        case 'resolved':
            return 'bg-blue-100 text-blue-800';
        case 'closed':
            return 'bg-green-100 text-green-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

function getIncidentTypeIcon(type) {
    switch (type) {
        case 'accident':
            return '🚨';
        case 'breakdown':
            return '🔧';
        case 'road_obstruction':
            return '🚧';
        case 'weather':
            return '🌧️';
        case 'other':
            return '❓';
        default:
            return '❗';
    }
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString();
}

function formatIncidentType(type) {
    return type.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
}
</script>

<template>
    <AppLayout title="Incidents">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Incidents
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Stats Overview -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <!-- Reported Incidents -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 border-red-500">
                        <div class="text-sm font-medium text-gray-500">Reported</div>
                        <div class="mt-1 text-3xl font-semibold text-gray-900">
                            {{ props.incidents.data.filter(i => i.status === 'reported').length }}
                        </div>
                    </div>
                    
                    <!-- In Progress -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 border-yellow-500">
                        <div class="text-sm font-medium text-gray-500">In Progress</div>
                        <div class="mt-1 text-3xl font-semibold text-gray-900">
                            {{ props.incidents.data.filter(i => i.status === 'in_progress').length }}
                        </div>
                    </div>
                    
                    <!-- Resolved -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 border-blue-500">
                        <div class="text-sm font-medium text-gray-500">Resolved</div>
                        <div class="mt-1 text-3xl font-semibold text-gray-900">
                            {{ props.incidents.data.filter(i => i.status === 'resolved').length }}
                        </div>
                    </div>
                    
                    <!-- Closed -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 border-green-500">
                        <div class="text-sm font-medium text-gray-500">Closed</div>
                        <div class="mt-1 text-3xl font-semibold text-gray-900">
                            {{ props.incidents.data.filter(i => i.status === 'closed').length }}
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <!-- Search and Filters -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                        <div>
                            <input 
                                v-model="search" 
                                type="text" 
                                placeholder="Search incidents..." 
                                class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                @input="applyFilters"
                            />
                        </div>
                        <div>
                            <select 
                                v-model="filterType"
                                @change="applyFilters"
                                class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="">All Types</option>
                                <option value="accident">Accident</option>
                                <option value="breakdown">Breakdown</option>
                                <option value="road_obstruction">Road Obstruction</option>
                                <option value="weather">Weather</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <select 
                                v-model="filterStatus"
                                @change="applyFilters"
                                class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="">All Statuses</option>
                                <option value="reported">Reported</option>
                                <option value="in_progress">In Progress</option>
                                <option value="resolved">Resolved</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <input 
                                v-model="dateRange.from" 
                                type="date" 
                                class="w-1/2 px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                @change="applyFilters"
                            />
                            <input 
                                v-model="dateRange.to" 
                                type="date" 
                                class="w-1/2 px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                @change="applyFilters"
                            />
                        </div>
                        <div>
                            <button 
                                @click="clearFilters"
                                class="w-full px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400"
                            >
                                Clear Filters
                            </button>
                        </div>
                    </div>
                    
                    <!-- Incidents Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Incident</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reported By</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="incident in props.incidents.data" :key="incident.id">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center text-xl">
                                                {{ getIncidentTypeIcon(incident.type) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ formatIncidentType(incident.type) }}
                                                </div>
                                                <div class="text-sm text-gray-500 line-clamp-1">
                                                    {{ incident.description }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ incident.driver?.user?.name || 'Unknown' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ formatDate(incident.created_at) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusColor(incident.status)]">
                                            {{ incident.status.charAt(0).toUpperCase() + incident.status.slice(1).replace('_', ' ') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Link :href="route('incidents.show', incident.id)" class="text-blue-600 hover:text-blue-900 mr-3">
                                            View
                                        </Link>
                                        <Link :href="route('incidents.edit', incident.id)" class="text-indigo-600 hover:text-indigo-900">
                                            Update
                                        </Link>
                                    </td>
                                </tr>
                                
                                <!-- Empty state -->
                                <tr v-if="props.incidents.data.length === 0">
                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                        No incidents found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-6 flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing {{ props.incidents.from }} to {{ props.incidents.to }} of {{ props.incidents.total }} incidents
                        </div>
                        
                        <div class="flex-1 flex justify-end">
                            <Link 
                                v-if="props.incidents.prev_page_url" 
                                :href="props.incidents.prev_page_url" 
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                Previous
                            </Link>
                            <Link 
                                v-if="props.incidents.next_page_url" 
                                :href="props.incidents.next_page_url" 
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