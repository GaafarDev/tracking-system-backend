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
const sidebarOpen = ref(false);
</script>

<template>
    <div>
        <Head :title="title" />
        
        <Banner />
        
        <div class="flex h-screen bg-gradient-to-br from-slate-50 via-blue-50/30 to-slate-50">
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
                <main class="flex-1 overflow-y-auto p-6">
                    <div class="max-w-7xl mx-auto">
                        <slot />
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>