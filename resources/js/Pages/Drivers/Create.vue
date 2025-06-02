<template>
    <AppLayout title="Create Driver">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Create New Driver</h1>
                        <p class="text-gray-600 mt-1">Add a new driver to the system</p>
                    </div>
                    <Link :href="route('drivers.index')" class="btn-secondary">
                        <ArrowLeftIcon class="w-4 h-4 mr-2" />
                        Back to Drivers
                    </Link>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <!-- Info Banner -->
                <div class="bg-blue-50 border-b border-blue-200 px-6 py-4">
                    <div class="flex items-start">
                        <InformationCircleIcon class="h-5 w-5 text-blue-400 mt-0.5 mr-3" />
                        <div>
                            <h3 class="text-sm font-medium text-blue-800">Account Creation Notice</h3>
                            <p class="text-sm text-blue-700 mt-1">A random secure password will be automatically generated and sent to the driver's email address along with their login credentials.</p>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Vendor Selection -->
                        <div class="md:col-span-2">
                            <label for="vendor_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Vendor <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="vendor_id"
                                v-model="form.vendor_id"
                                class="form-select"
                                required
                            >
                                <option value="">Select Vendor</option>
                                <option v-for="vendor in vendors" :key="vendor.id" :value="vendor.id">
                                    {{ vendor.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.vendor_id" class="mt-1 text-sm text-red-600">{{ form.errors.vendor_id }}</p>
                        </div>

                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="form-input"
                                placeholder="Enter driver's full name"
                                required
                            />
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="form-input"
                                placeholder="driver@example.com"
                                required
                            />
                            <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                        </div>

                        <!-- License Number -->
                        <div>
                            <label for="license_number" class="block text-sm font-medium text-gray-700 mb-2">
                                License Number <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="license_number"
                                v-model="form.license_number"
                                type="text"
                                class="form-input"
                                placeholder="Enter license number"
                                required
                            />
                            <p v-if="form.errors.license_number" class="mt-1 text-sm text-red-600">{{ form.errors.license_number }}</p>
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="phone_number"
                                v-model="form.phone_number"
                                type="tel"
                                class="form-input"
                                placeholder="+60123456789"
                                required
                            />
                            <p v-if="form.errors.phone_number" class="mt-1 text-sm text-red-600">{{ form.errors.phone_number }}</p>
                        </div>

                        <!-- Address -->
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                Address
                            </label>
                            <textarea
                                id="address"
                                v-model="form.address"
                                class="form-textarea"
                                rows="3"
                                placeholder="Enter complete address"
                            ></textarea>
                            <p v-if="form.errors.address" class="mt-1 text-sm text-red-600">{{ form.errors.address }}</p>
                        </div>

                        <!-- Status -->
                        <div>
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
                                <option value="inactive">Inactive</option>
                                <option value="on_leave">On Leave</option>
                            </select>
                            <p v-if="form.errors.status" class="mt-1 text-sm text-red-600">{{ form.errors.status }}</p>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200 space-x-3">
                        <Link :href="route('drivers.index')" class="btn-secondary">
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
                            <span v-else>Create Driver</span>
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
import { ArrowLeftIcon, InformationCircleIcon } from '@heroicons/vue/24/outline';

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