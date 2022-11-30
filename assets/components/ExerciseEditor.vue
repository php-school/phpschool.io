<script>

import PassNotification from "./PassNotification.vue";
import FileTree from "./FileTree.vue";
import ExerciseVerify from "./ExerciseVerify.vue";
import Tabs from "./Tabs.vue";
import Tab from "./Tab.vue";
import Modal from "./Modal.vue";
import AceEditor from "./AceEditor.vue";
import {results} from "./stores/results.js";
import {editor} from "./stores/editor.js";

export default {
  components: {
    PassNotification,
    FileTree,
    ExerciseVerify,
    Tabs,
    Tab,
    Modal,
    AceEditor
  },
  props: {
    nextExerciseLink: String,
    officialSolution: Array,
    workshopCode: String,
    exerciseSlug: String,
  },
  data() {
    return {
      openPassNotification: false,
      openProblemModel: true,
      studentFiles: [
        {'name': 'solution.php'}
      ],
      results,
      editor
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
      this.results.reset();
    }
  }
}
</script>

<template>
  <div>
    <pass-notification
        v-show="openPassNotification"
        :next-exercise-link="nextExerciseLink"
        :official-solution="officialSolution"
        @close="dismissPassNotification">

    </pass-notification>

    <div class="flex p-4 space-x-2">
      <div class="w-1/5">
        <file-tree
            :files="studentFiles"
            :file-select-function="studentSelectFile"
            :initial-selected-item="studentFiles[0]"
            show-controls/>
      </div>
      <div class="w-3/5 flex flex-col justify-center">
        <Tabs>
          <Tab title="solution.php">
            <div class="h-screen w-full border-0">
              <AceEditor @change="resetResults" class="h-screen w-full border-0"/>
            </div>
          </Tab>
        </Tabs>
      </div>
      <div class="w-1/5 flex flex-col ml-4">
        <exercise-verify @verify-success="openPassNotification = true" :workshopCode='workshopCode'
                         :exercise-slug='exerciseSlug'>
        </exercise-verify>
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