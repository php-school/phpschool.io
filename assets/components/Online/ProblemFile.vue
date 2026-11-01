<script setup>
import Modal from "./ModalDialog.vue";
import { ArrowPathIcon, MapIcon } from "@heroicons/vue/24/solid";
import { ref, watch } from "vue";

import hljs from "highlight.js/lib/core";
import php from "highlight.js/lib/languages/php";
import shell from "highlight.js/lib/languages/shell";
import javascript from "highlight.js/lib/languages/javascript";
import markdown from "highlight.js/lib/languages/markdown";
import json from "highlight.js/lib/languages/json";

hljs.registerLanguage("php", php);
hljs.registerLanguage("shell", shell);
hljs.registerLanguage("shell", javascript);
hljs.registerLanguage("md", markdown);
hljs.registerLanguage("json", json);

const props = defineProps({
  exercise: Object,
  openProblemModal: Boolean,
  problem: String,
});

const problemContainer = ref();

const emit = defineEmits(["close"]);

const highlightedCode = ref("");

watch(
  () => props.problem,
  (problem) => {
    if (problem) {
      const parser = new DOMParser();
      const doc = parser.parseFromString(problem, "text/html");

      const codeBlocks = doc.querySelectorAll("pre code");
      codeBlocks.forEach(hljs.highlightElement);

      const s = new XMLSerializer();
      highlightedCode.value = s.serializeToString(doc);
    }
  },
  { immediate: true },
);
</script>

<template>
  <Transition
    enter-active-class="transition-opacity duration-100 ease-in"
    leave-active-class="transition-opacity duration-200 ease-in"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <Modal id="problem-modal" :scroll-content="true" size="4xl" max-height="h-5/6" v-if="openProblemModal" @close="emit('close')">
      <template #header>
        <div class="flex flex-col">
          <div class="flex items-center">
            <MapIcon class="mr-2 h-6 w-6 text-pink-500" />
            <h3 class="mt-0 pt-0 font-mono text-base font-semibold text-white lg:text-xl">The problem...</h3>
          </div>
          <h2 class="mt-2 mb-2 pt-[13.5px] font-mono text-2xl text-white">
            {{ exercise.name }}
          </h2>
        </div>
      </template>

      <template #body>
        <div ref="problemContainer" v-if="problem" id="problem-file" class="text-white" v-html="highlightedCode"></div>
        <ArrowPathIcon v-cloak v-else class="mx-auto h-10 w-10 animate-spin text-white" />
      </template>

      <template #footer>
        <div class="flex justify-end">
          <button
            id="lets-go"
            @click="emit('close')"
            type="button"
            class="inline-flex w-full items-center justify-center rounded-full border border-transparent bg-pink-600 px-8 py-2 text-base font-medium text-white shadow-xs hover:bg-pink-700 focus:ring-3 focus:ring-pink-800 focus:outline-hidden sm:ml-3 sm:w-auto sm:text-sm"
          >
            Let's go!
          </button>
        </div>
      </template>
    </Modal>
  </Transition>
</template>

<style>
@reference "../../styles/online.css";
#problem-file p {
  @apply mt-2 mb-4 text-sm leading-loose tracking-wide;
}

#problem-file hr {
  @apply border-dotted border-gray-600;
}

#problem-file h2 {
  @apply mt-2 mb-4 text-2xl text-[#E91E63];
}

#problem-file h3 {
  @apply mt-2 mb-4 text-xl text-[#E91E63];
}

#problem-file h4 {
  @apply mt-2 mb-4 text-lg text-[#E91E63] not-italic;
}

#problem-file a {
  @apply text-[#E91E63];
}

#problem-file pre {
  background-color: initial;
  @apply mt-2 mb-4 rounded-none border-none p-0;
}

#problem-file pre code {
  @apply block rounded-lg border border-gray-600 bg-[#2a2c2d]! p-4 text-sm;
}

#problem-file :not(pre) > code {
  font-size: 90%;
  @apply rounded bg-[#2a2c2d] px-2 py-1 text-[#ff75b5];
}

#problem-file ul {
  @apply mt-2 mb-4 list-inside list-disc;
}

#problem-file ul li {
  @apply list-item p-1;
}
</style>
