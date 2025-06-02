<template>
    <AppLayout title="Edit Route">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Route: {{ routeData.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <InputLabel for="name" value="Route Name" />
                                <TextInput
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="input-modern"
                                    required
                                />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <!-- Distance -->
                            <div>
                                <InputLabel for="distance_km" value="Distance (km)" />
                                <TextInput
                                    id="distance_km"
                                    v-model="form.distance_km"
                                    type="number"
                                    step="0.1"
                                    min="0"
                                    class="input-modern"
                                />
                                <InputError :message="form.errors.distance_km" class="mt-2" />
                            </div>

                            <!-- Duration -->
                            <div>
                                <InputLabel for="estimated_duration_minutes" value="Estimated Duration (minutes)" />
                                <TextInput
                                    id="estimated_duration_minutes"
                                    v-model="form.estimated_duration_minutes"
                                    type="number"
                                    min="1"
                                    class="input-modern"
                                />
                                <InputError :message="form.errors.estimated_duration_minutes" class="mt-2" />
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <InputLabel for="description" value="Description" />
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    rows="3"
                                ></textarea>
                                <InputError :message="form.errors.description" class="mt-2" />
                            </div>

                            <!-- Stops -->
                            <div class="md:col-span-2">
                                <InputLabel value="Route Stops" />
                                <div class="mt-2 border rounded-md p-4">
                                    <div class="flex justify-between items-center mb-4">
                                        <h4 class="font-medium text-gray-900">Manage Stops</h4>
                                        <button 
                                            type="button" 
                                            @click="addStop" 
                                            class="px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm"
                                        >
                                            Add Stop
                                        </button>
                                    </div>

                                    <div v-if="form.stops.length === 0" class="text-center py-8 text-gray-500">
                                        No stops added yet. Click "Add Stop" to create your first stop.
                                    </div>

                                    <div v-else class="space-y-4">
                                        <div v-for="(stop, index) in form.stops" :key="index" class="p-4 border rounded-md bg-gray-50">
                                            <div class="flex justify-between items-start mb-2">
                                                <div class="flex items-center space-x-2">
                                                    <div class="flex items-center justify-center w-6 h-6 rounded-full text-xs font-medium text-white"
                                                         :class="getStopBadgeClass(index)">
                                                        {{ index + 1 }}
                                                    </div>
                                                    <h5 class="font-medium text-gray-900">
                                                        {{ index === 0 ? 'Start Point' : index === form.stops.length - 1 ? 'End Point' : `Stop ${index}` }}
                                                    </h5>
                                                </div>
                                                <button 
                                                    type="button" 
                                                    @click="removeStop(index)" 
                                                    class="text-red-600 hover:text-red-900 text-sm"
                                                >
                                                    Remove
                                                </button>
                                            </div>
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <div>
                                                    <InputLabel :for="`stop_${index}_name`" value="Stop Name" />
                                                    <TextInput
                                                        :id="`stop_${index}_name`"
                                                        v-model="stop.name"
                                                        type="text"
                                                        class="input-modern"
                                                        required
                                                        :placeholder="index === 0 ? 'Starting location' : index === form.stops.length - 1 ? 'Destination' : 'Stop name'"
                                                    />
                                                </div>
                                                <div>
                                                    <InputLabel :for="`stop_${index}_lat`" value="Latitude" />
                                                    <TextInput
                                                        :id="`stop_${index}_lat`"
                                                        v-model="stop.lat"
                                                        type="number"
                                                        step="0.0000001"
                                                        class="input-modern"
                                                        required
                                                        placeholder="3.1319"
                                                    />
                                                </div>
                                                <div>
                                                    <InputLabel :for="`stop_${index}_lng`" value="Longitude" />
                                                    <TextInput
                                                        :id="`stop_${index}_lng`"
                                                        v-model="stop.lng"
                                                        type="number"
                                                        step="0.0000001"
                                                        class="input-modern"
                                                        required
                                                        placeholder="101.6841"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <InputError :message="form.errors.stops" class="mt-2" />
                            </div>

                            <!-- Waypoints -->
                            <div class="md:col-span-2">
                                <InputLabel value="Waypoints (Optional)" />
                                <div class="mt-2 border rounded-md p-4">
                                    <div class="flex justify-between items-center mb-4">
                                        <div>
                                            <h4 class="font-medium text-gray-900">Intermediate Waypoints</h4>
                                            <p class="text-sm text-gray-500">Add waypoints between stops for better route guidance</p>
                                        </div>
                                        <button 
                                            type="button" 
                                            @click="addWaypoint" 
                                            class="px-3 py-1 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm"
                                        >
                                            Add Waypoint
                                        </button>
                                    </div>

                                    <div v-if="form.waypoints.length === 0" class="text-center py-4 text-gray-500 text-sm">
                                        No waypoints added. Waypoints are optional intermediate points along the route.
                                    </div>

                                    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div v-for="(waypoint, index) in form.waypoints" :key="index" class="p-3 border rounded-md bg-yellow-50">
                                            <div class="flex justify-between items-center mb-2">
                                                <h5 class="font-medium text-gray-900">Waypoint {{ index + 1 }}</h5>
                                                <button 
                                                    type="button" 
                                                    @click="removeWaypoint(index)" 
                                                    class="text-red-600 hover:text-red-900 text-sm"
                                                >
                                                    Remove
                                                </button>
                                            </div>
                                            
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <InputLabel :for="`waypoint_${index}_lat`" value="Lat" />
                                                    <TextInput
                                                        :id="`waypoint_${index}_lat`"
                                                        v-model="waypoint.lat"
                                                        type="number"
                                                        step="0.0000001"
                                                        class="input-modern"
                                                        required
                                                    />
                                                </div>
                                                <div>
                                                    <InputLabel :for="`waypoint_${index}_lng`" value="Lng" />
                                                    <TextInput
                                                        :id="`waypoint_${index}_lng`"
                                                        v-model="waypoint.lng"
                                                        type="number"
                                                        step="0.0000001"
                                                        class="input-modern"
                                                        required
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <InputError :message="form.errors.waypoints" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 space-x-4">
                            <Link
                                :href="$route('routes.show', routeData.id)"
                                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:shadow-outline-gray transition ease-in-out duration-150"
                            >
                                Cancel
                            </Link>
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                {{ form.processing ? 'Updating...' : 'Update Route' }}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    route: Object,
});

