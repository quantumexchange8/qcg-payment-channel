<script setup>
import BaseListbox from "@/Components/BaseListbox.vue";
import {ref} from "vue";
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Icon } from "@/Components/Icons/outline";

const usdt_address = ref('');

const rankFilter = [
    { value: '', label: "All" },
    { value: '1', label: "Member" },
    { value: '2', label: "Rank 1" },
    { value: '3', label: "Rank 2" },
    { value: '4', label: "Rank 3" },
    { value: '5', label: "Rank 4" },
];

const submitForm = () => {
    console.log('submitting');
}

const fullAmount = () => {
    console.log('full amount');
}
</script>

<template>
    <div class="mb-4 flex py-3 px-5 flex-col items-center gap-1 self-stretch rounded bg-gray-50">
        <div class="self-stretch text-gray-500 text-center text-sm font-normal">
            Cash Wallet Balance
        </div>
        <div class="self-stretch text-gray-950 text-center text-xl font-bold">
            $ 8,389.28
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
            <InputLabel for="amount" value="Amount" />
            <TextInput
                id="amount"
                type="text"
                class="block w-full"
                placeholder="$ 0.00"
            />
            <!-- full amount button -->
            <div class="text-gray-500 text-xs font-medium">Minimum withdrawal amount: $ 10.00</div>
        </div>

        <div class="mb-8 flex flex-col items-start gap-1.5 self-stretch">
            <InputLabel for="usdt_address" value="Select USDT Address" />
            <BaseListbox
                v-model="usdt_address"
                :options="rankFilter"
                class="w-full"
            />
            <div class="text-gray-500 text-xs font-medium">some text in here</div>
        </div>

        <button class="w-full flex py-3 px-4 justify-center items-center gap-2 self-stretch rounded bg-bilbao-800 
                text-white text-center text-sm font-semibold"
        >
        Request Withdrawal
        </button>
    </form>

</template>
