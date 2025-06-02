<template>
    <AppLayout title="Edit Incident">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Incident
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Type -->
                            <div>
                                <InputLabel for="type" value="Incident Type" />
                                <select
                                    id="type"
                                    v-model="form.type"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="">Select Type</option>
                                    <option value="accident">Accident</option>
                                    <option value="breakdown">Breakdown</option>
                                    <option value="road_obstruction">Road Obstruction</option>
                                    <option value="weather">Weather</option>
                                    <option value="other">Other</option>
                                </select>
                                <InputError :message="form.errors.type" class="mt-2" />
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
                                    <option value="">Select Status</option>
                                    <option value="reported">Reported</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="resolved">Resolved</option>
                                    <option value="closed">Closed</option>
                                </select>
                                <InputError :message="form.errors.status" class="mt-2" />
                            </div>

                            <!-- Location -->
                            <div>
                                <InputLabel for="latitude" value="Latitude" />
                                <TextInput
                                    id="latitude"
                                    v-model="form.latitude"
                                    type="number"
                                    step="0.000001"
                                    class="input-modern"
                                />
                                <InputError :message="form.errors.latitude" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="longitude" value="Longitude" />
                                <TextInput
                                    id="longitude"
                                    v-model="form.longitude"
                                    type="number"
                                    step="0.000001"
                                    class="input-modern"
                                />
                                <InputError :message="form.errors.longitude" class="mt-2" />
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <InputLabel for="description" value="Description" />
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    rows="4"
                                ></textarea>
                                <InputError :message="form.errors.description" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <Link
                                :href="route('incidents.show', incidentData.id)"
                                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 mr-3"
                            >
                                Cancel
                            </Link>
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Update Incident
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

const props = defineProps({
    incident: Object,
});

const incidentData = props.incident;

const form = useForm({
    type: incidentData.type || '',
    status: incidentData.status || '',
    description: incidentData.description || '',
    latitude: incidentData.latitude ? parseFloat(incidentData.latitude) : '',
    longitude: incidentData.longitude ? parseFloat(incidentData.longitude) : '',
});

function submit() {
    form.put(route('incidents.update', incidentData.id), {
        preserveScroll: true,
    });
}
</script>