<script setup>
import BaseListbox from "@/Components/BaseListbox.vue";
import {onMounted, ref, watch} from "vue";
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Icon } from "@/Components/Icons/outline";
import Button from "@/Components/Button.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";

const user = usePage().props.auth.user;
const props = defineProps({
    tradingAccounts: Array,
    paymentAccounts: Array,
})

const form = useForm({
    amount: null,
    account_no: ''
});

const submitForm = () => {
    form.account_no = accountNo.value;
    form.post(route('dashboard.withdrawal'), {
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

const usdtAddress = ref('');
const accountNo = ref('');
watch(usdtAddress, (newValue) => {
    const matchedAddress = props.paymentAccounts.find(paymentAccount => paymentAccount.value === newValue);
    if (matchedAddress) {
        accountNo.value = matchedAddress.address;
    }
})

onMounted(() => {
    if (props.paymentAccounts.length > 0) {
        usdtAddress.value = props.paymentAccounts[0].value;
        accountNo.value = props.paymentAccounts[0].address;
    }
})

</script>

<template>
    <div class="mb-4 flex py-3 px-5 flex-col items-center gap-1 self-stretch rounded bg-gray-50">
        <div class="self-stretch text-gray-500 text-center text-sm font-normal">
            {{ $t('public.rw_balance') }}
        </div>
        <div class="self-stretch text-gray-950 text-center text-xl font-bold">
            $ {{ user.rebate_wallet?.balance }}
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
            <InputLabel for="amount" :value="$t('public.amount')" />
            <TextInput
                v-model="form.amount"
                id="amount"
                type="text"
                class="block w-full"
                placeholder="$ 0.00"
            />
            <!-- full amount button -->
            <div class="text-gray-500 text-xs font-medium">{{ $t('public.min_withdrawal') }}</div>
            <InputError :message="form.errors.amount" />
        </div>

        <div class="mb-8 flex flex-col items-start gap-1.5 self-stretch">
            <InputLabel for="usdtAddress" :value="$t('public.select_usdt')" />
            <BaseListbox
                v-model="usdtAddress"
                :options="props.paymentAccounts"
                class="w-full"
            />
            <div class="text-gray-500 text-xs font-medium">
                <div v-if="accountNo">
                    {{ accountNo }}
                </div>
                <div v-else>
                    {{ $t('public.loading') }}
                </div>
            </div>
            <InputError :message="form.errors.account_no" />
        </div>

        <Button variant="primary" class="w-full justify-center text-sm" :disabled="form.processing">
            {{ $t('public.request_withdrawal') }}
        </Button>
    </form>

</template>
