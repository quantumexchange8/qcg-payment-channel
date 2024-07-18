<script setup>
import BaseListbox from "@/Components/BaseListbox.vue";
import {onMounted, ref, watch} from "vue";
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Icon } from "@/Components/Icons/outline";
import Button from "@/Components/Button.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import { trans } from "laravel-vue-i18n";

const user = usePage().props.auth.user;
const props = defineProps({
    tradingAccounts: Array,
    paymentAccounts: Array,
})

const transferModeSelect = [
    { value: '0', label: trans('public.transfer_mode_0') },
    { value: '1', label: trans('public.transfer_mode_1') },
    { value: '2', label: trans('public.transfer_mode_2') },
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

    let formRoute;
    if (form.transferMode === '0') {
        formRoute = route('dashboard.walletToAccount');
    }
    if (form.transferMode === '1') {
        formRoute = route('dashboard.accountToWallet');
    }
    if (form.transferMode === '2') {
        formRoute = route('dashboard.accountToAccount');
    }

    form.post(formRoute, {
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
        return matchedAccount.balance;
    }
}

const fromAccount = ref('');
const fromBalance = ref('');
watch(fromAccount, (newValue) => {
    fromBalance.value = updateBalance(newValue);
    checkAccount(newValue);
});

const toAccount = ref('');
const toBalance = ref('');
watch(toAccount, (newValue) => {
    toBalance.value = updateBalance(newValue);
    checkAccount(newValue);
});

onMounted(() => {
    if (props.tradingAccounts.length > 0) {
        fromAccount.value = props.tradingAccounts[0].value;
        fromBalance.value = props.tradingAccounts[0].balance;
        toAccount.value = props.tradingAccounts[0].value;
        toBalance.value = props.tradingAccounts[0].balance;
    }
})

// if to & from same account, display error msg
const checkAccount = (newValue) => {
    if (transfer_mode.value === '2') {
        form.errors.to_meta_login = "";
        if (fromAccount.value === newValue && toAccount.value === newValue) {
            form.errors.to_meta_login = trans('public.same_acc_error');
        }
    }
    else {
        form.errors.to_meta_login = "";
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
            {{ $t('public.cw_balance') }}
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
                {{ $t('public.attention') }}
            </div>
            <div class="self-stretch text-gray-800 text-xs font-normal">
                {{ $t('public.trading_acc_reminder') }}
            </div>
        </div>
    </div>

    <form @submit.prevent="submitForm">
        <div class="mb-4 flex flex-col items-start gap-1.5 self-stretch">
            <InputLabel for="transfer_mode" :value="$t('public.transfer_mode')" />
            <BaseListbox
                v-model="transfer_mode"
                :options="transferModeSelect"
                class="w-full"
            />
            <InputError :message="form.errors.transferMode" />
        </div>

        <div v-show="displayFrom" class="mb-4 flex flex-col items-start gap-1.5 self-stretch">
            <InputLabel for="account" :value="$t('public.from_trading_acc')" />
            <BaseListbox
                v-model="fromAccount"
                :options="props.tradingAccounts"
                class="w-full"
            />
            <div class="text-gray-500 text-xs font-medium">
                <div v-if="fromBalance">
                    $ {{ fromBalance }}
                </div>
                <div v-else>
                    {{ $t('public.loading') }}
                </div>
            </div>
            <InputError :message="form.errors.from_meta_login" />
        </div>

        <div v-show="displayTo" class="mb-4 flex flex-col items-start gap-1.5 self-stretch">
            <InputLabel for="account" :value="$t('public.to_trading_acc')" />
            <BaseListbox
                v-model="toAccount"
                :options="props.tradingAccounts"
                class="w-full"
            />
            <div class="text-gray-500 text-xs font-medium">
                <div v-if="toBalance">
                    $ {{ toBalance }}
                </div>
                <div v-else>
                    {{ $t('public.loading') }}
                </div>
            </div>
            <InputError :message="form.errors.to_meta_login" />
        </div>

        <div class="mb-8 flex flex-col items-start gap-1.5 self-stretch">
            <InputLabel for="amount" :value="$t('public.amount')" />
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
            {{ $t('public.process') }}
        </Button>
    </form>
</template>
