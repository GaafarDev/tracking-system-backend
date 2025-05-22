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
// Create reactive refs for the counts
const openIncidentsCount = ref(props.openIncidentsCount || 0);
const activeSosAlertsCount = ref(props.activeSosAlertsCount || 0);
const activeDriversCount = ref(0);
const activeVehiclesCount = ref(0);

onMounted(() => {
    initMap();
    // Removed the auto-refresh temporarily
    // refreshDashboardData(); 
    // refreshInterval.value = setInterval(refreshDashboardData, 10000);
});

onUnmounted(() => {
    if (refreshInterval.value) {
        clearInterval(refreshInterval.value);
    }
});

// Simplified refreshDashboardData function without auto-calling
async function refreshDashboardData() {
    try {
        console.log('Manually refreshing dashboard data...');
        
        // Get locations
        const locationsResponse = await axios.get('/api/locations/latest');
        locations.value = locationsResponse.data;
        
        // Update markers
        locations.value.forEach(location => {
            addOrUpdateMarker(location);
        });
        
        // Get updated stats
        const statsResponse = await axios.get('/api/dashboard/stats');
        if (statsResponse.data) {
            activeDriversCount.value = statsResponse.data.activeDriversCount || 0;
            activeVehiclesCount.value = statsResponse.data.activeVehiclesCount || 0;
            openIncidentsCount.value = statsResponse.data.openIncidentsCount || 0;
            activeSosAlertsCount.value = statsResponse.data.activeSosAlertsCount || 0;
            
            console.log('Updated dashboard stats:', statsResponse.data);
        }
    } catch (error) {
        console.error('Error refreshing dashboard data:', error);
        if (error.response?.status === 401) {
            console.log('Authentication required for dashboard data');
        }
    }
}

function initMap() {
    // Using Leaflet for maps
    if (!window.L) {
        console.error('Leaflet library not loaded');
        return;
    }
    
    map.value = L.map('map').setView([4.2105, 101.9758], 10); // Center on Malaysia
    
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

// Keep this for backward compatibility or specific location-only updates
async function fetchLatestLocations() {
    try {
        const config = {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            withCredentials: true
        };
        
        const response = await axios.get('/api/locations/latest', config);
        locations.value = response.data;
        
        // Update markers
        locations.value.forEach(location => {
            addOrUpdateMarker(location);
        });
    } catch (error) {
        console.error('Error fetching latest locations:', error);
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
                    </div>
                    
                    <!-- Active Vehicles -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="text-sm font-medium text-gray-500">Active Vehicles</div>
                        <div class="mt-1 text-3xl font-semibold text-gray-900">
                            {{ activeVehiclesCount }}
                        </div>
                    </div>
                    
                    <!-- Open Incidents -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="text-sm font-medium text-gray-500">Open Incidents</div>
                        <div class="mt-1 text-3xl font-semibold text-gray-900">
                            {{ openIncidentsCount }}
                        </div>
                    </div>
                    
                    <!-- Active SOS Alerts -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 bg-red-50">
                        <div class="text-sm font-medium text-red-500">Active SOS Alerts</div>
                        <div class="mt-1 text-3xl font-semibold text-red-600">
                            {{ activeSosAlertsCount }}
                        </div>
                    </div>
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