<script setup>
defineProps({
  title: {
    type: String,
    required: true,
  },
});
</script>
<template>
  <div class="py-3">
    <h5 class="text-[12px] uppercase text-white">{{ title }}</h5>
    <ul class="!ml-0 mt-2 !list-none border-t border-gray-600">
      <slot></slot>
    </ul>
  </div>
</template>
