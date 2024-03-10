<script setup>
import PassNotification from "./PassNotification.vue";
import FileTree from "./FileTree.vue";
import ExerciseVerify from "./ExerciseVerify.vue";
import EditorTabs from "./EditorTabs.vue";
import AceEditor from "./AceEditor.vue";
import { XMarkIcon, CircleStackIcon, MapIcon, AcademicCapIcon } from "@heroicons/vue/24/solid";
import ResultList from "./Results/ResultList.vue";
import Tour from "./EditorTour.vue";
import Confirm from "./ConfirmDialog.vue";
import Problem from "./ProblemFile.vue";
import ComposerPackages from "./ComposerPackages.vue";
import ProgressBar from "./ProgressBar.vue";
import EditorBreadcrumbs from "./EditorBreadcrumbs.vue";
import { onMounted, ref } from "vue";
import toFilePath from "./Utils/toFilePath";
import { useWorkshopStore } from "../../stores/workshops";
import { useStudentStore } from "../../stores/student";
import { FolderIcon } from "@heroicons/vue/24/outline";
import { vOnClickOutside } from "@vueuse/components";
import { TransitionChild, TransitionRoot } from "@headlessui/vue";

const workshopStore = useWorkshopStore();
const studentStore = useStudentStore();

const props = defineProps({
  workshop: String,
  exercise: String,
  nextExerciseLink: String,
});

const currentExercise = {
  workshop: workshopStore.getWorkshop(props.workshop),
  exercise: workshopStore.getExercise(props.workshop, props.exercise),
};

const entryPoint = ref("solution.php");
const initialFiles = ref([]);
const officialSolution = ref([]);
const firstRunLoaded = ref(false);
const firstVerifyLoaded = ref(false);
const openPassNotification = ref(false);
const openProblemModal = ref(true);
const openComposerModal = ref(false);
const studentFiles = ref([]);
const openResults = ref(false);
const results = ref([]);
const loadingResults = ref(false);
const openFiles = ref([]);
const activeTab = ref(0);
const composerDeps = ref([]);
const confirm = ref(null);
const nextExercise = ref(workshopStore.findNextExercise(props.workshop, props.exercise));
const problem = ref("");
const openFileBrowser = ref(false);
const openFileBrowserButton = ref(null);

onMounted(async () => {
  const response = await fetch("/api/online/workshop/" + currentExercise.workshop.code + "/exercise/" + currentExercise.exercise.slug);
  const data = await response.json();

  problem.value = data.problem;

  if (data.official_solution) {
    officialSolution.value = data.official_solution;
  }

  entryPoint.value = data.entry_point;
  initialFiles.value = data.initial_files;
  //sort the initial files so entry point is at the top
  //and opened in a tab
  initialFiles.value.sort((a) => {
    return a.name === props.entryPoint ? -1 : 0;
  });

  const initialFileCopy = initialFiles.value.map((file) => {
    return { ...file };
  });

  studentFiles.value = toTree(initialFileCopy);

  const files = getSavedFiles();

  for (const fileName in files) {
    const fileContent = files[fileName];
    const folderParts = fileName.split("/");

    createFileInFolderStructure(studentFiles.value, folderParts, fileContent);
  }

  //make sure new files added from saved files have two way relationship
  studentFiles.value = toTree(studentFiles.value);
  openFiles.value = [studentFiles.value[0]];
});

const addFile = (file) => {
  file.parent.push(file.file);
};

const deleteFile = (file) => {
  file.parent.splice(file.index, 1);
};

const renameFile = (file) => {
  if (file.file.new) {
    delete file.file.new;
  }

  file.file.name = file.name;
};

const toTree = (files, parent = null) => {
  return files.map((file) => {
    file.parent = parent;

    if (file.children) {
      file.children = toTree(file.children, file);
    }

    return file;
  });
};

const getSavedFiles = () => {
  const items = { ...localStorage };
  const key = currentExercise.workshop.code + "." + currentExercise.exercise.slug;

  const files = {};
  for (const localStorageKey in items) {
    if (localStorageKey.startsWith(key)) {
      files[localStorageKey.substring(key.length + 1)] = items[localStorageKey];
    }
  }
  return files;
};

