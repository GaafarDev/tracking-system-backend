<script setup>
import { ref, onMounted, onUnmounted, nextTick, computed } from 'vue';
import { Head, router, Link } from '@inertiajs/vue3';
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
    ClockIcon,
    ChevronLeftIcon,
    ChevronRightIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    activeDrivers: Array,
    openIncidentsCount: Number,
    activeSosAlertsCount: Number,
    activeDriversCount: Number,
    activeVehiclesCount: Number,
    schedules: Array,
    dashboardStats: Object // New prop for dynamic stats
});

const locations = ref(props.activeDrivers || []);
const map = ref(null);
const markers = ref({});
const refreshInterval = ref(null);

// Use dynamic stats from backend instead of hardcoded values
const activeDriversCount = ref(props.dashboardStats?.activeDriversCount || props.activeDriversCount || 0);
const activeVehiclesCount = ref(props.dashboardStats?.activeVehiclesCount || props.activeVehiclesCount || 0);
const openIncidentsCount = ref(props.dashboardStats?.openIncidentsCount || props.openIncidentsCount || 0);
const activeSosAlertsCount = ref(props.dashboardStats?.activeSosAlertsCount || props.activeSosAlertsCount || 0);

// Dynamic percentage changes from backend
const statsChanges = computed(() => props.dashboardStats?.changes || {
    drivers: { value: 0, isPositive: true },
    vehicles: { value: 0, isPositive: true },
    incidents: { value: 0, isPositive: false },
    sos: { value: 0, isPositive: true }
});

const { success, error, warning, sosAlert } = useToast();

// Weekly schedule calendar
const weekDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
const currentWeek = ref(0);

// Get unique vehicles for schedule display
const uniqueVehicles = computed(() => {
    if (!props.schedules) return [];
    const vehicles = new Map();
    props.schedules.forEach(schedule => {
        if (schedule.vehicle) {
            vehicles.set(schedule.vehicle.id, schedule.vehicle);
        }
    });
    return Array.from(vehicles.values());
});

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

// Schedule calendar functions
function getSchedulesForVehicleAndDay(vehicleId, day) {
    if (!props.schedules) return [];
    return props.schedules.filter(schedule => 
        schedule.vehicle?.id === vehicleId && 
        schedule.day_of_week === day.toLowerCase() &&
        schedule.is_active
    );
}

function getScheduleColorClass(schedule) {
    const colors = [
        'bg-gradient-to-r from-primary-500 to-primary-600',
        'bg-gradient-to-r from-blue-500 to-blue-600',
        'bg-gradient-to-r from-green-500 to-green-600',
        'bg-gradient-to-r from-purple-500 to-purple-600',
        'bg-gradient-to-r from-secondary-500 to-secondary-600',
    ];
    return colors[schedule.id % colors.length];
}

function previousWeek() {
    currentWeek.value = Math.max(0, currentWeek.value - 1);
}

function nextWeek() {
    currentWeek.value = currentWeek.value + 1;
}

function formatStatChange(change) {
    if (!change || change.value === 0) return '';
    const sign = change.isPositive ? '+' : '';
    return `${sign}${change.value}%`;
}

function getStatChangeClass(change) {
    if (!change || change.value === 0) return 'text-gray-500';
    return change.isPositive ? 'text-green-500' : 'text-red-500';
}
</script>

