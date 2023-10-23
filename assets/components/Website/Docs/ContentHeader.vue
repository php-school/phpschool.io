<script setup>
    const props = defineProps({
        'id': {
            type: String,
            required: true,
        }
    })
</script>
<template>
    <h3 :id="id" class="!text-lg flex relative items-center font-mono border-b border-dotted border-[#ccc] !pt-4 !pb-1 !mb-4">
        <slot></slot><a class="!text-[#ccc] fill-current inline-block ml-[2px]" :href="'#' + id">#</a><a href="#app" class="absolute right-0 text-xs hover:underline">^ TOP</a>
    </h3>
</template>

