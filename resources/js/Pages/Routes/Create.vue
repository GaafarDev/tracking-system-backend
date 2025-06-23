<template>
    <AppLayout title="Create Route">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gradient-primary">Create New Route</h1>
                        <p class="text-gray-600 mt-2">Define a new transportation route with stops and waypoints</p>
                    </div>
                    <Link :href="route('routes.index')" class="btn-secondary">
                        <ArrowLeftIcon class="w-4 h-4 mr-2" />
                        Back to Routes
                    </Link>
                </div>
            </div>

            <!-- Form Card with Modern Design -->
            <div class="card-modern">
                <!-- Form Header -->
                <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-primary-50 to-secondary-50">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-xl">
                            <MapIcon class="w-6 h-6 text-blue-600" />
                        </div>
                        <div class="ml-4">
                            <h2 class="text-xl font-semibold text-gray-900">Route Information</h2>
                            <p class="text-sm text-gray-600 mt-1">Enter the details of the new route including stops and waypoints</p>
                        </div>
                    </div>
                </div>

                <!-- Form Content -->
                <form @submit.prevent="submit" class="p-6 bg-white">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Vendor Selection -->
                        <div class="md:col-span-2">
                            <label for="vendor_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <BuildingOfficeIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    Vendor
                                    <span class="text-red-500 ml-1">*</span>
                                </span>
                            </label>
                            <select
                                id="vendor_id"
                                v-model="form.vendor_id"
                                class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-white"
                                required
                            >
                                <option value="" class="text-gray-500">Select Vendor</option>
                                <option v-for="vendor in vendors" :key="vendor.id" :value="vendor.id" class="text-gray-900">
                                    {{ vendor.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.vendor_id" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.vendor_id }}
                            </p>
                        </div>

                        <!-- Route Name -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <TagIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    Route Name
                                    <span class="text-red-500 ml-1">*</span>
                                </span>
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-white placeholder-gray-400"
                                placeholder="Enter route name"
                                required
                            />
                            <p v-if="form.errors.name" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <!-- Distance -->
                        <div>
                            <label for="distance_km" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <MapPinIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    Distance (km)
                                </span>
                            </label>
                            <input
                                id="distance_km"
                                v-model="form.distance_km"
                                type="number"
                                step="0.1"
                                min="0"
                                class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-white placeholder-gray-400"
                                placeholder="0.0"
                            />
                            <p v-if="form.errors.distance_km" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.distance_km }}
                            </p>
                        </div>

                        <!-- Start Location -->
                        <div>
                            <label for="start_location" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <PlayIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    Start Location
                                    <span class="text-red-500 ml-1">*</span>
                                </span>
                            </label>
                            <input
                                id="start_location"
                                v-model="form.start_location"
                                type="text"
                                class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-white placeholder-gray-400"
                                placeholder="Enter starting point"
                                required
                            />
                            <p v-if="form.errors.start_location" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.start_location }}
                            </p>
                        </div>

                        <!-- End Location -->
                        <div>
                            <label for="end_location" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <StopIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    End Location
                                    <span class="text-red-500 ml-1">*</span>
                                </span>
                            </label>
                            <input
                                id="end_location"
                                v-model="form.end_location"
                                type="text"
                                class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-white placeholder-gray-400"
                                placeholder="Enter destination"
                                required
                            />
                            <p v-if="form.errors.end_location" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.end_location }}
                            </p>
                        </div>

                        <!-- Duration -->
                        <div>
                            <label for="estimated_duration_minutes" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <ClockIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    Duration (minutes)
                                </span>
                            </label>
                            <input
                                id="estimated_duration_minutes"
                                v-model="form.estimated_duration_minutes"
                                type="number"
                                min="1"
                                class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-white placeholder-gray-400"
                                placeholder="e.g., 60"
                            />
                            <p v-if="form.errors.estimated_duration_minutes" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.estimated_duration_minutes }}
                            </p>
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <DocumentTextIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    Description
                                </span>
                            </label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                class="form-textarea w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-white placeholder-gray-400 resize-y"
                                rows="3"
                                placeholder="Enter route description (optional)"
                            ></textarea>
                            <p v-if="form.errors.description" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <!-- Route Planning with Two Options -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <MapIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    Route Planning
                                </span>
                            </label>
                            
                            <!-- Route Type Selection -->
                            <div class="border border-gray-300 rounded-xl p-4 bg-gray-50">
                                <div class="flex space-x-4 mb-6">
                                    <button 
                                        type="button" 
                                        @click="setRouteType('google')"
                                        :class="routeType === 'google' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-300'"
                                        class="flex-1 px-4 py-3 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center justify-center"
                                    >
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                        </svg>
                                        Auto Route (Google Maps)
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="setRouteType('custom')"
                                        :class="routeType === 'custom' ? 'bg-green-600 text-white' : 'bg-white text-gray-700 border border-gray-300'"
                                        class="flex-1 px-4 py-3 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center justify-center"
                                    >
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                        </svg>
                                        Draw Custom Route
                                    </button>
                                </div>

                                <!-- Instructions -->
                                <div class="mb-4 p-3 rounded-lg" :class="routeType === 'google' ? 'bg-blue-50' : 'bg-green-50'">
                                    <p class="text-sm" :class="routeType === 'google' ? 'text-blue-800' : 'text-green-800'">
                                        <strong v-if="routeType === 'google'">Auto Route:</strong>
                                        <strong v-else>Custom Route:</strong>
                                        <span v-if="routeType === 'google'">
                                            Click to set start point, end point, and optional stops. Google Maps will calculate the best route automatically.
                                        </span>
                                        <span v-else>
                                            Draw your own route by clicking points on the map. Perfect for private roads or custom paths not on Google Maps.
                                        </span>
                                    </p>
                                </div>

                                <!-- Interactive Map -->
                                <div id="route-creation-map" class="w-full h-96 rounded-lg border bg-white mb-4"></div>

                                <!-- Route Information -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Points List -->
                                    <div class="bg-white rounded-lg p-4 border">
                                        <h4 class="font-medium text-gray-900 mb-3 flex items-center justify-between">
                                            <span class="flex items-center">
                                                <MapPinIcon class="w-4 h-4 mr-2 text-blue-600" />
                                                <span v-if="routeType === 'google'">Route Points ({{ routePoints.length }})</span>
                                                <span v-else>Custom Route Points ({{ routePoints.length }})</span>
                                            </span>
                                            <button 
                                                type="button" 
                                                @click="clearRoute"
                                                v-if="routePoints.length > 0"
                                                class="text-xs text-red-600 hover:text-red-800 px-2 py-1 rounded"
                                            >
                                                Clear All
                                            </button>
                                        </h4>
                                        
                                        <div v-if="routePoints.length === 0" class="text-center py-4 text-gray-500 text-sm">
                                            Click on the map to add points
                                        </div>
                                        
                                        <div v-else class="space-y-2 max-h-48 overflow-y-auto">
                                            <div 
                                                v-for="(point, index) in routePoints" 
                                                :key="index"
                                                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                                            >
                                                <div class="flex items-center">
                                                    <div :class="getPointColorClass(index)" class="w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-bold mr-3">
                                                        {{ index + 1 }}
                                                    </div>
                                                    <div>
                                                        <input
                                                            v-if="routeType === 'google' && (index === 0 || index === routePoints.length - 1 || point.isStop)"
                                                            v-model="point.name"
                                                            type="text"
                                                            :placeholder="getPointPlaceholder(index)"
                                                            class="text-sm font-medium bg-transparent border-none p-0 focus:ring-0 focus:outline-none w-full"
                                                        />
                                                        <span v-else class="text-sm font-medium">
                                                            {{ getPointPlaceholder(index) }}
                                                        </span>
                                                        <div class="text-xs text-gray-500 font-mono">
                                                            {{ parseFloat(point.lat).toFixed(6) }}, {{ parseFloat(point.lng).toFixed(6) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <button 
                                                    type="button" 
                                                    @click="removePoint(index)" 
                                                    class="text-red-600 hover:text-red-900 p-1 rounded transition-colors duration-200"
                                                >
                                                    <TrashIcon class="w-4 h-4" />
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Auto-calculated Info -->
                                    <div class="bg-white rounded-lg p-4 border">
                                        <h4 class="font-medium text-gray-900 mb-3 flex items-center">
                                            <ClockIcon class="w-4 h-4 mr-2 text-orange-600" />
                                            Route Information
                                        </h4>
                                        
                                        <div class="space-y-3">
                                            <!-- Auto-calculated route info -->
                                            <div class="space-y-2">
                                                <div class="flex justify-between text-sm">
                                                    <span class="text-gray-600">Distance:</span>
                                                    <span class="font-medium">{{ routeInfo.distance || 'Click "Calculate" below' }}</span>
                                                </div>
                                                <div class="flex justify-between text-sm">
                                                    <span class="text-gray-600">Duration:</span>
                                                    <span class="font-medium">{{ routeInfo.duration || 'Click "Calculate" below' }}</span>
                                                </div>
                                            </div>

                                            <!-- Calculate Button -->
                                            <button 
                                                type="button" 
                                                @click="calculateRoute"
                                                v-if="routePoints.length >= 2"
                                                class="w-full px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors duration-200 text-sm font-medium"
                                            >
                                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                </svg>
                                                Calculate Route
                                            </button>

                                            <!-- Route Type Info -->
                                            <div class="text-xs text-gray-500 pt-2 border-t">
                                                <span v-if="routeType === 'google'">
                                                    Using Google Maps routing for optimal path
                                                </span>
                                                <span v-else>
                                                    Custom route drawn manually
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <p v-if="form.errors.stops" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.stops }}
                            </p>
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <DocumentTextIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    Description
                                </span>
                            </label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                class="form-textarea w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-white placeholder-gray-400 resize-y"
                                rows="3"
                                placeholder="Enter route description (optional)"
                            ></textarea>
                            <p v-if="form.errors.description" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200 space-x-4">
                            <Link :href="route('routes.index')" class="btn-secondary-modern">
                                <XMarkIcon class="w-4 h-4 mr-2" />
                                Cancel
                            </Link>
                            <button type="submit" :disabled="form.processing" class="btn-primary">
                                <span v-if="form.processing" class="inline-flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Creating Route...
                                </span>
                                <span v-else class="inline-flex items-center">
                                    <PlusIcon class="w-4 h-4 mr-2" />
                                    Create Route
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { 
    ArrowLeftIcon,
    InformationCircleIcon,
    MapIcon,
    BuildingOfficeIcon,
    TagIcon,
    MapPinIcon,
    PlayIcon,
    StopIcon,
    ClockIcon,
    DocumentTextIcon,
    TrashIcon,
    PlusIcon,
    XMarkIcon,
    ExclamationCircleIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    vendors: Array
});

