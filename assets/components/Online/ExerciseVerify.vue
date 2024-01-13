<script setup>

import Modal from "./Modal.vue";
import {ArrowPathIcon, CommandLineIcon, SparklesIcon } from '@heroicons/vue/24/solid'
import toFilePath from "./Utils/toFilePath";
import RunResult from "./RunResult.vue";
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'
import Alert from "./Alert.vue";
import {ref} from "vue";

const emit = defineEmits(['verify-loading', 'verify-success', 'verify-fail', 'run-loaded']);
const props = defineProps({
  currentExercise: Object,
  files: Array,
  composerDeps: Array,
  entryPoint: String,
});

const loadingRun = ref(false);
const programRunResult = ref(null);
const openRunModal = ref(false);
const loadingVerify = ref(false);
const showRateLimitError = ref(false);
const rateLimitTimerId = ref(null);

const flattenFiles = (nodes, files = {}) => {
  nodes.forEach((node) => {
    if (!node.children) {
      files[toFilePath(node)] = node.content ?? '';
    } else {
      flattenFiles(node.children, files);
    }
  })
  return files;
}

const enableRateLimitError = () => {
  showRateLimitError.value = true;

  if (rateLimitTimerId.value) {
    clearInterval(rateLimitTimerId.value);
  }

  rateLimitTimerId.value = setTimeout(() => showRateLimitError.value = false, 3000);
}

const runSolution = async () => {
  if (loadingRun.value) {
    return;
  }

  loadingRun.value = true;
  const url = '/api/online/workshop/run/' + props.currentExercise.workshop.code + '/exercise/' + props.currentExercise.exercise.slug;

  const opts = {
    method: 'POST',
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      entry_point: props.entryPoint,
      scripts: flattenFiles(props.files),
      composer_deps: props.composerDeps
    })
  };

  const response = await fetch(url, opts);
  const data = await response.json();

  if (response.ok) {
    programRunResult.value = data;
    openRunModal.value = true;
    loadingRun.value = false;

    emit('run-loaded');
    return;
  }

  if (response.status === 429) {
    enableRateLimitError();
    return;
  }

  loadingRun.value = false;
}

const verifySolution = () => {
  if (loadingVerify.value) {
    return;
  }

  emit('verify-loading');
  loadingVerify.value = true;

  const url = '/api/online/workshop/verify/' + props.currentExercise.workshop.code + '/exercise/' + props.currentExercise.exercise.slug;

  const opts = {
    method: 'POST',
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      entry_point: props.entryPoint,
      scripts: flattenFiles(props.files),
      composer_deps: props.composerDeps
    })
  };
  fetch(url, opts)
      .then(response => {
        if (response.ok) {
          return response.json();
        }

        if (response.status === 429) {
          enableRateLimitError();
        }

        throw Error(response.statusText, response.json())
      })
      .then(json => {
        if (json.success === true) {
          emit('verify-success');
        } else {
          emit('verify-fail', json.results);
        }

        loadingVerify.value = false;
      })
      .catch((error, data) => {
        loadingVerify.value = false;
      })
}
</script>

<template>
  <alert type="error" @close="showRateLimitError = false" v-show="showRateLimitError" message="Too many requests. Please try again in a few minutes."></alert>
  <div class="flex items-center">
    <button id="run" class="border-[#E91E63] hover:bg-[#E91E63] border-solid border-2 text-white text-sm h-full flex items-center justify-center mt-0 mr-2 px-4 w-36 h-[48px] rounded" @click.stop="runSolution" :disabled="loadingRun">
      <ArrowPathIcon v-cloak v-show="loadingRun" class="w-4 h-4 animate-spin"/>
      <span v-if="!loadingRun">Run</span>
      <CommandLineIcon v-if="!loadingRun" v-cloak class="ml-2 w-5 h-5"/>
    </button>
    <button id="verify" class="flex items-center justify-center mt-0 px-4 w-36 text-white text-sm h-full rounded bg-gradient-to-r from-pink-600 to-purple-500 hover:bg-[#aa1145] transition-all duration-300 ease-in h-[48px]" @click="verifySolution" :disabled="loadingVerify">
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
          <h3 class="font-mono text-base font-semibold lg:text-xl text-white pt-0 mt-0 ">
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
                  <run-result :exercise="currentExercise.exercise" :run="run" class="mt-6"/>
                </TabPanel>
            </TabPanels>
          </TabGroup>

          <run-result v-else :exercise="currentExercise.exercise" :run="programRunResult.runs[0]"/>
        </div>

      </template>
      <template #footer>
        <div class="flex justify-end">
          <button type="button" class="inline-flex items-center w-full justify-center rounded-full border border-transparent bg-pink-600 px-8 py-2 text-base font-medium text-white shadow-sm hover:bg-pink-700 focus:outline-none focus:ring focus:ring-pink-800 sm:ml-3 sm:w-auto sm:text-sm" @click="runSolution">
            <ArrowPathIcon :class="{ 'animate-spin': loadingRun}" class="w-4 h-4 mr-2 -ml-1"/>
            Run again
          </button>
        </div>
      </template>
    </Modal>
  </Transition>
</template>
