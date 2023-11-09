<script setup>

import {defineProps, ref} from "vue";

const props = defineProps({
  data: Object
});
</script>

<template>
  <div class="mt-1 w-full flex justify-between">
    <p class="text-red-500 text-sm">{{ data.reason }}</p>
  </div>
</template>