const createFileInFolderStructure = (rootFolder, parts, fileContent) => {
  if (parts.length === 1) {
    const file = rootFolder.find((child) => child.name === parts[0]);
    if (file) {
      file.content = fileContent;
      return;
    }

    rootFolder.push({ name: parts[0], content: fileContent });
    return;
  }

  const directories = parts;
  const fileName = directories.pop();

  let currentDirectory = rootFolder.find((directory) => directory.name === directories[0]);

  if (!currentDirectory) {
    currentDirectory = { name: directories.shift(), children: [] };
    rootFolder.push(currentDirectory);
  }

  currentDirectory = directories.reduce((parent, name) => {
    return findOrCreateDirectory(parent, name);
  }, currentDirectory);

  const file = { name: fileName, content: fileContent };
  currentDirectory.children.push(file);
};

const findOrCreateDirectory = (directory, name) => {
  let subdirectory = directory.children.find((child) => child.name === name);

  if (!subdirectory) {
    subdirectory = { name, children: [] };
    directory.children.push(subdirectory);
  }

  return subdirectory;
};

const closeTab = (tab) => {
  if (openFiles.value.length === 1) {
    return;
  }

  let index = openFiles.value.findIndex((file) => file.name === tab);

  openFiles.value.splice(index, 1);

  findAndActivateNearestTab(index);
};

const findAndActivateNearestTab = (index) => {
  //if there is a file to the right open, set that as active
  if (index in openFiles.value) {
    activeTab.value = index;
    return;
  }

  //if there is a file to the left open, set that as active
  if (index - 1 in openFiles.value) {
    activeTab.value = index - 1;
    return;
  }

  //if there are no more files open
  activeTab.value = null;
};

const resetResults = () => {
  results.value = [];
};

const verifyLoading = () => {
  resetResults();
  loadingResults.value = true;
};

const verifySuccess = async () => {
  openPassNotification.value = true;
  openResults.value = false;
  loadingResults.value = false;

  await studentStore.completeExercise(currentExercise.workshop.code, currentExercise.exercise.slug);
};

const verifyFail = (newResults) => {
  firstVerifyLoaded.value = true;
  results.value = newResults;
  openResults.value = true;
  loadingResults.value = false;
};

const saveSolution = (fileContent, file) => {
  const filePath = toFilePath(file);

  localStorage.setItem(currentExercise.workshop.code + "." + currentExercise.exercise.slug + "." + filePath, fileContent);
};

const resetFiles = async () => {
  const ok = await confirm.value.show({
    title: "Resetting...",
    message: "File tree will be completely reset. All of your code will be deleted. Are you sure you want to continue?",
    okMessage: "Confirm",
  });

  if (!ok) {
    return;
  }

  const key = currentExercise.workshop.code + "." + currentExercise.exercise.slug;
  const files = getSavedFiles();
  for (const fileName in files) {
    localStorage.removeItem(key + "." + fileName);
  }

  const initialFileCopy = initialFiles.value.map((file) => {
    return { ...file };
  });

  studentFiles.value = toTree(initialFileCopy);
  openFiles.value = [studentFiles.value[0]];
  activeTab.value = 0;
};

const studentSelectFile = (selectedFile) => {
  if ("new" in selectedFile && selectedFile.new === true) {
    return;
  }

  if (!selectedFile.content) {
    selectedFile.content = "";

    if (selectedFile.name.endsWith(".php")) {
      selectedFile.content = "<?php\n\n";
    }

    saveSolution(selectedFile.content, selectedFile);
  }

  const found = openFiles.value.find((file) => file === selectedFile);

  if (!found) {
    //10 files open max
    if (openFiles.value.length === 10) {
      openFiles.value.shift();
    }

    activeTab.value = openFiles.value.push(selectedFile) - 1;
  }
};

const deleteFileOrFolder = async (file) => {
  if (file.name === props.entryPoint && file.parent === null) {
    //cannot delete entry point
    confirm.value.show({
      title: "Error",
      message: "Cannot delete the entry point file. This file is required to run your solution.",
      okMessage: "OK",
      disableCancel: true,
    });

    return false;
  }

  const ok = await confirm.value.show({
    title: "Deleting...",
    message: "Selection will be permanently deleted. Are you sure you want to continue?",
    okMessage: "Confirm",
  });

  if (!ok) {
    return false;
  }

  const index = openFiles.value.findIndex((elem) => elem === file);
  if (index !== -1) {
    openFiles.value.splice(index, 1);
    findAndActivateNearestTab(index);
  }

  return true;
};
</script>

