<template>
    <AppLayout title="Create Schedule">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gradient-primary">Create New Schedule</h1>
                        <p class="text-gray-600 mt-2">Schedule a route with driver and vehicle assignments</p>
                    </div>
                    <Link :href="route('schedules.index')" class="btn-secondary">
                        <ArrowLeftIcon class="w-4 h-4 mr-2" />
                        Back to Schedules
                    </Link>
                </div>
            </div>

            <!-- Form Card with Modern Design -->
            <div class="card-modern">
                <!-- Form Header -->
                <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-primary-50 to-secondary-50">
                    <h2 class="text-xl font-semibold text-gray-900">Schedule Information</h2>
                    <p class="text-sm text-gray-600 mt-1">Configure the schedule details for route assignment</p>
                </div>

                <!-- Form Content -->
                <form @submit.prevent="submit" class="p-6 bg-white">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Route -->
                        <div>
                            <label for="route_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <MapIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    Route
                                    <span class="text-red-500 ml-1">*</span>
                                </span>
                            </label>
                            <select
                                id="route_id"
                                v-model="form.route_id"
                                class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-white"
                                required
                            >
                                <option value="" class="text-gray-500">Select Route</option>
                                <option v-for="route in routes" :key="route.id" :value="route.id" class="text-gray-900">
                                    {{ route.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.route_id" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.route_id }}
                            </p>
                        </div>

                        <!-- Driver -->
                        <div>
                            <label for="driver_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <UserIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    Driver
                                    <span class="text-red-500 ml-1">*</span>
                                </span>
                            </label>
                            <select
                                id="driver_id"
                                v-model="form.driver_id"
                                class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-white"
                                required
                            >
                                <option value="" class="text-gray-500">Select Driver</option>
                                <option v-for="driver in drivers" :key="driver.id" :value="driver.id" class="text-gray-900">
                                    {{ driver.user.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.driver_id" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.driver_id }}
                            </p>
                        </div>

                        <!-- Vehicle -->
                        <div>
                            <label for="vehicle_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <TruckIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    Vehicle
                                    <span class="text-red-500 ml-1">*</span>
                                </span>
                            </label>
                            <select
                                id="vehicle_id"
                                v-model="form.vehicle_id"
                                class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-white"
                                required
                            >
                                <option value="" class="text-gray-500">Select Vehicle</option>
                                <option v-for="vehicle in vehicles" :key="vehicle.id" :value="vehicle.id" class="text-gray-900">
                                    {{ vehicle.plate_number }} ({{ vehicle.model }})
                                </option>
                            </select>
                            <p v-if="form.errors.vehicle_id" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.vehicle_id }}
                            </p>
                        </div>

                        <!-- Day of Week -->
                        <div>
                            <label for="day_of_week" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <CalendarDaysIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    Day of Week
                                    <span class="text-red-500 ml-1">*</span>
                                </span>
                            </label>
                            <select
                                id="day_of_week"
                                v-model="form.day_of_week"
                                class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-white"
                                required
                            >
                                <option value="" class="text-gray-500">Select Day</option>
                                <option value="monday" class="text-gray-900">Monday</option>
                                <option value="tuesday" class="text-gray-900">Tuesday</option>
                                <option value="wednesday" class="text-gray-900">Wednesday</option>
                                <option value="thursday" class="text-gray-900">Thursday</option>
                                <option value="friday" class="text-gray-900">Friday</option>
                                <option value="saturday" class="text-gray-900">Saturday</option>
                                <option value="sunday" class="text-gray-900">Sunday</option>
                            </select>
                            <p v-if="form.errors.day_of_week" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.day_of_week }}
                            </p>
                        </div>

                        <!-- Departure Time -->
                        <div>
                            <label for="departure_time" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <ClockIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    Departure Time
                                    <span class="text-red-500 ml-1">*</span>
                                </span>
                            </label>
                            <input
                                id="departure_time"
                                v-model="form.departure_time"
                                type="time"
                                class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-white"
                                required
                            />
                            <p v-if="form.errors.departure_time" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.departure_time }}
                            </p>
                        </div>

                        <!-- Arrival Time -->
                        <div>
                            <label for="arrival_time" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <ClockIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    Arrival Time
                                    <span class="text-red-500 ml-1">*</span>
                                </span>
                            </label>
                            <input
                                id="arrival_time"
                                v-model="form.arrival_time"
                                type="time"
                                class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-white"
                                required
                            />
                            <p v-if="form.errors.arrival_time" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.arrival_time }}
                            </p>
                        </div>

                        <!-- Active Status -->
                        <div class="md:col-span-2">
                            <label for="is_active" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <CheckCircleIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    Schedule Status
                                </span>
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <label class="relative">
                                    <input
                                        type="radio"
                                        name="is_active"
                                        :value="true"
                                        v-model="form.is_active"
                                        class="sr-only"
                                    />
                                    <div :class="form.is_active === true ? 'border-green-500 bg-green-50 text-green-700' : 'border-gray-300 bg-white text-gray-700'" class="border-2 rounded-xl p-4 cursor-pointer transition-all duration-200 hover:border-green-400">
                                        <div class="flex items-center">
                                            <CheckCircleIcon class="w-5 h-5 mr-2" />
                                            <span class="font-medium">Active</span>
                                        </div>
                                        <p class="text-xs mt-1 opacity-75">Schedule is active and ready</p>
                                    </div>
                                </label>
                                <label class="relative">
                                    <input
                                        type="radio"
                                        name="is_active"
                                        :value="false"
                                        v-model="form.is_active"
                                        class="sr-only"
                                    />
                                    <div :class="form.is_active === false ? 'border-gray-500 bg-gray-50 text-gray-700' : 'border-gray-300 bg-white text-gray-700'" class="border-2 rounded-xl p-4 cursor-pointer transition-all duration-200 hover:border-gray-400">
                                        <div class="flex items-center">
                                            <XCircleIcon class="w-5 h-5 mr-2" />
                                            <span class="font-medium">Inactive</span>
                                        </div>
                                        <p class="text-xs mt-1 opacity-75">Schedule is disabled</p>
                                    </div>
                                </label>
                            </div>
                            <p v-if="form.errors.is_active" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.is_active }}
                            </p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200 space-x-4">
                        <Link :href="route('schedules.index')" class="btn-secondary-modern">
                            <XMarkIcon class="w-4 h-4 mr-2" />
                            Cancel
                        </Link>
                        <button type="submit" :disabled="form.processing" class="btn-primary">
                            <span v-if="form.processing" class="inline-flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Creating Schedule...
                            </span>
                            <span v-else class="inline-flex items-center">
                                <PlusIcon class="w-4 h-4 mr-2" />
                                Create Schedule
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
    UserIcon,
    TruckIcon,
    CalendarDaysIcon,
    ClockIcon,
    CheckCircleIcon,
    XCircleIcon,
    XMarkIcon,
    PlusIcon,
    ExclamationCircleIcon
} from '@heroicons/vue/24/outline';

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