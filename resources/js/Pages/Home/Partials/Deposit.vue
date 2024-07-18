<script setup>
import BaseListbox from "@/Components/BaseListbox.vue";
import { computed, onMounted, ref, watch } from "vue";
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Copy03Icon } from "@/Components/Icons/outline";
import { TetherIcon } from "@/Components/Icons/brands";
import { Icon } from "@/Components/Icons/outline";
import Button from "@/Components/Button.vue";
import { UploadIcon, XIcon } from "@/Components/Icons/outline";
import { useForm } from "@inertiajs/vue3";
import Qrcode from "qrcode.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    tradingAccounts: Array,
})

const form = useForm({
    meta_login: '',
});

const submitForm = () => {
    form.meta_login = account.value
    form.post(route('dashboard.deposit'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    })
}

// display balance based on the account selected
const account = ref('');
const balance = ref('');

onMounted(() => {
    if (props.tradingAccounts.length > 0) {
        account.value = props.tradingAccounts[0].value
        balance.value = props.tradingAccounts[0].balance
    }
})

watch(account, (newValue) => {
    const matchedAccount = props.tradingAccounts.find(trading_account => trading_account.value === newValue);
    if (matchedAccount) {
        balance.value = matchedAccount.balance;
    }
})
</script>

<template>
    <form @submit.prevent="submitForm" class="flex flex-col gap-8">
        <div class="flex flex-col items-start gap-1.5 self-stretch">
            <InputLabel for="tradingAccount" :value="$t('public.trading_account')" />
            <BaseListbox
                v-model="account"
                :options="tradingAccounts"
                class="w-full"
            />
            <div class="text-gray-500 text-xs font-medium">
                <div v-if="balance">
                    $ {{ balance }}
                </div>
                <div v-else>
                    {{ $t('public.loading') }}
                </div>
            </div>
        </div>

        <div class="flex p-3 items-start gap-2 self-stretch rounded bg-gray-50">
            <div class="w-5 h-5">
                <Icon class="text-gray-950"/>
            </div>

            <div class="flex-1 text-gray-800 text-xs">
                {{ $t('public.deposit_note') }}
            </div>
        </div>

        <Button
            variant="primary"
            class="w-full justify-center text-sm"
            :disabled="form.processing"
        >
        {{ $t('public.deposit_now') }}
        </Button>
    </form>
</template>
