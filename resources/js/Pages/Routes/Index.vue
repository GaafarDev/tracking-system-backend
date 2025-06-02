<script setup>
import { ref, watch, onMounted } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DashboardCard from '@/Components/DashboardCard.vue';
import { PlusIcon, MapIcon, ClockIcon, MapPinIcon } from '@heroicons/vue/24/outline';

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

// Calculate stats
const stats = {
    total: props.routes.total || 0,
    short: props.routes.data.filter(r => r.distance_km && r.distance_km < 10).length,
    medium: props.routes.data.filter(r => r.distance_km && r.distance_km >= 10 && r.distance_km <= 50).length,
    long: props.routes.data.filter(r => r.distance_km && r.distance_km > 50).length,
};
</script>

<template>
    <AppLayout title="Routes">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <DashboardCard
                    title="Total Routes"
                    :value="stats.total"
                    :icon="MapIcon"
                    icon-color="text-blue-600"
                    icon-bg-color="bg-blue-100"
                />
                <DashboardCard
                    title="Short Routes"
                    :value="stats.short"
                    :icon="MapPinIcon"
                    icon-color="text-green-600"
                    icon-bg-color="bg-green-100"
                    description="< 10km"
                />
                <DashboardCard
                    title="Medium Routes"
                    :value="stats.medium"
                    :icon="MapPinIcon"
                    icon-color="text-yellow-600"
                    icon-bg-color="bg-yellow-100"
                    description="10-50km"
                />
                <DashboardCard
                    title="Long Routes"
                    :value="stats.long"
                    :icon="MapPinIcon"
                    icon-color="text-red-600"
                    icon-bg-color="bg-red-100"
                    description="> 50km"
                />
            </div>

            <!-- Main Content Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Route Management</h3>
                            <p class="text-sm text-gray-600 mt-1">Manage and configure transportation routes</p>
                        </div>
                        <div class="mt-4 sm:mt-0">
                            <Link :href="route('routes.create')" class="btn-primary">
                                <PlusIcon class="w-4 h-4 mr-2" />
                                Add Route
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="px-6 py-4 bg-gradient-to-r from-primary-50 to-secondary-50 border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <input 
                                v-model="search"
                                type="text" 
                                placeholder="Search routes..." 
                                class="input-modern"
                            />
                        </div>
                        <div>
                            <select v-model="filterDistance" class="input-modern">
                                <option value="all">All Distances</option>
                                <option value="short">Short (< 10 km)</option>
                                <option value="medium">Medium (10-50 km)</option>
                                <option value="long">Long (> 50 km)</option>
                            </select>
                        </div>
                        <div>
                            <select v-model="filterStatus" class="input-modern">
                                <option value="all">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div>
                            <button @click="clearFilters" class="btn-secondary-modern w-full">
                                Clear Filters
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table -->
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
                            <tr v-for="routeItem in props.routes.data" :key="routeItem.id" class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center">
                                            <MapIcon class="h-6 w-6 text-blue-600" />
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-semibold text-gray-900">{{ routeItem.name }}</div>
                                            <div class="text-sm text-gray-500">{{ routeItem.description || 'No description' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 font-medium">{{ formatDistance(routeItem.distance_km) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center text-sm text-gray-900">
                                        <ClockIcon class="h-4 w-4 text-gray-400 mr-1" />
                                        {{ formatDuration(routeItem.estimated_duration_minutes) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ getStopsCount(routeItem) }} stops</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <Link :href="route('routes.show', routeItem.id)" class="btn-action-view">
                                            View
                                        </Link>
                                        <Link :href="route('routes.edit', routeItem.id)" class="btn-action-edit">
                                            Edit
                                        </Link>
                                        <button @click="confirmDelete(routeItem)" class="btn-action-delete">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Empty state -->
                            <tr v-if="props.routes.data.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <MapIcon class="h-12 w-12 text-gray-300 mb-4" />
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">No routes found</h3>
                                        <p class="text-gray-500">Get started by creating your first route.</p>
                                        <Link :href="route('routes.create')" class="mt-4 btn-primary">
                                            <PlusIcon class="w-4 h-4 mr-2" />
                                            Create Your First Route
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="props.routes.data.length > 0" class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing {{ props.routes.from || 0 }} to {{ props.routes.to || 0 }} of {{ props.routes.total || 0 }} routes
                        </div>
                        <div class="flex space-x-2">
                            <Link 
                                v-if="props.routes.prev_page_url" 
                                :href="props.routes.prev_page_url" 
                                class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                                Previous
                            </Link>
                            <Link 
                                v-if="props.routes.next_page_url" 
                                :href="props.routes.next_page_url" 
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