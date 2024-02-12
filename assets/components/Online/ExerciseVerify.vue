<script setup>
import Modal from "./ModalDialog.vue";
import { ArrowPathIcon, CommandLineIcon, SparklesIcon } from "@heroicons/vue/24/solid";
import toFilePath from "./Utils/toFilePath";
import RunResult from "./RunResult.vue";
import { Menu, MenuButton, MenuItem, MenuItems, Tab, TabGroup, TabList, TabPanel, TabPanels } from "@headlessui/vue";
import Alert from "./SiteAlert.vue";
import { ref } from "vue";
import { ChevronDownIcon } from "@heroicons/vue/20/solid";

const emit = defineEmits(["verify-loading", "verify-success", "verify-fail", "run-loaded"]);
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
const currentAction = ref("verify");

const flattenFiles = (nodes, files = {}) => {
  nodes.forEach((node) => {
    if (!node.children) {
      files[toFilePath(node)] = node.content ?? "";
    } else {
      flattenFiles(node.children, files);
    }
  });
  return files;
};

const enableRateLimitError = () => {
  showRateLimitError.value = true;
};

const runSolution = async () => {
  currentAction.value = "run";

  if (loadingRun.value) {
    return;
  }

  loadingRun.value = true;
  const url = "/api/online/workshop/run/" + props.currentExercise.workshop.code + "/exercise/" + props.currentExercise.exercise.slug;

  const opts = {
    method: "POST",
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      entry_point: props.entryPoint,
      scripts: flattenFiles(props.files),
      composer_deps: props.composerDeps,
    }),
  };

  const response = await fetch(url, opts);

  if (response.status === 429) {
    loadingRun.value = false;
    enableRateLimitError();
    return;
  }

  if (response.ok) {
    programRunResult.value = await response.json();
    openRunModal.value = true;
    loadingRun.value = false;
    emit("run-loaded");
    return;
  }

  loadingRun.value = false;
};

const verifySolution = () => {
  currentAction.value = "verify";

  if (loadingVerify.value) {
    return;
  }

  emit("verify-loading");
  loadingVerify.value = true;

  const url = "/api/online/workshop/verify/" + props.currentExercise.workshop.code + "/exercise/" + props.currentExercise.exercise.slug;

  const opts = {
    method: "POST",
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      entry_point: props.entryPoint,
      scripts: flattenFiles(props.files),
      composer_deps: props.composerDeps,
    }),
  };
  fetch(url, opts)
    .then((response) => {
      if (response.ok) {
        return response.json();
      }

      if (response.status === 429) {
        enableRateLimitError();
        return;
      }

      throw Error(response.statusText, response.json());
    })
    .then((json) => {
      if (json.success === true) {
        emit("verify-success");
      } else {
        emit("verify-fail", json.results);
      }

      loadingVerify.value = false;
    })
    .catch(() => {
      loadingVerify.value = false;
    });
};
</script>

