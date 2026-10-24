<script setup>
import { Combobox, ComboboxInput, ComboboxOptions, ComboboxOption, TransitionRoot } from "@headlessui/vue";
import { CheckIcon } from "@heroicons/vue/20/solid";
import { ref, watch } from "vue";
import { useDebounceFn } from "@vueuse/core";

const query = ref("");
const selected = ref(null);
const filteredPackages = ref([]);

const emit = defineEmits(["package-selected"]);

watch(selected, (newPackage) => {
  emit("package-selected", newPackage);
});

const reset = () => {
  query.value = "";
  selected.value = null;
  filteredPackages.value = [];
};

const searchPackages = useDebounceFn((newQuery) => {
  query.value = newQuery;

  if (newQuery.length < 4) {
    filteredPackages.value = [];
    return;
  }

  const opts = {
    method: "GET",
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json",
    },
  };
  fetch("/api/online/composer-package/search?package=" + encodeURIComponent(newQuery), opts)
    .then((response) => response.json())
    .then((packages) => {
      filteredPackages.value = packages.results;
    });
}, 500);

defineExpose({
  reset,
});
</script>

<template>
  <div class="relative top-0">
    <Combobox v-model="selected" nullable v-slot="{ open }">
      <div class="relative">
        <div
          class="relative w-full cursor-default overflow-hidden border-2 border-solid border-pink-500 bg-white text-left shadow-md focus:outline-hidden focus-visible:ring-2 focus-visible:ring-white/75 focus-visible:ring-offset-2 focus-visible:ring-offset-pink-300 sm:text-sm"
          :class="{
            'rounded-t-md': open,
            'rounded-md': !open,
          }"
        >
          <ComboboxInput
            placeholder="Start typing..."
            class="w-full border-none py-2 pr-10 pl-3 text-sm leading-5 text-gray-900 focus:ring-0"
            :displayValue="(composerPackage) => composerPackage"
            @change="searchPackages($event.target.value)"
          />
        </div>
        <TransitionRoot leave="transition ease-in duration-100" leaveFrom="opacity-100" leaveTo="opacity-0" @after-leave="query = ''">
          <ComboboxOptions
            class="absolute max-h-60 w-full overflow-auto rounded-b-md border-2 border-solid border-pink-500 bg-white text-base shadow-lg ring-1 ring-black/5 focus:outline-hidden sm:text-sm"
          >
            <div v-if="filteredPackages.length === 0 && query.length > 3" class="relative cursor-default px-4 py-2 text-gray-700 select-none">Nothing found.</div>
            <div v-if="query.length < 4" class="relative cursor-default px-4 py-2 text-gray-700 select-none">Type 3 or more characters to search.</div>
            <ComboboxOption v-for="composerPackage in filteredPackages" :key="composerPackage" as="template" :value="composerPackage" v-slot="{ selected, active }">
              <li
                class="relative cursor-default py-3 pr-4 pl-3 select-none"
                :class="{
                  'bg-pink-500 text-white': active,
                  'text-gray-900': !active,
                }"
              >
                <span class="block truncate" :class="{ 'font-medium': selected, 'font-normal': !selected }">
                  {{ composerPackage }}
                </span>
                <span v-if="selected" class="absolute inset-y-0 right-3 flex items-center pl-3" :class="{ 'text-white': active, 'text-pink-500': !active }">
                  <CheckIcon class="h-5 w-5" aria-hidden="true" />
                </span>
              </li>
            </ComboboxOption>
          </ComboboxOptions>
        </TransitionRoot>
      </div>
    </Combobox>
  </div>
</template>
