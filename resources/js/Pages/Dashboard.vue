<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';

const props = defineProps({
    activeDrivers: Array,
    openIncidentsCount: Number,
    activeSosAlertsCount: Number,
    activeDriversCount: Number
});

const locations = ref(props.activeDrivers || []);
const map = ref(null);
const markers = ref({});
const refreshInterval = ref(null);
const vendorFilter = ref('');

// Initialize with prop values and provide defaults
const activeDriversCount = ref(props.activeDriversCount || 0);
const activeVehiclesCount = ref(0);
const openIncidentsCount = ref(props.openIncidentsCount || 0);
const activeSosAlertsCount = ref(props.activeSosAlertsCount || 0);

onMounted(async () => {
    // Wait for the DOM to be ready
    await nextTick();
    
    // Initialize the map after a short delay to ensure Leaflet is loaded
    setTimeout(() => {
        initMap();
    }, 100);
    
    // Get initial stats
    await refreshDashboardData();
    
    // Auto-refresh every 30 seconds
    refreshInterval.value = setInterval(refreshDashboardData, 30000);
});

onUnmounted(() => {
    if (refreshInterval.value) {
        clearInterval(refreshInterval.value);
    }
});

async function refreshDashboardData() {
    try {
        const params = vendorFilter.value ? { vendor_id: vendorFilter.value } : {};
        
        // Get locations with vendor filter
        const locationsResponse = await axios.get('/api/locations/latest', {
            params,
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            withCredentials: true
        });
        
        locations.value = locationsResponse.data;
        
        // Update markers on map
        if (map.value && locations.value.length > 0) {
            locations.value.forEach(location => {
                addOrUpdateMarker(location);
            });
        }
        
        // Get updated stats
        const statsResponse = await axios.get('/api/dashboard/stats', {
            params,
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            withCredentials: true
        });
        
        if (statsResponse.data) {
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
    // Check if Leaflet is available
    if (!window.L) {
        setTimeout(initMap, 500);
        return;
    }
    
    try {
        // Initialize map with better settings
        map.value = L.map('map', {
            center: [3.1319, 101.6841], // Kuala Lumpur coordinates
            zoom: 12,
            zoomControl: true,
            attributionControl: true
        });
        
        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19,
            minZoom: 5
        }).addTo(map.value);
        
        // Add initial markers for existing locations
        if (locations.value && locations.value.length > 0) {
            locations.value.forEach(location => {
                addOrUpdateMarker(location);
            });
            
            // Fit map to show all markers
            if (Object.keys(markers.value).length > 0) {
                const group = new L.featureGroup(Object.values(markers.value));
                map.value.fitBounds(group.getBounds().pad(0.1));
            }
        }
    } catch (error) {
        console.error('Error initializing map:', error);
    }
}

function addOrUpdateMarker(location) {
    if (!map.value) {
        return;
    }
    
    const driverKey = `driver-${location.driver_id}`;
    const position = [parseFloat(location.latitude), parseFloat(location.longitude)];
    const driverName = location.driver?.user?.name || 'Unknown Driver';
    const vehicleInfo = location.vehicle?.plate_number || 'Unknown Vehicle';
    const timeInfo = new Date(location.recorded_at).toLocaleTimeString();
    
    // Create custom icon for better visibility
    const driverIcon = L.divIcon({
        html: `
            <div style="
                background-color: #3b82f6;
                border: 3px solid white;
                border-radius: 50%;
                width: 24px;
                height: 24px;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 2px 6px rgba(0,0,0,0.3);
                color: white;
                font-weight: bold;
                font-size: 12px;
            ">
                🚗
            </div>
        `,
        className: 'driver-marker',
        iconSize: [24, 24],
        iconAnchor: [12, 12],
        popupAnchor: [0, -12]
    });
    
    // Create enhanced popup content
    const popupContent = `
        <div style="min-width: 200px;">
            <div style="font-weight: bold; font-size: 14px; margin-bottom: 5px; color: #1f2937;">
                ${driverName}
            </div>
            <div style="margin-bottom: 3px;">
                <strong>Vehicle:</strong> ${vehicleInfo}
            </div>
            <div style="margin-bottom: 3px;">
                <strong>Last Update:</strong> ${timeInfo}
            </div>
            <div style="margin-bottom: 3px;">
                <strong>Speed:</strong> ${location.speed ? Math.round(location.speed * 3.6) + ' km/h' : 'N/A'}
            </div>
            <div style="margin-bottom: 3px;">
                <strong>Location:</strong> ${parseFloat(location.latitude).toFixed(6)}, ${parseFloat(location.longitude).toFixed(6)}
            </div>
        </div>
    `;
    
    if (markers.value[driverKey]) {
        // Update existing marker position and popup
        markers.value[driverKey].setLatLng(position);
        markers.value[driverKey].getPopup().setContent(popupContent);
    } else {
        // Create new marker
        try {
            const marker = L.marker(position, { icon: driverIcon })
                .addTo(map.value)
                .bindPopup(popupContent);
                
            markers.value[driverKey] = marker;
        } catch (error) {
            console.error('Error creating marker:', error);
        }
    }
}
</script>

<template>
    <Head>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css" />
    </Head>

    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Live Tracking Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Vendor Filter -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                    <div class="flex items-center space-x-4">
                        <label for="vendor-filter" class="text-sm font-medium text-gray-700">Filter by Vendor:</label>
                        <select 
                            id="vendor-filter"
                            v-model="vendorFilter" 
                            @change="refreshDashboardData"
                            class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">All Vendors</option>
                            <!-- Add vendor options here - you may need to fetch these from your API -->
                        </select>
                    </div>
                </div>

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
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6" 
                         :class="{'bg-red-50': activeSosAlertsCount > 0}">
                        <div class="text-sm font-medium" :class="activeSosAlertsCount > 0 ? 'text-red-500' : 'text-gray-500'">
                            Active SOS Alerts
                        </div>
                        <div class="mt-1 text-3xl font-semibold" :class="activeSosAlertsCount > 0 ? 'text-red-600' : 'text-gray-900'">
                            {{ activeSosAlertsCount }}
                        </div>
                    </div>
                </div>
                
                <!-- Refresh Button -->
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
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Live Vehicle Tracking</h3>
                    <div id="map" class="w-full h-[600px] rounded-lg border"></div>
                </div>
                
                <!-- Recent Activities -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activities</h3>
                    
                    <div v-if="locations.length === 0" class="text-gray-500 text-center py-8">
                        <div class="text-6xl mb-4">📍</div>
                        <div class="text-lg font-medium mb-2">No Active Drivers</div>
                        <div class="text-sm">No drivers are currently sending location updates.</div>
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
                                <tr v-for="location in locations" :key="location.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <span class="text-blue-600 font-medium text-sm">
                                                        {{ (location.driver?.user?.name || 'U').charAt(0).toUpperCase() }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ location.driver?.user?.name || 'Unknown Driver' }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ location.driver?.user?.email || 'No email' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ location.vehicle?.plate_number || 'Unknown' }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ location.vehicle?.model || 'Unknown Model' }}
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
                                            {{ location.speed ? `${Math.round(location.speed)} km/h` : 'N/A' }}
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