<script setup>
import { ref } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import LogoHeader from "@/Components/LogoHeader.vue";
import { UnitedKingdomIcon } from '@/Components/Icons/brands';
import { TaiwanIcon } from '@/Components/Icons/brands';
import {loadLanguageAsync} from "laravel-vue-i18n";
import {usePage} from "@inertiajs/vue3";

const showingNavigationDropdown = ref(false);
const currentLocale = ref(usePage().props.locale);

const changeLanguage = async (langVal) => {
    try {
        currentLocale.value = langVal
        await loadLanguageAsync(langVal);
        await axios.get(`/locale/${langVal}`);
    } catch (error) {
        console.error('Error changing locale:', error);
    }
};
</script>

<template>
    <div>
        <div class="min-h-screen bg-white">
            <nav class="max-w-md mx-auto bg-white sticky top-0 z-10">
                <!-- Primary Navigation Menu -->
                <div class="flex p-3 justify-end items-center gap-3">
                    <UnitedKingdomIcon
                        @click="changeLanguage('en')"
                        :class="{'ring ring-bilbao-800 ring-offset-2 rounded-full': currentLocale === 'en'}"
                    />
                    <TaiwanIcon
                        @click="changeLanguage('tw')"
                        :class="{'ring ring-bilbao-800 ring-offset-2 rounded-full': currentLocale === 'tw'}"
                    />

                    <!-- Hamburger -->
                    <div class="-me-2 flex items-center sm:hidden">
                        <button
                            @click="showingNavigationDropdown = !showingNavigationDropdown"
                            class="inline-flex items-center justify-center rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                        >
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path
                                    :class="{
                                        hidden: showingNavigationDropdown,
                                        'inline-flex': !showingNavigationDropdown,
                                    }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                                <path
                                    :class="{
                                        hidden: !showingNavigationDropdown,
                                        'inline-flex': showingNavigationDropdown,
                                    }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                    class="sm:hidden"
                >
                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="font-medium text-sm text-gray-500">{{ $page.props.auth.user.email }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')"> Profile </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="flex flex-col gap-8 py-5">
                <!-- Page Heading -->
                <LogoHeader class="sticky top-11 z-10 bg-white" />

                <!-- Page Content -->
                <main class="flex justify-center items-center">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