// Rename to avoid conflict with route() function
const routeData = props.route;

// Initialize form with existing route data
const form = useForm({
    name: routeData.name || '',
    description: routeData.description || '',
    distance_km: routeData.distance_km || '',
    estimated_duration_minutes: routeData.estimated_duration_minutes || '',
    stops: routeData.stops ? [...routeData.stops] : [],
    waypoints: routeData.waypoints ? [...routeData.waypoints] : []
});

const addStop = () => {
    form.stops.push({
        name: '',
        lat: '',
        lng: ''
    });
};

const removeStop = (index) => {
    if (form.stops.length > 1) {
        form.stops.splice(index, 1);
    }
};

const addWaypoint = () => {
    form.waypoints.push({
        lat: '',
        lng: ''
    });
};

const removeWaypoint = (index) => {
    form.waypoints.splice(index, 1);
};

const getStopBadgeClass = (index) => {
    if (index === 0) return 'bg-green-500';
    if (index === form.stops.length - 1) return 'bg-red-500';
    return 'bg-blue-500';
};

const submit = () => {
    form.put($route('routes.update', routeData.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Redirect to show page on success
        },
        onError: (errors) => {
            console.log('Form errors:', errors);
        }
    });
};

// Helper function to access the route helper
function $route(name, params) {
    return route(name, params);
}
</script>