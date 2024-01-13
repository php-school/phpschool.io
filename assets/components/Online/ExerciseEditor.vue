<script setup>

import PassNotification from "./PassNotification.vue";
import FileTree from "./FileTree.vue";
import ExerciseVerify from "./ExerciseVerify.vue";
import Tabs from "./Tabs.vue";
import AceEditor from "./AceEditor.vue";
import {XMarkIcon, CircleStackIcon, MapIcon, AcademicCapIcon} from '@heroicons/vue/24/solid';
import ResultList from "./Results/ResultList.vue";
import Tour from "./Tour.vue";
import Confirm from "./Confirm.vue";
import Problem from "./Problem.vue";
import ComposerPackages from "./ComposerPackages.vue";
import ProgressBar from "./ProgressBar.vue";
import EditorBreadcrumbs from "./EditorBreadcrumbs.vue";
import {onMounted, ref} from "vue";
import toFilePath from "./Utils/toFilePath";
import {useWorkshopStore} from "../../stores/workshops";
import {useStudentStore} from "../../stores/student";

const workshopStore = useWorkshopStore();
const studentStore = useStudentStore();

const props = defineProps({
    workshop: String,
    exercise: String,
    nextExerciseLink: String,
});

const currentExercise = {
    workshop: workshopStore.getWorkshop(props.workshop),
    exercise: workshopStore.getExercise(props.workshop, props.exercise)
};

const entryPoint = ref('solution.php');
const initialFiles = ref([]);
const officialSolution = ref([])
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
const nextExercise = ref(workshopStore.findNextExercise(props.workshop, props.exercise))
const problem = ref('');

