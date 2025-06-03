<template>
    <AppLayout title="Profile">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gradient-primary">Profile Settings</h1>
                <p class="text-gray-600 mt-2">Manage your account settings and security preferences</p>
            </div>

            <div class="space-y-8">
                <!-- Profile Information -->
                <div class="card-modern">
                    <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-primary-50 to-secondary-50">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-100 rounded-xl">
                                <UserIcon class="w-6 h-6 text-blue-600" />
                            </div>
                            <div class="ml-4">
                                <h2 class="text-xl font-semibold text-gray-900">Profile Information</h2>
                                <p class="text-sm text-gray-600 mt-1">Update your account's profile information and email address</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 bg-white">
                        <UpdateProfileInformationForm :user="$page.props.auth.user" />
                    </div>
                </div>

                <!-- Update Password -->
                <div class="card-modern">
                    <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-primary-50 to-secondary-50">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-100 rounded-xl">
                                <LockClosedIcon class="w-6 h-6 text-green-600" />
                            </div>
                            <div class="ml-4">
                                <h2 class="text-xl font-semibold text-gray-900">Update Password</h2>
                                <p class="text-sm text-gray-600 mt-1">Ensure your account is using a long, random password to stay secure</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 bg-white">
                        <UpdatePasswordForm />
                    </div>
                </div>

                <!-- Two Factor Authentication -->
                <div v-if="$page.props.jetstream.canManageTwoFactorAuthentication" class="card-modern">
                    <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-primary-50 to-secondary-50">
                        <div class="flex items-center">
                            <div class="p-3 bg-purple-100 rounded-xl">
                                <ShieldCheckIcon class="w-6 h-6 text-purple-600" />
                            </div>
                            <div class="ml-4">
                                <h2 class="text-xl font-semibold text-gray-900">Two Factor Authentication</h2>
                                <p class="text-sm text-gray-600 mt-1">Add additional security to your account using two factor authentication</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 bg-white">
                        <TwoFactorAuthenticationForm 
                            :requires-confirmation="confirmsTwoFactorAuthentication"
                        />
                    </div>
                </div>

                <!-- Browser Sessions -->
                <div v-if="sessions && sessions.length > 0" class="card-modern">
                    <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-primary-50 to-secondary-50">
                        <div class="flex items-center">
                            <div class="p-3 bg-indigo-100 rounded-xl">
                                <ComputerDesktopIcon class="w-6 h-6 text-indigo-600" />
                            </div>
                            <div class="ml-4">
                                <h2 class="text-xl font-semibold text-gray-900">Browser Sessions</h2>
                                <p class="text-sm text-gray-600 mt-1">Manage and log out your active sessions on other browsers and devices</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 bg-white">
                        <LogoutOtherBrowserSessionsForm :sessions="sessions" />
                    </div>
                </div>

                <!-- Delete Account -->
                <div v-if="$page.props.jetstream.hasAccountDeletionFeatures" class="card-modern border-red-200">
                    <div class="px-6 py-5 border-b border-red-200 bg-gradient-to-r from-red-50 to-pink-50">
                        <div class="flex items-center">
                            <div class="p-3 bg-red-100 rounded-xl">
                                <TrashIcon class="w-6 h-6 text-red-600" />
                            </div>
                            <div class="ml-4">
                                <h2 class="text-xl font-semibold text-gray-900">Delete Account</h2>
                                <p class="text-sm text-gray-600 mt-1">Permanently delete your account and all of its data</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 bg-white">
                        <DeleteUserForm />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';
import { 
    UserIcon, 
    LockClosedIcon, 
    ShieldCheckIcon, 
    ComputerDesktopIcon, 
    TrashIcon 
} from '@heroicons/vue/24/outline';

defineProps({
    confirmsTwoFactorAuthentication: {
        type: Boolean,
        default: false
    },
    sessions: {
        type: Array,
        default: () => []
    },
});
</script>