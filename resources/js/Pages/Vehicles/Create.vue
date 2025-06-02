<template>
    <AppLayout title="Create Vehicle">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Add New Vehicle</h1>
                        <p class="text-gray-600 mt-1">Register a new vehicle to the fleet</p>
                    </div>
                    <Link :href="route('vehicles.index')" class="btn-secondary">
                        <ArrowLeftIcon class="w-4 h-4 mr-2" />
                        Back to Vehicles
                    </Link>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Vehicle Information</h3>
                    <p class="text-sm text-gray-600 mt-1">Enter the details of the new vehicle</p>
                </div>

                <form @submit.prevent="submit" class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Plate Number -->
                        <div>
                            <label for="plate_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Plate Number <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="plate_number"
                                v-model="form.plate_number"
                                type="text"
                                class="form-input"
                                placeholder="e.g., ABC 1234"
                                required
                            />
                            <p v-if="form.errors.plate_number" class="mt-1 text-sm text-red-600">{{ form.errors.plate_number }}</p>
                        </div>

                        <!-- Model -->
                        <div>
                            <label for="model" class="block text-sm font-medium text-gray-700 mb-2">
                                Model <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="model"
                                v-model="form.model"
                                type="text"
                                class="form-input"
                                placeholder="e.g., Toyota Hiace"
                                required
                            />
                            <p v-if="form.errors.model" class="mt-1 text-sm text-red-600">{{ form.errors.model }}</p>
                        </div>

                        <!-- Type -->
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                Vehicle Type <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="type"
                                v-model="form.type"
                                class="form-select"
                                required
                            >
                                <option value="">Select Type</option>
                                <option value="school_bus">School Bus</option>
                                <option value="mini_bus">Mini Bus</option>
                                <option value="van">Van</option>
                                <option value="car">Car</option>
                            </select>
                            <p v-if="form.errors.type" class="mt-1 text-sm text-red-600">{{ form.errors.type }}</p>
                        </div>

                        <!-- Capacity -->
                        <div>
                            <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2">
                                Capacity (Seats) <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="capacity"
                                v-model="form.capacity"
                                type="number"
                                min="1"
                                class="form-input"
                                placeholder="e.g., 12"
                                required
                            />
                            <p v-if="form.errors.capacity" class="mt-1 text-sm text-red-600">{{ form.errors.capacity }}</p>
                        </div>

                        <!-- Status -->
                        <div class="md:col-span-2">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="status"
                                v-model="form.status"
                                class="form-select"
                                required
                            >
                                <option value="active">Active</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <p v-if="form.errors.status" class="mt-1 text-sm text-red-600">{{ form.errors.status }}</p>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200 space-x-3">
                        <Link :href="route('vehicles.index')" class="btn-secondary">
                            Cancel
                        </Link>
                        <button type="submit" :disabled="form.processing" class="btn-primary">
                            <span v-if="form.processing" class="inline-flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Creating...
                            </span>
                            <span v-else>Create Vehicle</span>
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
import { ArrowLeftIcon } from '@heroicons/vue/24/outline';

const form = useForm({
    plate_number: '',
    model: '',
    type: '',
    capacity: '',
    status: 'active'
});

const submit = () => {
    form.post(route('vehicles.store'));
};
</script>