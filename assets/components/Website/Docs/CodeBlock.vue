<script setup>
import { computed, useSlots } from "vue";

const slots = useSlots();

import { ref, onMounted, watch } from "vue";

import { highlightCode } from "../../../helpers/highlightCode";

import { TransitionRoot } from "@headlessui/vue";
import { ClipboardDocumentIcon } from "@heroicons/vue/24/solid";

const language = ref("php");
const formattedCode = ref("");
const code = ref(slots.default()[0].children);

const highlight = () => {
  formattedCode.value = highlightCode(code.value, language.value);
};

onMounted(() => {
  highlight();
});

watch(
  () => slots.default(),
  () => {
    highlight();
  },
);

const copied = ref(false);

const copy = () => {
  if (navigator.clipboard) {
    navigator.clipboard.writeText(code.value);

    copied.value = true;

    setTimeout(() => (copied.value = false), 2000);
  }
};

const clipboardAvailable = computed(() => {
  return typeof window !== "undefined" && navigator.clipboard;
});
</script>
<template>
  <div class="group relative">
    <ClipboardDocumentIcon
      v-if="clipboardAvailable && !copied"
      @click="copy"
      class="absolute right-4 top-4 h-4 w-4 bg-gray-900 text-gray-300 opacity-0 transition-all duration-300 group-hover:opacity-100 hover:cursor-pointer hover:text-pink-600"
    ></ClipboardDocumentIcon>

    <TransitionRoot
      :show="copied"
      enter="transition-opacity duration-25"
      enter-from="opacity-0"
      enter-to="opacity-100"
      leave="transition-opacity duration-150"
      leave-from="opacity-100"
      leave-to="opacity-0"
      class="leading-none"
    >
      <span class="absolute right-4 top-4 font-mono text-[10px] text-pink-600">Copied!</span>
    </TransitionRoot>
    <div class="mb-4 overflow-y-scroll rounded-md border border-gray-600 bg-gray-900 p-4">
      <pre><code class="block  text-xs " :class="language" v-html="formattedCode"></code></pre>
    </div>
  </div>
</template>
