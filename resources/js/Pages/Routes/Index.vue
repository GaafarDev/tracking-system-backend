<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DashboardCard from '@/Components/DashboardCard.vue';
import { PlusIcon, MapIcon, ClockIcon, MapPinIcon, ArrowPathIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    routes: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const filterDistance = ref(props.filters?.distance || 'all');
const filterStops = ref(props.filters?.stops || 'all');

// Watch for changes in filters and update URL
let searchTimeout;
watch([search, filterDistance, filterStops], ([newSearch, newDistance, newStops]) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('routes.index'), {
            search: newSearch || undefined,
            distance: newDistance !== 'all' ? newDistance : undefined,
            stops: newStops !== 'all' ? newStops : undefined,
        }, {
            preserveState: true,
            replace: true,
        });
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

function getDistanceColor(distance) {
    if (!distance) return 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 border border-gray-300 shadow-sm';
    
    if (distance < 10) {
        return 'bg-gradient-to-r from-green-100 to-green-200 text-green-800 border border-green-300 shadow-sm';
    } else if (distance <= 50) {
        return 'bg-gradient-to-r from-orange-100 to-orange-200 text-orange-800 border border-orange-300 shadow-sm';
    } else {
        return 'bg-gradient-to-r from-purple-100 to-purple-200 text-purple-800 border border-purple-300 shadow-sm';
    }
}

function clearFilters() {
    search.value = '';
    filterDistance.value = 'all';
    filterStops.value = 'all';
    
    router.get(route('routes.index'), {}, {
        preserveState: true,
        replace: true,
    });
}

function confirmDelete(routeItem) {
    if (confirm(`Are you sure you want to delete route "${routeItem.name}"?`)) {
        router.delete(route('routes.destroy', routeItem.id));
    }
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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gradient-primary">Route Management</h1>
                        <p class="text-gray-600 mt-2">Design and manage transportation routes for optimal efficiency</p>
                    </div>
                    <div class="mt-4 sm:mt-0">
                        <Link :href="route('routes.create')" class="btn-primary">
                            <PlusIcon class="w-4 h-4 mr-2" />
                            Add Route
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Enhanced Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <DashboardCard
                    title="Total Routes"
                    :value="stats.total"
                    :icon="MapIcon"
                    icon-color="blue"
                    :animation-delay="0"
                    subtitle="All routes"
                />
                <DashboardCard
                    title="Short Routes"
                    :value="stats.short"
                    :icon="MapPinIcon"
                    icon-color="green"
                    :animation-delay="100"
                    subtitle="< 10km distance"
                />
                <DashboardCard
                    title="Medium Routes"
                    :value="stats.medium"
                    :icon="ArrowPathIcon"
                    icon-color="orange"
                    :animation-delay="200"
                    subtitle="10-50km distance"
                />
                <DashboardCard
                    title="Long Routes"
                    :value="stats.long"
                    :icon="ClockIcon"
                    icon-color="purple"
                    :animation-delay="300"
                    subtitle="> 50km distance"
                />
            </div>

            <!-- Main Content Card -->
            <div class="card-modern">
                <!-- Enhanced Header -->
                <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-green-50 to-emerald-50">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Route Directory</h3>
                            <p class="text-sm text-gray-600">Complete overview of all transportation routes</p>
                        </div>
                        <div class="flex items-center space-x-4 mt-4 sm:mt-0">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                <span class="text-sm text-gray-600">Short</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-orange-500 rounded-full"></div>
                                <span class="text-sm text-gray-600">Medium</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                                <span class="text-sm text-gray-600">Long</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Filters -->
                <div class="px-6 py-5 bg-gradient-to-r from-gray-50 to-slate-50 border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-1">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input
                                    v-model="search"
                                    type="text"
                                    placeholder="Search routes by name, location..."
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 bg-white"
                                />
                            </div>
                        </div>
                        <div>
                            <select 
                                v-model="filterDistance"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 bg-white">
                                <option value="all">All Distances</option>
                                <option value="short">Short (< 10km)</option>
                                <option value="medium">Medium (10-50km)</option>
                                <option value="long">Long (> 50km)</option>
                            </select>
                        </div>
                        <div>
                            <select 
                                v-model="filterStops"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 bg-white">
                                <option value="all">All Stop Counts</option>
                                <option value="few">Few (1-3 stops)</option>
                                <option value="moderate">Moderate (4-7 stops)</option>
                                <option value="many">Many (8+ stops)</option>
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
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Route</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Distance</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Duration</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Stops</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="routeItem in props.routes.data" :key="routeItem.id" 
                                    class="hover:bg-gradient-to-r hover:from-green-50 hover:to-emerald-50 transition-all duration-200 group">
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-12 w-12 bg-gradient-to-br from-green-100 to-emerald-100 rounded-full flex items-center justify-center shadow-sm group-hover:shadow-md transition-shadow duration-200">
                                                <MapIcon class="h-6 w-6 text-green-600" />
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-semibold text-gray-900">{{ routeItem.name }}</div>
                                                <div class="text-sm text-gray-500">
                                                    {{ routeItem.start_location }} → {{ routeItem.end_location }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <span :class="getDistanceColor(routeItem.distance_km)" class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-semibold shadow-sm">
                                            {{ formatDistance(routeItem.distance_km) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="flex items-center text-sm text-gray-900">
                                            <ClockIcon class="h-4 w-4 text-gray-400 mr-1" />
                                            {{ formatDuration(routeItem.estimated_duration_minutes) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ getStopsCount(routeItem) }} stops</div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <Link :href="route('routes.show', routeItem.id)" 
                                                  class="inline-flex items-center px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-200 text-xs font-medium">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 616 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                View
                                            </Link>
                                            <Link :href="route('routes.edit', routeItem.id)" 
                                                  class="inline-flex items-center px-3 py-2 bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 transition-colors duration-200 text-xs font-medium">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Edit
                                            </Link>
                                            <button @click="confirmDelete(routeItem)" 
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
                                <tr v-if="props.routes.data.length === 0">
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-20 h-20 bg-gradient-to-br from-green-100 to-emerald-100 rounded-full flex items-center justify-center mb-4">
                                                <MapIcon class="h-10 w-10 text-green-500" />
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900 mb-2">No routes found</h3>
                                            <p class="text-gray-500 mb-4 max-w-sm">There are currently no routes matching your search criteria.</p>
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
                <div v-if="props.routes.data.length > 0" class="px-6 py-4 border-t border-gray-200 bg-gradient-to-r from-gray-50 to-slate-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center text-sm text-gray-700">
                            <span class="font-medium">
                                Showing {{ props.routes.from || 0 }} to {{ props.routes.to || 0 }} 
                            </span>
                            <span class="mx-2">of</span>
                            <span class="font-medium">{{ props.routes.total || 0 }} routes</span>
                        </div>
                        <div class="flex space-x-2">
                            <Link 
                                v-if="props.routes.prev_page_url" 
                                :href="props.routes.prev_page_url" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm">
                                ← Previous
                            </Link>
                            <Link 
                                v-if="props.routes.next_page_url" 
                                :href="props.routes.next_page_url" 
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