<script setup>

import {computed} from "vue";

const props = defineProps({
    'type': {
        type: String,
        default: 'success'
    },
})

const panelClasses = computed(() => {
    return {
        'info': 'border-sky-400 bg-sky-50',
        'success': 'border-green-400 bg-green-50',
        'warning': 'border-yellow-400 bg-yellow-50',
        'danger': 'border-red-400 bg-red-50',
    }[props.type]
});

const pClasses = computed(() => {
    return {
        'info': '!text-sky-700',
        'success': '!text-green-700',
        'warning': '!text-yellow-700',
        'danger': '!text-red-700',
    }[props.type]
});

</script>

<template>
    <div class="border-l-4 p-3 my-4" :class="panelClasses">
        <div class="flex">
            <div class="ml-3">
                <p class="!text-xs !mb-0" :class="pClasses">
                    <slot />
                </p>
            </div>
        </div>
    </div>
</template>