<template>
  <alert type="error" @close="showRateLimitError = false" :show="showRateLimitError" :timeout="4000" message="Too many requests. Please try again in a few minutes."></alert>
  <div class="flex h-[48px] flex-1 items-center gap-y-2">
    <button
      id="run"
      class="mr-2 mt-0 hidden h-[48px] w-44 items-center justify-center rounded border-2 border-solid border-[#E91E63] px-4 text-sm text-white hover:bg-[#E91E63] md:flex"
      @click.stop="runSolution"
      :disabled="loadingRun"
    >
      <ArrowPathIcon v-cloak v-show="loadingRun" class="h-4 w-4 animate-spin" />
      <span v-if="!loadingRun">Run</span>
      <CommandLineIcon v-if="!loadingRun" v-cloak class="ml-2 h-5 w-5" />
    </button>

    <div class="flex h-[48px] w-full justify-between rounded bg-gradient-to-r from-pink-600 to-purple-500 md:w-44 md:justify-center">
      <button class="mr-0 mt-0 flex h-[48px] w-full items-center pl-3 text-sm text-white outline-none transition-all duration-300 ease-in">
        <span v-if="currentAction === 'verify'" class="flex flex-1 items-center md:justify-center" @click="verifySolution">
          <ArrowPathIcon v-cloak v-show="loadingVerify" class="hidden h-4 w-4 animate-spin md:flex" />
          <span :class="{ 'md:hidden': loadingVerify }">Verify</span>
          <SparklesIcon :class="{ 'md:hidden': loadingVerify }" v-cloak class="ml-2 h-5 w-5" />
        </span>

        <span v-if="currentAction === 'run'" class="flex flex-1 items-center md:justify-center" @click="runSolution">
          <ArrowPathIcon v-cloak v-show="loadingRun" class="hidden h-4 w-4 animate-spin md:flex" />
          <span>Run</span>
          <CommandLineIcon v-cloak class="ml-2 h-5 w-5" />
        </span>
      </button>
      <Menu as="div" class="relative pr-3 text-xs uppercase text-white" v-slot="{ open }">
        <div class="group">
          <MenuButton class="h-[48px]" :disabled="loadingVerify || loadingRun">
            <ChevronDownIcon
              v-if="!loadingRun && !loadingVerify"
              :class="open ? '' : 'rotate-180'"
              class="ml-2 flex h-5 w-5 transition duration-200 duration-300 focus:outline-none md:hidden"
              aria-hidden="true"
            />
            <ArrowPathIcon v-cloak v-show="loadingVerify || loadingRun" class="ml-2 flex h-4 w-4 animate-spin md:hidden" />
          </MenuButton>
        </div>

        <transition
          enter-active-class="transition ease-out duration-100"
          enter-from-class="transform opacity-0 scale-95"
          enter-to-class="transform opacity-100 scale-100"
          leave-active-class="transition ease-in duration-75"
          leave-from-class="transform opacity-100 scale-100"
          leave-to-class="transform opacity-0 scale-95"
        >
          <MenuItems class="absolute -right-3 -top-2 z-40 w-[168px] origin-top-right -translate-y-full rounded-md bg-gradient-to-r from-pink-600 to-purple-500 px-3 text-left">
            <MenuItem v-if="currentAction === 'verify'">
              <button class="flex h-[48px] w-full flex-1 items-center justify-start rounded px-4 text-sm text-white" @click.stop="runSolution" :disabled="loadingRun">
                <span>Run</span>
                <CommandLineIcon class="ml-2 h-5 w-5" />
              </button>
            </MenuItem>
            <MenuItem v-if="currentAction === 'run'">
              <button
                v-if="currentAction === 'run'"
                class="flex h-[48px] w-full flex-1 items-center justify-start rounded px-4 text-sm text-white"
                @click.stop="verifySolution"
                :disabled="loadingVerify"
              >
                <span>Verify</span>
                <SparklesIcon class="ml-2 h-5 w-5" />
              </button>
            </MenuItem>
          </MenuItems>
        </transition>
      </Menu>
    </div>
  </div>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-opacity duration-100 ease-in"
      leave-active-class="transition-opacity duration-200 ease-in"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <Modal id="run-modal" :scroll-content="true" size="3xl" max-height="max-h-[calc(5/6*100%)]" v-if="openRunModal" @close="openRunModal = false">
        <template #header>
          <div class="flex items-center">
            <CommandLineIcon class="mr-2 h-6 w-6 text-pink-500" />
            <h3 class="mt-0 pt-0 font-mono text-base font-semibold text-white lg:text-xl">Program output</h3>
          </div>
        </template>
        <template #body>
          <div class="">
            <template v-if="programRunResult && programRunResult.success === false">
              <h2 class="mb-2 pt-0 font-mono text-lg text-[#E91E63]">Your program could not be executed, there was a syntax error</h2>
              <pre class="mb-4 whitespace-pre-wrap rounded-none border-none p-0"><code class="language-shell hljs shell  block rounded-lg p-4 text-sm">{{programRunResult.failure.reason}}</code></pre>
            </template>

            <TabGroup v-else-if="programRunResult && programRunResult.runs.length > 1">
              <TabList className="flex justify-center border-b border-solid border-gray-600">
                <Tab as="template" v-slot="{ selected }" v-for="(run, i) in programRunResult.runs" :key="i">
                  <button
                    :class="{
                      'border-b-2 border-pink-500 px-4 py-3 text-pink-400': selected,
                      ' px-4 py-3 text-white': !selected,
                    }"
                  >
                    Run #{{ i + 1 }}
                  </button>
                </Tab>
              </TabList>
              <TabPanels>
                <TabPanel v-for="(run, i) in programRunResult.runs" :key="i">
                  <run-result :exercise="currentExercise.exercise" :run="run" class="mt-6" />
                </TabPanel>
              </TabPanels>
            </TabGroup>

            <run-result v-else :exercise="currentExercise.exercise" :run="programRunResult.runs[0]" />
          </div>
        </template>
        <template #footer>
          <div class="flex justify-end">
            <button
              type="button"
              v-show="programRunResult.success === true"
              class="inline-flex w-full items-center justify-center rounded-full border border-transparent bg-pink-600 px-8 py-2 text-base font-medium text-white shadow-sm hover:bg-pink-700 focus:outline-none focus:ring focus:ring-pink-800 sm:ml-3 sm:w-auto sm:text-sm"
              @click="runSolution"
            >
              <ArrowPathIcon :class="{ 'animate-spin': loadingRun }" class="-ml-1 mr-2 h-4 w-4" />
              Run again
            </button>
          </div>
        </template>
      </Modal>
    </Transition>
  </Teleport>
</template>
