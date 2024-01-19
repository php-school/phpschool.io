<script setup>
const props = defineProps({
  divider: {
    type: Boolean,
    default: true,
  },
});
</script>
<template>
  <div class="mx-auto flex h-auto max-w-[2048px] flex-col items-center justify-center p-4 text-center lg:flex-row lg:gap-20">
    <slot name="left" />
    <slot name="right" />
  </div>
  <div v-if="props.divider" class="z-10 my-8 flex items-center justify-center">
    <div class="horizontal-divider"></div>
  </div>
</template>
