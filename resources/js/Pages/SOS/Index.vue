<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DashboardCard from '@/Components/DashboardCard.vue';
import { ExclamationTriangleIcon, ShieldExclamationIcon, CheckCircleIcon, ClockIcon } from '@heroicons/vue/24/outline';
import axios from 'axios';

const props = defineProps({
    sosAlerts: Object,
    drivers: Array,
    filters: Object,
});

const refreshInterval = ref(null);
const map = ref(null);
const markers = ref({});

// Initialize filters with props
const search = ref(props.filters?.search || '');
const filterStatus = ref(props.filters?.status || 'all');
const filterDriver = ref(props.filters?.driver_id || 'all');
const dateRange = ref({
    from: props.filters?.from_date || '',
    to: props.filters?.to_date || ''
});

// Watch for filter changes
let searchTimeout;
watch([search, filterStatus, filterDriver, () => dateRange.value], ([newSearch, newStatus, newDriver, newDateRange]) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 300);
}, { deep: true });

onMounted(() => {
    if (hasActiveSosAlerts()) {
        initMap();
    }
    
    // Auto-refresh every 30 seconds
    refreshInterval.value = setInterval(() => {
        router.reload({ only: ['sosAlerts'] });
    }, 30000);
});

onUnmounted(() => {
    if (refreshInterval.value) {
        clearInterval(refreshInterval.value);
    }
});

function hasActiveSosAlerts() {
    return props.sosAlerts.data.some(alert => alert.status === 'active');
}

function initMap() {
    if (!window.L) {
        console.error('Leaflet library not loaded');
        return;
    }
    
    map.value = L.map('sos-map').setView([4.2105, 101.9758], 10);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map.value);
    
    props.sosAlerts.data.forEach(alert => {
        if (alert.status === 'active') {
            addSosMarker(alert);
        }
    });
}

function addSosMarker(alert) {
    const alertKey = `sos-${alert.id}`;
    const position = [alert.latitude, alert.longitude];
    const driverName = alert.driver?.user?.name || 'Unknown Driver';
    const timeInfo = new Date(alert.created_at).toLocaleTimeString();
    
    const sosIcon = L.divIcon({
        html: `<div class="sos-marker-icon">SOS</div>`,
        className: '',
        iconSize: [40, 40]
    });
    
    const popupContent = `
        <div class="font-bold text-red-600">SOS ALERT</div>
        <div class="font-medium">${driverName}</div>
        <div>Time: ${timeInfo}</div>
        <div>${alert.message || 'No message provided'}</div>
        <div class="mt-2">
            <button 
                onclick="window.respondToSos(${alert.id})" 
                class="px-2 py-1 bg-blue-500 text-white rounded text-xs"
            >
                Respond
            </button>
            <button 
                onclick="window.resolveSos(${alert.id})" 
                class="px-2 py-1 bg-green-500 text-white rounded text-xs ml-1"
            >
                Resolve
            </button>
        </div>
    `;
    
    const marker = L.marker(position, { icon: sosIcon })
        .addTo(map.value)
        .bindPopup(popupContent);
        
    markers.value[alertKey] = marker;
    marker.openPopup();
}

function respondToSos(alertId) {
    axios.post(`/sos/${alertId}/respond`)
        .then(() => {
            router.reload({ only: ['sosAlerts'] });
        })
        .catch(error => {
            console.error('Error responding to SOS alert:', error);
        });
}

function resolveSos(alertId) {
    axios.post(`/sos/${alertId}/resolve`)
        .then(() => {
            router.reload({ only: ['sosAlerts'] });
        })
        .catch(error => {
            console.error('Error resolving SOS alert:', error);
        });
}

// Expose functions to window for popup button clicks
if (typeof window !== 'undefined') {
    window.respondToSos = respondToSos;
    window.resolveSos = resolveSos;
}

function applyFilters() {
    const filters = {};
    
    if (search.value) filters.search = search.value;
    if (filterStatus.value !== 'all') filters.status = filterStatus.value;
    if (filterDriver.value !== 'all') filters.driver_id = filterDriver.value;
    if (dateRange.value.from) filters.from_date = dateRange.value.from;
    if (dateRange.value.to) filters.to_date = dateRange.value.to;
    
    router.get(route('sos.index'), filters, {
        preserveState: true,
        replace: true,
        only: ['sosAlerts']
    });
}

