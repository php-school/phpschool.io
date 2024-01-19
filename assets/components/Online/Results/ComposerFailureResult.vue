<script setup>
import Modal from "../ModalDialog.vue";
import { ExclamationTriangleIcon } from "@heroicons/vue/24/solid";
import { ref } from "vue";

const openModal = ref(false);
defineProps({
  data: Object,
});
</script>

<template>
  <div class="mt-1 flex w-full items-start justify-between">
    <p class="w-2/3 text-sm text-red-500">Composer requirements were not fulfilled</p>
    <button class="p-x2 text-left text-sm text-[#E91E63] underline" @click="openModal = true">More info...</button>
  </div>

  <Transition
    enter-active-class="transition-opacity duration-100 ease-in"
    leave-active-class="transition-opacity duration-200 ease-in"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <Modal
      :scroll-content="true"
      size="xl"
      max-height="max-h-[calc(5/6*100%)]"
      v-if="openModal"
      @close="openModal = false"
    >
      <template #header>
        <div class="flex flex-col">
          <div class="flex items-center">
            <ExclamationTriangleIcon class="mr-2 h-6 w-6 text-rose-600" />

            <h2 class="mt-0 pt-0 font-mono text-base font-semibold text-white lg:text-xl">
              Composer requirements failure...
            </h2>
          </div>
        </div>
      </template>

      <template #body>
        <div class="mb-4 py-3">
          <div class="flex flex-wrap items-center">
            <div class="flex items-center">
              <p class="truncate font-medium text-white">
                <span class="text-sm">Your program was required to use various composer packages.</span>
              </p>
            </div>
          </div>
        </div>

        <div id="composer-requirements" class="flex space-x-4">
          <div v-if="data.is_missing_component" class="">
            <h2 class="mb-2 pt-0 font-mono text-lg text-[#E91E63]">Missing Composer configuration</h2>
            <p class="mb-4 text-sm">
              The file
              <code>{{ data.missing_component }}</code>
              is missing. You must specify some requirements for the file to be created.
            </p>
          </div>
        </div>

        <div id="composer-requirements" class="flex space-x-4">
          <div v-if="data.is_missing_packages" class="">
            <h2 class="mb-2 pt-0 font-mono text-lg text-[#E91E63]">Missing packages</h2>
            <p class="mb-4 text-sm">Some packages were not required which should be used in this exercise:</p>
            <code
              class="mb-2 mr-2 inline-block text-xs"
              v-for="composerPackage in data.missing_packages"
              :key="composerPackage"
            >
              {{ composerPackage }}
            </code>
          </div>
        </div>
      </template>

      <template #footer>
        <div class="flex justify-end">
          <button
            @click="openModal = false"
            type="button"
            class="inline-flex w-full items-center justify-center rounded-full border border-transparent bg-pink-600 px-8 py-2 text-base font-medium text-white shadow-sm hover:bg-pink-700 focus:outline-none focus:ring focus:ring-pink-800 sm:ml-3 sm:w-auto sm:text-sm"
          >
            Close
          </button>
        </div>
      </template>
    </Modal>
  </Transition>
</template>

<style>
#composer-requirements pre {
  background-color: initial;
  @apply mb-4 rounded-none border-none p-0;
}
#composer-requirements pre code {
  background-color: #2a2c2d !important;
  @apply rounded-lg p-4;
}

#composer-requirements :not(pre) > code {
  background-color: #2a2c2d;
  @apply rounded px-2 py-1;
  color: #ff75b5;
}
</style>