onMounted(async () => {
    const response = await fetch('/api/online/workshop/' + currentExercise.workshop.code + '/exercise/' + currentExercise.exercise.slug);
    const data = await response.json();

    problem.value = data.problem;

    if (data.official_solution) {
        officialSolution.value = data.official_solution;
    }

    entryPoint.value = data.entry_point;
    initialFiles.value = data.initial_files;
    //sort the initial files so entry point is at the top
    //and opened in a tab
    initialFiles.value.sort((a, b) => {
        return a.name === props.entryPoint ? -1 : 0;
    });

    const initialFileCopy = initialFiles.value.map(file => {
        return {...file}
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
})


const toTree = (files, parent = null) => {
    return files.map((file) => {
        file.parent = parent;

        if (file.children) {
            file.children = toTree(file.children, file);
        }

        return file;
    })
};

const getSavedFiles = () => {
    const items = { ...localStorage };
    const key = currentExercise.workshop.code + '.' + currentExercise.exercise.slug;

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
        const file = rootFolder.find(child => child.name === parts[0]);
        if (file) {
            file.content = fileContent;
            return;
        }

        rootFolder.push({ name: parts[0], content: fileContent });
        return;
    }

    const directories = parts;
    const fileName = directories.pop();

    let currentDirectory = rootFolder.find(directory => directory.name === directories[0]);

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
    let subdirectory = directory.children.find(child => child.name === name);

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

    let index = openFiles.value.findIndex(file => file.name === tab);

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

    localStorage.setItem(
        currentExercise.workshop.code + '.' + currentExercise.exercise.slug + '.' + filePath,
        fileContent
    );
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

    const key = currentExercise.workshop.code + '.' + currentExercise.exercise.slug;
    const files = getSavedFiles();
    for (const fileName in files) {
        localStorage.removeItem(key + '.' + fileName);
    }

    const initialFileCopy = initialFiles.value.map(file => {
        return {...file}
    });

    studentFiles.value = toTree(initialFileCopy);
    openFiles.value = [studentFiles.value[0]];
    activeTab.value = 0;
}

const tour = ref(null);

const studentSelectFile = (selectedFile) => {
    if ('new' in selectedFile && selectedFile.new === true) {
        return;
    }

    if (!selectedFile.content) {
        selectedFile.content = '';

        if (selectedFile.name.endsWith('.php')) {
            selectedFile.content = '<?php\n\n';
        }

        saveSolution(selectedFile.content, selectedFile);
    }

    const found = openFiles.value.find(file => file === selectedFile);

    if (!found) {
        //10 files open max
        if (openFiles.value.length === 10) {
            openFiles.value.shift();
        }

        activeTab.value = openFiles.value.push(selectedFile) - 1;
    }
};

const deleteFileOrFolder = (file) => {
    return new Promise(async function (resolve, reject) {
        if (file.name === props.entryPoint && file.parent === null) {
            //cannot delete entry point
            await confirm.value.show({
                title: "Error",
                message: "Cannot delete the entry point file. This file is required to run your solution.",
                okMessage: "OK",
                disableCancel: true
            });

            return resolve(false);
        }

        const ok = await confirm.value.show({
            title: "Deleting...",
            message: "Selection will be permanently deleted. Are you sure you want to continue?",
            okMessage: "Confirm",
        });

        if (!ok) {
            return resolve(false);
        }

        resolve(true);

        const index = openFiles.value.findIndex((elem) => elem === file);
        if (index !== -1) {
            openFiles.value.splice(index, 1);
            findAndActivateNearestTab(index);
        }
    })
};
</script>

<template>
    <section class="site-body h-full flex-1 flex flex-col bg-gray-900">
        <div class="h-full relative">

            <tour ref="tour" @tour-starting="openProblemModal = true" :solution-file="studentFiles[0]"
                  :first-run-loaded="firstRunLoaded"
                  :first-verify-loaded="firstVerifyLoaded" :problem-modal-open="openProblemModal"></tour>

            <confirm ref="confirm"></confirm>

            <pass-notification
                    v-if="openPassNotification"
                    :next-exercise-link="'/online/editor/' + workshop + '/' + nextExercise.slug"
                    :official-solution="officialSolution"
                    @close="openPassNotification = false">
            </pass-notification>

            <div class="h-full flex flex-col">
                <div class="flex flex-1 h-full relative border-t border-gray-600">
                    <div class="w-3/12 xl:w-2/12">
                        <FileTree
                                :files="studentFiles"
                                :file-select-function="studentSelectFile"
                                :initial-selected-item="studentFiles[0]"
                                :delete-function="deleteFileOrFolder"
                                show-controls
                                @reset="resetFiles"/>
                    </div>
                    <div class="flex border-l border-solid border-gray-600 h-full"
                         :class="[openResults ? 'w-6/12 xl:w-7/12' : 'w-9/12 xl:w-10/12']">
                        <Tabs :tabList="openFiles.map(file => file.name)" @close-tab="closeTab" :active-tab="activeTab">
                            <template v-slot:[`tab-content-`+index] v-for="(file, index) in openFiles">
                                <AceEditor :id="'editor-' + (index + 1)" v-model:value="file.content" @update:value="(content) => saveSolution(content, file)"
                                           class="w-full h-full border-0"/>
                            </template>
                        </Tabs>
                    </div>
                    <div v-show="openResults" id="results-col"
                         class="w-3/12 flex flex-col bg-gray-900 border-l border-solid border-gray-600 h-full absolute right-0 overflow-y-scroll">
                        <div class="pl-4 pr-4 py-4 flex justify-between items-center border-solid border-b border-gray-600">
                            <h1 class="font-mono text-xl pt-0 flex items-center text-white"><AcademicCapIcon class="h-5 w-5 mr-3"/> Results</h1>
                            <div>
                                <button @click="openResults = false" type="button"
                                        class="text-gray-400 bg-transparent rounded-lg text-sm p-1.5 ml-auto inline-flex items-center hover:hover:text-white">
                                    <XMarkIcon class="w-5 h-5"/>
                                </button>
                            </div>
                        </div>

                        <div v-show="loadingResults" class="animate-pulse flex space-x-4 mt-4 px-4">
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

                        <ResultList :workshop="currentExercise.workshop" :results="results"></ResultList>
                    </div>
                </div>
                <!-- start footer -->
                <div class="border-t border-solid border-gray-600 h-16 flex-none flex items-center justify-between p-2">
                    <editor-breadcrumbs :current-exercise="currentExercise"></editor-breadcrumbs>
                    <progress-bar></progress-bar>
                    <div class="flex">
                        <button class="border-[#E91E63] hover:bg-[#E91E63] border-solid border-2 text-white text-sm flex items-center justify-center mt-0 mr-2 rounded px-4 w-44 h-[48px]"
                                @click="openComposerModal = true">
                            <span>Composer deps</span>
                            <CircleStackIcon v-cloak class="ml-2 w-5 h-5"/>
                        </button>
                        <button id="show-problem"
                                class="border-[#E91E63] hover:bg-[#E91E63] border-solid border-2 text-white text-sm flex items-center justify-center mt-0 mr-2 rounded px-4 w-44 h-[48px]"
                                @click="openProblemModal = true">
                            <span>Show problem</span>
                            <MapIcon v-cloak class="ml-2 w-5 h-5"/>
                        </button>
                        <exercise-verify @verify-loading="verifyLoading"
                                         @verify-fail="verifyFail"
                                         @verify-success="verifySuccess"
                                         :current-exercise="currentExercise"
                                         :files="studentFiles"
                                         :entry-point="entryPoint"
                                         :composer-deps="composerDeps" @run-loaded="firstRunLoaded = true"/>
                    </div>
                </div>
            </div>
            <ComposerPackages
                    :open="openComposerModal"
                    :composer-deps="composerDeps"
                    @close="openComposerModal = false"
                    @package-added="(p) => composerDeps.push(p)"
                    @package-removed="(index) => composerDeps.splice(index, 1)">
            </ComposerPackages>
            <Problem :exercise="currentExercise.exercise" :open-problem-modal="openProblemModal" @close="openProblemModal = false" :problem="problem">
            </Problem>
        </div>
    </section>
</template>