const form = useForm({
    vendor_id: '',
    name: '',
    start_location: '',
    end_location: '',
    description: '',
    distance_km: '',
    estimated_duration_minutes: '',
    stops: [],
    waypoints: []
});

// Update the reactive variables
const routeType = ref('google'); // 'google' or 'custom'
const map = ref(null);
const routeLayer = ref(null);
const markersLayer = ref(null);
const routePoints = ref([]);
const routeInfo = ref({
    distance: null,
    duration: null
});

onMounted(() => {
    initializeMap();
});

onUnmounted(() => {
    if (map.value) {
        map.value.remove();
    }
});

const initializeMap = () => {
    if (!window.L) {
        setTimeout(initializeMap, 500);
        return;
    }
    
    try {
        map.value = L.map('route-creation-map').setView([3.1319, 101.6841], 12);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map.value);
        
        // Initialize layers
        markersLayer.value = L.layerGroup().addTo(map.value);
        routeLayer.value = L.layerGroup().addTo(map.value);
        
        // Add click event listener
        map.value.on('click', handleMapClick);
        
    } catch (error) {
        console.error('Error initializing map:', error);
    }
};

const setRouteType = (type) => {
    routeType.value = type;
    clearRoute();
    
    // Update cursor style
    const mapContainer = map.value?.getContainer();
    if (mapContainer) {
        if (type === 'google') {
            mapContainer.style.cursor = 'crosshair';
        } else {
            mapContainer.style.cursor = 'copy';
        }
    }
};

