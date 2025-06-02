<template>
    <AppLayout title="Create Schedule">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Schedule
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
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
                                        {{ vehicle.plate_number }} ({{ vehicle.model }})
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
                                    class="input-modern"
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
                                    class="input-modern"
                                    required
                                />
                                <InputError :message="form.errors.arrival_time" class="mt-2" />
                            </div>

                            <!-- Active Status -->
                            <div>
                                <div class="flex items-center mt-6">
                                    <input
                                        id="is_active"
                                        v-model="form.is_active"
                                        type="checkbox"
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                    />
                                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                        Active
                                    </label>
                                </div>
                                <InputError :message="form.errors.is_active" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <Link
                                :href="route('schedules.index')"
                                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:shadow-outline-gray transition ease-in-out duration-150 mr-3"
                            >
                                Cancel
                            </Link>
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Create Schedule
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
    drivers: Array,
    routes: Array,
    vehicles: Array
});

const form = useForm({
    route_id: '',
    driver_id: '',
    vehicle_id: '',
    day_of_week: '',
    departure_time: '',
    arrival_time: '',
    is_active: true
});

const submit = () => {
    form.post(route('schedules.store'));
};
</script>