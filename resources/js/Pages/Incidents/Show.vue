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
                    
                    <!-- Photo Section -->
                    <div v-if="incidentData.photo_path" class="mb-8">
                        <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Incident Photo
                            </h3>
                            <div class="relative group">
                                <img 
                                    :src="getPhotoUrl(incidentData.photo_path)" 
                                    :alt="`${formatIncidentType(incidentData.type)} incident photo`"
                                    class="w-full max-w-2xl mx-auto rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 cursor-pointer"
                                    @click="openPhotoModal"
                                    @error="handleImageError"
                                />
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all duration-300 rounded-xl flex items-center justify-center">
                                    <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 text-center">
                                <p class="text-sm text-gray-600">Click to view full size</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Show message if no photo -->
                    <div v-else class="mb-8 p-4 bg-gray-100 rounded">
                        <p class="text-gray-600">No photo available for this incident</p>
                    </div>

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
                            :href="route('incidents.index')" 
                            class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                        >
                            Back to Incidents
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Photo Modal -->
        <div v-if="showPhotoModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75" @click="closePhotoModal">
            <div class="relative max-w-4xl max-h-screen p-4">
                <button @click="closePhotoModal" class="absolute top-2 right-2 text-white hover:text-gray-300 z-10">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                <img 
                    :src="getPhotoUrl(incidentData.photo_path)" 
                    :alt="`${formatIncidentType(incidentData.type)} incident photo`"
                    class="max-w-full max-h-full rounded-lg shadow-2xl"
                    @click.stop
                />
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
const showPhotoModal = ref(false);

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

function getPhotoUrl(photoPath) {
    if (!photoPath) {
        return '';
    }
    
    // Handle different possible path formats
    let url = '';
    if (photoPath.startsWith('http')) {
        // Full URL
        url = photoPath;
    } else if (photoPath.startsWith('storage/')) {
        // Already has storage prefix
        url = `/${photoPath}`;
    } else if (photoPath.startsWith('incident_photos/')) {
        // Relative path from storage
        url = `/storage/${photoPath}`;
    } else {
        // Fallback
        url = `/storage/incident_photos/${photoPath}`;
    }
    
    return url;
}

function openPhotoModal() {
    showPhotoModal.value = true;
    document.body.style.overflow = 'hidden';
}

function closePhotoModal() {
    showPhotoModal.value = false;
    document.body.style.overflow = 'auto';
}

function handleImageError(event) {
    console.error('Failed to load incident photo:', event.target.src);
    // Optionally hide the image or show a placeholder
    event.target.style.display = 'none';
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