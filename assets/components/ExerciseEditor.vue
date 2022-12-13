<script>

import PassNotification from "./PassNotification.vue";
import FileTree from "./FileTree.vue";
import ExerciseVerify from "./ExerciseVerify.vue";
import Tabs from "./Tabs.vue";
import Modal from "./Modal.vue";
import AceEditor from "./AceEditor.vue";
import {editor} from "./stores/editor.js";
import {XMarkIcon, ArrowPathIcon, CircleStackIcon, MapIcon} from '@heroicons/vue/24/solid';
import PackageSearch from './PackageSearch.vue';

export default {
  components: {
    PassNotification,
    FileTree,
    ExerciseVerify,
    Tabs,
    Modal,
    AceEditor,
    XMarkIcon,
    ArrowPathIcon,
    CircleStackIcon,
    MapIcon,
    PackageSearch
  },
  props: {
    nextExerciseLink: String,
    officialSolution: Array,
    workshop: Object,
    exercise: Object,
  },
  data() {
    const studentFiles = this.toTree([
      {
        'name': 'solution.php',
        'content': "<?php\n",
        isNew() {
          return false;
        }
      }
    ]);

    return {
      openPassNotification: false,
      openProblemModal: true,
      openComposerModal: false,
      studentFiles: studentFiles,
      openResults : false,
      results: '',
      loadingResults: false,
      editor,
      openFiles: [studentFiles[0]],
      activeTab: 0,
      newDependency: '',
      composerDeps: [],
      loadingComposerAdd: false
    }
  },
  methods: {
    studentSelectFile(selectedFile) {
      if ('new' in selectedFile && selectedFile.new === true) {
        return;
      }

      const found = this.openFiles.find(file => file === selectedFile);

      if (!found) {
        //10 files open max
        if (this.openFiles.length === 10) {
          this.openFiles.shift();
        }

        this.activeTab = this.openFiles.push(selectedFile) - 1;
      }
    },
    dismissPassNotification() {
      this.openPassNotification = false;
    },
    resetResults() {
      this.results = '';
    },
    verifyLoading() {
      this.resetResults();
      this.loadingResults = true;
    },
    verifySuccess() {
      this.openPassNotification = true;
      this.openResults = false;
      this.loadingResults = false;
    },
    verifyFail(results) {
      this.results = results;
      this.openResults = true;
      this.loadingResults = false;
    },
    closeTab(tab) {
      const index = this.openFiles.findIndex(file => file === tab);

      this.openFiles.splice(index, 1);
    },
    toTree(files, parent = null) {
      return files.map((file) => {
        file.parent = parent;

        if (file.children) {
          file.children = this.toTree(file.children, file);
        }

        return file;
      })
    },
    packageSelected(composerPackage) {
      this.newDependency = composerPackage;
    },
    addDependency() {
      this.loadingComposerAdd = true;
      const index = this.composerDeps.find(p => p.name === this.newDependency);

      if (index) {
        this.newDependency = '';
        return;
      }

      const opts = {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        }
      };
      fetch('/cloud/composer-package/add?package=' + encodeURIComponent(this.newDependency), opts)
          .then(response => response.json())
          .then(json => {
            this.composerDeps.push({name: this.newDependency, version: json.latest_version, versions: json.versions});
            this.newDependency = '';
            this.loadingComposerAdd = false;
            this.$refs.packageSearch.reset();
          })
          .catch((error) => {
            this.loadingComposerAdd = false;
            this.$refs.packageSearch.reset();
          })
    },
    removeDependency(packageName) {
      const index = this.composerDeps.find(p => p.name === packageName);

      if (index) {
        this.composerDeps.splice(index, 1);
      }
    },
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
          <Tabs :tabList="openFiles.map(file => file.name)" @close-tab="closeTab" :active-tab="activeTab">
            <template v-slot:[`tab-content-`+index] v-for="(file, index) in openFiles">
              <AceEditor :file="file" @change="" class="w-full h-full border-0"/>
            </template>
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

            <div v-show="loadingResults" class="animate-pulse flex space-x-4 mt-4">
              <div class="flex-1 space-y-6 py-1">
                <div class="h-2 bg-slate-700 rounded"></div>
                <div class="space-y-3">
                  <div class="grid grid-cols-3 gap-4">
                    <div class="h-2 bg-slate-700 rounded col-span-2"></div>
                    <div class="h-2 bg-slate-700 rounded col-span-1"></div>
                  </div>
                  <div class="h-2 bg-slate-700 rounded"></div>
                </div>
                <div class="h-2 bg-slate-700 rounded"></div>
                <div class="space-y-3">
                  <div class="grid grid-cols-3 gap-4">
                    <div class="h-2 bg-slate-700 rounded col-span-1"></div>
                    <div class="h-2 bg-slate-700 rounded col-span-2"></div>
                  </div>
                  <div class="h-2 bg-slate-700 rounded"></div>
                </div>
              </div>
            </div>

            <ul v-show="results" id="results" class="my-8 space-y-4 text-left text-gray-500 dark:text-gray-400" v-html="results">

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
                <a href="#" class="ml-1 text-sm font-medium text-white hover:text-pink-500 md:ml-2"> {{ exercise.name }}</a>
              </div>
            </li>
          </ol>
        </nav>
        <div class="flex">
          <button class="border-[#E91E63] hover:bg-[#E91E63] border-solid border-2 text-white flex items-center justify-center mt-0 mr-2 rounded px-4 w-44" @click="openComposerModal = true">
            <span>Composer deps</span>
            <CircleStackIcon v-cloak class="ml-2 w-5 h-5" />
          </button>
          <button class="border-[#E91E63] hover:bg-[#E91E63] border-solid border-2 text-white flex items-center justify-center mt-0 mr-2 rounded px-4 w-44" @click="openProblemModal = true">
            <span>Show problem</span>
            <MapIcon v-cloak class="ml-2 w-5 h-5" />
          </button>
          <exercise-verify @verify-loading="verifyLoading"
                           @verify-fail="verifyFail"
                           @verify-success="verifySuccess"
                           :workshopCode='workshop.code'
                           :exercise-slug='exercise.slug'
                           :files="studentFiles"
                           :composer-deps="composerDeps" />
        </div>
      </div>
    </div>

    <Modal size="sm" max-height="max-h-[calc(1/2*100%)]" v-if="openComposerModal" @close="openComposerModal = false">
      <template #header>
        <div class="flex items-center ">
          <CircleStackIcon class="h-6 w-6 text-pink-500 mr-2"/>
          <h3 class="text-base font-semibold lg:text-xl text-white pt-0 mt-0 ">
            Composer Dependencies
          </h3>
        </div>

      </template>
      <template #body>
        <div class="flex justify-between items-center">
          <package-search ref="packageSearch" @package-selected="packageSelected" v-model="newDependency" class="w-full"></package-search>
          <button :disabled="newDependency === ''" @click.stop="addDependency" type="button" class="inline-flex items-center h-9 justify-center rounded-full border border-transparent w-16 bg-pink-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 sm:ml-3 sm:text-sm disabled:opacity-70 disabled:hover:bg-pink-600">
            <ArrowPathIcon v-cloak v-show="loadingComposerAdd" class="w-4 h-4 animate-spin"/>
            <span v-if="!loadingComposerAdd">Add</span>
          </button>
        </div>
        <ul v-show="composerDeps.length > 0" class="mt-4 overflow-y-scroll ">
          <li v-for="dep in composerDeps" class="text-white pl-2 mb-2 flex items-center">
            <p class="text-base">{{ dep.name }}</p>
            <p class="bg-gray-900 ml-2 px-2 py-1 rounded">{{ dep.version }}</p>
            <XMarkIcon @click.stop="removeDependency(dep.name)" class="cursor-pointer ml-2 w-5 h-5 text-zinc-400 hover:text-pink-600"  />
          </li>
        </ul>
        <div v-show="composerDeps.length === 0" class="pt-6" >
          <p class="text-white ">You currently have no dependencies.</p>
        </div>
      </template>

      <template #footer>
        <div class="flex justify-end">
          <button @click="openComposerModal = false" type="button" class="inline-flex items-center w-full justify-center rounded-full border border-transparent bg-pink-600 px-8 py-2 text-base font-medium text-white shadow-sm hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
            Close
          </button>
        </div>
      </template>
    </Modal>

    <Modal :scroll-content="true" size="4xl" max-height="max-h-[calc(5/6*100%)]" v-if="openProblemModal" @close="openProblemModal = false">
      <template #header>
        <div class="flex items-center ">
          <MapIcon class="h-6 w-6 text-pink-500 mr-2"/>
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
          <button @click="openProblemModal = false" type="button" class="inline-flex items-center w-full justify-center rounded-full border border-transparent bg-pink-600 px-8 py-2 text-base font-medium text-white shadow-sm hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
            Let's go!
          </button>
        </div>
      </template>
    </Modal>
  </div>
</template>

<style>
#problem-file p {
  @apply mt-2 mb-4 leading-loose tracking-wide;
}

#problem-file h2 {
  @apply mt-2 mb-4 text-2xl;
}

#problem-file h3 {
  color: #E91E63;
  @apply mt-2 mb-4 text-xl;
}

#problem-file a {
  color: #E91E63;
}

#problem-file pre {
  background-color: initial;
  @apply mt-2 mb-4 p-0 border-none rounded-none;
}
#problem-file pre code {
  background-color: #2a2c2d !important;
  @apply p-4 rounded-lg;
}

#problem-file :not(pre)>code {
  font-size: 90%;
  background-color: #2a2c2d;
  @apply px-2 py-1 rounded;
  color: #ff75b5;
}

#problem-file ul {
  @apply mt-2 mb-4 list-disc list-inside;
}

#problem-file ul li {
  @apply list-item p-1;
}
</style>