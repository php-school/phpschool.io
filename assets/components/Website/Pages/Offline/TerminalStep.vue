<script setup>
import TerminalDisplay from "./TerminalDisplay.vue";

defineProps({
  step: {
    type: Number,
  },
  title: {
    type: String,
  },
  description: {
    type: String,
  },
  lines: {
    type: Array,
  },
});
</script>
<template>
  <li class="mb-12 flex w-full justify-between p-4">
    <div class="flex flex-col">
      <div class="mb-4 flex h-8 w-8 items-center justify-center rounded bg-slate-800 font-bold">
        {{ step }}
      </div>
      <div class="ml-4 flex-grow border-l border-solid border-slate-800"></div>
    </div>

    <div class="flex w-full flex-col lg:flex-row">
      <div class="mr-8 flex-1 px-4">
        <h4 class="mt-0 p-0 font-work-sans text-lg font-bold capitalize not-italic text-pink-600">
          {{ title }}
        </h4>
        <p class="mt-4 text-sm text-gray-300">{{ description }}</p>
      </div>

      <TerminalDisplay :lines="lines" />
    </div>
  </li>
</template>
