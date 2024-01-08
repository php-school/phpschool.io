<script setup>
const props = defineProps({
    'divider': {
        type: Boolean,
        default: true
    }
})
</script>
<template>
    <div
        class="max-w-[2048px] flex flex-col text-center lg:flex-row  h-auto mx-auto p-4  justify-center items-center lg:gap-20">
        <slot name="left" />
        <slot name="right" />
    </div>
    <div v-if="props.divider" class="flex justify-center items-center my-8 z-10">
        <div class="horizontal-divider"></div>
    </div>
</template>