function clearFilters() {
    search.value = '';
    filterStatus.value = 'all';
    filterDriver.value = 'all';
    dateRange.value = { from: '', to: '' };
    
    router.get(route('sos.index'), {}, {
        preserveState: true,
        replace: true,
        only: ['sosAlerts']
    });
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString();
}

function getStatusColor(status) {
    switch (status) {
        case 'active':
            return 'bg-gradient-to-r from-red-100 to-red-200 text-red-800 border border-red-300 shadow-sm animate-pulse';
        case 'responded':
            return 'bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-800 border border-yellow-300 shadow-sm';
        case 'resolved':
            return 'bg-gradient-to-r from-green-100 to-green-200 text-green-800 border border-green-300 shadow-sm';
        default:
            return 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 border border-gray-300 shadow-sm';
    }
}

// Calculate stats
const stats = {
    total: props.sosAlerts.total || 0,
    active: props.sosAlerts.data.filter(s => s.status === 'active').length,
    responded: props.sosAlerts.data.filter(s => s.status === 'responded').length,
    resolved: props.sosAlerts.data.filter(s => s.status === 'resolved').length,
};
</script>

<template>
    <Head>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css" />
    </Head>
    
    <AppLayout title="SOS Alerts">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gradient-primary">Emergency Alert System</h1>
                        <p class="text-gray-600 mt-2">Monitor and respond to SOS alerts from drivers in real-time</p>
                    </div>
                </div>
            </div>

            <!-- Active SOS Alert Banner -->
            <div v-if="hasActiveSosAlerts()" 
                 class="bg-gradient-to-r from-red-600 to-red-700 rounded-xl shadow-lg p-6 mb-8 animate-pulse">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 bg-white/20 rounded-full flex items-center justify-center">
                            <ExclamationTriangleIcon class="h-8 w-8 text-white" />
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-xl font-bold text-white flex items-center">
                            🚨 URGENT: Active SOS Alerts
                            <span class="ml-3 px-3 py-1 bg-white/20 rounded-full text-sm font-medium">
                                {{ stats.active }}
                            </span>
                        </h3>
                        <p class="mt-2 text-red-100">
                            There are active SOS alerts that require immediate attention from drivers in the field.
                        </p>
                    </div>
                    <div class="ml-4">
                        <Link :href="route('sos.index')" class="bg-white text-red-600 px-6 py-3 rounded-xl font-semibold hover:bg-red-50 transition-colors duration-200">
                            View All Alerts
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Enhanced Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <DashboardCard
                    title="Total Alerts"
                    :value="stats.total"
                    :icon="ShieldExclamationIcon"
                    icon-color="blue"
                    :animation-delay="0"
                    subtitle="All emergency alerts"
                />
                <DashboardCard
                    title="Active"
                    :value="stats.active"
                    :icon="ExclamationTriangleIcon"
                    :icon-color="stats.active > 0 ? 'red' : 'gray'"
                    :animation-delay="100"
                    :subtitle="stats.active > 0 ? 'Immediate attention needed!' : 'No active alerts'"
                    :subtitle-type="stats.active > 0 ? 'danger' : 'success'"
                />
                <DashboardCard
                    title="Responded"
                    :value="stats.responded"
                    :icon="ClockIcon"
                    icon-color="orange"
                    :animation-delay="200"
                    subtitle="Response in progress"
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

            <!-- Map View for Active SOS Alerts -->
            <div v-if="hasActiveSosAlerts()" class="card-modern mb-8">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-red-50 to-pink-50">
                    <h3 class="text-lg font-semibold text-gray-900">Active SOS Alert Locations</h3>
                    <p class="text-sm text-gray-600 mt-1">Real-time locations of emergency alerts</p>
                </div>
                <div id="sos-map" class="h-96"></div>
            </div>

            <!-- Main Content Card -->
            <div class="card-modern">
                <!-- Enhanced Header -->
                <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-red-50 to-pink-50">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">SOS Alert Management</h3>
                            <p class="text-sm text-gray-600">Monitor and respond to emergency alerts from the field</p>
                        </div>
                        <div class="flex items-center space-x-4 mt-4 sm:mt-0">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                                <span class="text-sm text-gray-600">Active</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                <span class="text-sm text-gray-600">Responded</span>
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
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
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
                                    placeholder="Search alerts by driver, message..."
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 bg-white"
                                />
                            </div>
                        </div>
                        <div>
                            <select 
                                v-model="filterStatus"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 bg-white">
                                <option value="all">All Status</option>
                                <option value="active">🔴 Active</option>
                                <option value="responded">🟡 Responded</option>
                                <option value="resolved">🟢 Resolved</option>
                            </select>
                        </div>
                        <div>
                            <select 
                                v-model="filterDriver"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 bg-white">
                                <option value="all">All Drivers</option>
                                <option v-for="driver in props.drivers" :key="driver.id" :value="driver.id">
                                    {{ driver.user.name }}
                                </option>
                            </select>
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
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Driver</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Time</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Message</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="alert in props.sosAlerts.data" :key="alert.id" 
                                    :class="{ 'bg-red-50': alert.status === 'active' }" 
                                    class="hover:bg-gradient-to-r hover:from-red-50 hover:to-pink-50 transition-all duration-200 group">
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-14 w-14 bg-gradient-to-br from-red-100 to-pink-100 rounded-xl flex items-center justify-center text-2xl shadow-sm group-hover:shadow-md transition-shadow duration-200">
                                                🚨
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-semibold text-gray-900">
                                                    {{ alert.driver?.user?.name || 'Unknown' }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ alert.driver?.license_number || 'No license' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ formatDate(alert.created_at) }}</div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ Math.ceil((new Date() - new Date(alert.created_at)) / (1000 * 60)) }} min ago
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="text-sm text-gray-900 max-w-xs">{{ alert.message || 'No message provided' }}</div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <span :class="getStatusColor(alert.status)" class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-semibold shadow-sm">
                                            <div class="w-2 h-2 rounded-full mr-2" 
                                                 :class="{
                                                     'bg-red-500': alert.status === 'active',
                                                     'bg-yellow-500': alert.status === 'responded',
                                                     'bg-green-500': alert.status === 'resolved'
                                                 }">
                                            </div>
                                            {{ alert.status.charAt(0).toUpperCase() + alert.status.slice(1) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <button v-if="alert.status === 'active'" @click="respondToSos(alert.id)" 
                                                    class="inline-flex items-center px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-200 text-xs font-medium">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                </svg>
                                                Respond
                                            </button>
                                            <button v-if="alert.status !== 'resolved'" @click="resolveSos(alert.id)" 
                                                    class="inline-flex items-center px-3 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors duration-200 text-xs font-medium">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Resolve
                                            </button>
                                            <Link v-if="alert.status === 'resolved'" :href="route('sos.show', alert.id)" 
                                                  class="inline-flex items-center px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-200 text-xs font-medium">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                View Details
                                            </Link>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Enhanced Empty state -->
                                <tr v-if="props.sosAlerts.data.length === 0">
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-20 h-20 bg-gradient-to-br from-red-100 to-pink-100 rounded-full flex items-center justify-center mb-4">
                                                <ShieldExclamationIcon class="h-10 w-10 text-red-500" />
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900 mb-2">No SOS alerts found</h3>
                                            <p class="text-gray-500 mb-4 max-w-sm">No emergency alerts match your current filters.</p>
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
                <div v-if="props.sosAlerts.data.length > 0" class="px-6 py-4 border-t border-gray-200 bg-gradient-to-r from-gray-50 to-slate-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center text-sm text-gray-700">
                            <span class="font-medium">
                                Showing {{ props.sosAlerts.from || 0 }} to {{ props.sosAlerts.to || 0 }} 
                            </span>
                            <span class="mx-2">of</span>
                            <span class="font-medium">{{ props.sosAlerts.total || 0 }} alerts</span>
                        </div>
                        <div class="flex space-x-2">
                            <Link 
                                v-if="props.sosAlerts.prev_page_url" 
                                :href="props.sosAlerts.prev_page_url" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm">
                                ← Previous
                            </Link>
                            <Link 
                                v-if="props.sosAlerts.next_page_url" 
                                :href="props.sosAlerts.next_page_url" 
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

<style scoped>
.sos-marker-icon {
    background-color: #ef4444;
    color: white;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
    border: 2px solid white;
    box-shadow: 0 0 10px rgba(0,0,0,0.5);
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
    }
    70% {
        transform: scale(1);
        box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
    }
    100% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
    }
}
</style>