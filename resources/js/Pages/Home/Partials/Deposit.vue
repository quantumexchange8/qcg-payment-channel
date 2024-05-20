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
    walletAddresses: Array,
})

const walletAddressesSelect = props.walletAddresses;
const addressLength = walletAddressesSelect.length;
let currentIndex = 0;
const qrAddress = ref(walletAddressesSelect[currentIndex]);

//timer countdown
const totalTime = 300;
const currentTime = ref(totalTime);

const formatTime = computed(() => {
    const minutes = Math.floor(currentTime.value / 60);
    const seconds = currentTime.value % 60;
    return `${String(minutes)}:${String(seconds).padStart(2, '0')}`;
});

const countdown = () => {
    if (currentTime.value > 0) {
        setTimeout(() => {
            currentTime.value--;
            countdown();
        }, 1000);
    }
};

// when timeout, change to next wallet address and reset timer
watch(currentTime, (newValue) => {
    if (newValue === 0) {
        currentIndex = (currentIndex + 1) % addressLength;
        qrAddress.value = walletAddressesSelect[currentIndex];
        setTimeout(() => {
            currentTime.value = totalTime;
            countdown();
        }, 1000);
    }
})

onMounted(() => {
    countdown();
});

// copy the wallet address to clipboard
const hoverCopy = ref(false);
const copyCode = () => {
    const walletCode = document.querySelector('#WalletCode').textContent;

    const tempInput = document.createElement('input');
    tempInput.value = walletCode;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand('copy');
    document.body.removeChild(tempInput);

    setTimeout(function () {
        hoverCopy.value = false;
    }, 3000);
}

const form = useForm({
    meta_login: '',
    deposit_amount: null,
    txid: '',
    payment_receipt: null,
});

