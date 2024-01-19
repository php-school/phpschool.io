<script setup>
import { ClipboardDocumentIcon } from "@heroicons/vue/24/solid";
import { computed, ref } from "vue";
import { TransitionRoot } from "@headlessui/vue";

const props = defineProps({
  lines: {
    type: Array,
  },
});

const copied = ref(false);

const copy = () => {
  const lines = props.lines.join("\n");

  if (navigator.clipboard) {
    navigator.clipboard.writeText(lines);

    copied.value = true;

    setTimeout(() => (copied.value = false), 2000);
  }
};

const clipboardAvailable = computed(() => {
  return typeof window !== "undefined" && navigator.clipboard;
});
</script>

<template>
  <div class="group m-4 mt-8 w-full rounded-lg border border-solid border-gray-600 p-4 lg:m-0 lg:mt-0 lg:w-3/5">
    <div class="flex flex-col">
      <div class="flex items-center justify-between pb-10">
        <div class="flex">
          <div class="mr-2 h-6 w-6 rounded-full border border-solid border-gray-600"></div>
          <div class="mr-2 h-6 w-6 rounded-full border border-solid border-gray-600"></div>
          <div class="h-6 w-6 rounded-full border border-solid border-gray-600"></div>
        </div>

        <ClipboardDocumentIcon
          v-if="clipboardAvailable && !copied"
          @click="copy"
          class="h-5 w-5 text-gray-300 opacity-0 transition-all duration-300 group-hover:opacity-100 hover:cursor-pointer hover:text-pink-600"
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
          <span class="font-mono text-[10px] text-pink-600">Copied!</span>
        </TransitionRoot>
      </div>
      <div class="whitespace-pre-wrap border-none bg-gray-900 p-0 font-mono text-white lg:px-10">
        <div v-for="(line, i) in lines" :key="i">
          <span v-if="line.startsWith('//')" class="text-pink-600">
            <br />
            {{ line }}
          </span>
          <span v-else class="whitespace-pre before:mr-2 before:text-pink-600 before:content-['$']">
            {{ line }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>
