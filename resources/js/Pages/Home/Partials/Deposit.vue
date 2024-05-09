<script setup>
import BaseListbox from "@/Components/BaseListbox.vue";
import {ref} from "vue";
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Copy03Icon } from "@/Components/Icons/outline";
import { TetherIcon } from "@/Components/Icons/brands";
import { Icon } from "@/Components/Icons/outline";
import Button from "@/Components/Button.vue";

const trading_account = ref('');

const rankFilter = [
    { value: '', label: "All" },
    { value: '1', label: "Member" },
    { value: '2', label: "Rank 1" },
    { value: '3', label: "Rank 2" },
    { value: '4', label: "Rank 3" },
    { value: '5', label: "Rank 4" },
];

const submitForm = () => {
    console.log('submitting')
}

const hover_copy = ref(false);

const copyCode = () => {
    const walletCode = document.querySelector('#WalletCode').textContent;

    const tempInput = document.createElement('input');
    tempInput.value = walletCode;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand('copy');
    document.body.removeChild(tempInput);

    setTimeout(function() {
        hover_copy.value = false;
    }, 3000);
}
</script>

<template>
    <form @submit.prevent="submitForm">
        <div class="mb-4 flex flex-col items-start gap-1.5 self-stretch">
            <InputLabel for="trading_account" value="Trading Account" />
            <BaseListbox
                v-model="trading_account"
                :options="rankFilter"
                class="w-full"
            />
            <div class="text-gray-500 text-xs font-medium">Balance: $453</div>
        </div>

        <div class="mb-4 flex flex-col items-start gap-1.5 self-stretch">
            <InputLabel for="deposit_amount" value="Deposit Amount" />
            <TextInput
                id="deposit_amount"
                type="text"
                class="block w-full"
                placeholder="$ 0.00"
            />
        </div>

        <div class="mb-4 flex p-5 flex-col justify-between items-center self-stretch rounded bg-gray-50">
            <div class="flex gap-3 items-center">
                <TetherIcon />
                <div class="text-gray-950 text-base font-semibold uppercase">trc20</div>
            </div>
            <div class="text-gray-950 text-center text-xs font-normal">
                QR code will be refreshed automatically in
            </div>
            <div class="text-bilbao-700 text-center text-xs font-semibold">
                4:59 minutes
            </div>
            <div class="w-40 h-40 shrink-0">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/d0/QR_code_for_mobile_English_Wikipedia.svg/1200px-QR_code_for_mobile_English_Wikipedia.svg.png" alt="">
            </div>
            <div class="flex flex-row gap-2 items-center">
                <div class="max-w-64 h-5 whitespace-nowrap overflow-hidden text-gray-600 text-ellipsis text-xs font-normal" id="WalletCode">
                    Lorem ipsum dolor sit amet consectetur this is a test elit. Tempora iure nam earum error at ipsum minima eum magni nisi exercitationem?
                </div>
                <div class="relative" @click="hover_copy = true" @mouseleave="hover_copy = false">
                    <Copy03Icon class="text-bilbao-700 hover:cursor-pointer hover:text-bilbao-800 focus:text-bilbao-900" @click="copyCode"/>
                    <div
                        v-show="hover_copy"
                        id="copied_success"
                        class="w-32 -left-16 absolute bottom-4 p-1 mb-2 text-sm text-center text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 transition ease-in-out"
                    >
                        Copied wallet address
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-4 flex flex-row p-3 items-start gap-2 self-stretch rounded bg-gray-50">
            <div class="w-5 h-5">
                <Icon class="text-gray-950"/>
            </div>
            <div class="text-gray-800 text-xs font-normal">
                Please complete your deposit within the specified time. After you have successfully transferred the funds, copy your TxID and paste it in the space below, then click 'Complete Deposit'
            </div>
        </div>

        <div class="mb-4 flex flex-col items-start gap-1.5">
            <InputLabel for="txid" value="TxID" />
            <TextInput
                id="txid"
                type="text"
                class="block w-full"
                placeholder="Paste your TxID here"
            />
        </div>

        <div class="mb-8 flex flex-col items-start gap-1.5 self-stretch">
            <InputLabel for="txid" value="Upload Receipt" />
            <Button
                type="button"
                class="flex justify-center"
                variant="secondary"
                size="sm"
            >
                Browse
            </Button>
        </div>

        <button
            class="w-full flex py-3 px-4 justify-center items-center gap-2 self-stretch rounded bg-bilbao-800 text-white text-center text-sm font-semibold"
        >
        Complete Deposit
        </button>
    </form>
</template>
