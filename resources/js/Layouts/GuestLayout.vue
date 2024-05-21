<script setup>
import { usePage } from '@inertiajs/vue3';
import LogoHeader from "@/Components/LogoHeader.vue";
import { UnitedKingdomIcon } from '@/Components/Icons/brands';
import { TaiwanIcon } from '@/Components/Icons/brands';
import {loadLanguageAsync} from "laravel-vue-i18n";
import { onMounted, ref } from 'vue';

const currentLocale = ref(usePage().props.locale);

const changeLanguage = async (langVal) => {
    try {
        currentLocale.value = langVal;
        await loadLanguageAsync(langVal);
        await axios.get(`/locale/${langVal}`);
    } catch (error) {
        console.error('Error changing locale:', error);
    }
};

onMounted(() => {
    changeLanguage(currentLocale.value);
});
</script>

<template>
    <div class="min-h-screen">
        <nav class="max-w-md mx-auto bg-white">
            <div class="flex p-3 justify-end items-center gap-3">
                <UnitedKingdomIcon
                        @click="changeLanguage('en')"
                        :class="{'ring ring-bilbao-800 ring-offset-2 rounded-full': currentLocale === 'en'}"
                />
                <TaiwanIcon
                    @click="changeLanguage('tw')"
                    :class="{'ring ring-bilbao-800 ring-offset-2 rounded-full': currentLocale === 'tw'}"
                />
            </div>
        </nav>
        <div class="flex flex-col gap-8 sm:justify-center items-center py-5 px-3 sm:px-0 bg-white">
            <LogoHeader>
                <div class="text-center text-gray-700 text-sm font-normal">
                    {{ $t('public.welcome') }}
                </div>
            </LogoHeader>
            <div
                class="w-full sm:max-w-md overflow-hidden px-1"
            >
                <slot />
            </div>
        </div>
    </div>
</template>
