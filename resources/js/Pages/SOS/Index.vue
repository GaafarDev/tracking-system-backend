<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';

const props = defineProps({
    sosAlerts: Object,
    filters: Object, // Add filters prop
});

const refreshInterval = ref(null);
const map = ref(null);
const markers = ref({});

// Initialize filters with props
const search = ref(props.filters?.search || '');
const filterStatus = ref(props.filters?.status || 'all');
const filterDriver = ref(props.filters?.driver_id || 'all');

// Watch for filter changes
let searchTimeout;
watch([search, filterStatus, filterDriver], ([newSearch, newStatus, newDriver]) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 300);
});

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

// Add filter methods
function applyFilters() {
    const filters = {};
    
    if (search.value) filters.search = search.value;
    if (filterStatus.value !== 'all') filters.status = filterStatus.value;
    if (filterDriver.value !== 'all') filters.driver_id = filterDriver.value;
    
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
                <div v-if="hasActiveSosAlerts()" class="bg-red-600 shadow-xl sm:rounded-lg p-6 mb-6 animate-pulse">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-lg leading-6 font-medium text-white">
                                URGENT: Active SOS Alerts
                            </h3>
                            <div class="mt-2 text-sm text-red-100">
                                <p>
                                    There are {{ props.sosAlerts.data.filter(a => a.status === 'active').length }} active SOS alerts that require immediate attention.
                                </p>
                            </div>
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
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
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
                                <!-- You'll need to add drivers data from backend -->
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