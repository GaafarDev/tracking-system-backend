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

                        <!-- Route Stops -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <MapPinIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    Route Stops
                                </span>
                            </label>
                            <div class="border border-gray-300 rounded-xl p-4 bg-gray-50">
                                <div v-if="form.stops.length === 0" class="text-center py-8 text-gray-500">
                                    <MapPinIcon class="w-12 h-12 mx-auto mb-3 text-gray-400" />
                                    <p class="text-sm">No stops added yet. Click "Add Stop" to get started.</p>
                                </div>

                                <div v-for="(stop, index) in form.stops" :key="index" class="mb-4 last:mb-0">
                                    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                                        <div class="flex justify-between items-center mb-4">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                                    <span class="text-sm font-semibold text-blue-600">{{ index + 1 }}</span>
                                                </div>
                                                <h4 class="text-lg font-semibold text-gray-900">Stop {{ index + 1 }}</h4>
                                            </div>
                                            <button 
                                                type="button" 
                                                @click="removeStop(index)" 
                                                class="text-red-600 hover:text-red-900 p-2 rounded-lg hover:bg-red-50 transition-colors duration-200"
                                            >
                                                <TrashIcon class="w-4 h-4" />
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <label :for="`stop_${index}_name`" class="block text-sm font-medium text-gray-700 mb-1">
                                                    Stop Name <span class="text-red-500">*</span>
                                                </label>
                                                <input
                                                    :id="`stop_${index}_name`"
                                                    v-model="stop.name"
                                                    type="text"
                                                    class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-white placeholder-gray-400"
                                                    placeholder="Enter stop name"
                                                    required
                                                />
                                            </div>
                                            <div>
                                                <label :for="`stop_${index}_lat`" class="block text-sm font-medium text-gray-700 mb-1">
                                                    Latitude <span class="text-red-500">*</span>
                                                </label>
                                                <input
                                                    :id="`stop_${index}_lat`"
                                                    v-model="stop.lat"
                                                    type="number"
                                                    step="0.0000001"
                                                    class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-white placeholder-gray-400"
                                                    placeholder="e.g., 3.1390"
                                                    required
                                                />
                                            </div>
                                            <div>
                                                <label :for="`stop_${index}_lng`" class="block text-sm font-medium text-gray-700 mb-1">
                                                    Longitude <span class="text-red-500">*</span>
                                                </label>
                                                <input
                                                    :id="`stop_${index}_lng`"
                                                    v-model="stop.lng"
                                                    type="number"
                                                    step="0.0000001"
                                                    class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-white placeholder-gray-400"
                                                    placeholder="e.g., 101.6869"
                                                    required
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button 
                                    type="button" 
                                    @click="addStop" 
                                    class="mt-4 w-full flex items-center justify-center px-4 py-3 border-2 border-dashed border-gray-300 rounded-xl text-gray-600 hover:border-primary-400 hover:text-primary-600 transition-all duration-200"
                                >
                                    <PlusIcon class="w-5 h-5 mr-2" />
                                    Add Stop
                                </button>
                            </div>
                            <p v-if="form.errors.stops" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.stops }}
                            </p>
                        </div>
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
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { 
    ArrowLeftIcon,
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

const addStop = () => {
    form.stops.push({
        name: '',
        lat: '',
        lng: ''
    });
};

const removeStop = (index) => {
    form.stops.splice(index, 1);
};

const submit = () => {
    form.post(route('routes.store'));
};
</script>