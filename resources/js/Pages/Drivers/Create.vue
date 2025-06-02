<template>
    <AppLayout title="Create Driver">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Driver
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">
                                    Account Creation Notice
                                </h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>A random secure password will be automatically generated and sent to the driver's email address along with their login credentials.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Vendor Selection -->
                            <div class="md:col-span-2">
                                <FormInput
                                    id="vendor_id"
                                    label="Vendor"
                                    type="select"
                                    v-model="form.vendor_id"
                                    :options="vendors.map(v => ({ value: v.id, label: v.name }))"
                                    placeholder="Select Vendor"
                                    :error="form.errors.vendor_id"
                                    required
                                />
                            </div>

                            <!-- Name -->
                            <FormInput
                                id="name"
                                label="Name"
                                v-model="form.name"
                                placeholder="Enter driver's full name"
                                :error="form.errors.name"
                                required
                            />

                            <!-- Email -->
                            <FormInput
                                id="email"
                                label="Email"
                                type="email"
                                v-model="form.email"
                                placeholder="driver@example.com"
                                :error="form.errors.email"
                                autocomplete="email"
                                required
                            />

                            <!-- License Number -->
                            <FormInput
                                id="license_number"
                                label="License Number"
                                v-model="form.license_number"
                                placeholder="Enter license number"
                                :error="form.errors.license_number"
                                required
                            />

                            <!-- Phone Number -->
                            <FormInput
                                id="phone_number"
                                label="Phone Number"
                                type="tel"
                                v-model="form.phone_number"
                                placeholder="+60123456789"
                                :error="form.errors.phone_number"
                                required
                            />

                            <!-- Address -->
                            <div class="md:col-span-2">
                                <FormInput
                                    id="address"
                                    label="Address"
                                    type="textarea"
                                    v-model="form.address"
                                    placeholder="Enter complete address"
                                    :error="form.errors.address"
                                    :rows="3"
                                />
                            </div>

                            <!-- Status -->
                            <FormInput
                                id="status"
                                label="Status"
                                type="select"
                                v-model="form.status"
                                :options="[
                                    { value: 'active', label: 'Active' },
                                    { value: 'inactive', label: 'Inactive' },
                                    { value: 'on_leave', label: 'On Leave' }
                                ]"
                                :error="form.errors.status"
                                required
                            />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <Link
                                :href="route('drivers.index')"
                                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:shadow-outline-gray transition ease-in-out duration-150 mr-3"
                            >
                                Cancel
                            </Link>
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Create Driver
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
import FormInput from '@/Components/FormInput.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

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