<script>

import PassNotification from "./PassNotification.vue";
import FileTree from "./FileTree.vue";
import ExerciseVerify from "./ExerciseVerify.vue";
import Tabs from "./Tabs.vue";
import Modal from "./Modal.vue";
import AceEditor from "./AceEditor.vue";
import {
    XMarkIcon,
    ArrowPathIcon,
    CircleStackIcon,
    MapIcon,
    HomeIcon,
    ChevronRightIcon,
    ExclamationCircleIcon,
    AcademicCapIcon
} from '@heroicons/vue/24/solid';
import {TrophyIcon} from '@heroicons/vue/24/outline'
import OutputMismatch from './Results/CliOutputMismatch.vue';
import ResultList from "./Results/ResultList.vue";
import Tour from "./Tour.vue";
import Confirm from "./Confirm.vue";
import StudentDropdown from "./StudentDropdown.vue";
import toFilePath from "./Utils/toFilePath";
import Alert from "./Alert.vue";
import SiteNav from "../Website/SiteNav.vue";
import Problem from "./Problem.vue";
import ComposerPackages from "./ComposerPackages.vue";
import ProgressBar from "./ProgressBar.vue";
import EditorBreadcrumbs from "./EditorBreadcrumbs.vue";

export default {
    components: {
        EditorBreadcrumbs,
        ProgressBar,
        ComposerPackages,
        Problem,
        SiteNav,
        Alert,
        StudentDropdown,
        Tour,
        ResultList,
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
        TrophyIcon,
        HomeIcon,
        ChevronRightIcon,
        ExclamationCircleIcon,
        AcademicCapIcon,
        OutputMismatch,
        Confirm,
    },
    props: {
        nextExerciseLink: String,
        officialSolution: Array,
        initialFiles: Array,
        entryPoint: String,
        workshop: Object,
        exercise: Object,
        student: Object,
        totalExercises: Number,
        links: Object
    },
    mounted() {

    },
    data() {
        //sort the initial files so entry point is at the top
        //and opened in a tab
        this.initialFiles.sort((a, b) => {
            return a.name === this.entryPoint ? -1 : 0;
        });

        const initialFileCopy = this.initialFiles.map(file => {
            return {...file}
        });
        let studentFiles = this.toTree(initialFileCopy);

        const files = this.getSavedFiles();
        for (const fileName in files) {
            const fileContent = files[fileName];
            const folderParts = fileName.split("/");

            this.createFileInFolderStructure(studentFiles, folderParts, fileContent);
        }

        //make sure new files added from saved files have two way relationship
        studentFiles = this.toTree(studentFiles);

        return {
            firstRunLoaded: false,
            firstVerifyLoaded: false,
            openPassNotification: false,
            openProblemModal: true,
            openComposerModal: false,
            studentFiles: studentFiles,
            openResults: false,
            results: [],
            loadingResults: false,
            openFiles: [studentFiles[0]],
            activeTab: 0,
            composerDeps: [],
            studentState: {
                totalCompleted: this.student.state.total_completed,
                completedExercises: this.student.state.workshops[this.workshop.code].completedExercises
            },
            currentExercise: {
                workshop: this.workshop,
                exercise: this.exercise
            },
        }
    },
    methods: {
        getSavedFiles() {
            const items = { ...localStorage };
            const key = this.workshop.code + '.' + this.exercise.slug;

            const files = {};
            for (const localStorageKey in items) {
                if (localStorageKey.startsWith(key)) {
                    files[localStorageKey.substring(key.length + 1)] = items[localStorageKey];
                }
            }
            return files;
        },
        async resetFiles() {
            const confirm = this.$refs.confirm;

            const ok = await confirm.show({
                title: "Resetting...",
                message: "File tree will be completely reset. All of your code will be deleted. Are you sure you want to continue?",
                okMessage: "Confirm",
            });

            if (!ok) {
                return;
            }

            const key = this.currentExercise.workshop.code + '.' + this.currentExercise.exercise.slug;
            const files = this.getSavedFiles();
            for (const fileName in files) {
                localStorage.removeItem(key + '.' + fileName);
            }

            const initialFileCopy = this.initialFiles.map(file => {
                return {...file}
            });

            this.studentFiles = this.toTree(initialFileCopy);
            this.openFiles = [this.studentFiles[0]];
            this.activeTab = 0;
        },
        createFileInFolderStructure(rootFolder, parts, fileContent) {
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
                return this.findOrCreateDirectory(parent, name);
            }, currentDirectory);

            const file = { name: fileName, content: fileContent };
            currentDirectory.children.push(file);
        },
        findOrCreateDirectory(directory, name) {
            let subdirectory = directory.children.find(child => child.name === name);

            if (!subdirectory) {
                subdirectory = { name, children: [] };
                directory.children.push(subdirectory);
            }

            return subdirectory;
        },
        saveSolution(fileContent, file) {
            const filePath = toFilePath(file);

            localStorage.setItem(
                this.currentExercise.workshop.code + '.' + this.currentExercise.exercise.slug + '.' + filePath,
                fileContent
            );
        },
        resetState() {
            const currentExercise = this.currentExercise;
            const studentState = this.studentState;

            return new Promise(async function (resolve, reject) {
                const url = '/cloud/workshop/' + currentExercise.workshop.code + '/exercise/' + currentExercise.exercise.slug + '/reset';

                const opts = {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                };
                fetch(url, opts)
                    .then(response => {
                        if (response.ok) {
                            studentState.totalCompleted = 0;
                            studentState.completedExercises = [];

                            resolve();
                        }

                        reject();
                    });
            });
        },
        tourStarting() {
            this.openProblemModal = true;
        },
        forceTour() {
            this.$refs.tour.forceTour();
        },
        studentSelectFile(selectedFile) {
            if ('new' in selectedFile && selectedFile.new === true) {
                return;
            }

            if (!selectedFile.content) {
                selectedFile.content = '';

                if (selectedFile.name.endsWith('.php')) {
                    selectedFile.content = '<?php\n\n';
                }

                this.saveSolution(selectedFile.content, selectedFile);
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
        deleteFileOrFolder(file) {
            const confirm = this.$refs.confirm;
            const openFiles = this.openFiles;
            const findAndActivateNearestTab = this.findAndActivateNearestTab;
            const entryPoint = this.entryPoint;

            return new Promise(async function (resolve, reject) {
                if (file.name === entryPoint && file.parent === null) {
                    //cannot delete entry point
                    await confirm.show({
                        title: "Error",
                        message: "Cannot delete the entry point file. This file is required to run your solution.",
                        okMessage: "OK",
                        disableCancel: true
                    });

                    return resolve(false);
                }

                const ok = await confirm.show({
                    title: "Deleting...",
                    message: "Selection will be permanently deleted. Are you sure you want to continue?",
                    okMessage: "Confirm",
                });

                if (!ok) {
                    return resolve(false);
                }

                resolve(true);

                const index = openFiles.findIndex((elem) => elem === file);
                if (index !== -1) {
                    openFiles.splice(index, 1);
                    findAndActivateNearestTab(index);
                }
            })
        },

        resetResults() {
            this.results = [];
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
            this.firstVerifyLoaded = true;
            this.results = results;
            this.openResults = true;
            this.loadingResults = false;
        },
        closeTab(tab) {
            if (this.openFiles.length === 1) {
                return;
            }

            let index = this.openFiles.findIndex(file => file.name === tab);

            this.openFiles.splice(index, 1);

            this.findAndActivateNearestTab(index);
        },
        findAndActivateNearestTab(index) {
            //if there is a file to the right open, set that as active
            if (index in this.openFiles) {
                this.activeTab = index;
                return;
            }

            //if there is a file to the left open, set that as active
            if (index - 1 in this.openFiles) {
                this.activeTab = index - 1;
                return;
            }

            //if there are no more files open
            this.activeTab = null;
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
    }
}
</script>

<template>
    <site-nav compact :links="links" :show-login-button="false">
        <template v-slot:nav-after>
            <ul v-if="student" class="order-3">
                <li>
                    <student-dropdown
                            @show-tour="forceTour"
                            :student="student"
                            :student-state="studentState"
                            :total-exercises='totalExercises'
                            :reset-function="resetState"
                    />
                </li>
            </ul>
        </template>
    </site-nav>

    <section class="site-body h-full flex-1 flex flex-col bg-gray-900">
        <div class="h-full relative">

            <tour ref="tour" @tour-starting="tourStarting" :student="student" :solution-file="studentFiles[0]"
                  :first-run-loaded="firstRunLoaded"
                  :first-verify-loaded="firstVerifyLoaded" :problem-modal-open="openProblemModal"></tour>

            <confirm ref="confirm"></confirm>

            <pass-notification
                    v-if="openPassNotification"
                    :next-exercise-link="nextExerciseLink"
                    :official-solution="officialSolution"
                    @close="dismissPassNotification">
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

                        <ResultList :workshop="workshop" :results="results"></ResultList>
                    </div>
                </div>
                <!-- start footer -->
                <div class="border-t border-solid border-gray-600 h-16 flex-none flex items-center justify-between p-2">
                    <editor-breadcrumbs :student-state="studentState" :current-exercise="currentExercise"></editor-breadcrumbs>
                    <progress-bar :student-state="studentState" :total-exercises="totalExercises"></progress-bar>
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
            <Problem :exercise="exercise" :open-problem-modal="openProblemModal" @close="openProblemModal = false">
                <slot name="problem"></slot>
            </Problem>
        </div>
    </section>
</template>
