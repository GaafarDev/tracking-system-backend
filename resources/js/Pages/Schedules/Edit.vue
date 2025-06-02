<template>
    <AppLayout title="Edit Schedule">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Schedule #{{ schedule.id }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <!-- Schedule Information (Read-only) -->
                    <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Schedule Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm font-medium text-gray-500">Schedule ID</span>
                                <p class="text-sm text-gray-900">#{{ schedule.id }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Created On</span>
                                <p class="text-sm text-gray-900">{{ formatDateTime(schedule.created_at) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Editable Form -->
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Route -->
                            <div>
                                <InputLabel for="route_id" value="Route" />
                                <select
                                    id="route_id"
                                    v-model="form.route_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="">Select Route</option>
                                    <option v-for="route in routes" :key="route.id" :value="route.id">
                                        {{ route.name }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.route_id" class="mt-2" />
                            </div>

                            <!-- Driver -->
                            <div>
                                <InputLabel for="driver_id" value="Driver" />
                                <select
                                    id="driver_id"
                                    v-model="form.driver_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="">Select Driver</option>
                                    <option v-for="driver in drivers" :key="driver.id" :value="driver.id">
                                        {{ driver.user.name }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.driver_id" class="mt-2" />
                            </div>

                            <!-- Vehicle -->
                            <div>
                                <InputLabel for="vehicle_id" value="Vehicle" />
                                <select
                                    id="vehicle_id"
                                    v-model="form.vehicle_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="">Select Vehicle</option>
                                    <option v-for="vehicle in vehicles" :key="vehicle.id" :value="vehicle.id">
                                        {{ vehicle.plate_number }} - {{ vehicle.model }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.vehicle_id" class="mt-2" />
                            </div>

                            <!-- Day of Week -->
                            <div>
                                <InputLabel for="day_of_week" value="Day of Week" />
                                <select
                                    id="day_of_week"
                                    v-model="form.day_of_week"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="">Select Day</option>
                                    <option value="monday">Monday</option>
                                    <option value="tuesday">Tuesday</option>
                                    <option value="wednesday">Wednesday</option>
                                    <option value="thursday">Thursday</option>
                                    <option value="friday">Friday</option>
                                    <option value="saturday">Saturday</option>
                                    <option value="sunday">Sunday</option>
                                </select>
                                <InputError :message="form.errors.day_of_week" class="mt-2" />
                            </div>

                            <!-- Departure Time -->
                            <div>
                                <InputLabel for="departure_time" value="Departure Time" />
                                <TextInput
                                    id="departure_time"
                                    v-model="form.departure_time"
                                    type="time"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.departure_time" class="mt-2" />
                            </div>

                            <!-- Arrival Time -->
                            <div>
                                <InputLabel for="arrival_time" value="Arrival Time" />
                                <TextInput
                                    id="arrival_time"
                                    v-model="form.arrival_time"
                                    type="time"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.arrival_time" class="mt-2" />
                            </div>

                            <!-- Active Status -->
                            <div class="md:col-span-2">
                                <div class="flex items-center mt-6">
                                    <input
                                        id="is_active"
                                        v-model="form.is_active"
                                        type="checkbox"
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                    />
                                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                        Active Schedule
                                    </label>
                                </div>
                                <InputError :message="form.errors.is_active" class="mt-2" />
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end mt-6 space-x-4">
                            <Link
                                :href="route('schedules.show', schedule.id)"
                                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 transition"
                            >
                                Cancel
                            </Link>
                            
                            <PrimaryButton 
                                :class="{ 'opacity-25': form.processing }" 
                                :disabled="form.processing"
                            >
                                Update Schedule
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
    schedule: Object,
    drivers: Array,
    routes: Array,
    vehicles: Array,
});

const form = useForm({
    route_id: props.schedule.route_id || '',
    driver_id: props.schedule.driver_id || '',
    vehicle_id: props.schedule.vehicle_id || '',
    day_of_week: props.schedule.day_of_week || '',
    departure_time: props.schedule.departure_time || '',
    arrival_time: props.schedule.arrival_time || '',
    is_active: props.schedule.is_active || false,
});

function submit() {
    form.put(route('schedules.update', props.schedule.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Redirect to show page after successful update
        }
    });
}

function formatDateTime(dateString) {
    return new Date(dateString).toLocaleString();
}
</script>