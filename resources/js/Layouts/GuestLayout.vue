<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import LogoHeader from "@/Components/LogoHeader.vue";
</script>

<template>
    <div class="min-h-screen flex flex-col gap-8 sm:justify-center items-center py-5 px-3 sm:p-0 bg-white">
        <LogoHeader>
            <div class="text-center text-gray-700 text-sm font-normal">
                Welcome! Please login first.
            </div>
        </LogoHeader>

        <div
            class="w-full sm:max-w-md overflow-hidden px-1"
        >
            <slot />
        </div>
    </div>
</template>