<template>
  <section class="site-body flex h-full flex-1 flex-col bg-gray-900">
    <div class="relative h-full">
      <Tour
        @tour-starting="openProblemModal = true"
        :solution-file="studentFiles[0]"
        :first-run-loaded="firstRunLoaded"
        :first-verify-loaded="firstVerifyLoaded"
        :problem-modal-open="openProblemModal"
      ></Tour>

      <Confirm ref="confirm"></Confirm>

      <pass-notification
        v-if="openPassNotification"
        :next-exercise-link="'/online/editor/' + workshop + '/' + nextExercise.slug"
        :official-solution="officialSolution"
        @close="openPassNotification = false"
      ></pass-notification>

      <div class="flex h-full flex-col">
        <div class="relative flex h-full flex-1">
          <TransitionRoot :show="openFileBrowser">
            <TransitionChild
              as="template"
              enter="transform transition ease-in-out duration-500 sm:duration-700"
              enter-from="-translate-x-full"
              enter-to="translate-x-0"
              leave="transform transition ease-in-out duration-500 sm:duration-700"
              leave-from="translate-x-0"
              leave-to="-translate-x-full"
            >
              <div class="absolute left-0 top-0 z-10 h-full w-4/6 border-r border-t border-gray-600 bg-gray-900" v-on-click-outside.bubble="() => (openFileBrowser = false)">
                <FileTree
                  :files="studentFiles"
                  :file-select-function="studentSelectFile"
                  :initial-selected-item="studentFiles[0]"
                  :delete-function="deleteFileOrFolder"
                  show-controls
                  @reset="resetFiles"
                  @add-file="addFile"
                  @delete-file="deleteFile"
                  @rename-file="renameFile"
                />
              </div>
            </TransitionChild>
          </TransitionRoot>

          <div class="hidden h-full border-t border-gray-600 bg-gray-900 md:flex md:w-3/12 xl:w-2/12">
            <FileTree
              :files="studentFiles"
              :file-select-function="studentSelectFile"
              :initial-selected-item="studentFiles[0]"
              :delete-function="deleteFileOrFolder"
              show-controls
              @reset="resetFiles"
              @add-file="addFile"
              @delete-file="deleteFile"
              @rename-file="renameFile"
            />
          </div>
          <div class="flex h-full w-full border-solid border-gray-600 md:w-9/12 md:border-l xl:w-10/12">
            <EditorTabs :tabList="openFiles.map((file) => file.name)" @close-tab="closeTab" :active-tab="activeTab" @change-tab="(tab) => (activeTab = tab)">
              <template v-slot:[`tab-content-`+index] v-for="(file, index) in openFiles" :key="file.name">
                <AceEditor :id="'editor-' + (index + 1)" v-model:value="file.content" @update:value="(content) => saveSolution(content, file)" class="h-full w-full border-0" />
              </template>
            </EditorTabs>
          </div>
          <TransitionRoot :show="openResults">
            <TransitionChild
              as="template"
              enter="transform transition ease-in-out duration-300 sm:duration-700"
              enter-from="-translate-y-full md:translate-y-0 md:translate-x-full"
              enter-to="translate-y-0 md:translate-x-0"
              leave="transform transition ease-in-out duration-300 sm:duration-700"
              leave-from="translate-y-0 md:translate-x-0"
              leave-to="-translate-y-full md:translate-y-0 md:translate-x-full"
            >
              <div id="results-col" class="absolute right-0 z-10 flex h-full w-full flex-col overflow-y-scroll border-t border-solid border-gray-600 bg-gray-900 md:mt-0 md:w-3/12 md:border-l">
                <div class="flex items-center justify-between border-b border-solid border-gray-600 py-4 pl-4 pr-4">
                  <h1 class="flex items-center pt-0 font-mono text-xl text-white">
                    <AcademicCapIcon class="mr-3 h-5 w-5" />
                    Results
                  </h1>
                  <div>
                    <button @click="openResults = false" type="button" class="ml-auto inline-flex items-center rounded-lg bg-transparent p-1.5 text-sm text-gray-400 hover:hover:text-white">
                      <XMarkIcon class="h-5 w-5" />
                    </button>
                  </div>
                </div>

                <div v-show="loadingResults" class="mt-4 flex animate-pulse space-x-4 px-4">
                  <div class="flex-1 space-y-6 py-1">
                    <div class="h-2 rounded bg-slate-700"></div>
                    <div class="space-y-3">
                      <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-2 h-2 rounded bg-slate-700"></div>
                        <div class="col-span-1 h-2 rounded bg-slate-700"></div>
                      </div>
                      <div class="h-2 rounded bg-slate-700"></div>
                    </div>
                    <div class="h-2 rounded bg-slate-700"></div>
                    <div class="space-y-3">
                      <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-1 h-2 rounded bg-slate-700"></div>
                        <div class="col-span-2 h-2 rounded bg-slate-700"></div>
                      </div>
                      <div class="h-2 rounded bg-slate-700"></div>
                    </div>
                  </div>
                </div>

                <ResultList :workshop="currentExercise.workshop" :results="results"></ResultList>
              </div>
            </TransitionChild>
          </TransitionRoot>
        </div>
        <!-- start footer -->
        <div class="flex flex-wrap items-center justify-between gap-y-3 border-t border-solid border-gray-600 p-2 md:mb-0 lg:flex-nowrap">
          <editor-breadcrumbs :current-exercise="currentExercise" class="order-3 mr-3 lg:order-1"></editor-breadcrumbs>
          <progress-bar></progress-bar>
          <div class="order-1 flex w-full items-start justify-end gap-x-2 md:gap-x-0 lg:order-3 lg:w-auto">
            <button
              ref="openFileBrowserButton"
              class="mr-0 flex h-[48px] w-auto items-center justify-center rounded border-2 border-solid border-[#E91E63] px-4 text-sm text-white hover:bg-[#E91E63] md:mr-2 md:hidden lg:mb-0"
              @click.stop="openFileBrowser = !openFileBrowser"
            >
              <FolderIcon v-cloak class="h-5 w-5 xl:ml-2" />
            </button>
            <button
              class="mr-0 flex h-[48px] w-auto items-center justify-center rounded border-2 border-solid border-[#E91E63] px-4 text-sm text-white hover:bg-[#E91E63] md:mr-2 md:w-44 lg:mb-0 lg:w-auto xl:w-44"
              @click="openComposerModal = true"
            >
              <span class="hidden md:flex lg:hidden xl:flex">Composer deps</span>
              <CircleStackIcon v-cloak class="h-5 w-5 md:ml-2 lg:ml-0 xl:ml-2" />
            </button>
            <button
              id="show-problem"
              class="mr-0 mt-0 flex h-[48px] w-auto items-center justify-center rounded border-2 border-solid border-[#E91E63] px-4 text-sm text-white hover:bg-[#E91E63] md:mr-2 md:w-44 lg:w-auto xl:w-44"
              @click="openProblemModal = true"
            >
              <span class="hidden md:flex lg:hidden xl:flex">Show problem</span>
              <MapIcon v-cloak class="h-5 w-5 md:ml-2 lg:ml-0 xl:ml-2" />
            </button>
            <exercise-verify
              @verify-loading="verifyLoading"
              @verify-fail="verifyFail"
              @verify-success="verifySuccess"
              :current-exercise="currentExercise"
              :files="studentFiles"
              :entry-point="entryPoint"
              :composer-deps="composerDeps"
              @run-loaded="firstRunLoaded = true"
            />
          </div>
        </div>
      </div>
      <ComposerPackages
        :open="openComposerModal"
        :composer-deps="composerDeps"
        @close="openComposerModal = false"
        @package-added="(p) => composerDeps.push(p)"
        @package-removed="(index) => composerDeps.splice(index, 1)"
      ></ComposerPackages>
      <Problem :exercise="currentExercise.exercise" :open-problem-modal="openProblemModal" @close="openProblemModal = false" :problem="problem"></Problem>
    </div>
  </section>
</template>
