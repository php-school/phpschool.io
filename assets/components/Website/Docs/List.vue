<script setup>
const props = defineProps({
    'title': {
        type: String,
        required: true
    }
})
</script>
<template>
    <div class=" py-3">
        <h5 class="text-[#939da3] text-[12px] uppercase">{{title}}</h5>
        <ul class="border-t border-[#e1e8ed] mt-2 !list-none !ml-0">
            <slot></slot>
        </ul>
    </div>
</template>