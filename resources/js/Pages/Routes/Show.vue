<template>
    <AppLayout title="Route Details">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Route: {{ routeData.name }}
                </h2>
                <div class="flex space-x-2">
                    <Link :href="$route('routes.edit', routeData.id)" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        Edit Route
                    </Link>
                    <button @click="confirmDelete" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                        Delete Route
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Route Details -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Basic Information -->
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Route Information</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <h4 class="text-xl font-semibold text-gray-900">{{ routeData.name }}</h4>
                                    <p v-if="routeData.description" class="text-gray-600 mt-1">{{ routeData.description }}</p>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4">
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Distance</span>
                                        <p class="text-lg font-semibold text-gray-900">
                                            {{ routeData.distance_km ? `${routeData.distance_km} km` : 'N/A' }}
                                        </p>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Estimated Duration</span>
                                        <p class="text-lg font-semibold text-gray-900">
                                            {{ formatDuration(routeData.estimated_duration_minutes) }}
                                        </p>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Number of Stops</span>
                                        <p class="text-lg font-semibold text-gray-900">
                                            {{ getStopsCount(routeData) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Route Stops -->
                        <div v-if="routeData.stops && routeData.stops.length > 0" class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Route Stops</h3>
                                <span class="text-sm text-gray-500">{{ routeData.stops.length }} stops</span>
                            </div>
                            
                            <div class="space-y-4">
                                <div v-for="(stop, index) in routeData.stops" :key="index" class="flex items-start">
                                    <div class="flex-shrink-0 mr-4">
                                        <div class="flex items-center justify-center w-8 h-8 rounded-full" 
                                             :class="getStopNumberClass(index, routeData.stops.length)">
                                            <span class="text-sm font-medium text-white">{{ index + 1 }}</span>
                                        </div>
                                        <div v-if="index < routeData.stops.length - 1" class="w-0.5 h-8 bg-gray-300 mx-auto mt-2"></div>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-900">{{ stop.name || `Stop ${index + 1}` }}</h4>
                                        <p class="text-xs text-gray-500 font-mono">
                                            {{ stop.lat }}, {{ stop.lng }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Waypoints -->
                        <div v-if="routeData.waypoints && routeData.waypoints.length > 0" class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Waypoints</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div v-for="(waypoint, index) in routeData.waypoints" :key="index" class="p-3 bg-gray-50 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-900">Waypoint {{ index + 1 }}</h4>
                                    <p class="text-xs text-gray-500 font-mono">
                                        {{ waypoint.lat }}, {{ waypoint.lng }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Route Map -->
                    <div class="lg:col-span-1">
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 sticky top-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Route Map</h3>
                            <div id="route-detail-map" class="w-full h-96 rounded-lg border"></div>
                        </div>
                    </div>
                </div>

                <!-- Assigned Schedules -->
                <div v-if="routeData.schedules && routeData.schedules.length > 0" class="mt-6">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Assigned Schedules</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Driver</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="schedule in routeData.schedules" :key="schedule.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ schedule.driver?.user?.name || 'Unknown' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ schedule.vehicle?.plate_number || 'Unknown' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 capitalize">
                                            {{ schedule.day_of_week }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ schedule.departure_time }} - {{ schedule.arrival_time }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="schedule.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'" 
                                                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                {{ schedule.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Back Button -->
                <div class="mt-6">
                    <Link :href="$route('routes.index')" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        ← Back to Routes
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { onMounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    route: Object,
});

// Rename to avoid conflict with route() function
const routeData = props.route;

let map = null;

onMounted(() => {
    initMap();
});

function initMap() {
    if (!window.L) {
        setTimeout(initMap, 500);
        return;
    }
    
    try {
        // Initialize map
        map = L.map('route-detail-map').setView([3.1319, 101.6841], 12); // Default to KL
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        // Add route stops if available
        if (routeData.stops && routeData.stops.length > 0) {
            const bounds = L.latLngBounds();
            
            routeData.stops.forEach((stop, index) => {
                if (stop.lat && stop.lng) {
                    const lat = parseFloat(stop.lat);
                    const lng = parseFloat(stop.lng);
                    
                    // Create stop marker
                    const stopIcon = L.divIcon({
                        html: `<div style="
                            background-color: ${getStopColor(index, routeData.stops.length)};
                            color: white;
                            width: 30px;
                            height: 30px;
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-weight: bold;
                            font-size: 12px;
                            border: 2px solid white;
                            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
                        ">${index + 1}</div>`,
                        className: '',
                        iconSize: [30, 30]
                    });
                    
                    L.marker([lat, lng], { icon: stopIcon })
                        .addTo(map)
                        .bindPopup(`
                            <div class="font-bold">${stop.name || `Stop ${index + 1}`}</div>
                            <div class="text-sm font-mono">${lat}, ${lng}</div>
                        `);
                    
                    bounds.extend([lat, lng]);
                }
            });
            
            // Draw route line if there are multiple stops
            if (routeData.stops.length > 1) {
                const routePoints = routeData.stops
                    .filter(stop => stop.lat && stop.lng)
                    .map(stop => [parseFloat(stop.lat), parseFloat(stop.lng)]);
                
                if (routePoints.length > 1) {
                    L.polyline(routePoints, {
                        color: '#3b82f6',
                        weight: 4,
                        opacity: 0.7
                    }).addTo(map);
                }
            }
            
            // Fit map to show all stops
            if (bounds.isValid()) {
                map.fitBounds(bounds, { padding: [20, 20] });
            }
        }
        
        // Add waypoints if available
        if (routeData.waypoints && routeData.waypoints.length > 0) {
            routeData.waypoints.forEach((waypoint, index) => {
                if (waypoint.lat && waypoint.lng) {
                    const lat = parseFloat(waypoint.lat);
                    const lng = parseFloat(waypoint.lng);
                    
                    L.circleMarker([lat, lng], {
                        radius: 5,
                        fillColor: '#f59e0b',
                        color: '#ffffff',
                        weight: 2,
                        opacity: 1,
                        fillOpacity: 0.8
                    }).addTo(map)
                    .bindPopup(`<div class="text-sm">Waypoint ${index + 1}</div>`);
                }
            });
        }
    } catch (error) {
        console.error('Error initializing map:', error);
    }
}

function formatDuration(minutes) {
    if (!minutes) return 'N/A';
    
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;
    
    if (hours > 0) {
        return `${hours}h ${mins > 0 ? `${mins}m` : ''}`;
    } else {
        return `${mins}m`;
    }
}

function getStopsCount(route) {
    return route.stops ? route.stops.length : 0;
}

function getStopNumberClass(index, total) {
    if (index === 0) return 'bg-green-500';
    if (index === total - 1) return 'bg-red-500';
    return 'bg-blue-500';
}

function getStopColor(index, total) {
    if (index === 0) return '#10b981'; // green
    if (index === total - 1) return '#ef4444'; // red
    return '#3b82f6'; // blue
}

function confirmDelete() {
    if (confirm(`Are you sure you want to delete the route "${routeData.name}"? This action cannot be undone.`)) {
        router.delete($route('routes.destroy', routeData.id));
    }
}

// Helper function to access the route helper
function $route(name, params) {
    return route(name, params);
}
</script>