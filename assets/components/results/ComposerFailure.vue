<script>

import Modal from "../Modal.vue";
import {ExclamationTriangleIcon} from '@heroicons/vue/24/solid'

export default {
  components: {
    Modal,
    ExclamationTriangleIcon
  },
  props: {
    data: Object,
    renderers: Object
  },
  data() {
    return {
      openModal: false,
    }
  },
}
</script>

<template>
  <div class="mt-1 w-full flex justify-between items-start">
    <p class="text-red-500 w-2/3">Composer requirements were not fulfilled</p>
    <button class="underline text-[#E91E63] text-left p-x2" @click="openModal = true">More info...</button>
  </div>

  <Transition enter-active-class="transition-opacity duration-100 ease-in" leave-active-class="transition-opacity duration-200 ease-in" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
    <Modal :scroll-content="true" size="xl" max-height="max-h-[calc(5/6*100%)]" v-if="openModal" @close="openModal = false">
      <template #header>
        <div class="flex flex-col">
          <div class="flex items-center ">
            <ExclamationTriangleIcon class="h-6 w-6 text-rose-600 mr-2"/>

            <h2 class="text-base font-semibold lg:text-xl text-white pt-0 mt-0 ">Composer requirements failure...</h2>
          </div>
        </div>
      </template>

      <template #body class="">
        <div class="py-3 mb-4">
          <div class="flex flex-wrap items-center">
            <div class="flex items-center">
              <p class=" truncate font-medium text-white">
                <span class="">Your program was required to use various composer packages.</span>
              </p>
            </div>
          </div>
        </div>

        <div id="composer-requirements" class="flex space-x-4">
          <div v-if="data.is_missing_component" class="">
            <h2 class="mb-2 text-lg pt-0">Missing Composer configuration</h2>
            <p class="mb-4">The file <code>{{ data.missing_component }}</code> is missing. You must specify some requirements for the file to be created.</p>
          </div>
        </div>

        <div id="composer-requirements" class="flex space-x-4">
          <div v-if="data.is_missing_packages" class="">
            <h2 class="mb-2 text-lg pt-0">Missing packages</h2>
            <p class="mb-4">Some packages were not required which should be used in this exercise:</p>
            <code class="inline-block mr-2" v-for="composerPackage in data.missing_packages">{{ composerPackage }}</code>
          </div>
        </div>
      </template>

      <template #footer>
        <div class="flex justify-end">
          <button @click="openModal = false" type="button" class="inline-flex items-center w-full justify-center rounded-full border border-transparent bg-pink-600 px-8 py-2 text-base font-medium text-white shadow-sm hover:bg-pink-700 focus:outline-none focus:ring focus:ring-pink-800 sm:ml-3 sm:w-auto sm:text-sm">
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
  @apply mb-4 p-0 border-none rounded-none;
}
#composer-requirements pre code {
  background-color: #2a2c2d !important;
  @apply p-4 rounded-lg;
}

#composer-requirements :not(pre)>code {
  font-size: 90%;
  background-color: #2a2c2d;
  @apply px-2 py-1 rounded;
  color: #ff75b5;
}
</style>