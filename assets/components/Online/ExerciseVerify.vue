<script setup>
import Modal from "./ModalDialog.vue";
import { ArrowPathIcon, CommandLineIcon, SparklesIcon } from "@heroicons/vue/24/solid";
import { flatten } from "./Utils/studentFiles";
import RunResult from "./RunResult.vue";
import { Menu, MenuButton, MenuItem, MenuItems, Tab, TabGroup, TabList, TabPanel, TabPanels } from "@headlessui/vue";
import Alert from "./SiteAlert.vue";
import { ref } from "vue";
import { ChevronDownIcon } from "@heroicons/vue/20/solid";

const emit = defineEmits(["verify-loading", "verify-success", "verify-fail", "verify-error", "run-loaded"]);
const props = defineProps({
  currentExercise: Object,
  files: Array,
  composerDeps: Array,
  entryPoint: String,
});

const openRunModal = ref(false);
const errorMessage = ref("");
const showError = ref(false);
const currentAction = ref("verify");

const stageMessages = { "reference-execute": "Running our solution", "student-execute": "Running your solution" };
// "run" or "verify" while one is in progress, and what it is doing
const loading = ref(null);
const stage = ref("");

// Posts the solution to the run or verify endpoint. It answers with a line per stage while it works, eg
// {"stage":"student-execute"}, then the result. Returns the result, or null after showing an error.
const submit = async (action) => {
  loading.value = action;
  // until the first stage the solution is uploading, and composer installs any packages
  stage.value = props.composerDeps?.length ? "Installing dependencies" : "Starting";

  try {
    const response = await fetch(`/api/online/workshop/${action}/${props.currentExercise.workshop.code}/exercise/${props.currentExercise.exercise.slug}`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ entry_point: props.entryPoint, scripts: flatten(props.files), composer_deps: props.composerDeps }),
    });

    if (!response.ok) {
      throw { 429: "Too many requests. Please try again in a few minutes.", 503: "Lots of people are running code right now. Please try again in a moment." }[response.status];
    }

    const reader = response.body.pipeThrough(new TextDecoderStream()).getReader();
    let text = "";
    for (let chunk = await reader.read(); !chunk.done; chunk = await reader.read()) {
      text += chunk.value;
      // the latest stage is the last line with a new line after it
      const lines = text.split("\n");
      if (lines.length > 1) {
        stage.value = stageMessages[JSON.parse(lines.at(-2)).stage];
      }
    }

    // the result is the last line, or an error if something failed after the stages were sent
    const result = JSON.parse(text.split("\n").at(-1));
    if (result.error) {
      throw result.error;
    }

    return result;
  } catch (e) {
    // a message thrown above, or eg. a network error
    errorMessage.value = typeof e === "string" ? e : "Something went wrong. Please try again.";
    showError.value = true;

    return null;
  } finally {
    loading.value = null;
  }
};

// the last successful run; a failed re-run keeps showing it
const programRunResult = ref(null);

const runSolution = async () => {
  if (loading.value) {
    return;
  }

  const result = await submit("run");

  if (result) {
    programRunResult.value = result;
    openRunModal.value = true;
    emit("run-loaded");
  }
};

const verifySolution = async () => {
  if (loading.value) {
    return;
  }

  emit("verify-loading");
  const result = await submit("verify");

  if (!result) {
    emit("verify-error");
  } else if (result.success === true) {
    emit("verify-success");
  } else {
    emit("verify-fail", result.results);
  }
};
</script>

