<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import {Link} from "@inertiajs/vue3";
</script>

<template>
    <div class="flex flex-col items-center gap-3 self-stretch">
        <div class="flex pt-12 flex-col gap-2 items-center self-stretch">
            <div class="text-center text-bilbao-800 text-base font-extrabold uppercase">
                <div>{{ $t('public.deposit_to_account') }}</div>
            </div>
            <slot />
        </div>
    </div>
</template>
