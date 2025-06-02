<template>
    <AppLayout title="Create Vendor">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Vendor
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <InputLabel for="name" value="Vendor Name" />
                                <TextInput
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="input-modern"
                                    required
                                />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <!-- Company Registration Number -->
                            <div>
                                <InputLabel for="company_registration_number" value="Company Registration Number" />
                                <TextInput
                                    id="company_registration_number"
                                    v-model="form.company_registration_number"
                                    type="text"
                                    class="input-modern"
                                    required
                                />
                                <InputError :message="form.errors.company_registration_number" class="mt-2" />
                            </div>

                            <!-- Contact Person -->
                            <div>
                                <InputLabel for="contact_person" value="Contact Person" />
                                <TextInput
                                    id="contact_person"
                                    v-model="form.contact_person"
                                    type="text"
                                    class="input-modern"
                                    required
                                />
                                <InputError :message="form.errors.contact_person" class="mt-2" />
                            </div>

                            <!-- Email -->
                            <div>
                                <InputLabel for="email" value="Email" />
                                <TextInput
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    class="input-modern"
                                    required
                                />
                                <InputError :message="form.errors.email" class="mt-2" />
                            </div>

                            <!-- Phone Number -->
                            <div>
                                <InputLabel for="phone_number" value="Phone Number" />
                                <TextInput
                                    id="phone_number"
                                    v-model="form.phone_number"
                                    type="text"
                                    class="input-modern"
                                    required
                                />
                                <InputError :message="form.errors.phone_number" class="mt-2" />
                            </div>

                            <!-- Commission Rate -->
                            <div>
                                <InputLabel for="commission_rate" value="Commission Rate (%)" />
                                <TextInput
                                    id="commission_rate"
                                    v-model="form.commission_rate"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    max="100"
                                    class="input-modern"
                                    required
                                />
                                <InputError :message="form.errors.commission_rate" class="mt-2" />
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
                                    <option value="inactive">Inactive</option>
                                    <option value="suspended">Suspended</option>
                                </select>
                                <InputError :message="form.errors.status" class="mt-2" />
                            </div>

                            <!-- Contract Start Date -->
                            <div>
                                <InputLabel for="contract_start_date" value="Contract Start Date" />
                                <TextInput
                                    id="contract_start_date"
                                    v-model="form.contract_start_date"
                                    type="date"
                                    class="input-modern"
                                    required
                                />
                                <InputError :message="form.errors.contract_start_date" class="mt-2" />
                            </div>

                            <!-- Contract End Date -->
                            <div>
                                <InputLabel for="contract_end_date" value="Contract End Date" />
                                <TextInput
                                    id="contract_end_date"
                                    v-model="form.contract_end_date"
                                    type="date"
                                    class="input-modern"
                                />
                                <InputError :message="form.errors.contract_end_date" class="mt-2" />
                            </div>

                            <!-- Address -->
                            <div class="md:col-span-2">
                                <InputLabel for="address" value="Address" />
                                <textarea
                                    id="address"
                                    v-model="form.address"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    rows="3"
                                    required
                                ></textarea>
                                <InputError :message="form.errors.address" class="mt-2" />
                            </div>

                            <!-- Notes -->
                            <div class="md:col-span-2">
                                <InputLabel for="notes" value="Notes (Optional)" />
                                <textarea
                                    id="notes"
                                    v-model="form.notes"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    rows="3"
                                ></textarea>
                                <InputError :message="form.errors.notes" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <Link
                                :href="route('vendors.index')"
                                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 mr-3"
                            >
                                Cancel
                            </Link>
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Create Vendor
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