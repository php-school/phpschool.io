<script>

import Modal from "./Modal.vue";
import {ArrowPathIcon, ExclamationTriangleIcon, CommandLineIcon, SparklesIcon, XMarkIcon} from '@heroicons/vue/24/solid'
import toFilePath from "./utils/toFilePath";

export default {
  components: {
    Modal,
    ArrowPathIcon,
    ExclamationTriangleIcon,
    CommandLineIcon,
    SparklesIcon,
    XMarkIcon
  },
  emits: ["verify-loading", "verify-success", "verify-fail"],
  props: {
    workshopCode: String,
    exerciseSlug: String,
    files: Array,
    composerDeps: Array
  },
  data() {
    return {
      loadingRun: false,
      programOutput: '',
      openRunModal: false,
      loadingVerify: false,
      showRateLimitError: false,
    }
  },
  methods: {
    flattenFiles(nodes, files = {}) {
      nodes.forEach((node) => {
        if (!node.children) {
          files[toFilePath(node)] = node.content ?? '';
        } else {
          this.flattenFiles(node.children, files);
        }
      })
      return files;
    },
    enableRateLimitError() {
      this.showRateLimitError = true;

      setTimeout(() => this.showRateLimitError = false, 3000);
    },
    runSolution() {
      if (this.loadingRun) {
        return;
      }

      this.loadingRun = true;
      const url = '/cloud/workshop/' + this.workshopCode + '/exercise/' + this.exerciseSlug + '/run';

      const opts = {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          scripts: this.flattenFiles(this.files),
          composerDeps: this.composerDeps
        })
      };
      fetch(url, opts)
          .then(response => {
            if (response.ok) {
              return response.json();
            }

            if (response.status === 429) {
              this.enableRateLimitError();
            }

            throw Error(response.statusText)
          })
          .then(json => {
            this.programOutput = json.output;
            this.openRunModal = true;
            this.loadingRun = false;
          })
          .catch(error => {
            this.loadingRun = false;
          })

    },
    verifySolution() {
      if (this.loadingVerify) {
        return;
      }

      this.$emit('verify-loading');
      this.loadingVerify = true;

      const url = '/cloud/workshop/' + this.workshopCode + '/exercise/' + this.exerciseSlug + '/verify';

      const opts = {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          scripts: this.flattenFiles(this.files),
          composerDeps: this.composerDeps
        })
      };
      fetch(url, opts)
          .then(response => {
            if (response.ok) {
              return response.json();
            }

            if (response.status === 429) {
              this.enableRateLimitError();
            }

            throw Error(response.statusText)
          })
          .then(json => {
            if (json.success === true) {
              this.$emit('verify-success');
            } else {
              this.$emit('verify-fail', json.results);
            }

            this.loadingVerify = false;
          })
          .catch(error => {
            this.loadingVerify = false;
          })
    },
  }
}
</script>

<template>
  <div  v-show="showRateLimitError"  v-cloak class="absolute top-4 left-0 z-[51] shadow-lg w-full flex justify-center">
    <div class="mx-auto py-3 px-3 sm:px-6 lg:px-8 bg-red-600 rounded-lg">
      <div class="flex flex-wrap items-center justify-center">
        <div class="flex items-center">
          <ExclamationTriangleIcon class="h-6 w-6 text-white"/>
          <p class="ml-3 truncate font-medium text-white">
            <span class="">Too many requests. Please try again in a few minutes.</span>
          </p>
        </div>
        <div class="order-2 flex-shrink-0 sm:order-3 sm:ml-3">
          <button @click="showRateLimitError = false" type="button" class="-mr-1 flex rounded-md p-2 hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-white sm:-mr-2">
            <span class="sr-only">Dismiss</span>
            <XMarkIcon class="h-6 w-6 text-white"/>
          </button>
        </div>
      </div>
    </div>
  </div>
  <div class="flex items-center">
    <button id="run" class="border-[#E91E63] hover:bg-[#E91E63] border-solid border-2 text-white h-full flex items-center justify-center mt-0 mr-2 px-4 w-36 rounded" @click.stop="runSolution" :disabled="loadingRun">
      <ArrowPathIcon v-cloak v-show="loadingRun" class="w-4 h-4 animate-spin"/>
      <span v-if="!loadingRun">Run</span>
      <CommandLineIcon v-if="!loadingRun" v-cloak class="ml-2 w-5 h-5"/>
    </button>
    <button id="verify" class="button flex items-center justify-center mt-0 px-4 w-36 rounded" @click="verifySolution" :disabled="loadingVerify">
      <ArrowPathIcon v-cloak v-show="loadingVerify" class="w-4 h-4 animate-spin"/>
      <span v-if="!loadingVerify">Verify</span>
      <SparklesIcon v-if="!loadingVerify" v-cloak class="ml-2 w-5 h-5"/>
    </button>
  </div>

  <Modal :scroll-content="true" size="xl" max-height="max-h-[calc(5/6*100%)]" v-if="openRunModal" @close="openRunModal = false">
    <template #header>
      <div class="flex items-center ">
        <CommandLineIcon class="h-6 w-6 text-pink-500 mr-2"/>
        <h3 class="text-base font-semibold lg:text-xl text-white pt-0 mt-0 ">
          Program output
        </h3>
      </div>

    </template>
    <template #body>
      <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
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
        <button type="button" class="inline-flex items-center w-full justify-center rounded-full border border-transparent bg-pink-600 px-8 py-2 text-base font-medium text-white shadow-sm hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm" @click="runSolution">
          <ArrowPathIcon :class="{ 'animate-spin': loadingRun}" class="w-4 h-4 mr-2 -ml-1"/>
          Run again
        </button>
      </div>
    </template>
  </Modal>
</template>