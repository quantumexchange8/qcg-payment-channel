<script setup>
import { Head } from '@inertiajs/vue3';
import { SuccessIcon } from '@/Components/Icons/solid';
import Button from '@/Components/Button.vue';

const props = defineProps({
    title: String,
    description:String,
});

</script>

<template>
    <Head title="Success" />

        <div class="flex py-20 px-3 flex-col items-center gap-8">
            <div class="max-w-md flex flex-col items-center gap-6">
                <SuccessIcon />
                <div class="flex flex-col items-center gap-2">
                    <div class="text-gray-950 text-center text-xl font-semibold">
                        {{ props.title }}
                    </div>
                    <div class="text-gray-500 text-center text-sm font-normal">
                        {{ props.description }}
                    </div>
                </div>
            </div>
            <Button
                variant="primary"
                href="/dashboard"
                class="w-full max-w-md justify-center text-sm"
            >
                Return to cTrader
            </Button>
        </div>
</template>