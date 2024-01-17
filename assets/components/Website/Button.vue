<script setup>
 const props = defineProps({
     'to' : String,
 })
</script>

<template>
    <component :to="props.to" :is="to ? 'router-link' : 'button'" class="text-white font-bold text-base  px-10 py-2 uppercase my-4 mr-2 rounded-full bg-pink-600 hover:bg-pink-500 shadow-brand-shadow">
        <slot />
    </component>
</template>