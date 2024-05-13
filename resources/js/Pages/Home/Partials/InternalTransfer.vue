<script setup>
import BaseListbox from "@/Components/BaseListbox.vue";
import {ref, watch} from "vue";
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Icon } from "@/Components/Icons/outline";
import Button from "@/Components/Button.vue";
import { useForm, usePage } from "@inertiajs/vue3";

const transferModeSelect = [
    { value: '0', label: "Cash wallet to trading account" },
    { value: '1', label: "Trading account to cash wallet" },
    { value: '2', label: "Trading account to trading account" },
];

const user = usePage().props.auth.user;
const props = defineProps({
    tradingAccounts: Array,
    walletAddresses: Array,
})

const form = useForm({
    transferMode: '',
    trading_account: '',
    amount: null,
});

const submitForm = () => {
    form.post(route('dashboard.internalTransfer'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
        onError: () => {
            alert('error');
        }
    })
}

const transfer_mode = ref('');
watch(transfer_mode, 
    (newValue) => {
        form.transferMode = newValue;
    }
);

const account = ref('');
const balance = ref('Loading...');
watch(account, (newValue) => {
    const matchedAccount = props.tradingAccounts.find(trading_account => trading_account.value === newValue);
    if (matchedAccount) {
        balance.value = '$ ' + matchedAccount.balance;
    }
    form.trading_account = newValue;
})
</script>

<template>
    <div class="mb-4 flex py-3 px-4 flex-col items-center gap-1 self-stretch rounded bg-gray-50">
        <div class="self-stretch text-gray-500 text-center text-sm font-normal">
            Cash Wallet Balance
        </div>
        <div class="self-stretch text-gray-950 text-center text-xl font-bold">
            $ {{ user.cash_wallet }}
        </div>
    </div>

    <div class="mb-4 flex p-3 items-start gap-2 self-stretch rounded bg-gray-50">
        <div class="w-5 h-5">
            <Icon class="text-gray-950"/>
        </div>
        <div class="flex flex-col items-start">
            <div class="h-5 justify-center self-stretch text-gray-950 text-xs font-semibold">
                Attention
            </div>
            <div class="self-stretch text-gray-800 text-xs font-normal">
                Please ensure there are no open positions in your trading account to maintain a certain margin and avoid potential losses.
            </div>
        </div>
    </div>

    <form @submit.prevent="submitForm">
        <div class="mb-4 flex flex-col items-start gap-1.5 self-stretch">
            <InputLabel for="transfer_mode" value="Transfer Mode" />
            <BaseListbox
                v-model="transfer_mode"
                :options="transferModeSelect"
                class="w-full"
            />
        </div>
    
        <div class="mb-4 flex flex-col items-start gap-1.5 self-stretch">
            <InputLabel for="account" value="Trading Account" />
            <BaseListbox
                v-model="account"
                :options="props.tradingAccounts"
                class="w-full"
            />
            <div class="text-gray-500 text-xs font-medium">Balance: {{ balance }}</div>
        </div>
    
        <div class="mb-8 flex flex-col items-start gap-1.5 self-stretch">
            <InputLabel for="amount" value="Amount" />
            <TextInput
                v-model="form.amount"
                id="amount"
                type="text"
                class="block w-full"
                placeholder="$ 0.00"
            />
        </div>
    
        <Button variant="primary" class="w-full justify-center text-sm" :disabled="form.processing">
            Process
        </Button>
    </form>
</template>
