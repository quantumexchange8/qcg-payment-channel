<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import {h, onMounted, ref} from 'vue'
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'
import Deposit from "@/Pages/Home/Partials/Deposit.vue";
import InternalTransfer from "@/Pages/Home/Partials/InternalTransfer.vue";
import Withdrawal from "@/Pages/Home/Partials/Withdrawal.vue";

const props = defineProps({
    tradingAccounts: Array,
    walletAddresses: Array,
    paymentAccounts: Array,
})

const categories = ref({
    deposit: h(Deposit),
    internal_transfer: h(InternalTransfer),
    withdrawal: h(Withdrawal),
})

const type = ref('Deposit');
const selectedTab = ref(0);
function changeTab(index) {
    selectedTab.value = index;
}

const updateTransactionType = (transaction_type) => {
    type.value = transaction_type
};

onMounted(() => {
    const params = new Proxy(new URLSearchParams(window.location.search), {
        get: (searchParams, prop) => searchParams.get(prop),
    });
    if (params.status === 'Deposit'){
        selectedTab.value = 0;
        type.value = 'Deposit';
    } else if (params.status === 'Withdrawal') {
        selectedTab.value = 2;
        type.value = 'Withdrawal';
    }
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="w-full max-w-md">
            <TabGroup :selectedIndex="selectedTab" @change="changeTab">
                <TabList class="py-1 flex justify-center gap-3 px-3 sticky top-[9.5rem] z-10 bg-white">
                    <Tab
                        v-for="category in Object.keys(categories)"
                        as="template"
                        :key="category"
                        v-slot="{ selected }"
                    >
                        <button
                            :class="[
                              'flex justify-center items-center w-full rounded p-2 text-xs font-semibold',
                              'ring-transparent focus:outline-none',
                              selected
                                ? 'bg-bilbao-100 text-bilbao-800'
                                : 'bg-gray-100 text-gray-700 hover:bg-bilbao-500/[0.12] hover:text-bilbao-800',
                            ]"
                            @click="updateTransactionType(category)"
                        >
                            {{ $t('public.' + category) }}
                        </button>
                    </Tab>
                </TabList>

                <TabPanels class="mt-8">
                    <TabPanel
                        v-for="component in Object.values(categories)"
                        :class="[
                            'bg-white px-3',
                            'ring-transparent focus:outline-none',
                          ]"
                    >
                        <component :is="component"
                            :tradingAccounts="tradingAccounts"
                            :walletAddresses="walletAddresses"
                            :paymentAccounts="paymentAccounts"
                        />
                    </TabPanel>
                </TabPanels>
            </TabGroup>
        </div>
    </AuthenticatedLayout>
</template>
