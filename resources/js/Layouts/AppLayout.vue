<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { HomeIcon, UserGroupIcon, BellIcon, TruckIcon, MapIcon, CalendarIcon, ExclamationTriangleIcon, ShieldExclamationIcon, BuildingOfficeIcon } from '@heroicons/vue/24/outline';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);
// Add this for SOS alerts count - you'll need to pass this from your backend
const activeSosAlertsCount = ref(0); // Replace with actual prop or computed value

const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div>
        <!-- Remove the Head section with Leaflet links from here since it's already in app.js -->
        <Banner />

        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white/80 backdrop-blur-xl border-b border-gray-200/50 sticky top-0 z-50 shadow-sm">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <!-- Enhanced Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('dashboard')" class="flex items-center space-x-3 group">
                                    <div class="relative">
                                        <ApplicationMark class="block h-10 w-auto text-red-600 group-hover:text-red-700 transition-all duration-300 transform group-hover:scale-105" />
                                        <div class="absolute -inset-2 bg-gradient-to-r from-red-600/20 to-purple-600/20 rounded-lg blur opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                    </div>
                                    <div class="hidden md:block">
                                        <span class="text-xl font-bold bg-gradient-to-r from-gray-900 via-red-600 to-purple-600 bg-clip-text text-transparent">
                                            MaraLinear
                                        </span>
                                        <div class="text-xs text-gray-500 -mt-1">Transportation Hub</div>
                                    </div>
                                </Link>
                            </div>

                            <!-- Enhanced Navigation Links -->
                            <div class="hidden space-x-1 sm:-my-px sm:ms-10 sm:flex">
                                <Link :href="route('dashboard')" 
                                      :class="route().current('dashboard') ? 'bg-blue-50 text-blue-700 border-blue-200 shadow-sm' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50'"
                                      class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 border">
                                    <HomeIcon class="w-4 h-4 mr-2" />
                                    Dashboard
                                </Link>
                                
                                <Link :href="route('drivers.index')" 
                                      :class="route().current('drivers.*') ? 'bg-emerald-50 text-emerald-700 border-emerald-200 shadow-sm' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50'"
                                      class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 border">
                                    <UserGroupIcon class="w-4 h-4 mr-2" />
                                    Drivers
                                </Link>
                                
                                <Link :href="route('vehicles.index')" 
                                      :class="route().current('vehicles.*') ? 'bg-purple-50 text-purple-700 border-purple-200 shadow-sm' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50'"
                                      class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 border">
                                    <TruckIcon class="w-4 h-4 mr-2" />
                                    Vehicles
                                </Link>
                                
                                <Link :href="route('routes.index')" 
                                      :class="route().current('routes.*') ? 'bg-indigo-50 text-indigo-700 border-indigo-200 shadow-sm' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50'"
                                      class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 border">
                                    <MapIcon class="w-4 h-4 mr-2" />
                                    Routes
                                </Link>
                                
                                <Link :href="route('schedules.index')" 
                                      :class="route().current('schedules.*') ? 'bg-amber-50 text-amber-700 border-amber-200 shadow-sm' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50'"
                                      class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 border">
                                    <CalendarIcon class="w-4 h-4 mr-2" />
                                    Schedules
                                </Link>
                                
                                <Link :href="route('incidents.index')" 
                                      :class="route().current('incidents.*') ? 'bg-orange-50 text-orange-700 border-orange-200 shadow-sm' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50'"
                                      class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 border">
                                    <ExclamationTriangleIcon class="w-4 h-4 mr-2" />
                                    Incidents
                                </Link>
                                
                                <Link :href="route('sos.index')" 
                                      :class="route().current('sos.*') ? 'bg-red-50 text-red-700 border-red-200 shadow-sm' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50'"
                                      class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 border relative">
                                    <ShieldExclamationIcon class="w-4 h-4 mr-2" />
                                    SOS
                                    <span v-if="activeSosAlertsCount > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center animate-pulse">
                                        {{ activeSosAlertsCount }}
                                    </span>
                                </Link>
                                
                                <Link :href="route('vendors.index')" 
                                      :class="route().current('vendors.*') ? 'bg-teal-50 text-teal-700 border-teal-200 shadow-sm' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50'"
                                      class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 border">
                                    <BuildingOfficeIcon class="w-4 h-4 mr-2" />
                                    Vendors
                                </Link>
                            </div>
                        </div>

                        <!-- Enhanced user dropdown with notifications -->
                        <div class="hidden sm:flex sm:items-center sm:space-x-4">
                            <!-- Notifications Bell -->
                            <button class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                <BellIcon class="w-6 h-6" />
                                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                            </button>
                            
                            <div class="ms-3 relative">
                                <!-- Teams Dropdown -->
                                <Dropdown v-if="$page.props.jetstream.hasTeamFeatures" align="right" width="60">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                                {{ $page.props.auth.user.current_team.name }}

                                                <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <div class="w-60">
                                            <!-- Team Management -->
                                            <div class="block px-4 py-2 text-xs text-gray-400">
                                                Manage Team
                                            </div>

                                            <!-- Team Settings -->
                                            <DropdownLink :href="route('teams.show', $page.props.auth.user.current_team)">
                                                Team Settings
                                            </DropdownLink>

                                            <DropdownLink v-if="$page.props.jetstream.canCreateTeams" :href="route('teams.create')">
                                                Create New Team
                                            </DropdownLink>

                                            <!-- Team Switcher -->
                                            <template v-if="$page.props.auth.user.all_teams.length > 1">
                                                <div class="border-t border-gray-200" />

                                                <div class="block px-4 py-2 text-xs text-gray-400">
                                                    Switch Teams
                                                </div>

                                                <template v-for="team in $page.props.auth.user.all_teams" :key="team.id">
                                                    <form @submit.prevent="switchToTeam(team)">
                                                        <DropdownLink as="button">
                                                            <div class="flex items-center">
                                                                <svg v-if="team.id == $page.props.auth.user.current_team_id" class="me-2 size-5 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>

                                                                <div>{{ team.name }}</div>
                                                            </div>
                                                        </DropdownLink>
                                                    </form>
                                                </template>
                                            </template>
                                        </div>
                                    </template>
                                </Dropdown>
                            </div>

                            <!-- Settings Dropdown -->
                            <div class="ms-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                            <img class="size-8 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                                        </button>

                                        <span v-else class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                                {{ $page.props.auth.user.name }}

                                                <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <!-- Account Management -->
                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            Manage Account
                                        </div>

                                        <DropdownLink :href="route('profile.show')">
                                            Profile
                                        </DropdownLink>

                                        <DropdownLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')">
                                            API Tokens
                                        </DropdownLink>

                                        <div class="border-t border-gray-200" />

                                        <!-- Authentication -->
                                        <form @submit.prevent="logout">
                                            <DropdownLink as="button">
                                                Log Out
                                            </DropdownLink>
                                        </form>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out" @click="showingNavigationDropdown = ! showingNavigationDropdown">
                                <svg
                                    class="size-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('drivers.index')" :active="route().current('drivers.*')">
                            Drivers
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('vehicles.index')" :active="route().current('vehicles.*')">
                            Vehicles
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('routes.index')" :active="route().current('routes.*')">
                            Routes
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('schedules.index')" :active="route().current('schedules.*')">
                            Schedules
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('incidents.index')" :active="route().current('incidents.*')">
                            Incidents
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('sos.index')" :active="route().current('sos.*')">
                            SOS Alerts
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('vendors.index')" :active="route().current('vendors.*')">
                            Vendors
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="flex items-center px-4">
                            <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 me-3">
                                <img class="size-10 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                            </div>

                            <div>
                                <div class="font-medium text-base text-gray-800">
                                    {{ $page.props.auth.user.name }}
                                </div>
                                <div class="font-medium text-sm text-gray-500">
                                    {{ $page.props.auth.user.email }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.show')" :active="route().current('profile.show')">
                                Profile
                            </ResponsiveNavLink>

                            <ResponsiveNavLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')" :active="route().current('api-tokens.index')">
                                API Tokens
                            </ResponsiveNavLink>

                            <!-- Authentication -->
                            <form method="POST" @submit.prevent="logout">
                                <ResponsiveNavLink as="button">
                                    Log Out
                                </ResponsiveNavLink>
                            </form>

                            <!-- Team Management -->
                            <template v-if="$page.props.jetstream.hasTeamFeatures">
                                <div class="border-t border-gray-200" />

                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    Manage Team
                                </div>

                                <!-- Team Settings -->
                                <ResponsiveNavLink :href="route('teams.show', $page.props.auth.user.current_team)" :active="route().current('teams.show')">
                                    Team Settings
                                </ResponsiveNavLink>

                                <ResponsiveNavLink v-if="$page.props.jetstream.canCreateTeams" :href="route('teams.create')" :active="route().current('teams.create')">
                                    Create New Team
                                </ResponsiveNavLink>

                                <!-- Team Switcher -->
                                <template v-if="$page.props.auth.user.all_teams.length > 1">
                                    <div class="border-t border-gray-200" />

                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        Switch Teams
                                    </div>

                                    <template v-for="team in $page.props.auth.user.all_teams" :key="team.id">
                                        <form @submit.prevent="switchToTeam(team)">
                                            <ResponsiveNavLink as="button">
                                                <div class="flex items-center">
                                                    <svg v-if="team.id == $page.props.auth.user.current_team_id" class="me-2 size-5 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <div>{{ team.name }}</div>
                                                </div>
                                            </ResponsiveNavLink>
                                        </form>
                                    </template>
                                </template>
                            </template>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header v-if="$slots.header" class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>