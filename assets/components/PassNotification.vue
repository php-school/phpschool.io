<script>

import {toRaw} from "vue";
import Modal from "./Modal.vue";
import FileTree from "./FileTree.vue";
import AceEditor from "./AceEditor.vue";

export default {
  components: {
    Modal,
    FileTree,
    AceEditor
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
                          <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0" />
                          </svg>
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
            <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
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