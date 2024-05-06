<script setup>
import { onMounted, ref } from 'vue'

defineProps({
    modelValue: [String, Number],
    withIcon: {
        type: Boolean,
        default: false,
    },
    invalid: [String, Array]
})

defineEmits(['update:modelValue'])

const input = ref(null)

const focus = () => input.value?.focus()

defineExpose({
    input,
    focus
})

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus()
    }
})
</script>

<template>
    <input
        :class="[
            'py-2 rounded text-base font-normal shadow-sm border placeholder:text-gray-300 text-gray-950',
            'bg-white',
            'disabled:bg-gray-200 disabled:cursor-not-allowed disabled:text-gray-400',
            {
                'px-4': !withIcon,
                'pl-11 pr-4': withIcon,
            },
            {
                'border-gray-200 focus:ring-bilbao-800 hover:border-bilbao-600 focus:border-bilbao-800' :!invalid,
                'border-error-300 focus:ring-error-300 hover:border-error-300 focus:border-error-300' :invalid,
            }
        ]"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        ref="input"
    />
</template>
