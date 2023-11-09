<script>
import {
  Combobox,
  ComboboxInput,
  ComboboxButton,
  ComboboxOptions,
  ComboboxOption,
  TransitionRoot,
} from '@headlessui/vue'
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid'
import debounce from "./Utils/debounce.js";

export default {
  components: {
    Combobox,
    ComboboxInput,
    ComboboxButton,
    ComboboxOptions,
    ComboboxOption,
    TransitionRoot,
    CheckIcon,
    ChevronUpDownIcon
  },
  data() {
    return {
      query: '',
      selected: null,
      filteredPackages: [],
    }
  },
  watch: {
    selected(newPackage, oldPackage) {
      this.$emit('package-selected', newPackage);
    }
  },
  methods: {
    reset() {
      this.query = '';
      this.selected = null;
      this.filteredPackages = [];
    },
    searchPackages: debounce(function (query) {
      this.query = query;

      if (query.length < 4) {
        this.filteredPackages = [];
        return;
      }

      const opts = {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
      };
      fetch('/cloud/composer-package/search?package=' + encodeURIComponent(query), opts)
          .then(response => response.json())
          .then(packages => {
            this.filteredPackages = packages.results
          });
    })
  }
};
</script>

<template>
  <div class="relative top-0 ">
    <Combobox v-model="selected" nullable v-slot="{ open }">
      <div class="relative">
        <div
            class="relative w-full cursor-default overflow-hidden border-2 border-pink-500 border-solid bg-white text-left shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75 focus-visible:ring-offset-2 focus-visible:ring-offset-pink-300 sm:text-sm"
            :class="{
            'rounded-t-md': open,
            'rounded-md': !open,
          }"
        >
          <ComboboxInput

              placeholder="Start typing..."
                         class="w-full border-none py-2 pl-3 pr-10 text-sm leading-5 text-gray-900 focus:ring-0"
                         :displayValue="(composerPackage) => composerPackage"
                         @change="searchPackages($event.target.value)"
          />  
        </div>
        <TransitionRoot
            leave="transition ease-in duration-100"
            leaveFrom="opacity-100"
            leaveTo="opacity-0"
            @after-leave="query = ''"
        >
          <ComboboxOptions
              class="absolute border-2 border-pink-500 border-solid  max-h-60 w-full overflow-auto rounded-b-md bg-white text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
          >
            <div
                v-if="filteredPackages.length === 0 && query.length > 3"
                class="relative cursor-default select-none py-2 px-4 text-gray-700"
            >
              Nothing found.
            </div>
            <div
                v-if="query.length < 4"
                class="relative cursor-default select-none py-2 px-4 text-gray-700"
            >
              Type 3 or more characters to search.
            </div>
            <ComboboxOption
                v-for="composerPackage in filteredPackages"
                as="template"
                :value="composerPackage"
                v-slot="{ selected, active }"
            >
              <li
                  class="relative cursor-default select-none py-3 pl-3 pr-4"
                  :class="{
                  'bg-pink-500 text-white': active,
                  'text-gray-900': !active,
                }"
              >
                <span
                    class="block truncate"
                    :class="{ 'font-medium': selected, 'font-normal': !selected }"
                >
                  {{ composerPackage }}
                </span>
                <span
                    v-if="selected"
                    class="absolute inset-y-0 right-3 flex items-center pl-3"
                    :class="{ 'text-white': active, 'text-pink-500': !active }"
                >
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