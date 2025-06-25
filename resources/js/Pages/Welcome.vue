<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    appName: String,
    appDescription: String,
});
</script>

<template>
    <Head title="Transportation Tracking System" />
    
    <div class="min-h-screen bg-gradient-to-br from-primary-50 to-secondary-50 flex flex-col items-center justify-center">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <!-- Logo -->
            <div class="mb-8">
                <img src="/images/Maraliner_logo_Full.png" alt="MaraLinear Logo" class="h-20 mx-auto" />
            </div>
            
            <!-- Hero Section -->
            <h1 class="text-5xl font-bold text-gray-900 mb-6">
                Transportation Tracking System
            </h1>
            <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
                Real-time vehicle and route tracking for efficient transportation management
            </p>
            
            <!-- Login/Register Buttons -->
            <div v-if="canLogin" class="flex justify-center space-x-4 mb-12">
                <Link
                    :href="route('login')"
                    class="bg-gradient-to-r from-primary-500 to-primary-600 text-white px-8 py-3 rounded-xl font-semibold hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-lg"
                >
                    Sign In
                </Link>
                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="bg-white text-primary-600 px-8 py-3 rounded-xl font-semibold hover:bg-gray-50 transition-all duration-200 shadow-lg border border-primary-200"
                >
                    Register
                </Link>
            </div>
            
            <!-- Features Grid -->
            <div class="grid md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Real-time Tracking</h3>
                    <p class="text-gray-600">Monitor vehicle locations and routes in real-time</p>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Fleet Management</h3>
                    <p class="text-gray-600">Comprehensive vehicle and driver management</p>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2L3 7v11a1 1 0 001 1h12a1 1 0 001-1V7l-7-5zM8 15a1 1 0 01-2 0v-3a1 1 0 012 0v3zm4 0a1 1 0 01-2 0v-3a1 1 0 012 0v3z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Emergency Response</h3>
                    <p class="text-gray-600">SOS alerts and incident management system</p>
                </div>
            </div>
        </div>
    </div>
</template>
