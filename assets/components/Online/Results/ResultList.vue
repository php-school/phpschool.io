<script setup>

import {CheckIcon, XMarkIcon} from '@heroicons/vue/24/solid'
import resultRenderers from "../../../result.manifest.json";
import results from "./results.js";
import {computed} from "vue";

const props = defineProps({
  results: Array,
  workshop: Object,
});

const successes = computed(() => {
  return props.results.filter(r => r.success === true);
});

const failures = computed(() => {
  return props.results.filter(r => r.success === false);
});

const workshopResultRenderers = resultRenderers[props.workshop.code];
</script>

<template>
  <ul v-show="results" id="results" class="px-4 my-8 space-y-4 text-left text-gray-100 max-h-full h-full overflow-y-scroll">
    <li v-for="success in successes" class="flex items-start space-x-3">
      <CheckIcon fill="currentColor" class="flex-shrink-0 w-5 h-5 text-green-500"/>
      <div class="flex flex-col w-full">
        <span class="text-sm">{{ success.name }} </span>
      </div>
    </li>

    <li v-for="failure in failures" class="flex items-start space-x-3">
      <XMarkIcon fill="currentColor" class="flex-shrink-0 w-5 h-5 text-red-500"/>
      <div class="flex flex-col w-full">
        <span class="text-sm">{{failure.name }}</span>
        <component v-if="workshopResultRenderers.hasOwnProperty(failure.type)" :is="workshopResultRenderers[failure.type]" :data="failure" :renderers="workshopResultRenderers"></component>
      </div>
    </li>
  </ul>
</template>