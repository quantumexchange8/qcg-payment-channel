<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import {Link} from "@inertiajs/vue3";
</script>

<template>
    <div class="flex flex-col items-center gap-3 self-stretch">
        <Link href="/">
            <ApplicationLogo class="" />
        </Link>
        <div class="flex flex-col gap-2 items-center self-stretch">
            <div class="text-center text-bilbao-800 text-base font-extrabold uppercase">
                <div>Quantum Capital Global</div>
                <div>Payment Channel</div>
            </div>
            <slot />
        </div>
    </div>
</template>
