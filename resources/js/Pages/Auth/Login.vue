<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Sign In - Transportation Tracking System" />

    <div class="min-h-screen bg-gradient-to-br from-primary-50 to-secondary-50 flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Logo Section -->
            <div class="text-center">
                <img src="/images/Maraliner_logo_Full.png" alt="MaraLinear Logo" class="h-16 mx-auto mb-6" />
                <h2 class="text-3xl font-bold text-gray-900 mb-2">
                    Welcome Back
                </h2>
                <p class="text-gray-600">
                    Sign in to your Transportation Tracking System account
                </p>
            </div>

            <!-- Login Form Card -->
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div v-if="status" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
                    <p class="text-sm text-green-600">{{ status }}</p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="email" value="Email Address" class="text-gray-700 font-medium" />
                        <TextInput
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="mt-2 block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-gray-50 focus:bg-white"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="Enter your email"
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
                            autocomplete="current-password"
                            placeholder="Enter your password"
                        />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <Checkbox v-model:checked="form.remember" name="remember" class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500" />
                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                        </label>

                        <Link 
                            v-if="canResetPassword" 
                            :href="route('password.request')" 
                            class="text-sm text-primary-600 hover:text-primary-700 font-medium transition-colors duration-200"
                        >
                            Forgot password?
                        </Link>
                    </div>

                    <PrimaryButton 
                        class="w-full justify-center py-3 px-4 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold rounded-xl shadow-lg hover:from-primary-600 hover:to-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200 transform hover:scale-105 active:scale-95" 
                        :class="{ 'opacity-25': form.processing }" 
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Signing in...</span>
                        <span v-else>Sign In</span>
                    </PrimaryButton>
                </form>

                <!-- Register Link -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Don't have an account?
                        <Link :href="route('register')" class="font-medium text-primary-600 hover:text-primary-700 transition-colors duration-200">
                            Create account
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
