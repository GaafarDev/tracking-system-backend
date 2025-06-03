<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DashboardCard from '@/Components/DashboardCard.vue';
import { ExclamationTriangleIcon, ExclamationCircleIcon, ClockIcon, CheckCircleIcon, XCircleIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    incidents: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const filterType = ref(props.filters?.type || 'all');
const filterStatus = ref(props.filters?.status || 'all');
const dateRange = ref({
    from: props.filters?.from_date || '',
    to: props.filters?.to_date || ''
});

// Watch for changes in filters and update URL
let searchTimeout;
watch([search, filterType, filterStatus], ([newSearch, newType, newStatus]) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('incidents.index'), {
            search: newSearch || undefined,
            type: newType !== 'all' ? newType : undefined,
            status: newStatus !== 'all' ? newStatus : undefined,
            from_date: dateRange.value.from || undefined,
            to_date: dateRange.value.to || undefined,
        }, {
            preserveState: true,
            replace: true,
        });
    }, 300);
});

watch([() => dateRange.value.from, () => dateRange.value.to], () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('incidents.index'), {
            search: search.value || undefined,
            type: filterType.value !== 'all' ? filterType.value : undefined,
            status: filterStatus.value !== 'all' ? filterStatus.value : undefined,
            from_date: dateRange.value.from || undefined,
            to_date: dateRange.value.to || undefined,
        }, {
            preserveState: true,
            replace: true,
        });
    }, 300);
});

