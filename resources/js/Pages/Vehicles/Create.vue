<template>
    <AppLayout title="Create Vehicle">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gradient-primary">Add New Vehicle</h1>
                        <p class="text-gray-600 mt-2">Register a new vehicle to the fleet management system</p>
                    </div>
                    <Link :href="route('vehicles.index')" class="btn-secondary">
                        <ArrowLeftIcon class="w-4 h-4 mr-2" />
                        Back to Vehicles
                    </Link>
                </div>
            </div>

            <!-- Form Card with Modern Design -->
            <div class="card-modern">
                <!-- Form Header -->
                <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-primary-50 to-secondary-50">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-xl">
                            <TruckIcon class="w-6 h-6 text-blue-600" />
                        </div>
                        <div class="ml-4">
                            <h2 class="text-xl font-semibold text-gray-900">Vehicle Information</h2>
                            <p class="text-sm text-gray-600 mt-1">Enter the details of the new vehicle to add to your fleet</p>
                        </div>
                    </div>
                </div>

                <!-- Form Content -->
                <form @submit.prevent="submit" class="p-6 bg-white">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Plate Number -->
                        <div>
                            <label for="plate_number" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <IdentificationIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    Plate Number
                                    <span class="text-red-500 ml-1">*</span>
                                </span>
                            </label>
                            <input
                                id="plate_number"
                                v-model="form.plate_number"
                                type="text"
                                class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-white placeholder-gray-400"
                                placeholder="e.g., ABC 1234"
                                required
                            />
                            <p v-if="form.errors.plate_number" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.plate_number }}
                            </p>
                        </div>

                        <!-- Model -->
                        <div>
                            <label for="model" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <CogIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    Model
                                    <span class="text-red-500 ml-1">*</span>
                                </span>
                            </label>
                            <input
                                id="model"
                                v-model="form.model"
                                type="text"
                                class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-white placeholder-gray-400"
                                placeholder="e.g., Toyota Hiace"
                                required
                            />
                            <p v-if="form.errors.model" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.model }}
                            </p>
                        </div>

                        <!-- Type -->
                        <div>
                            <label for="type" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <TagIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    Vehicle Type
                                    <span class="text-red-500 ml-1">*</span>
                                </span>
                            </label>
                            <select
                                id="type"
                                v-model="form.type"
                                class="form-select w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-white"
                                required
                            >
                                <option value="" class="text-gray-500">Select Vehicle Type</option>
                                <option value="school_bus" class="text-gray-900">🚌 School Bus</option>
                                <option value="mini_bus" class="text-gray-900">🚐 Mini Bus</option>
                                <option value="van" class="text-gray-900">🚐 Van</option>
                                <option value="car" class="text-gray-900">🚗 Car</option>
                            </select>
                            <p v-if="form.errors.type" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.type }}
                            </p>
                        </div>

                        <!-- Capacity -->
                        <div>
                            <label for="capacity" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <UserGroupIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    Capacity (Seats)
                                    <span class="text-red-500 ml-1">*</span>
                                </span>
                            </label>
                            <input
                                id="capacity"
                                v-model="form.capacity"
                                type="number"
                                min="1"
                                max="50"
                                class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-white placeholder-gray-400"
                                placeholder="e.g., 12"
                                required
                            />
                            <p v-if="form.errors.capacity" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.capacity }}
                            </p>
                        </div>

                        <!-- Status -->
                        <div class="md:col-span-2">
                            <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <CheckCircleIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    Vehicle Status
                                    <span class="text-red-500 ml-1">*</span>
                                </span>
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <label class="relative">
                                    <input
                                        type="radio"
                                        name="status"
                                        value="active"
                                        v-model="form.status"
                                        class="sr-only"
                                    />
                                    <div :class="form.status === 'active' ? 'border-green-500 bg-green-50 text-green-700' : 'border-gray-300 bg-white text-gray-700'" class="border-2 rounded-xl p-4 cursor-pointer transition-all duration-200 hover:border-green-400">
                                        <div class="flex items-center">
                                            <CheckCircleIcon class="w-5 h-5 mr-2" />
                                            <span class="font-medium">Active</span>
                                        </div>
                                        <p class="text-xs mt-1 opacity-75">Vehicle is ready for use</p>
                                    </div>
                                </label>
                                <label class="relative">
                                    <input
                                        type="radio"
                                        name="status"
                                        value="maintenance"
                                        v-model="form.status"
                                        class="sr-only"
                                    />
                                    <div :class="form.status === 'maintenance' ? 'border-yellow-500 bg-yellow-50 text-yellow-700' : 'border-gray-300 bg-white text-gray-700'" class="border-2 rounded-xl p-4 cursor-pointer transition-all duration-200 hover:border-yellow-400">
                                        <div class="flex items-center">
                                            <WrenchScrewdriverIcon class="w-5 h-5 mr-2" />
                                            <span class="font-medium">Maintenance</span>
                                        </div>
                                        <p class="text-xs mt-1 opacity-75">Vehicle under maintenance</p>
                                    </div>
                                </label>
                                <label class="relative">
                                    <input
                                        type="radio"
                                        name="status"
                                        value="inactive"
                                        v-model="form.status"
                                        class="sr-only"
                                    />
                                    <div :class="form.status === 'inactive' ? 'border-gray-500 bg-gray-50 text-gray-700' : 'border-gray-300 bg-white text-gray-700'" class="border-2 rounded-xl p-4 cursor-pointer transition-all duration-200 hover:border-gray-400">
                                        <div class="flex items-center">
                                            <XCircleIcon class="w-5 h-5 mr-2" />
                                            <span class="font-medium">Inactive</span>
                                        </div>
                                        <p class="text-xs mt-1 opacity-75">Vehicle not in service</p>
                                    </div>
                                </label>
                            </div>
                            <p v-if="form.errors.status" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.status }}
                            </p>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200 space-x-4">
                        <Link :href="route('vehicles.index')" class="btn-secondary-modern">
                            <XMarkIcon class="w-4 h-4 mr-2" />
                            Cancel
                        </Link>
                        <button type="submit" :disabled="form.processing" class="btn-primary">
                            <span v-if="form.processing" class="inline-flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Creating Vehicle...
                            </span>
                            <span v-else class="inline-flex items-center">
                                <PlusIcon class="w-4 h-4 mr-2" />
                                Create Vehicle
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
    TruckIcon,
    IdentificationIcon,
    CogIcon,
    TagIcon,
    UserGroupIcon,
    CheckCircleIcon,
    WrenchScrewdriverIcon,
    XCircleIcon,
    XMarkIcon,
    PlusIcon,
    ExclamationCircleIcon
} from '@heroicons/vue/24/outline';

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