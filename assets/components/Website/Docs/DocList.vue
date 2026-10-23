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
    <h5 class="text-[12px] text-white uppercase">{{ title }}</h5>
    <ul class="mt-2 ml-0! list-none! border-t border-gray-600">
      <slot></slot>
    </ul>
  </div>
</template>
