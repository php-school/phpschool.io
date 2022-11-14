<script>

import Modal from "./Modal.vue";

export default {
  components: {
      Modal
  },
  emits: ["verify-success"],
  props: {
    workshopCode: String,
    exerciseSlug: String
  },
  data() {
    return {
      loadingRun: false,
      programOutput: '',
      openRunModal: false,
      loadingVerify: false,
      verifyResults: '',
    }
  },
  methods: {
    runSolution() {
      this.loadingRun = true;
      const url = '/cloud/workshop/' + this.workshopCode + '/exercise/' + this.exerciseSlug + '/run';
      const content = window.editor.getValue();

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
      this.loadingVerify = true;
      this.verifyResults = '';

      const url = '/cloud/workshop/' + this.workshopCode + '/exercise/' + this.exerciseSlug + '/verify';
      const content = window.editor.getValue();

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
            this.verifyResults = json.results;

            if (json.success === true) {
              this.$emit('verify-success');
            }

            this.loadingVerify = false;
          })
    },
  }
}
</script>

<template>
  <button id="run" class="button mt-10 flex items-center justify-center" @click="runSolution">
    <svg v-cloak v-if="loadingRun" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 animate-spin">
      <path fill-rule="evenodd" d="M4.755 10.059a7.5 7.5 0 0112.548-3.364l1.903 1.903h-3.183a.75.75 0 100 1.5h4.992a.75.75 0 00.75-.75V4.356a.75.75 0 00-1.5 0v3.18l-1.9-1.9A9 9 0 003.306 9.67a.75.75 0 101.45.388zm15.408 3.352a.75.75 0 00-.919.53 7.5 7.5 0 01-12.548 3.364l-1.902-1.903h3.183a.75.75 0 000-1.5H2.984a.75.75 0 00-.75.75v4.992a.75.75 0 001.5 0v-3.18l1.9 1.9a9 9 0 0015.059-4.035.75.75 0 00-.53-.918z" clip-rule="evenodd" />
    </svg>
    <span v-if="!loadingRun">Run</span>
  </button>
  <button id="verify" class="button mt-2 flex items-center justify-center" @click="verifySolution">
    <svg v-cloak v-if="loadingVerify" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 animate-spin">
      <path fill-rule="evenodd" d="M4.755 10.059a7.5 7.5 0 0112.548-3.364l1.903 1.903h-3.183a.75.75 0 100 1.5h4.992a.75.75 0 00.75-.75V4.356a.75.75 0 00-1.5 0v3.18l-1.9-1.9A9 9 0 003.306 9.67a.75.75 0 101.45.388zm15.408 3.352a.75.75 0 00-.919.53 7.5 7.5 0 01-12.548 3.364l-1.902-1.903h3.183a.75.75 0 000-1.5H2.984a.75.75 0 00-.75.75v4.992a.75.75 0 001.5 0v-3.18l1.9 1.9a9 9 0 0015.059-4.035.75.75 0 00-.53-.918z" clip-rule="evenodd" />
    </svg>
    <span v-if="!loadingVerify">Verify</span>
  </button>
  <ul id="results" class="my-8 space-y-4 text-left text-gray-500 dark:text-gray-400" v-html="verifyResults">
  </ul>

  <Modal size="xl" v-if="openRunModal" @keydown.esc="openRunModal = false" @close="openRunModal = false">
    <template #header>
      <div class="flex items-center ">
        <svg class="h-6 w-6 text-green-600 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
          <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd" />
        </svg>

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
          <svg :class="{ 'animate-spin': loadingRun}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 mr-2 -ml-1">
            <path fill-rule="evenodd" d="M4.755 10.059a7.5 7.5 0 0112.548-3.364l1.903 1.903h-3.183a.75.75 0 100 1.5h4.992a.75.75 0 00.75-.75V4.356a.75.75 0 00-1.5 0v3.18l-1.9-1.9A9 9 0 003.306 9.67a.75.75 0 101.45.388zm15.408 3.352a.75.75 0 00-.919.53 7.5 7.5 0 01-12.548 3.364l-1.902-1.903h3.183a.75.75 0 000-1.5H2.984a.75.75 0 00-.75.75v4.992a.75.75 0 001.5 0v-3.18l1.9 1.9a9 9 0 0015.059-4.035.75.75 0 00-.53-.918z" clip-rule="evenodd" />
          </svg>
          Run again
        </button>
      </div>
    </template>
  </Modal>
</template>