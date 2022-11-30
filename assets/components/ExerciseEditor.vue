<script>

import PassNotification from "./PassNotification.vue";
import FileTree from "./FileTree.vue";
import ExerciseVerify from "./ExerciseVerify.vue";
import Tabs from "./Tabs.vue";
import Tab from "./Tab.vue";
import Modal from "./Modal.vue";
import AceEditor from "./AceEditor.vue";
import {editor} from "./stores/editor.js";
import { XMarkIcon } from '@heroicons/vue/24/solid'

export default {
  components: {
    PassNotification,
    FileTree,
    ExerciseVerify,
    Tabs,
    Tab,
    Modal,
    AceEditor,
    XMarkIcon
  },
  props: {
    nextExerciseLink: String,
    officialSolution: Array,
    workshop: Object,
    exerciseSlug: String,
  },
  data() {
    return {
      openPassNotification: false,
      openProblemModel: true,
      studentFiles: [
        {'name': 'solution.php'}
      ],
      openResults : false,
      results: '',
      editor,
    }
  },
  created() {
    this.editor.addFile('solution.php', '<' + '?php' + "\n");
  },
  methods: {
    studentSelectFile(file) {
      console.log(file);
    },
    dismissPassNotification() {
      this.openPassNotification = false;
    },
    resetResults() {
      this.results = '';
    },
    verifySuccess() {
      this.openPassNotification = true;
      this.openResults = false;
    },
    verifyFail(results) {
      this.results = results;
      this.openResults = true;
    }
  }
}
</script>

<template>
  <div class="h-full relative">
    <pass-notification
        v-show="openPassNotification"
        :next-exercise-link="nextExerciseLink"
        :official-solution="officialSolution"
        @close="dismissPassNotification">

    </pass-notification>

    <div class="h-full flex flex-col">
      <div class="flex flex-1">
        <div class="w-1/5 p-4">
          <file-tree
              :files="studentFiles"
              :file-select-function="studentSelectFile"
              :initial-selected-item="studentFiles[0]"
              show-controls/>
        </div>
        <div class="w-4/5 flex border-l border-solid border-gray-600 p-4" :class="[openResults ? 'w-3/5' : 'w-4/5']">
          <Tabs>
            <Tab title="solution.php">
              <div class="w-full border-0">
                <AceEditor @change="resetResults" class="w-full h-full border-0"/>
              </div>
            </Tab>
          </Tabs>
        </div>
        <div v-show="openResults" class="w-1/5 flex flex-col border-l border-solid border-gray-600 p-4">
            <div class="ml-8 flex justify-between items-center">
              <h1 class="text-2xl pt-0 ">Results</h1>
              <div>
                <button @click="openResults = false" type="button"
                        class="text-gray-400 bg-transparent rounded-lg text-sm p-1.5 ml-auto inline-flex items-center hover:bg-gray-600 hover:text-white">
                  <XMarkIcon class="w-5 h-5"/>
                </button>
              </div>
            </div>

            <ul id="results" class="my-8 space-y-4 text-left text-gray-500 dark:text-gray-400" v-html="results">
            </ul>
        </div>

      </div>
      <div class="border-t border-solid border-gray-600 h-16 flex-none flex items-center justify-between p-2">
        <nav class="flex" aria-label="Breadcrumb">
          <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
              <a href="/cloud" class="inline-flex items-center text-sm font-medium text-white hover:text-pink-500">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                {{ workshop.name }}
              </a>
            </li>
            <li>
              <div class="flex items-center">
                <svg class="w-6 h-6 text-pink-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                <a href="#" class="ml-1 text-sm font-medium text-white hover:text-pink-500 md:ml-2"> {{ exerciseSlug }}</a>
              </div>
            </li>
          </ol>
        </nav>
        <div class="flex">

          <button class="button flex items-center justify-center mt-0 mr-2 rounded px-4 w-36" @click="openProblemModel = true">
            <span>Show problem</span>
          </button>
          <exercise-verify @verify-loading="results = ''" @verify-fail="verifyFail" @verify-success="verifySuccess" :workshopCode='workshop.code'
                           :exercise-slug='exerciseSlug'>
          </exercise-verify>
        </div>
      </div>
    </div>

    <Modal size="4xl" max-height="max-h-[calc(5/6*100%)]" v-if="openProblemModel" @keydown.esc="openProblemModel = false" @close="openProblemModel = false">
      <template #header>
        <div class="flex items-center ">

          <h3 class="text-base font-semibold lg:text-xl text-white pt-0 mt-0 ">
            The problem...
          </h3>
        </div>

      </template>
      <template #body class="">
        <div id="problem-file" class="text-white">
          <slot name="problem"></slot>
        </div>
      </template>

      <template #footer>
        <div class="flex justify-end">
          <button @click="openProblemModel = false" type="button" class="inline-flex items-center w-full justify-center rounded-full border border-transparent bg-pink-600 px-8 py-2 text-base font-medium text-white shadow-sm hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
            Let's go!
          </button>
        </div>
      </template>
    </Modal>
  </div>
</template>