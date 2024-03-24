<script setup>
import { CheckIcon, XMarkIcon } from "@heroicons/vue/24/solid";
import resultRenderers from "../../../result.manifest.json";
import { computed } from "vue";

const props = defineProps({
  results: Array,
  workshop: Object,
});

const successes = computed(() => {
  return props.results.filter((r) => r.success === true);
});

const failures = computed(() => {
  return props.results.filter((r) => r.success === false);
});

const workshopResultRenderers = resultRenderers[props.workshop.code];
</script>

<template>
  <ul class="my-8 h-full max-h-full space-y-4 overflow-y-scroll px-4 text-left text-gray-100">
    <li v-for="success in successes" :key="success.name" class="flex items-start space-x-3">
      <CheckIcon fill="currentColor" class="h-5 w-5 flex-shrink-0 text-green-500" />
      <div class="flex w-full flex-col">
        <span class="text-sm">{{ success.name }}</span>
      </div>
    </li>

    <li v-for="failure in failures" :key="failure.name" class="flex items-start space-x-3">
      <XMarkIcon fill="currentColor" class="h-5 w-5 flex-shrink-0 text-red-500" />
      <div class="flex w-full flex-col">
        <span class="text-sm">{{ failure.name }}</span>
        <component v-if="workshopResultRenderers.hasOwnProperty(failure.type)" :is="workshopResultRenderers[failure.type]" :data="failure" :renderers="workshopResultRenderers"></component>
      </div>
    </li>
  </ul>
</template>
