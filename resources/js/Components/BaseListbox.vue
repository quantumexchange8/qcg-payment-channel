<script setup>
import {computed, ref} from 'vue'
import {
    Listbox,
    ListboxLabel,
    ListboxButton,
    ListboxOptions,
    ListboxOption,
} from '@headlessui/vue'
import { CheckIcon, ChevronDownIcon } from '@heroicons/vue/20/solid'

const props = defineProps({
    options: Array,
    modelValue: [String, Number, Array],
    placeholder: {
        type: String,
        default: 'please_select'
    },
    multiple: Boolean,
    error: String
})

const emit = defineEmits(['update:modelValue'])

const label = computed(() => {
    return props.options.filter(option => {
        if (Array.isArray(props.modelValue)) {
            return props.modelValue.includes(option.value);
        }

        return props.modelValue === option.value;
    }).map(option => option.label).join(', ')
})
</script>

<template>
    <Listbox
        :multiple="props.multiple"
        :model-value="props.modelValue"
        @update:modelValue="value => emit('update:modelValue', value)"
    >
        <div class="relative">
            <ListboxButton
                class="py-2 px-4 w-full rounded text-base text-left font-normal shadow-sm border placeholder:text-gray-300 text-gray-950 bg-white disabled:bg-gray-200 disabled:cursor-not-allowed disabled:text-gray-400"
                :class="[
                    {
                        'border-gray-200 focus:ring-bilbao-800 hover:border-bilbao-600 focus:border-bilbao-800' :!error,
                        'border-error-300 focus:ring-error-300 hover:border-error-300 focus:border-error-300' :error,
                    }
                ]"
            >
                <span class="block truncate" v-if="label">{{ label }}</span>
                <span v-else class="text-gray-400">{{ $t('public.' + placeholder) }}</span>
                <span
                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2"
                >
            <ChevronDownIcon
                class="h-5 w-5 text-gray-400"
                aria-hidden="true"
            />
          </span>
            </ListboxButton>

            <transition
                leave-active-class="transition duration-100 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <ListboxOptions
                    class="z-10 absolute mt-2 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
                >
                    <ListboxOption
                        v-slot="{ active, selected }"
                        v-for="option in props.options"
                        :key="option.label"
                        :value="option.value"
                        as="template"
                    >
                        <li
                            :class="[
                  active ? 'bg-bilbao-50' : 'text-gray-950',
                  'relative cursor-default select-none py-2 px-4',
                ]"
                        >
                <span
                    :class="[
                    selected ? 'font-medium' : 'font-normal',
                    'block truncate',
                  ]"
                >{{ option.label }}</span
                >
                            <span
                                v-if="selected"
                                class="absolute inset-y-0 right-0 flex items-center pr-3"
                            >
                  <CheckIcon class="h-5 w-5 text-bilbao-800" aria-hidden="true" />
                </span>
                        </li>
                    </ListboxOption>
                </ListboxOptions>
            </transition>
            <div class="text-sm text-error-500 mt-2" v-if="props.error">{{ props.error }}</div>
        </div>
    </Listbox>
</template>
