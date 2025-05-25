<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';

const props = defineProps({
    activeDrivers: Array,
    openIncidentsCount: Number,
    activeSosAlertsCount: Number
});

const locations = ref(props.activeDrivers || []);
const map = ref(null);
const markers = ref({});
const refreshInterval = ref(null);

// Initialize with prop values and provide defaults
const activeDriversCount = ref(0);
const activeVehiclesCount = ref(0);
const openIncidentsCount = ref(props.openIncidentsCount || 0);
const activeSosAlertsCount = ref(props.activeSosAlertsCount || 0);

// Statistics container
const stats = ref({
    drivers: { total: 0, active: 0, inactive: 0, on_leave: 0 },
    vehicles: { total: 0, active: 0, maintenance: 0, inactive: 0 },
    routes: { total: 0 },
    schedules: { total: 0, active: 0 },
    incidents: { total: 0, active: 0, resolved: 0 },
    sos_alerts: { total: 0, active: 0 }
});

onMounted(() => {
    initMap();
    // Load dashboard data immediately
    refreshDashboardData();
    // Set up auto-refresh every 30 seconds
    refreshInterval.value = setInterval(refreshDashboardData, 30000);
});

onUnmounted(() => {
    if (refreshInterval.value) {
        clearInterval(refreshInterval.value);
    }
});

async function refreshDashboardData() {
    try {
        // Get locations
        const locationsResponse = await axios.get('/api/locations/latest', {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            withCredentials: true
        });
        locations.value = locationsResponse.data;
        
        // Update markers
        locations.value.forEach(location => {
            addOrUpdateMarker(location);
        });
        
        // Get updated stats
        const statsResponse = await axios.get('/api/dashboard/stats', {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            withCredentials: true
        });
        
        if (statsResponse.data) {
            stats.value = statsResponse.data;
            
            // Update reactive references
            activeDriversCount.value = statsResponse.data.activeDriversCount || 0;
            activeVehiclesCount.value = statsResponse.data.activeVehiclesCount || 0;
            openIncidentsCount.value = statsResponse.data.openIncidentsCount || 0;
            activeSosAlertsCount.value = statsResponse.data.activeSosAlertsCount || 0;
        }
    } catch (error) {
        console.error('Error refreshing dashboard data:', error);
    }
}

function initMap() {
    // Maps
    if (!window.L) {
        console.error('Leaflet library not loaded');
        return;
    }
    
    map.value = L.map('map').setView([4.2105, 101.9758], 10); 
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map.value);
    
    // Add initial markers
    if (locations.value.length > 0) {
        locations.value.forEach(location => {
            addOrUpdateMarker(location);
        });
    }
}

function addOrUpdateMarker(location) {
    const driverKey = `driver-${location.driver_id}`;
    const position = [location.latitude, location.longitude];
    const driverName = location.driver?.user?.name || 'Unknown Driver';
    const vehicleInfo = location.vehicle?.plate_number || 'Unknown Vehicle';
    const timeInfo = new Date(location.recorded_at).toLocaleTimeString();
    
    // Create marker popup content
    const popupContent = `
        <div class="font-medium">${driverName}</div>
        <div>Vehicle: ${vehicleInfo}</div>
        <div>Last updated: ${timeInfo}</div>
        <div>Speed: ${location.speed ? location.speed + ' km/h' : 'N/A'}</div>
    `;
    
    if (markers.value[driverKey]) {
        // Update existing marker
        markers.value[driverKey].setLatLng(position);
        markers.value[driverKey].getPopup().setContent(popupContent);
    } else {
        // Create new marker
        const marker = L.marker(position)
            .addTo(map.value)
            .bindPopup(popupContent);
            
        markers.value[driverKey] = marker;
    }
}
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Live Tracking Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Stats Overview -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <!-- Active Drivers -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="text-sm font-medium text-gray-500">Active Drivers</div>
                        <div class="mt-1 text-3xl font-semibold text-gray-900">
                            {{ activeDriversCount }}
                        </div>
                        <div class="text-xs text-gray-400 mt-1">
                            Total: {{ stats.drivers.total }}
                        </div>
                    </div>
                    
                    <!-- Active Vehicles -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="text-sm font-medium text-gray-500">Active Vehicles</div>
                        <div class="mt-1 text-3xl font-semibold text-gray-900">
                            {{ activeVehiclesCount }}
                        </div>
                        <div class="text-xs text-gray-400 mt-1">
                            Total: {{ stats.vehicles.total }}
                        </div>
                    </div>
                    
                    <!-- Open Incidents -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="text-sm font-medium text-gray-500">Open Incidents</div>
                        <div class="mt-1 text-3xl font-semibold text-gray-900">
                            {{ openIncidentsCount }}
                        </div>
                        <div class="text-xs text-gray-400 mt-1">
                            Total: {{ stats.incidents.total }}
                        </div>
                    </div>
                    
                    <!-- Active SOS Alerts -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6" 
                         :class="{'bg-red-50': activeSosAlertsCount > 0}">
                        <div class="text-sm font-medium" :class="activeSosAlertsCount > 0 ? 'text-red-500' : 'text-gray-500'">
                            Active SOS Alerts
                        </div>
                        <div class="mt-1 text-3xl font-semibold" :class="activeSosAlertsCount > 0 ? 'text-red-600' : 'text-gray-900'">
                            {{ activeSosAlertsCount }}
                        </div>
                        <div class="text-xs mt-1" :class="activeSosAlertsCount > 0 ? 'text-red-400' : 'text-gray-400'">
                            Total: {{ stats.sos_alerts.total }}
                        </div>
                    </div>
                </div>
                
                <!-- Refresh Button - More subtle styling -->
                <div class="flex justify-end mb-6">
                    <button 
                        @click="refreshDashboardData" 
                        class="px-3 py-1 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 text-sm flex items-center"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Refresh
                    </button>
                </div>
                
                <!-- Map -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div id="map" class="w-full h-[600px] rounded-lg"></div>
                </div>
                
                <!-- Recent Activities -->
                <div class="mt-6 bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activities</h3>
                    
                    <div v-if="locations.length === 0" class="text-gray-500">
                        No recent activities found.
                    </div>
                    
                    <div v-else class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Driver</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Seen</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Speed</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="location in locations" :key="location.id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ location.driver?.user?.name || 'Unknown' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ location.vehicle?.plate_number || 'Unknown' }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ location.vehicle?.model || '' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ new Date(location.recorded_at).toLocaleTimeString() }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ new Date(location.recorded_at).toLocaleDateString() }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ location.speed ? `${location.speed} km/h` : 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style>
@import 'https://unpkg.com/leaflet@1.8.0/dist/leaflet.css';
</style>