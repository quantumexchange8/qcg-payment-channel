<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import {computed, h, onMounted, ref} from 'vue'
import Deposit from "@/Pages/Home/Partials/Deposit.vue";
import Withdrawal from "@/Pages/Home/Partials/Withdrawal.vue";

const props = defineProps({
    tradingAccounts: Array,
    paymentAccounts: Array,
})

const categories = {
    deposit: h(Deposit),
    withdrawal: h(Withdrawal),
    // internal_transfer: h(InternalTransfer),
}

const status = ref('deposit')

onMounted(() => {
    const params = new URLSearchParams(window.location.search)
    status.value = params.get('status') || 'deposit'
})

const activeComponent = computed(() => {
    return categories[status.value] || categories.deposit
})
</script>

<template>
    <Head
        :title="status === 'deposit' ? $t('public.deposit_to_account') : $t('public.withdraw_from_account')"
    />

    <AuthenticatedLayout
        :title="status"
    >
        <div class="w-full max-w-md">
            <component
                :is="activeComponent"
                :tradingAccounts="tradingAccounts"
                :paymentAccounts="paymentAccounts"
            />
        </div>
    </AuthenticatedLayout>
</template>
