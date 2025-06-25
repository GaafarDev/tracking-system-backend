<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Create Account - Transportation Tracking System" />

    <div class="min-h-screen bg-gradient-to-br from-primary-50 to-secondary-50 flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Logo Section -->
            <div class="text-center">
                <img src="/images/Maraliner_logo_Full.png" alt="MaraLinear Logo" class="h-16 mx-auto mb-6" />
                <h2 class="text-3xl font-bold text-gray-900 mb-2">
                    Create Account
                </h2>
                <p class="text-gray-600">
                    Join our Transportation Tracking System
                </p>
            </div>

            <!-- Register Form Card -->
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="name" value="Full Name" class="text-gray-700 font-medium" />
                        <TextInput
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="mt-2 block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-gray-50 focus:bg-white"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="Enter your full name"
                        />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div>
                        <InputLabel for="email" value="Email Address" class="text-gray-700 font-medium" />
                        <TextInput
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="mt-2 block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-gray-50 focus:bg-white"
                            required
                            autocomplete="username"
                            placeholder="Enter your email address"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div>
                        <InputLabel for="password" value="Password" class="text-gray-700 font-medium" />
                        <TextInput
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="mt-2 block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-gray-50 focus:bg-white"
                            required
                            autocomplete="new-password"
                            placeholder="Create a strong password"
                        />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div>
                        <InputLabel for="password_confirmation" value="Confirm Password" class="text-gray-700 font-medium" />
                        <TextInput
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            class="mt-2 block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-gray-50 focus:bg-white"
                            required
                            autocomplete="new-password"
                            placeholder="Confirm your password"
                        />
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>

                    <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="mt-4">
                        <InputLabel for="terms">
                            <div class="flex items-start">
                                <Checkbox 
                                    id="terms" 
                                    v-model:checked="form.terms" 
                                    name="terms" 
                                    required 
                                    class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500 mt-1"
                                />
                                <div class="ml-3 text-sm">
                                    <span class="text-gray-600">
                                        I agree to the 
                                        <a target="_blank" :href="route('terms.show')" class="text-primary-600 hover:text-primary-700 font-medium transition-colors duration-200">
                                            Terms of Service
                                        </a> 
                                        and 
                                        <a target="_blank" :href="route('policy.show')" class="text-primary-600 hover:text-primary-700 font-medium transition-colors duration-200">
                                            Privacy Policy
                                        </a>
                                    </span>
                                </div>
                            </div>
                            <InputError class="mt-2" :message="form.errors.terms" />
                        </InputLabel>
                    </div>

                    <PrimaryButton 
                        class="w-full justify-center py-3 px-4 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold rounded-xl shadow-lg hover:from-primary-600 hover:to-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200 transform hover:scale-105 active:scale-95" 
                        :class="{ 'opacity-25': form.processing }" 
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Creating account...</span>
                        <span v-else>Create Account</span>
                    </PrimaryButton>
                </form>

                <!-- Login Link -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Already have an account?
                        <Link :href="route('login')" class="font-medium text-primary-600 hover:text-primary-700 transition-colors duration-200">
                            Sign in
                        </Link>
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center">
                <p class="text-xs text-gray-500">
                    © 2024 MaraLinear Transportation System. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</template>