<template>
    <Head>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css" />
    </Head>

    <AppLayout title="Dashboard">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Dashboard Overview Cards with Dynamic Data -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <DashboardCard
                    title="Active Drivers"
                    :value="activeDriversCount"
                    :icon="UserGroupIcon"
                    icon-color="blue"
                    :animation-delay="0"
                    :subtitle="`${formatStatChange(statsChanges.drivers)} vs last week`"
                    :subtitle-type="statsChanges.drivers.isPositive ? 'success' : 'danger'"
                />
                
                <DashboardCard
                    title="Active Vehicles"
                    :value="activeVehiclesCount"
                    :icon="TruckIcon"
                    icon-color="green"
                    :animation-delay="100"
                    :subtitle="`${formatStatChange(statsChanges.vehicles)} vs last week`"
                    :subtitle-type="statsChanges.vehicles.isPositive ? 'success' : 'danger'"
                />
                
                <DashboardCard
                    title="Open Incidents"
                    :value="openIncidentsCount"
                    :icon="ExclamationTriangleIcon"
                    icon-color="yellow"
                    :animation-delay="200"
                    :subtitle="`${formatStatChange(statsChanges.incidents)} vs last week`"
                    :subtitle-type="statsChanges.incidents.isPositive ? 'danger' : 'success'"
                />
                
                <DashboardCard
                    title="SOS Alerts"
                    :value="activeSosAlertsCount"
                    :icon="ShieldExclamationIcon"
                    :icon-color="activeSosAlertsCount > 0 ? 'red' : 'gray'"
                    :animation-delay="300"
                    :subtitle="activeSosAlertsCount > 0 ? 'Immediate attention required!' : 'All clear'"
                    :subtitle-type="activeSosAlertsCount > 0 ? 'danger' : 'success'"
                />
            </div>

            <!-- Main Content Grid - Improved Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Expanded Live Map - Now takes more space -->
                <div class="lg:col-span-2">
                    <div class="card-modern">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-primary-50 to-secondary-50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="p-3 bg-blue-100 rounded-xl">
                                        <MapIcon class="w-6 h-6 text-blue-600" />
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-xl font-bold text-gray-900">Live Vehicle Tracking</h3>
                                        <p class="text-sm text-gray-600">Real-time driver locations and status</p>
                                    </div>
                                </div>
                                <button 
                                    @click="refreshDashboardData" 
                                    class="btn-secondary-modern"
                                >
                                    <ClockIcon class="h-4 w-4 mr-1.5" />
                                    Refresh
                                </button>
                            </div>
                        </div>
                        <div id="map" class="h-96"></div>
                    </div>
                </div>
                
                <!-- Weekly Schedule Preview -->
                <div class="lg:col-span-1">
                    <div class="card-modern">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-primary-50 to-secondary-50">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Weekly Schedule</h3>
                                    <p class="text-sm text-gray-600">Current week overview</p>
                                </div>
                                <Link :href="route('schedules.index')" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                                    View All →
                                </Link>
                            </div>
                            
                            <!-- Week Navigation -->
                            <div class="flex items-center justify-between">
                                <button @click="previousWeek" class="p-2 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-200">
                                    <ChevronLeftIcon class="w-4 h-4 text-gray-600" />
                                </button>
                                <span class="text-sm font-medium text-gray-900">Week {{ currentWeek + 1 }}</span>
                                <button @click="nextWeek" class="p-2 rounded-lg hover:bg-white hover:shadow-sm transition-all duration-200">
                                    <ChevronRightIcon class="w-4 h-4 text-gray-600" />
                                </button>
                            </div>
                        </div>

                        <!-- Mini Calendar Grid -->
                        <div class="p-4">
                            <div class="space-y-3">
                                <div v-for="day in weekDays.slice(0, 5)" :key="day" class="border-b border-gray-100 pb-2 last:border-b-0">
                                    <h4 class="text-xs font-medium text-gray-500 mb-2">{{ day }}</h4>
                                    <div class="space-y-1">
                                        <template v-for="vehicle in uniqueVehicles.slice(0, 3)" :key="vehicle.id">
                                            <div 
                                                v-for="schedule in getSchedulesForVehicleAndDay(vehicle.id, day).slice(0, 2)" 
                                                :key="`${schedule.id}-${vehicle.id}`"
                                                :class="getScheduleColorClass(schedule)"
                                                class="text-xs text-white p-2 rounded-lg"
                                            >
                                                <div class="font-medium truncate">{{ schedule.driver?.user?.name }}</div>
                                                <div class="opacity-90">{{ schedule.departure_time }}</div>
                                            </div>
                                        </template>
                                        <div v-if="getSchedulesForVehicleAndDay(uniqueVehicles[0]?.id, day).length === 0" class="text-xs text-gray-400 p-2">
                                            No schedules
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities Table -->
            <div class="card-modern">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-primary-50 to-secondary-50">
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
                            <tr v-for="location in locations.slice(0, 10)" :key="location.id" class="hover:bg-gray-50 transition-colors duration-150">
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