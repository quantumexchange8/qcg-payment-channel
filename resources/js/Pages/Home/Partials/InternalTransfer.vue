<script setup>
import BaseListbox from "@/Components/BaseListbox.vue";
import {ref, watch} from "vue";
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Icon } from "@/Components/Icons/outline";
import Button from "@/Components/Button.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";

const user = usePage().props.auth.user;
const props = defineProps({
    tradingAccounts: Array,
    walletAddresses: Array,
    paymentAccounts: Array,
})

const transferModeSelect = [
    { value: '0', label: "Cash wallet to trading account" },
    { value: '1', label: "Trading account to cash wallet" },
    { value: '2', label: "Trading account to trading account" },
];
const transfer_mode = ref(transferModeSelect[0].value);

const form = useForm({
    transferMode: '',
    from_meta_login: '',
    to_meta_login: '',
    amount: null,
});

const submitForm = () => {
    form.transferMode = transfer_mode.value;
    form.from_meta_login = fromAccount.value;
    form.to_meta_login = toAccount.value;
    form.post(route('dashboard.internalTransfer'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
        onError: () => {
            if (form.errors.amount) {
                form.reset('amount');
            }
        }
    })
}

// update balance based on account
const updateBalance = (newValue) => {
    const matchedAccount = props.tradingAccounts.find(trading_account => trading_account.value === newValue);
    if (matchedAccount) {
        toBalance.value = '$ ' + matchedAccount.balance;
    }
    checkAccount(newValue);
}

const fromAccount = ref(props.tradingAccounts[0].value);
const fromBalance = ref(props.tradingAccounts[0].balance);
watch(fromAccount, (newValue) => updateBalance(newValue));

const toAccount = ref(props.tradingAccounts[0].value);
const toBalance = ref(props.tradingAccounts[0].balance);
watch(toAccount, (newValue) => updateBalance(newValue));

// if to & from same account, display error msg
const checkAccount = (newValue) => {
    if (transfer_mode.value === '2') {
        form.errors.to_meta_login = "";
        if (fromAccount.value === newValue && toAccount.value === newValue) {
            form.errors.to_meta_login = "Cannot transfer to the same trading account";
        }
    }
}

const displayFrom = ref(false);
const displayTo = ref(true);

watch(transfer_mode, (newValue) => {
    if(newValue === '0') {
        displayFrom.value = false;
        displayTo.value = true;
    }
    if(newValue === '1') {
        displayFrom.value = true;
        displayTo.value = false;
    }
    if(newValue === '2') {
        displayFrom.value = true;
        displayTo.value = true;
        checkAccount(toAccount.value);
    }
});
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
            <InputError :message="form.errors.transferMode" />
        </div>

        <div v-show="displayFrom" class="mb-4 flex flex-col items-start gap-1.5 self-stretch">
            <InputLabel for="account" value="From Trading Account" />
            <BaseListbox
                v-model="fromAccount"
                :options="props.tradingAccounts"
                class="w-full"
            />
            <div class="text-gray-500 text-xs font-medium">Balance: {{ fromBalance }}</div>
            <InputError :message="form.errors.from_meta_login" />
        </div>

        <div v-show="displayTo" class="mb-4 flex flex-col items-start gap-1.5 self-stretch">
            <InputLabel for="account" value="To Trading Account" />
            <BaseListbox
                v-model="toAccount"
                :options="props.tradingAccounts"
                class="w-full"
            />
            <div class="text-gray-500 text-xs font-medium">Balance: {{ toBalance }}</div>
            <InputError :message="form.errors.to_meta_login" />
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
            <InputError :message="form.errors.amount" />
        </div>
    
        <Button variant="primary" class="w-full justify-center text-sm" :disabled="form.processing">
            Process
        </Button>
    </form>
</template>
