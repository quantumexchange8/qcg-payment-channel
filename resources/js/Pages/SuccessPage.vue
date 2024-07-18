<script setup>
import { Head } from '@inertiajs/vue3';
import { SuccessIcon } from '@/Components/Icons/solid';
import Button from '@/Components/Button.vue';
import {ref} from 'vue';
import {transactionFormat} from "@/Composables/index.js";
import Tooltip from '@/Components/Tooltip.vue';

const {formatAmount} = transactionFormat();

const props = defineProps({
    title: String,
    description:String,
    payment: Array,
});

const date = ref('');
const amount = ref('');
const txid = ref('');

if (props.payment) {
    date.value = props.payment.date;
    amount.value = props.payment.amount;
    txid.value = props.payment.TxID;
}

const tooltipContent = ref('copy_link');
const copyCode = (contentId) => {
    const transaction_hash = document.querySelector(contentId).textContent;

    const tempInput = document.createElement('input');
    tempInput.value = transaction_hash;
    document.body.appendChild(tempInput);
    tempInput.select();

    try {
        var successful = document.execCommand('copy');
        if (successful) {
            tooltipContent.value = 'copied';
            setTimeout(() => {
                tooltipContent.value = 'copy_link';
            }, 3000);
        } else {
            tooltipContent.value = 'try_again_later';
        }
    } catch (err) {
        console.error(err)
    }
    document.body.removeChild(tempInput);
}
</script>

<template>
    <Head title="Success" />

        <div class="flex justify-center py-20 px-3 flex-col items-center">
            <div class="w-full max-w-md flex flex-col gap-8">
                <div class="max-w-md flex flex-col items-center gap-6">
                    <SuccessIcon />
                    <div class="flex flex-col items-center gap-2">
                        <div class="text-gray-950 text-center text-xl font-semibold">
                            {{ title }}
                        </div>
                        <div class="text-gray-500 text-center text-sm font-normal">
                            {{ description }}
                        </div>
                    </div>
                </div>
                <div
                    v-if="payment"
                    class="flex flex-col items-center gap-3 self-stretch"
                >
                    <div class="flex flex-col items-start gap-1 self-stretch">
                        <div class="max-w-[120px] text-gray-500 font-medium">
                            {{ $t('public.date') }}
                        </div>
                        <div class="text-gray-950 text-center font-semibold">
                            {{ date }}
                        </div>
                    </div>
                    <div class="flex flex-col items-start gap-1 self-stretch">
                        <div class="max-w-[120px] text-gray-500 font-medium">
                            {{ $t('public.amount') }}
                        </div>
                        <div class="text-gray-950 text-center font-semibold">
                            $ {{ formatAmount(amount) }}
                        </div>
                    </div>
                    <div class="flex flex-col items-start gap-1 self-stretch">
                        <div class="max-w-[120px] text-gray-500 font-medium">
                            {{ $t('public.txid') }}
                        </div>
                        <Tooltip :content="$t('public.'+ tooltipContent)" placement="top">
                            <div class="text-gray-950 font-semibold break-all" id="TxID" @click="copyCode('#TxID')">
                                {{ txid }}
                            </div>
                        </Tooltip>
                    </div>
                </div>
                <Button
                    variant="primary"
                    href="/"
                    class="w-full max-w-md justify-center text-sm"
                >
                    {{ $t('public.success_return') }}
                </Button>
            </div>
        </div>
</template>