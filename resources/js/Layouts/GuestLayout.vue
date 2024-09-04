<script setup>
import { usePage } from '@inertiajs/vue3';
import LogoHeader from "@/Components/LogoHeader.vue";
import { IconWorld } from '@tabler/icons-vue';
import Dropdown from '@/Components/Dropdown.vue';
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
                <!-- Language Dropdown -->
                <div class="ms-3 relative">
                    <Dropdown align="right" width="20">
                                <template #trigger>
                                    <span class="inline-flex rounded-md">
                                        <button @click="logout" class="flex items-center px-2 py-2">
                                            <IconWorld :size="24" stroke-width="1" />
                                        </button>
                                    </span>
                                </template>
                                <template #content>
                                    <ul class="py-1">
                                    <li
                                    @click="changeLanguage('en')"
                                    class="px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-center select-none hover:cursor-pointer"
                                    >
                                    English
                                    </li>
                                    <li
                                    @click="changeLanguage('tw')"
                                    class="px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-center select-none hover:cursor-pointer"
                                    >
                                    中文
                                    </li>
                                    </ul>
                                </template>
                    </Dropdown>
                </div>
            </div>
        </nav>
        
        <div class="flex flex-col gap-8 0sm:justify-center items-center py-5 px-3 sm:px-0 bg-white">
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
