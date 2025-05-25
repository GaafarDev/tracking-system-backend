<script setup>
import { ref, watch, onMounted } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const page = usePage();
const props = defineProps({
    routes: Object,
    filters: Object,
});

// Initialize filters with props
const search = ref(props.filters?.search || '');
const filterDistance = ref(props.filters?.distance || 'all');
const filterStops = ref(props.filters?.stops || 'all');

// Load fresh data when component mounts
onMounted(() => {
    router.reload({ only: ['routes'] });
});

// Watch for filter changes with debounce
let searchTimeout;
watch([search, filterDistance, filterStops], ([newSearch, newDistance, newStops]) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 300);
});

function formatDistance(distance) {
    if (!distance) return 'N/A';
    return `${distance} km`;
}

function formatDuration(minutes) {
    if (!minutes) return 'N/A';
    
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;
    
    if (hours > 0) {
        return `${hours}h ${mins}m`;
    } else {
        return `${mins}m`;
    }
}

function getStopsCount(route) {
    if (!route.stops) return 0;
    return Array.isArray(route.stops) ? route.stops.length : 0;
}

function confirmDelete(routeItem) {
    if (confirm(`Are you sure you want to delete the route "${routeItem.name}"?`)) {
        router.delete(route('routes.destroy', routeItem.id));
    }
}

function applyFilters() {
    const filters = {};
    
    if (search.value) filters.search = search.value;
    if (filterDistance.value !== 'all') filters.distance = filterDistance.value;
    if (filterStops.value !== 'all') filters.stops = filterStops.value;
    
    router.get(route('routes.index'), filters, {
        preserveState: true,
        replace: true,
        only: ['routes']
    });
}

function clearFilters() {
    search.value = '';
    filterDistance.value = 'all';
    filterStops.value = 'all';
    
    router.get(route('routes.index'), {}, {
        preserveState: true,
        replace: true,
        only: ['routes']
    });
}
</script>

<template>
    <AppLayout title="Routes">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Routes
                </h2>
                <Link :href="route('routes.create')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 active:bg-blue-600 transition">
                    Add Route
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <!-- Search and Filters -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div>
                            <input 
                                v-model="search" 
                                type="text" 
                                placeholder="Search routes..." 
                                class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                        </div>
                        <div>
                            <select 
                                v-model="filterDistance"
                                class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="all">All Distances</option>
                                <option value="short">Short (< 10km)</option>
                                <option value="medium">Medium (10-50km)</option>
                                <option value="long">Long (> 50km)</option>
                            </select>
                        </div>
                        <div>
                            <select 
                                v-model="filterStops"
                                class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="all">All Stop Counts</option>
                                <option value="few">Few (1-3 stops)</option>
                                <option value="medium">Medium (4-7 stops)</option>
                                <option value="many">Many (8+ stops)</option>
                            </select>
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
                    
                    <!-- Routes Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Route Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Distance</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stops</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="routeItem in props.routes.data" :key="routeItem.id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ routeItem.name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ routeItem.description || 'No description' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ formatDistance(routeItem.distance_km) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ formatDuration(routeItem.estimated_duration_minutes) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ getStopsCount(routeItem) }} stops</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Link :href="route('routes.show', routeItem.id)" class="text-blue-600 hover:text-blue-900 mr-3">
                                            View
                                        </Link>
                                        <Link :href="route('routes.edit', routeItem.id)" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                            Edit
                                        </Link>
                                        <button @click="confirmDelete(routeItem)" class="text-red-600 hover:text-red-900">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                                
                                <!-- Empty state -->
                                <tr v-if="props.routes.data.length === 0">
                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                        No routes found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-6 flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing {{ props.routes.from || 0 }} to {{ props.routes.to || 0 }} of {{ props.routes.total || 0 }} routes
                        </div>
                        
                        <div class="flex-1 flex justify-end">
                            <Link 
                                v-if="props.routes.prev_page_url" 
                                :href="props.routes.prev_page_url" 
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                Previous
                            </Link>
                            <Link 
                                v-if="props.routes.next_page_url" 
                                :href="props.routes.next_page_url" 
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