const handleMapClick = (e) => {
    const { lat, lng } = e.latlng;
    
    const newPoint = {
        lat: lat.toFixed(6),
        lng: lng.toFixed(6),
        name: '',
        isStop: false
    };
    
    if (routeType.value === 'google') {
        // For Google routes: Start, Stops, End
        if (routePoints.value.length === 0) {
            newPoint.name = 'Start Location';
        } else {
            newPoint.name = `Stop ${routePoints.value.length}`;
            newPoint.isStop = true;
        }
    } else {
        // For custom routes: just point numbers
        newPoint.name = `Point ${routePoints.value.length + 1}`;
    }
    
    routePoints.value.push(newPoint);
    updateMapDisplay();
    updateFormData();
};

const calculateRoute = async () => {
    if (routePoints.value.length < 2) return;
    
    if (routeType.value === 'google') {
        // Try to use a routing service
        await calculateGoogleRoute();
    } else {
        // Calculate straight-line distance for custom routes
        calculateCustomRoute();
    }
};

const calculateGoogleRoute = async () => {
    try {
        // Using OpenRouteService as an example (free tier available)
        // You can replace this with Google Directions API if you have an API key
        const start = routePoints.value[0];
        const end = routePoints.value[routePoints.value.length - 1];
        const waypoints = routePoints.value.slice(1, -1);
        
        let url = `https://api.openrouteservice.org/v2/directions/driving-car?api_key=YOUR_API_KEY&start=${start.lng},${start.lat}&end=${end.lng},${end.lat}`;
        
        if (waypoints.length > 0) {
            const waypointCoords = waypoints.map(w => `${w.lng},${w.lat}`).join('|');
            url += `&waypoints=${waypointCoords}`;
        }
        
        // For now, let's use the fallback calculation
        calculateApproximateDistance();
        
    } catch (error) {
        console.error('Error calculating Google route:', error);
        calculateApproximateDistance();
    }
};

