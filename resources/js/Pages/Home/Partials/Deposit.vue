<script setup>
import BaseListbox from "@/Components/BaseListbox.vue";
import {ref} from "vue";

const trading_account = ref('');

const rankFilter = [
    { value: '', label: "All" },
    { value: '1', label: "Member" },
    { value: '2', label: "Rank 1" },
    { value: '3', label: "Rank 2" },
    { value: '4', label: "Rank 3" },
    { value: '5', label: "Rank 4" },
];
</script>

<template>
    <BaseListbox
        v-model="trading_account"
        :options="rankFilter"
        class="w-full"
    />
</template>
