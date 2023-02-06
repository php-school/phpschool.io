<script>
import { ExclamationTriangleIcon } from "@heroicons/vue/24/solid";

export default {
  components: {
    ExclamationTriangleIcon,
  },
  props: {
    isOpen: {
      default: false,
      type: Boolean,
    },
  },
  methods: {
    confirm() {
      this.$emit("confirm");
    },
    decline() {
      this.$emit("decline");
    },
  },
};
</script>

<template>
  <div v-show="isOpen" class="fixed rounded-lg bg-gray-800 flex flex-col justify-start w-full h-full inset-0 z-40 bg-opacity-80">
    <div class="bg-grey-800 h-full w-full flex justify-center items-center drop-shadow-xl">
      <div class="bg-gray-900 max-w-sm max-h-[calc(1/2*100%)] rounded-lg">
        <div class="p-4 rounded-t flex-none flex justify-between items-top border-b border-solid border-slate-600">
          <div class="flex items-center">
            <ExclamationTriangleIcon class="h-6 w-6 text-pink-500 mr-2" />
            <h3 class="text-base font-semibold lg:text-xl text-white pt-0 mt-0">Deleting...</h3>
          </div>
        </div>
        <div class="p-4 rounded-t flex-none flex justify-between items-top border-b border-solid border-slate-600">
          <div class="flex items-center">
            <p class="text-white">Selection will be permanently deleted, would you like to proceed?</p>
          </div>
        </div>
        <div class="p-6 rounded-b border-solid border-slate-600 flex-none">
          <div class="flex justify-end">
            <button @click="confirm" type="button" class="inline-flex items-center w-full justify-center rounded-full border border-transparent bg-pink-600 px-8 py-2 text-base font-medium text-white shadow-sm hover:bg-pink-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Yes</button>
            <button @click="decline" type="button" class="inline-flex items-center w-full justify-center rounded-full border border-transparent bg-pink-600 px-8 py-2 text-base font-medium text-white shadow-sm hover:bg-pink-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">No</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
