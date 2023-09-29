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
import PackageSearch from './PackageSearch.vue';
import OutputMismatch from './results/CliOutputMismatch.vue';
import ResultList from "./results/ResultList.vue";
import Tour from "./Tour.vue";
import Confirm from "./Confirm.vue";
import HeaderNav from "./HeaderNav.vue";
import StudentDropdown from "./StudentDropdown.vue";
import toFilePath from "./utils/toFilePath";
import Alert from "./Alert.vue";

export default {
    components: {
        Alert,
        StudentDropdown,
        HeaderNav,
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
        PackageSearch,
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
            newDependency: '',
            composerDeps: [],
            loadingComposerAdd: false,
            studentState: {
                totalCompleted: this.student.state.total_completed,
                completedExercises: this.student.state.workshops[this.workshop.code].completedExercises
            },
            currentExercise: {
                workshop: this.workshop,
                exercise: this.exercise
            },
            showPackageAddError: false,
            showPackageErrorTimerId: null
        }
    },
    computed: {
        percentComplete() {
            return (this.studentState.totalCompleted / this.totalExercises) * 100;
        },
        exerciseCompleted() {
            return this.studentState.completedExercises.includes(this.exercise.name);
        },
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
        saveSolution(file) {
            const filePath = toFilePath(file);

            localStorage.setItem(
                this.currentExercise.workshop.code + '.' + this.currentExercise.exercise.slug + '.' + filePath,
                file.content
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
                    if (json.status === 'error') {
                        this.loadingComposerAdd = false;
                        this.$refs.packageSearch.reset();

                        this.showPackageAddError = true;

                        if (this.showPackageErrorTimerId) {
                            clearInterval(this.showPackageErrorTimerId);
                        }

                        this.showPackageErrorTimerId = setTimeout(() => this.showPackageAddError = false, 3000);
                        return;
                    }

                    this.composerDeps.push({
                        name: this.newDependency,
                        version: json.latest_version,
                        versions: json.versions
                    });
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

    <header-nav :links="links">
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
    </header-nav>

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

            <alert type="error" @close="showPackageAddError= false" v-show="showPackageAddError" message="Package could not be added because it has no tagged version."></alert>


            <div class="h-full flex flex-col">
                <div class="flex flex-1 h-full relative">
                    <div class="w-3/12 xl:w-2/12">
                        <file-tree
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
                                <AceEditor :id="'editor-' + (index + 1)" :file="file" @changeContent="saveSolution"
                                           class="w-full h-full border-0"/>
                            </template>
                        </Tabs>
                    </div>
                    <div v-show="openResults" id="results-col"
                         class="w-3/12 flex flex-col bg-gray-950 border-l border-solid border-gray-600 h-full absolute right-0 overflow-y-scroll">
                        <div class="pl-4 pr-4 py-4 flex justify-between items-center border-solid border-b border-gray-600">
                            <h1 class="text-2xl pt-0 flex items-center"><AcademicCapIcon class="h-5 w-5 mr-2"/> Results</h1>
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
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            <li class="inline-flex items-center">
                                <a href="/cloud"
                                   class="inline-flex items-center text-sm font-medium text-white hover:text-pink-500">
                                    <HomeIcon class="w-4 h-4 mr-2"></HomeIcon>
                                    {{ workshop.name }}
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <ChevronRightIcon class="w-4 h-4 text-pink-500"></ChevronRightIcon>
                                    <a href="#" class="ml-1 text-sm font-medium text-white hover:text-pink-500 md:ml-2">
                                        {{ exercise.name }}</a>
                                </div>
                            </li>
                            <li>
                                <div class="flex items-center">
                  <span v-if="exerciseCompleted" title="You've already completed this exercise!">
                    <TrophyIcon class="h-6 w-6 text-yellow-400"/>
                  </span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <!-- Progress Tracker Bar -->
                    <div class="w-1/6 bg-gray-200 rounded-full h-5 bg-gray-700 mt-0 relative flex justify-center items-center">
                        <div class="absolute left-0 h-5 rounded-full bg-pink-500"
                             :style="{ 'width': percentComplete + '%' }"></div>
                        <p class="absolute  inline-flex items-center text-xs font-bold text-white ml-2 mx-auto">
                            {{ studentState.totalCompleted }} / {{ totalExercises }} completed</p>
                    </div>
                    <div class="flex">
                        <button class="border-[#E91E63] hover:bg-[#E91E63] border-solid border-2 text-white flex items-center justify-center mt-0 mr-2 rounded px-4 w-44"
                                @click="openComposerModal = true">
                            <span>Composer deps</span>
                            <CircleStackIcon v-cloak class="ml-2 w-5 h-5"/>
                        </button>
                        <button id="show-problem"
                                class="border-[#E91E63] hover:bg-[#E91E63] border-solid border-2 text-white flex items-center justify-center mt-0 mr-2 rounded px-4 w-44"
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

            <Transition enter-active-class="transition-opacity duration-100 ease-in"
                        leave-active-class="transition-opacity duration-200 ease-in" enter-from-class="opacity-0"
                        enter-to-class="opacity-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <Modal size="sm" max-height="max-h-[calc(1/2*100%)]" v-if="openComposerModal"
                       @close="openComposerModal = false">
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
                            <package-search ref="packageSearch" @package-selected="packageSelected"
                                            v-model="newDependency" class="w-full"></package-search>
                            <button :disabled="newDependency === ''" @click.stop="addDependency" type="button"
                                    class="inline-flex items-center h-9 justify-center rounded-full border border-transparent w-16 bg-pink-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-pink-700 focus:outline-none focus:ring focus:ring-pink-800 sm:ml-3 sm:text-sm disabled:opacity-70 disabled:hover:bg-pink-600">
                                <ArrowPathIcon v-cloak v-show="loadingComposerAdd" class="w-4 h-4 animate-spin"/>
                                <span v-if="!loadingComposerAdd">Add</span>
                            </button>
                        </div>
                        <ul v-show="composerDeps.length > 0" class="mt-4 overflow-y-scroll ">
                            <li v-for="dep in composerDeps" class="text-white pl-2 mb-2 flex items-center">
                                <p class="text-base">{{ dep.name }}</p>
                                <p class="bg-gray-900 ml-2 px-2 py-1 rounded">{{ dep.version }}</p>
                                <XMarkIcon @click.stop="removeDependency(dep.name)"
                                           class="cursor-pointer ml-2 w-5 h-5 text-zinc-400 hover:text-pink-600"/>
                            </li>
                        </ul>
                        <div v-show="composerDeps.length === 0" class="pt-6">
                            <p class="text-white ">You currently have no dependencies.</p>
                        </div>
                    </template>

                    <template #footer>
                        <div class="flex justify-end">
                            <button @click="openComposerModal = false" type="button"
                                    class="inline-flex items-center w-full justify-center rounded-full border border-transparent bg-pink-600 px-8 py-2 text-base font-medium text-white shadow-sm hover:bg-pink-700 focus:outline-none focus:ring focus:ring-pink-800 sm:ml-3 sm:w-auto sm:text-sm">
                                Close
                            </button>
                        </div>
                    </template>
                </Modal>
            </Transition>
            <Transition enter-active-class="transition-opacity duration-500 ease-in"
                        leave-active-class="transition-opacity duration-500 ease-in" enter-from-class="opacity-0"
                        enter-to-class="opacity-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <Modal id="problem-modal" :scroll-content="true" size="4xl" max-height="max-h-[calc(5/6*100%)]"
                       v-if="openProblemModal" @close="openProblemModal = false">
                    <template #header>
                        <div class="flex flex-col">
                            <div class="flex items-center ">
                                <MapIcon class="h-6 w-6 text-pink-500 mr-2"/>
                                <h3 class="text-base font-semibold lg:text-xl text-white pt-0 mt-0 ">
                                    The problem...
                                </h3>
                            </div>
                            <h2 class="mt-2 mb-2 ml-3 text-2xl">{{ exercise.name }}</h2>
                        </div>
                    </template>

                    <template #body class="">
                        <div id="problem-file" class="text-white">
                            <slot name="problem"></slot>
                        </div>
                    </template>

                    <template #footer>
                        <div class="flex justify-end">
                            <button id="lets-go" @click="openProblemModal = false" type="button"
                                    class="inline-flex items-center w-full justify-center rounded-full border border-transparent bg-pink-600 px-8 py-2 text-base font-medium text-white shadow-sm hover:bg-pink-700 focus:outline-none focus:ring focus:ring-pink-800 sm:ml-3 sm:w-auto sm:text-sm">
                                Let's go!
                            </button>
                        </div>
                    </template>
                </Modal>
            </Transition>
        </div>
    </section>
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

#problem-file h4 {
    color: #E91E63;
    @apply mt-2 mb-4 text-lg not-italic;
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

#problem-file :not(pre) > code {
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
