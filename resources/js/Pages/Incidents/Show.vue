<template>
    <AppLayout title="Incident Details">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Incident Details
                </h2>
                <div class="flex space-x-2">
                    <Link 
                        :href="route('incidents.edit', incidentData.id)" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
                    >
                        Edit Incident
                    </Link>
                    <Link 
                        :href="route('incidents.index')" 
                        class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                    >
                        Back to Incidents
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <!-- Incident Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Incident Details</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Type</dt>
                                    <dd class="text-sm text-gray-900">{{ formatIncidentType(incidentData.type) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="text-sm text-gray-900">
                                        <span :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusColor(incidentData.status)]">
                                            {{ formatStatus(incidentData.status) }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                                    <dd class="text-sm text-gray-900">{{ incidentData.description || 'No description provided' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Location</dt>
                                    <dd class="text-sm text-gray-900">
                                        {{ formatCoordinate(incidentData.latitude) }}, {{ formatCoordinate(incidentData.longitude) }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Reported By</dt>
                                    <dd class="text-sm text-gray-900">{{ incidentData.driver?.user?.name || 'Unknown' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Reported At</dt>
                                    <dd class="text-sm text-gray-900">{{ formatDateTime(incidentData.created_at) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                    <dd class="text-sm text-gray-900">{{ formatDateTime(incidentData.updated_at) }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-6 flex items-center space-x-4">
                        <button 
                            v-if="!['resolved', 'closed'].includes(incidentData.status)" 
                            @click="resolveIncident" 
                            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700"
                        >
                            Mark as Resolved
                        </button>
                        <Link 
                            :href="route('incidents.edit', incidentData.id)" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
                        >
                            Edit Incident
                        </Link>
                        <Link 
                            :href="route('incidents.index')" 
                            class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                        >
                            Back to Incidents
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';

const props = defineProps({
    incident: Object,
});

const incidentData = props.incident;

function formatDateTime(dateString) {
    return new Date(dateString).toLocaleString();
}

function formatIncidentType(type) {
    return type.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
}

function formatStatus(status) {
    return status.charAt(0).toUpperCase() + status.slice(1).replace('_', ' ');
}

function formatCoordinate(coord) {
    if (coord !== null && coord !== undefined && typeof parseFloat(coord) === 'number' && !isNaN(parseFloat(coord))) {
        return parseFloat(coord).toFixed(6);
    }
    return 'N/A';
}

function getStatusColor(status) {
    switch (status) {
        case 'reported':
            return 'bg-red-100 text-red-800';
        case 'in_progress':
            return 'bg-yellow-100 text-yellow-800';
        case 'resolved':
            return 'bg-blue-100 text-blue-800';
        case 'closed':
            return 'bg-green-100 text-green-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

function resolveIncident() {
    axios.post(`/incidents/${props.incident.id}/resolve`)
        .then(() => {
            router.reload();
        })
        .catch(error => {
            console.error('Error resolving incident:', error);
        });
}
</script>