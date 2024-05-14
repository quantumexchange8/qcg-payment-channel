<script setup>
defineProps({
    value: {
        type: String,
    },
    is_required: {
        type: Boolean,
        default: true
    }
});
</script>

<template>
    <label class="block font-medium text-sm text-gray-500">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
        <span v-if="is_required" class="text-red-500 text-sm"> *</span>
    </label>
</template>
