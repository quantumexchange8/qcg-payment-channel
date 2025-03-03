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
    paymentAccounts: Array,
})

const categories = ref({
    deposit: h(Deposit),
    // internal_transfer: h(InternalTransfer),
    // withdrawal: h(Withdrawal),
})

const selectedTab = ref(0);
function changeTab(index) {
    selectedTab.value = index;
}

onMounted(() => {
    const params = new Proxy(new URLSearchParams(window.location.search), {
        get: (searchParams, prop) => searchParams.get(prop),
    });

    if (params.status === 'deposit') {
        selectedTab.value = 0;
    } else if (params.status === 'withdrawal') {
        selectedTab.value = 2;
    }
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="w-full max-w-md">
            <TabGroup :selectedIndex="selectedTab" @change="changeTab">
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
                            :paymentAccounts="paymentAccounts"
                        />
                    </TabPanel>
                </TabPanels>
            </TabGroup>
        </div>
    </AuthenticatedLayout>
</template>
