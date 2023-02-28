<script>

import Modal from "./Modal.vue";
import Tabs from "./Tabs.vue";
import {ArrowPathIcon, ExclamationTriangleIcon, CommandLineIcon, SparklesIcon, XMarkIcon, ChevronRightIcon} from '@heroicons/vue/24/solid'
import toFilePath from "./utils/toFilePath";
import RunResult from "./RunResult.vue";
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'


export default {
  components: {
    TabGroup,
    TabList,
    Tab,
    TabPanels,
    TabPanel,
    RunResult,
    Modal,
    Tabs,
    ArrowPathIcon,
    ExclamationTriangleIcon,
    CommandLineIcon,
    SparklesIcon,
    XMarkIcon,
    ChevronRightIcon
  },
  emits: ["verify-loading", "verify-success", "verify-fail", "run-loaded"],
  props: {
    currentExercise: Object,
    files: Array,
    composerDeps: Array,
    entryPoint: String,
  },
  data() {
    return {
      loadingRun: false,
      programRunResult: null,
      openRunModal: false,
      loadingVerify: false,
      showRateLimitError: false,
      rateLimitTimerId: null,
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

      if (this.rateLimitTimerId) {
        clearInterval(this.rateLimitTimerId);
      }

      this.rateLimitTimerId = setTimeout(() => this.showRateLimitError = false, 3000);
    },

    runSolution() {
      if (this.loadingRun) {
        return;
      }

      this.loadingRun = true;
      const url = '/cloud/workshop/' + this.currentExercise.workshop.code + '/exercise/' + this.currentExercise.exercise.slug + '/run';

      const opts = {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          entry_point: this.entryPoint,
          scripts: this.flattenFiles(this.files),
          composer_deps: this.composerDeps
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
            this.programRunResult = json;
            this.openRunModal = true;
            this.loadingRun = false;

            this.$emit('run-loaded');
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

      const url = '/cloud/workshop/' + this.currentExercise.workshop.code + '/exercise/' + this.currentExercise.exercise.slug + '/verify';

      const opts = {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          entry_point: this.entryPoint,
          scripts: this.flattenFiles(this.files),
          composer_deps: this.composerDeps
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
  <Transition enter-active-class="transition-opacity duration-100 ease-in" leave-active-class="transition-opacity duration-300 ease-in" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
    <div  v-show="showRateLimitError"  v-cloak class="absolute top-4 left-0 z-[51] shadow-lg w-full flex justify-center">
      <div class="mx-auto py-3 px-3 sm:px-6 lg:px-8  bg-gradient-to-r from-red-600 to-pink-700 rounded-lg">
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
  </Transition>
  <div class="flex items-center">
    <button id="run" class="border-[#E91E63] hover:bg-[#E91E63] border-solid border-2 text-white h-full flex items-center justify-center mt-0 mr-2 px-4 w-36 rounded" @click.stop="runSolution" :disabled="loadingRun">
      <ArrowPathIcon v-cloak v-show="loadingRun" class="w-4 h-4 animate-spin"/>
      <span v-if="!loadingRun">Run</span>
      <CommandLineIcon v-if="!loadingRun" v-cloak class="ml-2 w-5 h-5"/>
    </button>
    <button id="verify" class="button flex items-center justify-center mt-0 px-4 w-36 rounded bg-gradient-to-r from-pink-600 to-purple-500" @click="verifySolution" :disabled="loadingVerify">
      <ArrowPathIcon v-cloak v-show="loadingVerify" class="w-4 h-4 animate-spin"/>
      <span v-if="!loadingVerify">Verify</span>
      <SparklesIcon v-if="!loadingVerify" v-cloak class="ml-2 w-5 h-5"/>
    </button>
  </div>

  <Transition enter-active-class="transition-opacity duration-100 ease-in" leave-active-class="transition-opacity duration-200 ease-in" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
    <Modal id="run-modal" :scroll-content="true" size="3xl" max-height="max-h-[calc(5/6*100%)]" v-if="openRunModal" @close="openRunModal = false">
      <template #header>
        <div class="flex items-center ">
          <CommandLineIcon class="h-6 w-6 text-pink-500 mr-2"/>
          <h3 class="text-base font-semibold lg:text-xl text-white pt-0 mt-0 ">
            Program output
          </h3>
        </div>

      </template>
      <template #body>
        <div class="">
          <TabGroup v-if="programRunResult && programRunResult.runs.length > 1">
            <TabList className="flex justify-center border-b border-solid border-gray-600">
              <Tab as="template" v-slot="{ selected }" v-for="(run, i) in programRunResult.runs" >
                <button :class="{ 'border-b-2 border-pink-500 py-3 px-4 text-pink-400': selected, ' py-3 px-4 text-white': !selected }">
                  Run #{{ i + 1 }}
                </button>
              </Tab>
            </TabList>
            <TabPanels>
                <TabPanel v-for="(run, i) in programRunResult.runs">
                  <run-result :exercise="exercise" :run="run" class="mt-6"/>
                </TabPanel>
            </TabPanels>
          </TabGroup>

          <run-result v-else :exercise="exercise" :run="programRunResult.runs[0]"/>
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
  </Transition>
</template>
