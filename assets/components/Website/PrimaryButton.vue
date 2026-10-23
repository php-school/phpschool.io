<script setup>
const props = defineProps({
  href: String,
  to: String,
});
</script>

<template>
  <component :href="props.href" :is="to ? 'router-link' : href ? 'a' : 'button'" :to="to" class="btn-primary">
    <slot />
  </component>
</template>

<style>
@reference "../../styles/online.css";

/* In the components layer so classes passed by the parent override these. */
@layer components {
  .btn-primary {
    @apply my-4 mr-2 cursor-pointer rounded-full bg-pink-600 px-10 py-2 text-base font-bold text-white uppercase shadow-brand-shadow hover:bg-pink-500;
  }
}
</style>
