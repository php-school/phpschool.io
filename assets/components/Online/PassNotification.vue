<script setup>

import {computed, onMounted, onUnmounted, ref} from "vue";
import Modal from "./Modal.vue";
import FileTree from "./FileTree.vue";
import AceEditor from "./AceEditor.vue";
import { TrophyIcon } from '@heroicons/vue/24/outline'
import { XMarkIcon } from '@heroicons/vue/24/solid'
import { CodeBracketSquareIcon } from '@heroicons/vue/24/solid'

const props = defineProps({
  nextExerciseLink: String,
  officialSolution: Array,
});

const emit = defineEmits(['close']);

// this is an absolute hack to make sure the solution has at least 10 lines
// so the width of the ace editor gutter is consistent for each file in the solution
const padStringToLines = (inputString, desiredLines) => {
  return inputString + '\n'.repeat(Math.max(0, desiredLines - inputString.split('\n').length));
}

const el = ref(null);

onMounted(() => {
  el.escapeEventHandler = (evt) => {
    if (evt.code === 'Escape') {

      if (openOfficialSolutionModal.value) {
        return;
      }

      dismissPassNotification();
    }
  };

  document.addEventListener('keyup', el.escapeEventHandler, true);
});

onUnmounted(() => {
  document.removeEventListener('keyup', el.escapeEventHandler);
});

const hasOfficialSolution = props.officialSolution !== null;
const currentSolutionFile = ref(props.officialSolution !== null ? props.officialSolution[0] : null);
const officialSolutionFileTree = ref(props.officialSolution === null
    ? []
    : props.officialSolution.map((file, i) => {
      return {
        id: i,
        name: file.file_path
      }
    }));

const fileTreeStyles = {
  selectedFileClasses: 'bg-pink-500'
}

const openOfficialSolutionModal = ref(false);

const files = computed(() => {
  const files = props.officialSolution.map(file => ({
    name: file.file_path,
    content: atob(file.content)
  }));

  const maxLen = Math.max(...files.map(file => file.content.split('\n').length));

  return files.map(file => ({
      name: file.name,
      content: padStringToLines(file.content, maxLen)
  }));
});

const dismissPassNotification = () => {
  emit('close');
}

const showOfficialSolution = () => {
  openOfficialSolutionModal.value = true;
}

const selectSolutionFile = (file) => {
  currentSolutionFile.value = props.officialSolution.find(elem => elem.file_path === file.name);
}

const isSelectedFile = (file) => {
  return currentSolutionFile.value && file.name === currentSolutionFile.value.file_path
}
</script>

<template>
  <div ref="el">
    <div v-cloak class="bg-gray-900 bg-opacity-70 fixed inset-0 z-40"/>
    <div v-click-away="dismissPassNotification" v-cloak id="pass-notification" class="absolute top-4 z-40 shadow-lg w-full flex justify-center">

      <div class="mx-auto py-3 px-3 sm:px-6 lg:px-8  bg-gradient-to-r from-pink-500 to-purple-500  rounded-lg">
        <div class="flex flex-wrap items-center justify-center">
          <div class="flex items-center">
          <span class="flex rounded-lg  p-2">
            <TrophyIcon class="h-6 w-6 text-yellow-400 "/>
          </span>
            <p class="ml-3 truncate font-medium text-white">
              <span class="md:hidden">Congratulations! your solution passed.</span>
              <span class="hidden md:inline">Congratulations! your solution passed.</span>
            </p>
          </div>
          <div v-if="hasOfficialSolution" class="order-3 mt-2 w-full flex-shrink-0 sm:order-2 sm:mt-0 sm:w-auto">
            <button @click="showOfficialSolution" class="flex items-center justify-center px-2 py-2 text-sm font-bold text-white underline">
              See the official solution
            </button>
          </div>
          <div v-if="nextExerciseLink" class="order-3 mt-2 w-full flex-shrink-0 sm:order-2 sm:mt-0 sm:w-auto flex">
            <span v-if="hasOfficialSolution" class="flex items-center justify-center text-sm text-white">or</span>
            <router-link id="next-exercise-link" :to="nextExerciseLink" class="flex items-center justify-center px-2 py-2 text-sm font-bold text-white underline">
              Continue to the next exercise &rarr;
            </router-link>
          </div>
          <div class="order-2 flex-shrink-0 sm:order-3 sm:ml-3">
            <button @click="dismissPassNotification" type="button" class="-mr-1 flex rounded-md p-2 hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-white sm:-mr-2">
              <span class="sr-only">Dismiss</span>
              <XMarkIcon class="h-6 w-6 text-white"/>
            </button>
          </div>
        </div>
      </div>
      <Modal size="4xl" v-if="openOfficialSolutionModal" @close="openOfficialSolutionModal = false" body-classes="p-0">
        <template #header>
          <div class="flex flex-col">
            <div class="flex items-center ">
              <CodeBracketSquareIcon class="h-10 w-10 text-pink-500 mr-2" />
              <h3 class="text-base font-semibold lg:text-xl text-white pt-0 mt-0">Official Solution</h3>
            </div>
          </div>
        </template>
        <template #body>
          <div class="flex">
            <div class="w-1/3 bg-gray-900 border-solid border-r border-r-gray-600">
              <file-tree
                  :files="officialSolutionFileTree"
                  :file-select-function="selectSolutionFile"
                  :initial-selected-item="officialSolutionFileTree[0]"
                  :custom-styles="fileTreeStyles"/>

            </div>
            <div class="w-2/3">
              <template v-for="file in files" >
                <AceEditor v-show="isSelectedFile(file)"
                           v-model:value="file.content"
                           :min-lines="20"
                           :max-lines="20"
                           readonly> </AceEditor>
              </template>

            </div>
          </div>
        </template>
      </Modal>
    </div>
  </div>
</template>