function getStatusColor(status) {
    switch (status) {
        case 'reported':
            return 'bg-gradient-to-r from-red-100 to-red-200 text-red-800 border border-red-300 shadow-sm';
        case 'in_progress':
            return 'bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-800 border border-yellow-300 shadow-sm';
        case 'resolved':
            return 'bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 border border-blue-300 shadow-sm';
        case 'closed':
            return 'bg-gradient-to-r from-green-100 to-green-200 text-green-800 border border-green-300 shadow-sm';
        default:
            return 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 border border-gray-300 shadow-sm';
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

function clearFilters() {
    search.value = '';
    filterType.value = 'all';
    filterStatus.value = 'all';
    dateRange.value = { from: '', to: '' };
    
    router.get(route('incidents.index'), {}, {
        preserveState: true,
        replace: true,
    });
}

// Calculate stats
const stats = {
    total: props.incidents.total || 0,
    active: props.incidents.data.filter(i => i.status === 'reported' || i.status === 'in_progress').length,
    reported: props.incidents.data.filter(i => i.status === 'reported').length,
    resolved: props.incidents.data.filter(i => i.status === 'resolved' || i.status === 'closed').length,
};
</script>

<template>
    <AppLayout title="Incidents">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gradient-primary">Incident Management</h1>
                        <p class="text-gray-600 mt-2">Track and manage all reported incidents across your fleet</p>
                    </div>
                </div>
            </div>

            <!-- Enhanced Stats Overview with proper DashboardCard props -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <DashboardCard
                    title="Total Incidents"
                    :value="stats.total"
                    :icon="ExclamationTriangleIcon"
                    icon-color="blue"
                    :animation-delay="0"
                    subtitle="All time incidents"
                />
                <DashboardCard
                    title="Active Cases"
                    :value="stats.active"
                    :icon="ExclamationCircleIcon"
                    :icon-color="stats.active > 0 ? 'red' : 'gray'"
                    :animation-delay="100"
                    :subtitle="stats.active > 0 ? 'Requires attention' : 'All clear'"
                    :subtitle-type="stats.active > 0 ? 'danger' : 'success'"
                />
                <DashboardCard
                    title="Reported"
                    :value="stats.reported"
                    :icon="ClockIcon"
                    icon-color="orange"
                    :animation-delay="200"
                    subtitle="Awaiting response"
                />
                <DashboardCard
                    title="Resolved"
                    :value="stats.resolved"
                    :icon="CheckCircleIcon"
                    icon-color="green"
                    :animation-delay="300"
                    subtitle="Successfully handled"
                />
            </div>

            <!-- Main Content Card with Modern Design -->
            <div class="card-modern">
                <!-- Enhanced Header -->
                <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-orange-50 to-red-50">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Incident Reports</h3>
                            <p class="text-sm text-gray-600">Monitor and respond to field incidents in real-time</p>
                        </div>
                        <div class="flex items-center space-x-4 mt-4 sm:mt-0">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                <span class="text-sm text-gray-600">Critical</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                <span class="text-sm text-gray-600">In Progress</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                <span class="text-sm text-gray-600">Resolved</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Filters -->
                <div class="px-6 py-5 bg-gradient-to-r from-gray-50 to-slate-50 border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                        <div class="lg:col-span-2">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input
                                    v-model="search"
                                    type="text"
                                    placeholder="Search incidents, drivers, descriptions..."
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 bg-white"
                                />
                            </div>
                        </div>
                        <div>
                            <select 
                                v-model="filterType"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 bg-white">
                                <option value="all">All Types</option>
                                <option value="accident">🚨 Accident</option>
                                <option value="breakdown">🔧 Breakdown</option>
                                <option value="road_obstruction">🚧 Road Block</option>
                                <option value="weather">🌧️ Weather</option>
                                <option value="other">❓ Other</option>
                            </select>
                        </div>
                        <div>
                            <select 
                                v-model="filterStatus"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 bg-white">
                                <option value="all">All Statuses</option>
                                <option value="reported">🔴 Reported</option>
                                <option value="in_progress">🟡 In Progress</option>
                                <option value="resolved">🔵 Resolved</option>
                                <option value="closed">🟢 Closed</option>
                            </select>
                        </div>
                        <div>
                            <input 
                                v-model="dateRange.from" 
                                type="date" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 bg-white"
                                placeholder="From date"
                            />
                        </div>
                        <div>
                            <button 
                                @click="clearFilters"
                                class="w-full px-4 py-3 bg-gradient-to-r from-gray-500 to-gray-600 text-white rounded-xl shadow-sm hover:from-gray-600 hover:to-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200 font-medium"
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
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Incident Details</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Reported By</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date & Time</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="incident in props.incidents.data" :key="incident.id" 
                                    class="hover:bg-gradient-to-r hover:from-orange-50 hover:to-red-50 transition-all duration-200 group">
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-14 w-14 bg-gradient-to-br from-orange-100 to-red-100 rounded-xl flex items-center justify-center text-2xl shadow-sm group-hover:shadow-md transition-shadow duration-200">
                                                {{ getIncidentTypeIcon(incident.type) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-semibold text-gray-900 mb-1">
                                                    {{ formatIncidentType(incident.type) }}
                                                </div>
                                                <div class="text-sm text-gray-600 line-clamp-2 max-w-xs">
                                                    {{ incident.description || 'No description provided' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center">
                                                <span class="text-white font-semibold text-sm">
                                                    {{ (incident.driver?.user?.name || 'U').charAt(0).toUpperCase() }}
                                                </span>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ incident.driver?.user?.name || 'Unknown Driver' }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    ID: #{{ incident.driver?.id || 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ formatDate(incident.created_at) }}</div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ Math.ceil((new Date() - new Date(incident.created_at)) / (1000 * 60 * 60 * 24)) }} days ago
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <span :class="getStatusColor(incident.status)" class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-semibold shadow-sm">
                                            <div class="w-2 h-2 rounded-full mr-2" 
                                                 :class="{
                                                     'bg-red-500': incident.status === 'reported',
                                                     'bg-yellow-500': incident.status === 'in_progress',
                                                     'bg-blue-500': incident.status === 'resolved',
                                                     'bg-green-500': incident.status === 'closed'
                                                 }">
                                            </div>
                                            {{ incident.status.charAt(0).toUpperCase() + incident.status.slice(1).replace('_', ' ') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <Link :href="route('incidents.show', incident.id)" 
                                                  class="inline-flex items-center px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-200 text-xs font-medium">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                View
                                            </Link>
                                            <Link :href="route('incidents.edit', incident.id)" 
                                                  class="inline-flex items-center px-3 py-2 bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 transition-colors duration-200 text-xs font-medium">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Update
                                            </Link>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Enhanced Empty state -->
                                <tr v-if="props.incidents.data.length === 0">
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-20 h-20 bg-gradient-to-br from-orange-100 to-red-100 rounded-full flex items-center justify-center mb-4">
                                                <ExclamationTriangleIcon class="h-10 w-10 text-orange-500" />
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900 mb-2">No incidents found</h3>
                                            <p class="text-gray-500 mb-4 max-w-sm">There are currently no incidents matching your search criteria.</p>
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
                <div v-if="props.incidents.data.length > 0" class="px-6 py-4 border-t border-gray-200 bg-gradient-to-r from-gray-50 to-slate-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center text-sm text-gray-700">
                            <span class="font-medium">
                                Showing {{ props.incidents.from || 0 }} to {{ props.incidents.to || 0 }} 
                            </span>
                            <span class="mx-2">of</span>
                            <span class="font-medium">{{ props.incidents.total || 0 }} incidents</span>
                        </div>
                        <div class="flex space-x-2">
                            <Link 
                                v-if="props.incidents.prev_page_url" 
                                :href="props.incidents.prev_page_url" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm">
                                ← Previous
                            </Link>
                            <Link 
                                v-if="props.incidents.next_page_url" 
                                :href="props.incidents.next_page_url" 
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