<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DashboardCard from '@/Components/DashboardCard.vue';
import ModernTable from '@/Components/ModernTable.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { useToast } from '@/Composables/useToast.js';
import axios from 'axios';
import { 
    UserGroupIcon, 
    TruckIcon, 
    ExclamationTriangleIcon, 
    ShieldExclamationIcon,
    MapIcon,
    ClockIcon
} from '@heroicons/vue/24/outline';

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

// Initialize with prop values and provide defaults
const activeDriversCount = ref(props.activeDriversCount || 0);
const activeVehiclesCount = ref(0);
const openIncidentsCount = ref(props.openIncidentsCount || 0);
const activeSosAlertsCount = ref(props.activeSosAlertsCount || 0);

const { success, error, warning, sosAlert } = useToast();

onMounted(async () => {
    await nextTick();
    setTimeout(() => {
        initMap();
    }, 100);
    
    await refreshDashboardData();
    
    if (activeSosAlertsCount.value > 0) {
        sosAlert(`${activeSosAlertsCount.value} active SOS alert(s) require immediate attention!`);
    }
    
    refreshInterval.value = setInterval(refreshDashboardData, 30000);
});

onUnmounted(() => {
    if (refreshInterval.value) {
        clearInterval(refreshInterval.value);
    }
});

async function refreshDashboardData() {
    try {
        const locationsResponse = await axios.get('/api/locations/latest', {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            withCredentials: true
        });
        
        locations.value = locationsResponse.data;
        
        if (map.value && locations.value.length > 0) {
            locations.value.forEach(location => {
                addOrUpdateMarker(location);
            });
        }
        
        const statsResponse = await axios.get('/api/dashboard/stats', {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            withCredentials: true
        });
        
        if (statsResponse.data) {
            const previousSosCount = activeSosAlertsCount.value;
            
            activeDriversCount.value = statsResponse.data.activeDriversCount || 0;
            activeVehiclesCount.value = statsResponse.data.activeVehiclesCount || 0;
            openIncidentsCount.value = statsResponse.data.openIncidentsCount || 0;
            activeSosAlertsCount.value = statsResponse.data.activeSosAlertsCount || 0;
            
            if (activeSosAlertsCount.value > previousSosCount) {
                sosAlert(`${activeSosAlertsCount.value} active SOS alert(s) require immediate attention!`);
            }
        }
    } catch (error) {
        console.error('Error refreshing dashboard data:', error);
    }
}

function initMap() {
    if (!window.L) {
        setTimeout(initMap, 500);
        return;
    }
    
    try {
        map.value = L.map('map', {
            center: [3.1319, 101.6841],
            zoom: 12,
            zoomControl: true,
            attributionControl: true
        });
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19,
            minZoom: 5
        }).addTo(map.value);
        
        if (locations.value && locations.value.length > 0) {
            locations.value.forEach(location => {
                addOrUpdateMarker(location);
            });
            
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
        markers.value[driverKey].setLatLng(position);
        markers.value[driverKey].getPopup().setContent(popupContent);
    } else {
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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Dashboard Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="card-modern p-6 hover-lift">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Active Drivers</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ activeDriversCount }}</p>
                            <div class="flex items-center mt-2">
                                <div class="flex items-center text-green-500 text-sm">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    +12%
                                </div>
                                <span class="text-gray-500 text-sm ml-2">vs last week</span>
                            </div>
                        </div>
                        <div class="p-3 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl">
                            <UserGroupIcon class="w-8 h-8 text-white" />
                        </div>
                    </div>
                </div>

                <div class="card-modern p-6 hover-lift">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Active Vehicles</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ activeVehiclesCount }}</p>
                            <div class="flex items-center mt-2">
                                <div class="flex items-center text-green-500 text-sm">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    +8%
                                </div>
                                <span class="text-gray-500 text-sm ml-2">vs last week</span>
                            </div>
                        </div>
                        <div class="p-3 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl">
                            <TruckIcon class="w-8 h-8 text-white" />
                        </div>
                    </div>
                </div>

                <div class="card-modern p-6 hover-lift">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Open Incidents</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ openIncidentsCount }}</p>
                            <div class="flex items-center mt-2">
                                <div class="flex items-center text-red-500 text-sm">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    -5%
                                </div>
                                <span class="text-gray-500 text-sm ml-2">vs last week</span>
                            </div>
                        </div>
                        <div class="p-3 bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl">
                            <ExclamationTriangleIcon class="w-8 h-8 text-white" />
                        </div>
                    </div>
                </div>

                <div class="card-modern p-6 hover-lift">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">SOS Alerts</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ activeSosAlertsCount }}</p>
                            <div class="flex items-center mt-2">
                                <div :class="activeSosAlertsCount > 0 ? 'text-red-500' : 'text-green-500'" class="flex items-center text-sm">
                                    <div :class="activeSosAlertsCount > 0 ? 'bg-red-500' : 'bg-green-500'" class="w-2 h-2 rounded-full mr-2"></div>
                                    {{ activeSosAlertsCount > 0 ? 'Active' : 'Normal' }}
                                </div>
                                <span class="text-gray-500 text-sm ml-2">status</span>
                            </div>
                        </div>
                        <div :class="activeSosAlertsCount > 0 ? 'bg-gradient-to-r from-red-500 to-red-600' : 'bg-gradient-to-r from-gray-400 to-gray-500'" class="p-3 rounded-xl">
                            <ShieldExclamationIcon class="w-8 h-8 text-white" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Live Map -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <MapIcon class="h-5 w-5 text-gray-400 mr-2" />
                                    <h3 class="text-lg font-semibold text-gray-900">Live Vehicle Tracking</h3>
                                </div>
                                <button 
                                    @click="refreshDashboardData" 
                                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                >
                                    <ClockIcon class="h-4 w-4 mr-1.5" />
                                    Refresh
                                </button>
                            </div>
                        </div>
                        <div id="map" class="h-96"></div>
                    </div>
                </div>
                
                <!-- Quick Stats -->
                <div class="space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <Link :href="route('drivers.create')" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Add New Driver
                            </Link>
                            <Link :href="route('vehicles.create')" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Add New Vehicle
                            </Link>
                            <Link :href="route('routes.create')" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Create Route
                            </Link>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">System Status</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">GPS Tracking</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Online
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">SMS Gateway</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">API Status</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Healthy
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Activities</h3>
                    <p class="text-sm text-gray-600">Live tracking data from active drivers</p>
                </div>
                
                <div class="overflow-x-auto">
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
                            <tr v-for="location in locations.slice(0, 10)" :key="location.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center">
                                            <span class="text-white font-medium text-sm">
                                                {{ (location.driver?.user?.name || 'U').charAt(0).toUpperCase() }}
                                            </span>
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
                                    <div class="text-sm font-medium text-gray-900">{{ location.vehicle?.plate_number || 'Unknown' }}</div>
                                    <div class="text-sm text-gray-500">{{ location.vehicle?.model || 'Unknown Model' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ new Date(location.recorded_at).toLocaleTimeString() }}</div>
                                    <div class="text-sm text-gray-500">{{ new Date(location.recorded_at).toLocaleDateString() }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ location.speed ? `${Math.round(location.speed)} km/h` : 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <StatusBadge status="active" />
                                </td>
                            </tr>
                            
                            <tr v-if="locations.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <TruckIcon class="h-12 w-12 text-gray-300 mb-4" />
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Active Drivers</h3>
                                        <p class="text-gray-500">No drivers are currently sending location updates.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>