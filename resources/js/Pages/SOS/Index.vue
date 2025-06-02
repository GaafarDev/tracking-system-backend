<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ExclamationTriangleIcon } from '@heroicons/vue/24/outline';
import axios from 'axios';

const props = defineProps({
    sosAlerts: Object,
    drivers: Array, // Add drivers prop
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
    // Using Leaflet for maps
    if (!window.L) {
        console.error('Leaflet library not loaded');
        return;
    }
    
    map.value = L.map('sos-map').setView([4.2105, 101.9758], 10); // Center on Malaysia
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map.value);
    
    // Add markers for active SOS alerts
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
    
    // Create custom icon for SOS alerts
    const sosIcon = L.divIcon({
        html: `<div class="sos-marker-icon">SOS</div>`,
        className: '',
        iconSize: [40, 40]
    });
    
    // Create marker popup content
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
    
    // Auto-open the popup for active SOS alerts
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

// Update applyFilters function
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
            return 'bg-red-100 text-red-800';
        case 'responded':
            return 'bg-yellow-100 text-yellow-800';
        case 'resolved':
            return 'bg-green-100 text-green-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}
</script>

<template>
    <AppLayout title="SOS Alerts">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    SOS Alerts
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Active SOS Alert Banner -->
                <div v-if="hasActiveSosAlerts()" 
                     class="bg-gradient-to-r from-red-600 to-red-700 shadow-2xl rounded-2xl p-6 mb-8"
                     v-motion
                     :initial="{ opacity: 0, scale: 0.95 }"
                     :enter="{ opacity: 1, scale: 1 }">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 bg-white/20 rounded-full flex items-center justify-center animate-pulse">
                                <ExclamationTriangleIcon class="h-8 w-8 text-white" />
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-xl font-bold text-white flex items-center">
                                🚨 URGENT: Active SOS Alerts
                                <span class="ml-3 px-3 py-1 bg-white/20 rounded-full text-sm font-medium animate-pulse">
                                    {{ props.sosAlerts.data.filter(a => a.status === 'active').length }}
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
                
                <!-- Map View for Active SOS Alerts -->
                <div v-if="hasActiveSosAlerts()" class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Active SOS Alert Locations</h3>
                    <div id="sos-map" class="w-full h-[400px] rounded-lg"></div>
                </div>
                
                <!-- Search and Filters -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
                        <div>
                            <input 
                                v-model="search" 
                                type="text" 
                                placeholder="Search SOS alerts..." 
                                class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                        </div>
                        <div>
                            <select 
                                v-model="filterStatus"
                                class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="all">All Statuses</option>
                                <option value="active">Active</option>
                                <option value="responded">Responded</option>
                                <option value="resolved">Resolved</option>
                            </select>
                        </div>
                        <div>
                            <select 
                                v-model="filterDriver"
                                class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="all">All Drivers</option>
                                <option v-for="driver in props.drivers" :key="driver.id" :value="driver.id">
                                    {{ driver.user.name }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <input 
                                v-model="dateRange.from" 
                                type="date" 
                                class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="From date"
                            />
                        </div>
                        <div>
                            <input 
                                v-model="dateRange.to" 
                                type="date" 
                                class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="To date"
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
                </div>
                
                <!-- SOS Alerts Table -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">All SOS Alerts</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Driver</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="alert in props.sosAlerts.data" :key="alert.id" :class="{ 'bg-red-50': alert.status === 'active' }">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center text-xl">
                                                🚨
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ alert.driver?.user?.name || 'Unknown' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ formatDate(alert.created_at) }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ alert.message || 'No message provided' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusColor(alert.status)]">
                                            {{ alert.status.charAt(0).toUpperCase() + alert.status.slice(1) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button v-if="alert.status === 'active'" @click="respondToSos(alert.id)" class="text-blue-600 hover:text-blue-900 mr-3">
                                            Respond
                                        </button>
                                        <button v-if="alert.status !== 'resolved'" @click="resolveSos(alert.id)" class="text-green-600 hover:text-green-900">
                                            Resolve
                                        </button>
                                        <Link v-if="alert.status === 'resolved'" :href="route('sos.show', alert.id)" class="text-blue-600 hover:text-blue-900">
                                            View Details
                                        </Link>
                                    </td>
                                </tr>
                                
                                <!-- Empty state -->
                                <tr v-if="props.sosAlerts.data.length === 0">
                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                        No SOS alerts found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-6 flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing {{ props.sosAlerts.from || 0 }} to {{ props.sosAlerts.to || 0 }} of {{ props.sosAlerts.total || 0 }} alerts
                        </div>
                        
                        <div class="flex-1 flex justify-end">
                            <Link 
                                v-if="props.sosAlerts.prev_page_url" 
                                :href="props.sosAlerts.prev_page_url" 
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                Previous
                            </Link>
                            <Link 
                                v-if="props.sosAlerts.next_page_url" 
                                :href="props.sosAlerts.next_page_url" 
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