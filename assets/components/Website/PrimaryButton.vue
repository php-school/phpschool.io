<script setup>
const props = defineProps({
  href: String,
  as: String,
});
</script>

<template>
  <component :href="props.href" :is="href ? 'a' : 'button'" class="my-4 mr-2 rounded-full bg-pink-600 px-10 py-2 text-base font-bold uppercase text-white shadow-brand-shadow hover:bg-pink-500">
    <slot />
  </component>
</template>
