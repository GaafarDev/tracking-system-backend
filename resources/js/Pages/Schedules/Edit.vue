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
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Route -->
                            <div>
                                <InputLabel for="route_id" value="Route" />
                                <select
                                    id="route_id"
                                    v-model="form.route_id"
                                    class="input-modern"
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
                                    class="input-modern"
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
                                    class="input-modern"
                                    required
                                >
                                    <option value="">Select Vehicle</option>
                                    <option v-for="vehicle in vehicles" :key="vehicle.id" :value="vehicle.id">
                                        {{ vehicle.plate_number }} - {{ vehicle.model }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.vehicle_id" class="mt-2" />
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end space-x-4">
                            <Link :href="route('schedules.index')" class="btn-secondary-modern">
                                Cancel
                            </Link>
                            <PrimaryButton class="btn-modern" :disabled="form.processing">
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