const calculateCustomRoute = () => {
    // For custom routes, calculate total distance along the drawn path
    let totalDistance = 0;
    
    for (let i = 0; i < routePoints.value.length - 1; i++) {
        const lat1 = parseFloat(routePoints.value[i].lat);
        const lng1 = parseFloat(routePoints.value[i].lng);
        const lat2 = parseFloat(routePoints.value[i + 1].lat);
        const lng2 = parseFloat(routePoints.value[i + 1].lng);
        
        totalDistance += calculateHaversineDistance(lat1, lng1, lat2, lng2);
    }
    
    const estimatedDuration = Math.round(totalDistance * 2.5); // Custom routes might be slower
    
    routeInfo.value.distance = `${totalDistance.toFixed(2)} km`;
    routeInfo.value.duration = `${estimatedDuration} min`;
    
    // Update form
    form.distance_km = totalDistance.toFixed(2);
    form.estimated_duration_minutes = estimatedDuration;
};

const calculateApproximateDistance = () => {
    if (routePoints.value.length < 2) return;
    
    let totalDistance = 0;
    for (let i = 0; i < routePoints.value.length - 1; i++) {
        const lat1 = parseFloat(routePoints.value[i].lat);
        const lng1 = parseFloat(routePoints.value[i].lng);
        const lat2 = parseFloat(routePoints.value[i + 1].lat);
        const lng2 = parseFloat(routePoints.value[i + 1].lng);
        
        totalDistance += calculateHaversineDistance(lat1, lng1, lat2, lng2);
    }
    
    const estimatedDuration = Math.round(totalDistance * 2); // 2 minutes per km average
    
    routeInfo.value.distance = `${totalDistance.toFixed(2)} km`;
    routeInfo.value.duration = `${estimatedDuration} min`;
    
    // Update form
    form.distance_km = totalDistance.toFixed(2);
    form.estimated_duration_minutes = estimatedDuration;
};

const calculateHaversineDistance = (lat1, lng1, lat2, lng2) => {
    const R = 6371; // Earth's radius in km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLng = (lng2 - lng1) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLng/2) * Math.sin(dLng/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
};

