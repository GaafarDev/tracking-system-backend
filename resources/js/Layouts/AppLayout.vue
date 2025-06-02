<script setup>
import { ref, computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';
import TopHeader from '@/Components/TopHeader.vue';
import Banner from '@/Components/Banner.vue';

defineProps({
    title: String,
});

const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<template>
    <div>
        <Head :title="title" />
        
        <Banner />
        
        <div class="flex h-screen bg-gray-50">
            <!-- Sidebar -->
            <Sidebar :active-sos-count="0" />
            
            <!-- Main content -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Top Header -->
                <TopHeader 
                    :title="title" 
                    :user="user"
                    :notification-count="0"
                />
                
                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto">
                    <div class="py-6">
                        <slot />
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>