const submitForm = () => {
    form.meta_login = account.value
    form.post(route('dashboard.deposit'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
        onError: () => {
            if (form.errors.deposit_amount) {
                form.reset('deposit_amount');
            }
            if (form.errors.txid) {
                form.reset('txid');
            }
        }
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

const selectedPaymentReceipt = ref(null);
const selectedPaymentReceiptName = ref(null);
const handlePaymentIncentive = (event) => {
    const paymentReceiptInput = event.target;
    const file = paymentReceiptInput.files[0];

    if (file) {
        // Display the selected image
        const reader = new FileReader();
        reader.onload = () => {
            selectedPaymentReceipt.value = reader.result;
        };
        reader.readAsDataURL(file);
        selectedPaymentReceiptName.value = file.name;
        form.payment_receipt = event.target.files[0];
    } else {
        selectedPaymentReceipt.value = null;
    }
};

const removePaymentIncentive = () => {
    selectedPaymentReceipt.value = null;
    form.payment_receipt = null;
};
</script>

<template>
    <form @submit.prevent="submitForm">
        <div class="mb-4 flex flex-col items-start gap-1.5 self-stretch">
            <InputLabel for="tradingAccount" value="Trading Account" />
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
                    loading..
                </div>
            </div>
            <InputError :message="form.errors.meta_login" />
        </div>

        <div class="mb-4 flex flex-col items-start gap-1.5 self-stretch">
            <InputLabel for="deposit_amount" value="Deposit Amount" />
            <TextInput
                v-model="form.deposit_amount"
                id="deposit_amount"
                type="text"
                class="block w-full"
                placeholder="$ 0.00"
            />
            <InputError :message="form.errors.deposit_amount" />
        </div>

        <div class="mb-4 flex p-5 flex-col justify-between items-center self-stretch rounded bg-gray-50">
            <div class="flex gap-3 items-center">
                <TetherIcon />
                <div class="text-gray-950 text-base font-semibold uppercase">trc20</div>
            </div>
            <div class="text-gray-950 text-center text-xs font-normal">
                QR code will be refreshed automatically in
            </div>
            <div id="time" class="text-bilbao-700 text-center text-xs font-semibold">
                {{ formatTime }} minutes
            </div>
            <div class="shrink-0">
                <Qrcode :value="qrAddress" :size="160" render-as="svg" :margin="1" level="M" background="#F7F7F7" />
            </div>
            <div class="flex flex-row gap-2 items-center">
                <div class="max-w-64 h-5 whitespace-nowrap overflow-hidden text-gray-600 text-ellipsis text-xs font-normal" id="WalletCode">
                    {{ qrAddress }}
                </div>
                <div class="relative" @click="hoverCopy = true" @mouseleave="hoverCopy = false">
                    <Copy03Icon class="text-bilbao-700 hover:cursor-pointer hover:text-bilbao-800 focus:text-bilbao-900" @click="copyCode" />
                    <div
                        v-show="hoverCopy"
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
                <Icon class="text-gray-950" />
            </div>
            <div class="text-gray-800 text-xs font-normal">
                Please complete your deposit within the specified time. After you have successfully transferred the funds, copy your TxID and paste it in the space below, then click 'Complete Deposit'
            </div>
        </div>

        <div class="mb-4 flex flex-col items-start gap-1.5">
            <InputLabel for="txid" value="TxID" />
            <TextInput
                v-model="form.txid"
                id="txid"
                type="text"
                class="block w-full"
                placeholder="Paste your TxID here"
            />
            <InputError :message="form.errors.txid" />
        </div>

        <div class="mb-8 flex flex-col items-start gap-1.5 self-stretch">
            <InputLabel for="payment_receipt" value="Upload Receipt" :is_required="false" />
            <div class="flex gap-3">
                <input
                    ref="paymentReceiptInput"
                    id="payment_receipt"
                    type="file"
                    class="hidden"
                    accept="image/*"
                    @change="handlePaymentIncentive"
                />
                <Button
                    type="button"
                    class="flex gap-2 justify-center"
                    variant="secondary"
                    size="sm"
                    @click="$refs.paymentReceiptInput.click()"
                    v-slot="{ iconSizeClasses }"
                >
                    <UploadIcon :class="iconSizeClasses" class="text-bilbao-800" />
                    Browse
                </Button>
                <InputError :message="form.errors.payment_receipt" class="mt-2" />
            </div>
            <div
                v-if="selectedPaymentReceipt"
                class="relative w-full py-2 pl-4 flex justify-between rounded-lg border focus:ring-1 focus:outline-none"
                :class="[
                            {
                                  'border-error-300 focus-within:ring-error-300 hover:border-error-300 focus-within:border-error-300 focus-within:shadow-error-light dark:border-error-600 dark:focus-within:ring-error-600 dark:hover:border-error-600 dark:focus-within:border-error-600 dark:focus-within:shadow-error-dark': form.errors.payment_receipt,
                                  'border-gray-light-300 dark:border-gray-dark-800 focus:ring-primary-400 hover:border-primary-400 focus-within:border-primary-400 focus-within:shadow-primary-light dark:focus-within:ring-primary-500 dark:hover:border-primary-500 dark:focus-within:border-primary-500 dark:focus-within:shadow-primary-dark': !form.errors.payment_receipt,
                            }
                        ]"
            >
                <div class="inline-flex items-center gap-3">
                    <img :src="selectedPaymentReceipt" alt="Selected Image" class="max-w-full h-9 object-contain rounded" />
                    <div class="text-gray-light-900 dark:text-gray-dark-50">
                        {{ selectedPaymentReceiptName }}
                    </div>
                </div>
                <Button
                    type="button"
                    variant="transparent"
                    @click="removePaymentIncentive"
                >
                    <XIcon class="text-gray-700 w-5 h-5" />
                </Button>
            </div>
        </div>

        <Button
            variant="primary"
            class="w-full justify-center text-sm"
            :disabled="form.processing"
        >
            Complete Deposit
        </Button>
    </form>
</template>
