<script>

import {toRaw} from "vue";
import Modal from "./Modal.vue";
import FileTree from "./FileTree.vue";
import AceEditor from "./AceEditor.vue";
import { TrophyIcon } from '@heroicons/vue/24/outline'
import { XMarkIcon } from '@heroicons/vue/24/solid'

export default {
  components: {
    Modal,
    FileTree,
    AceEditor,
    TrophyIcon,
    XMarkIcon
  },
  props: {
    nextExerciseLink: String,
    officialSolution: Array,
  },
  data() {
    return {
      hasOfficialSolution: this.officialSolution !== null,
      currentSolutionFile: this.officialSolution !== null ? this.officialSolution[0] : null,
      officialSolutionFileTree: this.officialSolution === null
          ? []
          : this.officialSolution.map((file, i) => {
            return {
              id: i,
              name: file.file_path
            }
          }),
      openOfficialSolutionModal: false,
      fileTreeStyles: {
        selectedFileClasses: 'bg-pink-500 rounded'
      }
    }
  },
  methods: {
    dismissPassNotification() {
      this.$emit('close');
    },
    showOfficialSolution() {
      this.openOfficialSolutionModal = true;
    },
    selectSolutionFile(file) {
      this.currentSolutionFile = this.officialSolution.find(elem => elem.file_path === file.name);
    },
    atob(file) {
      return atob(file);
    },
    isSelectedFile(file) {
      return this.currentSolutionFile && file === toRaw(this.currentSolutionFile)
    }
  },
}
</script>

<template>
  <div v-cloak id="pass-notification" class="bg-green-500">
    <div class="mx-auto py-3 px-3 sm:px-6 lg:px-8">
      <div class="flex flex-wrap items-center justify-center">
        <div class="flex items-center">
          <span class="flex rounded-lg bg-yellow-400 p-2">
            <TrophyIcon class="h-6 w-6 text-white"/>
          </span>
          <p class="ml-3 truncate font-medium text-white">
            <span class="md:hidden">Congratulations! your solution passed.</span>
            <span class="hidden md:inline">Congratulations! your solution passed.</span>
          </p>
        </div>
        <div v-if="hasOfficialSolution" class="order-3 mt-2 w-full flex-shrink-0 sm:order-2 sm:mt-0 sm:w-auto">
          <button @click="showOfficialSolution" class="flex items-center justify-center px-2 py-2 text-sm font-bold text-white underline">
            See official solution
          </button>
        </div>
        <div v-if="nextExerciseLink" class="order-3 mt-2 w-full flex-shrink-0 sm:order-2 sm:mt-0 sm:w-auto flex">
          <span v-if="hasOfficialSolution" class="flex items-center justify-center text-sm text-white">or</span>
          <a id="next-exercise-link" :href="nextExerciseLink" class="flex items-center justify-center px-2 py-2 text-sm font-bold text-white underline">
            Continue to next exercise &rarr;
          </a>
        </div>
        <div class="order-2 flex-shrink-0 sm:order-3 sm:ml-3">
          <button @click="dismissPassNotification" type="button" class="-mr-1 flex rounded-md p-2 hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-white sm:-mr-2">
            <span class="sr-only">Dismiss</span>
            <XMarkIcon class="h-6 w-6 text-white"/>
          </button>
        </div>
      </div>
    </div>
    <Modal size="3xl" v-if="openOfficialSolutionModal" @keydown.esc="openOfficialSolutionModal = false" @close="openOfficialSolutionModal = false">
      <template #header>
        <h3 class="text-base font-semibold lg:text-xl text-white">
          Official Solution
        </h3>
      </template>
      <template #body>
        <div class="flex space-x-3">
          <div class="w-1/3">
            <file-tree
                :files="officialSolutionFileTree"
                :file-select-function="selectSolutionFile"
                :initial-selected-item="officialSolutionFileTree[0]"
                :custom-styles="fileTreeStyles"/>

          </div>
          <div class="w-2/3">
            <template v-for="file in officialSolution" >
              <AceEditor v-show="isSelectedFile(file)"
                         :file_path="file.file_path"
                         :file_content="atob(file.content)"
                         readonly> </AceEditor>
            </template>

          </div>
        </div>
      </template>
    </Modal>
  </div>
</template>