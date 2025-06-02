<template>
    <AppLayout title="SOS Alert Details">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                SOS Alert Details
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <!-- Alert Status Banner -->
                    <div v-if="sosAlertData.status === 'active'" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <strong>ACTIVE ALERT:</strong> This SOS alert requires immediate attention!
                    </div>

                    <!-- SOS Alert Information -->
                    <div class="card-modern p-6 space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">SOS Alert Details</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Driver</dt>
                                <dd class="text-sm text-gray-900 font-semibold">{{ sosAlertData.driver?.user?.name || 'Unknown' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="text-sm text-gray-900">
                                    <span :class="['px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-xl', getStatusColor(sosAlertData.status)]">
                                        {{ sosAlertData.status?.charAt(0).toUpperCase() + sosAlertData.status?.slice(1) }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Message</dt>
                                <dd class="text-sm text-gray-900">{{ sosAlertData.message || 'No message provided' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Location</dt>
                                <dd class="text-sm text-gray-900">
                                    {{ sosAlertData.latitude }}, {{ sosAlertData.longitude }}
                                </dd>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-6 flex items-center space-x-4">
                        <button 
                            v-if="sosAlertData.status === 'active'" 
                            @click="respondToSos" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
                        >
                            Mark as Responded
                        </button>
                        <button 
                            v-if="sosAlertData.status !== 'resolved'" 
                            @click="resolveSos" 
                            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700"
                        >
                            Mark as Resolved
                        </button>
                        <Link 
                            :href="route('sos.index')" 
                            class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                        >
                            Back to SOS Alerts
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';

const props = defineProps({
    sosAlert: Object,
});

const sosAlertData = props.sosAlert;

function formatDateTime(dateString) {
    return new Date(dateString).toLocaleString();
}

function formatCoordinate(coord) {
    if (coord !== null && coord !== undefined && typeof parseFloat(coord) === 'number' && !isNaN(parseFloat(coord))) {
        return parseFloat(coord).toFixed(6);
    }
    return 'N/A';
}

function getStatusColor(status) {
    switch (status) {
        case 'active':
            return 'bg-red-100 text-red-800';
        case 'responded':
            return 'bg-yellow-100 text-yellow-800';
        case 'resolved':
            return 'bg-green-100 text-green-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

function respondToSos() {
    axios.post(`/sos/${sosAlertData.id}/respond`)
        .then(() => {
            router.reload();
        })
        .catch(error => {
            console.error('Error responding to SOS alert:', error);
        });
}

function resolveSos() {
    axios.post(`/sos/${sosAlertData.id}/resolve`)
        .then(() => {
            router.reload();
        })
        .catch(error => {
            console.error('Error resolving SOS alert:', error);
        });
}
</script>