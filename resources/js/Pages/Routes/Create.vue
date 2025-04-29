<template>
    <AppLayout title="Create Route">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Route
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
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <!-- Start Location -->
                            <div>
                                <InputLabel for="start_location" value="Start Location" />
                                <TextInput
                                    id="start_location"
                                    v-model="form.start_location"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.start_location" class="mt-2" />
                            </div>

                            <!-- End Location -->
                            <div>
                                <InputLabel for="end_location" value="End Location" />
                                <TextInput
                                    id="end_location"
                                    v-model="form.end_location"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.end_location" class="mt-2" />
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
                                    class="mt-1 block w-full"
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
                                    class="mt-1 block w-full"
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
                                    <div v-for="(stop, index) in form.stops" :key="index" class="mb-4 p-4 border rounded-md bg-gray-50">
                                        <div class="flex justify-between items-center mb-2">
                                            <h4 class="font-medium">Stop #{{ index + 1 }}</h4>
                                            <button 
                                                type="button" 
                                                @click="removeStop(index)" 
                                                class="text-red-600 hover:text-red-900"
                                            >Remove</button>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <InputLabel :for="`stop_${index}_name`" value="Name" />
                                                <TextInput
                                                    :id="`stop_${index}_name`"
                                                    v-model="stop.name"
                                                    type="text"
                                                    class="mt-1 block w-full"
                                                    required
                                                />
                                            </div>
                                            <div>
                                                <InputLabel :for="`stop_${index}_lat`" value="Latitude" />
                                                <TextInput
                                                    :id="`stop_${index}_lat`"
                                                    v-model="stop.lat"
                                                    type="number"
                                                    step="0.0000001"
                                                    class="mt-1 block w-full"
                                                    required
                                                />
                                            </div>
                                            <div>
                                                <InputLabel :for="`stop_${index}_lng`" value="Longitude" />
                                                <TextInput
                                                    :id="`stop_${index}_lng`"
                                                    v-model="stop.lng"
                                                    type="number"
                                                    step="0.0000001"
                                                    class="mt-1 block w-full"
                                                    required
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    <button 
                                        type="button" 
                                        @click="addStop" 
                                        class="mt-2 px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300 text-sm"
                                    >
                                        Add Stop
                                    </button>
                                </div>
                                <InputError :message="form.errors.stops" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <Link
                                :href="route('routes.index')"
                                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:shadow-outline-gray transition ease-in-out duration-150 mr-3"
                            >
                                Cancel
                            </Link>
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Create Route
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

const form = useForm({
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