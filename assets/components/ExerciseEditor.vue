<script>

import PassNotification from "./PassNotification.vue";
import FileTree from "./FileTree.vue";
import ExerciseVerify from "./ExerciseVerify.vue";
import Tabs from "./Tabs.vue";
import Tab from "./Tab.vue";

export default {
  components: {
    PassNotification,
    FileTree,
    ExerciseVerify,
    Tabs,
    Tab
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
      studentFiles: [
        {'name': 'solution.php'}
      ]
    }
  },
  methods: {
    studentSelectFile(file) {
      console.log(file);
    },
    dismissPassNotification() {
      this.openPassNotification = false;
    },
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
          <Tab title="Problem.md">
              <pre id="editor-problem" class="h-screen w-full border-0">
              </pre>
          </Tab>
          <Tab title="solution.php">
              <pre id="editor" class="h-screen w-full border-0">
              </pre>
          </Tab>
        </Tabs>
      </div>
      <div class="w-1/5 flex flex-col ml-4">
        <exercise-verify @verify-success="openPassNotification = true" :workshopCode='workshopCode'
                         :exercise-slug='exerciseSlug'>
        </exercise-verify>
      </div>
    </div>
  </div>
</template>