<script setup>
import { computed } from "vue";

const props = defineProps({
  type: {
    type: String,
    default: "success",
  },
});

const panelClasses = computed(() => {
  return {
    info: "border-sky-500/50",
    success: "border-green-400/50",
    warning: "border-yellow-400/50",
    danger: "border-red-400/50",
  }[props.type];
});
</script>

<template>
  <div class="my-6 border-l-2 p-3" :class="panelClasses">
    <div class="flex items-start">
      <div class="">
        <p class="!mb-0 text-sm text-gray-300">
          <slot />
        </p>
      </div>
    </div>
  </div>
</template>
