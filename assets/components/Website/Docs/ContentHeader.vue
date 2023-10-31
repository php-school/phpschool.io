<script setup>
    const props = defineProps({
        'id': {
            type: String,
            required: true,
        },
        level: {
            type: String,
            default: 'h3',
        }
    })
</script>
<template>
    <h3 v-if="level === 'h3'" :id="id" class="!text-lg flex relative items-center font-mono border-b border-dotted border-[#ccc] !pt-4 !pb-1 !mb-4">
        <slot></slot>
        <a class="!text-[#ccc] fill-current inline-block ml-[2px]" :href="'#' + id">#</a><a href="#app" class="absolute right-0 text-xs hover:underline">^ TOP</a>
    </h3>

    <h4 v-if="level === 'h4'" :id="id" class="!text-base flex relative items-center font-mono !pt-4 !pb-1 !mb-2 italic font-semibold border-b border-dotted border-[#ccc]">
        <slot></slot>
        <a class="!text-[#ccc] fill-current inline-block ml-[2px]" :href="'#' + id">#</a>
    </h4>

    <h4 v-if="level === 'h4-code'" :id="id" class="!text-base flex relative items-center font-mono !pt-4 !pb-1 !mb-2 !not-italic">
        <code class="text-pink-500"><slot></slot></code>
        <a class="!text-[#ccc] fill-current inline-block ml-[3px]" :href="'#' + id">#</a>
    </h4>

    <h5 v-if="level === 'h5'" :id="id" class="!text-base flex relative items-center font-mono !pt-4 !pb-1 !mb-2 italic">
        <slot></slot>
        <a class="!text-[#ccc] fill-current inline-block ml-[2px]" :href="'#' + id">#</a>
    </h5>
</template>

