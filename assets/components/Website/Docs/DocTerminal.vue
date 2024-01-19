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
  <div
    class="group mb-8 w-full overflow-y-scroll rounded-lg border border-solid border-gray-600 bg-gray-900 p-4 font-mono"
  >
    <div class="flex flex-col">
      <div class="flex items-center justify-between pb-3">
        <div class="flex">
          <div class="mr-2 h-4 w-4 rounded-full border border-solid border-gray-600"></div>
          <div class="mr-2 h-4 w-4 rounded-full border border-solid border-gray-600"></div>
          <div class="h-4 w-4 rounded-full border border-solid border-gray-600"></div>
        </div>

        <ClipboardDocumentIcon
          v-if="clipboardAvailable && !copied"
          @click="copy"
          class="h-4 w-4 text-gray-300 opacity-0 transition-all duration-300 group-hover:opacity-100 hover:cursor-pointer hover:text-pink-600"
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
      <div class="whitespace-pre-wrap border-none bg-gray-900 p-0 font-mono text-white">
        <div v-for="line in lines" :key="line">
          <span v-if="line.startsWith('//')" class="text-xs text-pink-600">
            <br />
            {{ line }}
          </span>
          <span v-else class="whitespace-pre text-xs before:mr-2 before:text-pink-600 before:content-['>']">
            {{ line }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>
