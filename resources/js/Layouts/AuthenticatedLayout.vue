<script setup>
import { ref } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import { IconWorld } from '@tabler/icons-vue';
import { IconLogout } from '@tabler/icons-vue';
import {loadLanguageAsync} from "laravel-vue-i18n";
import {usePage} from "@inertiajs/vue3";
import { Link } from '@inertiajs/vue3'

defineProps({
    title: String,
})

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
            <nav class="max-w-md mx-auto bg-white sticky top-0 z-20">
                <!-- Primary Navigation Menu -->
                <div class="flex p-3 justify-end items-center gap-3">
                    <div class="flex justify-end">
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
                        <Link :href="route('logout')" method="post" as="button">
                            <button @click="logout" class="flex items-center px-2 py-2">
                                <IconLogout :size="24" stroke-width="1" />
                            </button>
                        </Link>
                </div>
            </nav>

            <div class="flex flex-col gap-8 py-5">
                <!-- Page Heading -->
                <div class="sticky top-11 z-10 bg-white">
                    <div class="flex flex-col items-center gap-3 self-stretch">
                        <div class="flex pt-12 flex-col gap-2 items-center self-stretch">
                            <div class="text-center text-bilbao-800 text-base font-extrabold uppercase">
                                <div v-if="title === 'deposit'">{{ $t('public.deposit_to_account') }}</div>
                                <div v-else>{{ $t('public.withdraw_from_account') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Page Content -->
                <main class="flex justify-center items-center">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>

