<template>
    <AppLayout title="Create Driver">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gradient-primary">Create New Driver</h1>
                        <p class="text-gray-600 mt-2">Add a new driver to the transportation system</p>
                    </div>
                    <Link :href="route('drivers.index')" class="btn-secondary">
                        <ArrowLeftIcon class="w-4 h-4 mr-2" />
                        Back to Drivers
                    </Link>
                </div>
            </div>

            <!-- Form Card with Modern Design -->
            <div class="card-modern">
                <!-- Info Banner -->
                <div class="px-6 py-4 bg-gradient-to-r from-blue-50 via-indigo-50 to-purple-50 border-b border-gray-200">
                    <div class="flex items-start">
                        <div class="p-2 bg-blue-100 rounded-xl">
                            <InformationCircleIcon class="h-6 w-6 text-blue-600" />
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Account Creation Notice</h3>
                            <p class="text-sm text-gray-700 mt-1">A secure password will be automatically generated and sent to the driver's email address along with their login credentials.</p>
                        </div>
                    </div>
                </div>

                <!-- Form Header -->
                <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-primary-50 to-secondary-50">
                    <h2 class="text-xl font-semibold text-gray-900">Driver Information</h2>
                    <p class="text-sm text-gray-600 mt-1">Please fill in all required information to create a new driver account</p>
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

                        <!-- Full Name -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <UserIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    Full Name
                                    <span class="text-red-500 ml-1">*</span>
                                </span>
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-white placeholder-gray-400"
                                placeholder="Enter driver's full name"
                                required
                            />
                            <p v-if="form.errors.name" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <EnvelopeIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    Email Address
                                    <span class="text-red-500 ml-1">*</span>
                                </span>
                            </label>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-white placeholder-gray-400"
                                placeholder="driver@example.com"
                                required
                            />
                            <p v-if="form.errors.email" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <!-- License Number -->
                        <div>
                            <label for="license_number" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <IdentificationIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    License Number
                                    <span class="text-red-500 ml-1">*</span>
                                </span>
                            </label>
                            <input
                                id="license_number"
                                v-model="form.license_number"
                                type="text"
                                class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-white placeholder-gray-400"
                                placeholder="Enter driving license number"
                                required
                            />
                            <p v-if="form.errors.license_number" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.license_number }}
                            </p>
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label for="phone_number" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <PhoneIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    Phone Number
                                    <span class="text-red-500 ml-1">*</span>
                                </span>
                            </label>
                            <input
                                id="phone_number"
                                v-model="form.phone_number"
                                type="text"
                                class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-white placeholder-gray-400"
                                placeholder="+60 12-345 6789"
                                required
                            />
                            <p v-if="form.errors.phone_number" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.phone_number }}
                            </p>
                        </div>

                        <!-- Address -->
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <MapPinIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    Address
                                </span>
                            </label>
                            <textarea
                                id="address"
                                v-model="form.address"
                                class="form-textarea w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-white placeholder-gray-400 resize-y"
                                rows="3"
                                placeholder="Enter complete address"
                            ></textarea>
                            <p v-if="form.errors.address" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.address }}
                            </p>
                        </div>

                        <!-- Status -->
                        <div class="md:col-span-2">
                            <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <CheckCircleIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    Status
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
                                        <p class="text-xs mt-1 opacity-75">Driver is available for assignments</p>
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
                                        <p class="text-xs mt-1 opacity-75">Driver is not available</p>
                                    </div>
                                </label>
                                <label class="relative">
                                    <input
                                        type="radio"
                                        name="status"
                                        value="on_leave"
                                        v-model="form.status"
                                        class="sr-only"
                                    />
                                    <div :class="form.status === 'on_leave' ? 'border-yellow-500 bg-yellow-50 text-yellow-700' : 'border-gray-300 bg-white text-gray-700'" class="border-2 rounded-xl p-4 cursor-pointer transition-all duration-200 hover:border-yellow-400">
                                        <div class="flex items-center">
                                            <ClockIcon class="w-5 h-5 mr-2" />
                                            <span class="font-medium">On Leave</span>
                                        </div>
                                        <p class="text-xs mt-1 opacity-75">Driver is temporarily unavailable</p>
                                    </div>
                                </label>
                            </div>
                            <p v-if="form.errors.status" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.status }}
                            </p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200 space-x-4">
                        <Link :href="route('drivers.index')" class="btn-secondary-modern">
                            <XMarkIcon class="w-4 h-4 mr-2" />
                            Cancel
                        </Link>
                        <button type="submit" :disabled="form.processing" class="btn-primary">
                            <span v-if="form.processing" class="inline-flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Creating Driver...
                            </span>
                            <span v-else class="inline-flex items-center">
                                <UserPlusIcon class="w-4 h-4 mr-2" />
                                Create Driver
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
    InformationCircleIcon,
    BuildingOfficeIcon,
    UserIcon,
    EnvelopeIcon,
    IdentificationIcon,
    PhoneIcon,
    MapPinIcon,
    CheckCircleIcon,
    XCircleIcon,
    ClockIcon,
    XMarkIcon,
    UserPlusIcon,
    ExclamationCircleIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    vendors: Array,
});

const form = useForm({
    vendor_id: '',
    name: '',
    email: '',
    license_number: '',
    phone_number: '',
    address: '',
    status: 'active'
});

const submit = () => {
    form.post(route('drivers.store'));
};
</script>