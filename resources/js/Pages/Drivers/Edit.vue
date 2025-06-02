<template>
    <AppLayout title="Edit Driver">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Driver: {{ driver.user.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <InputLabel for="name" value="Name" />
                                <TextInput
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <!-- Email -->
                            <div>
                                <InputLabel for="email" value="Email" />
                                <TextInput
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.email" class="mt-2" />
                            </div>

                            <!-- License Number -->
                            <div>
                                <InputLabel for="license_number" value="License Number" />
                                <TextInput
                                    id="license_number"
                                    v-model="form.license_number"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.license_number" class="mt-2" />
                            </div>

                            <!-- Phone Number -->
                            <div>
                                <InputLabel for="phone_number" value="Phone Number" />
                                <TextInput
                                    id="phone_number"
                                    v-model="form.phone_number"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.phone_number" class="mt-2" />
                            </div>

                            <!-- Address -->
                            <div class="md:col-span-2">
                                <InputLabel for="address" value="Address" />
                                <textarea
                                    id="address"
                                    v-model="form.address"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    rows="3"
                                ></textarea>
                                <InputError :message="form.errors.address" class="mt-2" />
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
                                    <option value="on_leave">On Leave</option>
                                </select>
                                <InputError :message="form.errors.status" class="mt-2" />
                            </div>
                        </div>

                        <!-- Password Reset Section -->
                        <div class="mt-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <h3 class="text-lg font-medium text-yellow-800 mb-2">Password Reset</h3>
                            <p class="text-sm text-yellow-700 mb-4">
                                If you need to reset the driver's password, click the button below. A new password will be generated and sent to their email address.
                            </p>
                            <button 
                                type="button" 
                                @click="resetPassword" 
                                class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700"
                                :disabled="isResettingPassword"
                            >
                                {{ isResettingPassword ? 'Resetting...' : 'Reset Password' }}
                            </button>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <Link
                                :href="route('drivers.index')"
                                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:shadow-outline-gray transition ease-in-out duration-150 mr-3"
                            >
                                Cancel
                            </Link>
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Update Driver
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    driver: Object,
});

const isResettingPassword = ref(false);

const form = useForm({
    name: props.driver.user.name,
    email: props.driver.user.email,
    license_number: props.driver.license_number,
    phone_number: props.driver.phone_number,
    address: props.driver.address,
    status: props.driver.status,
});

const submit = () => {
    form.put(route('drivers.update', props.driver.id));
};

const resetPassword = () => {
    if (confirm(`Are you sure you want to reset the password for ${props.driver.user.name}? A new password will be sent to their email.`)) {
        isResettingPassword.value = true;
        
        router.post(route('drivers.reset-password', props.driver.id), {}, {
            preserveScroll: true,
            onSuccess: () => {
                // Success message will be shown via flash message
            },
            onError: (errors) => {
                console.error('Password reset failed:', errors);
            },
            onFinish: () => {
                isResettingPassword.value = false;
            }
        });
    }
};
</script>