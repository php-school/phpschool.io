<script setup>
defineProps({
  data: Object,
});
</script>

<template>
  <div class="mt-1 flex w-full justify-between">
    <p class="text-sm text-red-500">{{ data.reason }}</p>
  </div>
</template>
