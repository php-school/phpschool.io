<script>

import Modal from "./Modal.vue";
import { ArrowPathIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/solid'
import {editor} from "./stores/editor.js";

export default {
  components: {
    Modal,
    ArrowPathIcon,
    ExclamationTriangleIcon
  },
  emits: ["verify-loading", "verify-success", "verify-fail"],
  props: {
    workshopCode: String,
    exerciseSlug: String,
  },
  data() {
    return {
      loadingRun: false,
      programOutput: '',
      openRunModal: false,
      loadingVerify: false,
      editor
    }
  },
  methods: {
    runSolution() {
      this.loadingRun = true;
      const url = '/cloud/workshop/' + this.workshopCode + '/exercise/' + this.exerciseSlug + '/run';
      const content = this.editor.getFileContent('solution.php');

      const opts = {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({script: content})
      };
      fetch(url, opts)
          .then(response => response.json())
          .then(json => {
            this.programOutput = json.output;
            this.openRunModal = true;
            this.loadingRun = false;
          })
    },
    verifySolution() {
      this.$emit('verify-loading');
      this.loadingVerify = true;

      const url = '/cloud/workshop/' + this.workshopCode + '/exercise/' + this.exerciseSlug + '/verify';

      const opts = {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({script: this.editor.getFileContent('solution.php')})
      };
      fetch(url, opts)
          .then(response => response.json())
          .then(json => {
            if (json.success === true) {
              this.$emit('verify-success');
            } else {
              this.$emit('verify-fail', json.results);
            }

            this.loadingVerify = false;
          })
    },
  }
}
</script>

<template>
  <div class="flex items-center">
    <button id="run" class="button flex items-center justify-center mt-0 mr-2 px-4 w-36 rounded" @click="runSolution">
      <ArrowPathIcon v-cloak v-show="loadingRun" class="w-4 h-4 animate-spin"/>
      <span v-if="!loadingRun">Run</span>
    </button>
    <button id="verify" class="button flex items-center justify-center mt-0 px-4 w-36 rounded" @click="verifySolution">
      <ArrowPathIcon v-cloak v-show="loadingVerify" class="w-4 h-4 animate-spin"/>
      <span v-if="!loadingVerify">Verify</span>
    </button>
  </div>

  <Modal size="xl" v-if="openRunModal" @keydown.esc="openRunModal = false" @close="openRunModal = false">
    <template #header>
      <div class="flex items-center ">
        <ExclamationTriangleIcon class="h-6 w-6 text-green-600 mr-2"/>

        <h3 class="text-base font-semibold lg:text-xl text-white pt-0 mt-0 ">
          Program output
        </h3>
      </div>

    </template>
    <template #body>
      <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1 overflow-x-auto">
        <div class="mt-2 flex" v-if="programOutput">
          <p class="p-4 rounded text-sm font-mono bg-gray-800 text-white whitespace-pre-wrap flex-1 overflow-x-scroll">{{ programOutput }}</p>
        </div>
        <div v-if="!programOutput" class="" role="alert">
          <span class="sr-only">Info</span>
          <div class="text-white">Your program produced no output.</div>
        </div>
      </div>
    </template>
    <template #footer>
      <div class="flex justify-end">
        <button type="button" class="inline-flex items-center  w-full justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm" @click="runSolution">
          <ArrowPathIcon :class="{ 'animate-spin': loadingRun}" class="w-4 h-4 mr-2 -ml-1"/>
          Run again
        </button>
      </div>
    </template>
  </Modal>
</template>