const updateFormData = () => {
    if (routeType.value === 'google') {
        // For Google routes: first and last are start/end, middle ones are stops
        form.start_location = routePoints.value[0]?.name || 'Start Location';
        form.end_location = routePoints.value[routePoints.value.length - 1]?.name || 'End Location';
        
        // Stops are all points except first and last
        form.stops = routePoints.value.map((point, index) => ({
            name: point.name || `Point ${index + 1}`,
            lat: point.lat,
            lng: point.lng
        }));
        
        form.waypoints = [];
    } else {
        // For custom routes: all points are waypoints
        form.start_location = routePoints.value[0]?.name || 'Custom Route Start';
        form.end_location = routePoints.value[routePoints.value.length - 1]?.name || 'Custom Route End';
        
        form.stops = [
            {
                name: form.start_location,
                lat: routePoints.value[0]?.lat || '',
                lng: routePoints.value[0]?.lng || ''
            },
            {
                name: form.end_location,
                lat: routePoints.value[routePoints.value.length - 1]?.lat || '',
                lng: routePoints.value[routePoints.value.length - 1]?.lng || ''
            }
        ];
        
        form.waypoints = routePoints.value.map(point => ({
            lat: point.lat,
            lng: point.lng
        }));
    }
};

const getPointColorClass = (index) => {
    if (routeType.value === 'google') {
        if (index === 0) return 'bg-green-500';
        if (index === routePoints.value.length - 1) return 'bg-red-500';
        return 'bg-blue-500';
    } else {
        return 'bg-purple-500';
    }
};

const getPointPlaceholder = (index) => {
    if (routeType.value === 'google') {
        if (index === 0) return 'Start Location';
        if (index === routePoints.value.length - 1) return 'End Location';
        return `Stop ${index}`;
    } else {
        return `Point ${index + 1}`;
    }
};

const clearRoute = () => {
    routePoints.value = [];
    routeInfo.value = { distance: null, duration: null };
    form.stops = [];
    form.waypoints = [];
    form.distance_km = '';
    form.estimated_duration_minutes = '';
    
    if (markersLayer.value) markersLayer.value.clearLayers();
    if (routeLayer.value) routeLayer.value.clearLayers();
};

const removePoint = (index) => {
    routePoints.value.splice(index, 1);
    updateMapDisplay();
    updateFormData();
};

const updateMapDisplay = () => {
    if (!map.value || !markersLayer.value || !routeLayer.value) return;
    
    // Clear existing markers and routes
    markersLayer.value.clearLayers();
    routeLayer.value.clearLayers();
    
    // Add markers for each point
    routePoints.value.forEach((point, index) => {
        const lat = parseFloat(point.lat);
        const lng = parseFloat(point.lng);
        
        if (!isNaN(lat) && !isNaN(lng)) {
            const color = routeType.value === 'google' ? 
                (index === 0 ? '#10b981' : index === routePoints.value.length - 1 ? '#ef4444' : '#3b82f6') :
                '#8b5cf6';
            
            const icon = L.divIcon({
                html: `<div style="
                    background-color: ${color};
                    color: white;
                    width: 30px;
                    height: 30px;
                    border-radius: 50%;
                    border: 3px solid white;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-weight: bold;
                    font-size: 12px;
                    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
                ">${index + 1}</div>`,
                className: '',
                iconSize: [30, 30],
                iconAnchor: [15, 15]
            });
            
            const marker = L.marker([lat, lng], { icon }).addTo(markersLayer.value);
            marker.bindPopup(`
                <div class="text-center">
                    <strong>${point.name || `Point ${index + 1}`}</strong><br>
                    <small class="font-mono">${lat.toFixed(6)}, ${lng.toFixed(6)}</small>
                </div>
            `);
        }
    });
    
    // Draw route line
    if (routePoints.value.length > 1) {
        const routeCoords = routePoints.value
            .map(point => [parseFloat(point.lat), parseFloat(point.lng)])
            .filter(coord => !isNaN(coord[0]) && !isNaN(coord[1]));
        
        if (routeCoords.length > 1) {
            const lineColor = routeType.value === 'google' ? '#3b82f6' : '#8b5cf6';
            const lineStyle = routeType.value === 'google' ? 'solid' : 'dashed';
            
            L.polyline(routeCoords, {
                color: lineColor,
                weight: 4,
                opacity: 0.8,
                dashArray: lineStyle === 'dashed' ? '10, 10' : null
            }).addTo(routeLayer.value);
        }
    }
    
    // Fit map to show all points
    if (routePoints.value.length > 0) {
        const bounds = L.latLngBounds(routePoints.value.map(p => [parseFloat(p.lat), parseFloat(p.lng)]));
        map.value.fitBounds(bounds.pad(0.1));
    }
};

const submit = () => {
    form.post(route('routes.store'));
};
</script>