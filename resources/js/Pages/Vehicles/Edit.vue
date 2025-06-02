<template>
    <AppLayout title="Edit Vehicle">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Vehicle - {{ vehicle.plate_number }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <!-- Vehicle Information (Read-only) -->
                    <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Vehicle Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm font-medium text-gray-500">Vehicle ID</span>
                                <p class="text-sm text-gray-900">#{{ vehicle.id }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Registered On</span>
                                <p class="text-sm text-gray-900">{{ formatDateTime(vehicle.created_at) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Editable Form -->
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Plate Number -->
                            <div>
                                <InputLabel for="plate_number" value="Plate Number" />
                                <TextInput
                                    id="plate_number"
                                    v-model="form.plate_number"
                                    type="text"
                                    class="input-modern"
                                    required
                                />
                                <InputError :message="form.errors.plate_number" class="mt-2" />
                            </div>

                            <!-- Model -->
                            <div>
                                <InputLabel for="model" value="Model" />
                                <TextInput
                                    id="model"
                                    v-model="form.model"
                                    type="text"
                                    class="input-modern"
                                    required
                                />
                                <InputError :message="form.errors.model" class="mt-2" />
                            </div>

                            <!-- Type -->
                            <div>
                                <InputLabel for="type" value="Vehicle Type" />
                                <select
                                    id="type"
                                    v-model="form.type"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="bus">Bus</option>
                                    <option value="van">Van</option>
                                    <option value="car">Car</option>
                                    <option value="truck">Truck</option>
                                    <option value="boat">Boat</option>
                                </select>
                                <InputError :message="form.errors.type" class="mt-2" />
                            </div>

                            <!-- Capacity -->
                            <div>
                                <InputLabel for="capacity" value="Capacity (seats)" />
                                <TextInput
                                    id="capacity"
                                    v-model="form.capacity"
                                    type="number"
                                    min="1"
                                    class="input-modern"
                                    required
                                />
                                <InputError :message="form.errors.capacity" class="mt-2" />
                            </div>

                            <!-- Year -->
                            <div>
                                <InputLabel for="year" value="Year" />
                                <TextInput
                                    id="year"
                                    v-model="form.year"
                                    type="number"
                                    min="1900"
                                    :max="new Date().getFullYear() + 1"
                                    class="input-modern"
                                />
                                <InputError :message="form.errors.year" class="mt-2" />
                            </div>

                            <!-- Color -->
                            <div>
                                <InputLabel for="color" value="Color" />
                                <TextInput
                                    id="color"
                                    v-model="form.color"
                                    type="text"
                                    class="input-modern"
                                />
                                <InputError :message="form.errors.color" class="mt-2" />
                            </div>

                            <!-- Fuel Type -->
                            <div>
                                <InputLabel for="fuel_type" value="Fuel Type" />
                                <select
                                    id="fuel_type"
                                    v-model="form.fuel_type"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                >
                                    <option value="">Select Fuel Type</option>
                                    <option value="petrol">Petrol</option>
                                    <option value="diesel">Diesel</option>
                                    <option value="electric">Electric</option>
                                    <option value="hybrid">Hybrid</option>
                                    <option value="cng">CNG</option>
                                </select>
                                <InputError :message="form.errors.fuel_type" class="mt-2" />
                            </div>

                            <!-- Status -->
                            <div>
                                <InputLabel for="status" value="Status" />
                                <select
                                    id="status"
                                    v-model="form.status"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="active">Active</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <InputError :message="form.errors.status" class="mt-2" />
                            </div>

                            <!-- Engine Number -->
                            <div>
                                <InputLabel for="engine_number" value="Engine Number" />
                                <TextInput
                                    id="engine_number"
                                    v-model="form.engine_number"
                                    type="text"
                                    class="input-modern"
                                />
                                <InputError :message="form.errors.engine_number" class="mt-2" />
                            </div>

                            <!-- Chassis Number -->
                            <div>
                                <InputLabel for="chassis_number" value="Chassis Number" />
                                <TextInput
                                    id="chassis_number"
                                    v-model="form.chassis_number"
                                    type="text"
                                    class="input-modern"
                                />
                                <InputError :message="form.errors.chassis_number" class="mt-2" />
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end mt-6 space-x-4">
                            <Link
                                :href="route('vehicles.show', vehicle.id)"
                                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 transition"
                            >
                                Cancel
                            </Link>
                            
                            <PrimaryButton 
                                :class="{ 'opacity-25': form.processing }" 
                                :disabled="form.processing"
                            >
                                Update Vehicle
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
    vehicle: Object,
});

const form = useForm({
    plate_number: props.vehicle.plate_number || '',
    model: props.vehicle.model || '',
    type: props.vehicle.type || 'car',
    capacity: props.vehicle.capacity || '',
    year: props.vehicle.year || '',
    color: props.vehicle.color || '',
    fuel_type: props.vehicle.fuel_type || '',
    status: props.vehicle.status || 'active',
    engine_number: props.vehicle.engine_number || '',
    chassis_number: props.vehicle.chassis_number || '',
});

function submit() {
    form.put(route('vehicles.update', props.vehicle.id), {
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