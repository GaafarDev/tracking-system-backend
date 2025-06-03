<template>
    <AppLayout title="Profile">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Profile Settings</h1>
                <p class="text-gray-600 mt-1">Manage your account settings and preferences</p>
            </div>

            <div class="space-y-6">
                <!-- Profile Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <UpdateProfileInformationForm :user="$page.props.auth.user" />
                </div>

                <!-- Update Password -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <UpdatePasswordForm />
                </div>

                <!-- Two Factor Authentication -->
                <div v-if="$page.props.jetstream.canManageTwoFactorAuthentication" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <TwoFactorAuthenticationForm 
                        :requires-confirmation="confirmsTwoFactorAuthentication"
                        class="mt-10 sm:mt-0"
                    />
                </div>

                <!-- Browser Sessions -->
                <div v-if="sessions && sessions.length > 0" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <LogoutOtherBrowserSessionsForm :sessions="sessions" class="mt-10 sm:mt-0" />
                </div>

                <!-- Delete Account -->
                <div v-if="$page.props.jetstream.hasAccountDeletionFeatures" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <DeleteUserForm class="mt-10 sm:mt-0" />
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