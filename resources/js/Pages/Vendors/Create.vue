<template>
    <AppLayout title="Create Vendor">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gradient-primary">Create New Vendor</h1>
                        <p class="text-gray-600 mt-2">Add a new vendor partner to the transportation system</p>
                    </div>
                    <Link :href="route('vendors.index')" class="btn-secondary">
                        <ArrowLeftIcon class="w-4 h-4 mr-2" />
                        Back to Vendors
                    </Link>
                </div>
            </div>

            <!-- Form Card with Modern Design -->
            <div class="card-modern">
                <!-- Form Header -->
                <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-primary-50 to-secondary-50">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-xl">
                            <BuildingOfficeIcon class="w-6 h-6 text-purple-600" />
                        </div>
                        <div class="ml-4">
                            <h2 class="text-xl font-semibold text-gray-900">Vendor Information</h2>
                            <p class="text-sm text-gray-600 mt-1">Register a new vendor company and establish partnership details</p>
                        </div>
                    </div>
                </div>

                <!-- Form Content -->
                <form @submit.prevent="submit" class="p-6 bg-white">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Vendor Name -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <BuildingOfficeIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    Vendor Name
                                    <span class="text-red-500 ml-1">*</span>
                                </span>
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-white placeholder-gray-400"
                                placeholder="Enter vendor company name"
                                required
                            />
                            <p v-if="form.errors.name" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <!-- Company Registration Number -->
                        <div>
                            <label for="company_registration_number" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <IdentificationIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    Registration Number
                                    <span class="text-red-500 ml-1">*</span>
                                </span>
                            </label>
                            <input
                                id="company_registration_number"
                                v-model="form.company_registration_number"
                                type="text"
                                class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-white placeholder-gray-400"
                                placeholder="Company registration number"
                                required
                            />
                            <p v-if="form.errors.company_registration_number" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.company_registration_number }}
                            </p>
                        </div>

                        <!-- Contact Person -->
                        <div>
                            <label for="contact_person" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <UserIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    Contact Person
                                    <span class="text-red-500 ml-1">*</span>
                                </span>
                            </label>
                            <input
                                id="contact_person"
                                v-model="form.contact_person"
                                type="text"
                                class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-white placeholder-gray-400"
                                placeholder="Primary contact person name"
                                required
                            />
                            <p v-if="form.errors.contact_person" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.contact_person }}
                            </p>
                        </div>

                        <!-- Email -->
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
                                placeholder="vendor@company.com"
                                required
                            />
                            <p v-if="form.errors.email" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.email }}
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
                                placeholder="+60 3-1234 5678"
                                required
                            />
                            <p v-if="form.errors.phone_number" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.phone_number }}
                            </p>
                        </div>

                        <!-- Commission Rate -->
                        <div>
                            <label for="commission_rate" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <CurrencyDollarIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    Commission Rate (%)
                                    <span class="text-red-500 ml-1">*</span>
                                </span>
                            </label>
                            <input
                                id="commission_rate"
                                v-model="form.commission_rate"
                                type="number"
                                step="0.01"
                                min="0"
                                max="100"
                                class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-white placeholder-gray-400"
                                placeholder="e.g., 15.00"
                                required
                            />
                            <p v-if="form.errors.commission_rate" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.commission_rate }}
                            </p>
                        </div>

                        <!-- Contract Start Date -->
                        <div>
                            <label for="contract_start_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <CalendarIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    Contract Start Date
                                    <span class="text-red-500 ml-1">*</span>
                                </span>
                            </label>
                            <input
                                id="contract_start_date"
                                v-model="form.contract_start_date"
                                type="date"
                                class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-white"
                                required
                            />
                            <p v-if="form.errors.contract_start_date" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.contract_start_date }}
                            </p>
                        </div>

                        <!-- Contract End Date -->
                        <div>
                            <label for="contract_end_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <CalendarIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    Contract End Date
                                </span>
                            </label>
                            <input
                                id="contract_end_date"
                                v-model="form.contract_end_date"
                                type="date"
                                class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-white"
                            />
                            <p v-if="form.errors.contract_end_date" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.contract_end_date }}
                            </p>
                        </div>

                        <!-- Address -->
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <MapPinIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    Company Address
                                    <span class="text-red-500 ml-1">*</span>
                                </span>
                            </label>
                            <textarea
                                id="address"
                                v-model="form.address"
                                class="form-textarea w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-white placeholder-gray-400 resize-y"
                                rows="3"
                                placeholder="Enter complete company address"
                                required
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
                                    Vendor Status
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
                                        <p class="text-xs mt-1 opacity-75">Vendor is active and operational</p>
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
                                        <p class="text-xs mt-1 opacity-75">Vendor is temporarily inactive</p>
                                    </div>
                                </label>
                                <label class="relative">
                                    <input
                                        type="radio"
                                        name="status"
                                        value="suspended"
                                        v-model="form.status"
                                        class="sr-only"
                                    />
                                    <div :class="form.status === 'suspended' ? 'border-red-500 bg-red-50 text-red-700' : 'border-gray-300 bg-white text-gray-700'" class="border-2 rounded-xl p-4 cursor-pointer transition-all duration-200 hover:border-red-400">
                                        <div class="flex items-center">
                                            <ExclamationTriangleIcon class="w-5 h-5 mr-2" />
                                            <span class="font-medium">Suspended</span>
                                        </div>
                                        <p class="text-xs mt-1 opacity-75">Vendor is suspended</p>
                                    </div>
                                </label>
                            </div>
                            <p v-if="form.errors.status" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.status }}
                            </p>
                        </div>

                        <!-- Notes -->
                        <div class="md:col-span-2">
                            <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <DocumentTextIcon class="w-4 h-4 mr-2 text-gray-500" />
                                    Additional Notes
                                </span>
                            </label>
                            <textarea
                                id="notes"
                                v-model="form.notes"
                                class="form-textarea w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-white placeholder-gray-400 resize-y"
                                rows="3"
                                placeholder="Any additional notes or comments about this vendor (optional)"
                            ></textarea>
                            <p v-if="form.errors.notes" class="mt-2 text-sm text-red-600 flex items-center">
                                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                                {{ form.errors.notes }}
                            </p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200 space-x-4">
                        <Link :href="route('vendors.index')" class="btn-secondary-modern">
                            <XMarkIcon class="w-4 h-4 mr-2" />
                            Cancel
                        </Link>
                        <button type="submit" :disabled="form.processing" class="btn-primary">
                            <span v-if="form.processing" class="inline-flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Creating Vendor...
                            </span>
                            <span v-else class="inline-flex items-center">
                                <PlusIcon class="w-4 h-4 mr-2" />
                                Create Vendor
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
    BuildingOfficeIcon,
    IdentificationIcon,
    UserIcon,
    EnvelopeIcon,
    PhoneIcon,
    CurrencyDollarIcon,
    CalendarIcon,
    MapPinIcon,
    CheckCircleIcon,
    XCircleIcon,
    ExclamationTriangleIcon,
    DocumentTextIcon,
    XMarkIcon,
    PlusIcon,
    ExclamationCircleIcon
} from '@heroicons/vue/24/outline';

const form = useForm({
    name: '',
    company_registration_number: '',
    contact_person: '',
    email: '',
    phone_number: '',
    address: '',
    status: 'active',
    contract_start_date: '',
    contract_end_date: '',
    commission_rate: '',
    notes: ''
});

const submit = () => {
    form.post(route('vendors.store'));
};
</script>