<template>
  <alert type="error" @close="showError = false" :show="showError" :timeout="4000" :message="errorMessage"></alert>
  <div class="flex h-[48px] flex-1 items-center gap-y-2 md:flex-none">
    <button
      id="run"
      class="mt-0 mr-2 hidden h-[48px] w-44 items-center justify-center rounded-sm border-2 border-solid border-[#E91E63] px-4 text-sm text-white hover:bg-[#E91E63] md:flex"
      @click.stop="runSolution"
      :disabled="loading === 'run'"
    >
      <ArrowPathIcon v-cloak v-show="loading === 'run'" class="h-4 w-4 animate-spin" />
      <span v-if="loading === 'run'" class="ml-2 truncate text-xs">{{ stage }}</span>
      <span v-else>Run</span>
      <CommandLineIcon v-if="loading !== 'run'" v-cloak class="ml-2 h-5 w-5" />
    </button>

    <div class="flex h-[48px] w-full justify-between rounded-sm bg-linear-to-r/srgb from-pink-600 to-purple-500 md:w-44 md:justify-center">
      <button class="mt-0 mr-0 flex h-[48px] w-full items-center pl-3 text-sm text-white outline-hidden transition-all duration-300 ease-in">
        <span v-if="currentAction === 'verify'" class="flex flex-1 items-center md:justify-center" @click="verifySolution">
          <ArrowPathIcon v-cloak v-show="loading === 'verify'" class="hidden h-4 w-4 animate-spin md:flex" />
          <span v-if="loading === 'verify'" class="ml-2 truncate text-xs">{{ stage }}</span>
          <span v-else>Verify</span>
          <SparklesIcon :class="{ 'md:hidden': loading === 'verify' }" v-cloak class="ml-2 h-5 w-5" />
        </span>

        <span v-if="currentAction === 'run'" class="flex flex-1 items-center md:justify-center" @click="runSolution">
          <ArrowPathIcon v-cloak v-show="loading === 'run'" class="hidden h-4 w-4 animate-spin md:flex" />
          <span v-if="loading === 'run'" class="ml-2 truncate text-xs">{{ stage }}</span>
          <span v-else>Run</span>
          <CommandLineIcon :class="{ 'md:hidden': loading === 'run' }" v-cloak class="ml-2 h-5 w-5" />
        </span>
      </button>
      <Menu as="div" class="relative mr-3 text-xs text-white uppercase" v-slot="{ open }">
        <div class="group">
          <MenuButton class="h-[48px]" :disabled="loading">
            <ChevronDownIcon v-if="!loading" :class="open ? '' : 'rotate-180'" class="ml-2 flex h-5 w-5 transition duration-200 duration-300 focus:outline-hidden md:hidden" aria-hidden="true" />
            <ArrowPathIcon v-cloak v-show="loading" class="ml-2 flex h-4 w-4 animate-spin md:hidden" />
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
          <MenuItems class="absolute -top-2 -right-3 z-40 w-[168px] origin-top-right -translate-y-full rounded-md bg-linear-to-r/srgb from-pink-600 to-purple-500 px-3 text-left">
            <MenuItem v-if="currentAction === 'verify'">
              <button
                @click.stop="
                  currentAction = 'run';
                  runSolution();
                "
                class="flex h-[48px] w-full flex-1 items-center justify-start rounded-sm px-4 text-sm text-white"
                :disabled="loading === 'run'"
              >
                <span>Run</span>
                <CommandLineIcon class="ml-2 h-5 w-5" />
              </button>
            </MenuItem>
            <MenuItem v-if="currentAction === 'run'">
              <button
                @click.stop="
                  currentAction = 'verify';
                  verifySolution();
                "
                class="flex h-[48px] w-full flex-1 items-center justify-start rounded-sm px-4 text-sm text-white"
                :disabled="loading === 'verify'"
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
      <Modal id="run-modal" :scroll-content="true" size="3xl" max-height="max-h-5/6" v-if="openRunModal" @close="openRunModal = false">
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
              <pre class="mb-4 rounded-none border-none p-0 whitespace-pre-wrap"><code class="language-shell hljs shell  block rounded-lg p-4 text-sm">{{programRunResult.failure.reason}}</code></pre>
            </template>

            <TabGroup v-else-if="programRunResult && programRunResult.runs.length > 1">
              <TabList className="flex justify-center border-b border-solid border-gray-600">
                <Tab as="template" v-slot="{ selected }" v-for="(run, i) in programRunResult.runs" :key="i">
                  <button
                    :class="{
                      'border-b-2 border-pink-500 px-4 py-3 text-pink-400': selected,
                      'px-4 py-3 text-white': !selected,
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
              class="inline-flex w-full items-center justify-center rounded-full border border-transparent bg-pink-600 px-8 py-2 text-base font-medium text-white shadow-xs hover:bg-pink-700 focus:ring-3 focus:ring-pink-800 focus:outline-hidden sm:ml-3 sm:w-auto sm:text-sm"
              @click="runSolution"
            >
              <ArrowPathIcon :class="{ 'animate-spin': loading === 'run' }" class="mr-2 -ml-1 h-4 w-4" />
              Run again
            </button>
          </div>
        </template>
      </Modal>
    </Transition>
  </Teleport>
</template>
