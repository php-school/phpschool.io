<script>

import {TrophyIcon} from '@heroicons/vue/24/outline'
import {ArrowPathIcon, UserCircleIcon} from '@heroicons/vue/24/solid'
import Alert from "./Alert.vue";

export default {
    components: {
        Alert,
        TrophyIcon,
        UserCircleIcon,
        ArrowPathIcon
    },
    props: {
        student: {
            type: Object,
        },
        studentState: {
            type: Object,
        },
        totalExercises: Number,
        resetFunction: Function,
        enableShowTour: {
            type: Boolean,
            default: true
        }
    },
    data() {
        return {
            isOpen: false,
            loadingStateReset: false,
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
        showTour() {
            this.$emit('show-tour');
        },
        clickAway() {
            this.isOpen = false;
        },
        toggleDropdown() {
            this.isOpen = !this.isOpen;
        },
        async resetState() {
            if (this.loadingStateReset) {
                return;
            }

            const confirm = this.$refs.resetProgressAlert;

            const ok = await confirm.show({
                title: "Resetting progress...",
                message: "All workshop progress will be reset. Are you sure you want to continue?",
                okMessage: "Confirm",
            });

            if (!ok) {
                return;
            }

            this.loadingStateReset = true;

            this.resetFunction()
                .then(() => {
                    this.loadingStateReset = false;
                })
                .catch(() => {
                    this.loadingStateReset = false;
                });
        }
    }
}
</script>

<template>
    <div class="relative">
        <button @click.stop="toggleDropdown"
                class="flex mx-3 text-sm bg-gray-800 rounded-full md:mr-4 focus:ring-4  focus:ring-gray-600"
                type="button">
            <span class="sr-only">Open user menu</span>
            <img class="w-8 h-8 rounded-full" :src="student.profile_picture" alt="{{ student.name }}">
        </button>

        <div v-show="isOpen" v-click-away="clickAway"
             class="absolute top-10 right-2.5 z-10 w-[250px] divide-solid divide-y divide-gray-600 rounded-lg shadow-xl bg-gray-800">
            <div class="px-6 py-4 text-sm text-white text-left">
                <div>{{ student.name }}</div>
                <div class="font-medium truncate">{{ student.email }}</div>
            </div>
            <div class="py-5">
                <div class="px-6 flex justify-between">
                    <div class="flex items-center">
                        <span class="flex rounded-lg p-2">
                          <TrophyIcon class="h-6 w-6 text-yellow-400"/>
                        </span>
                        <p class="ml-2 text-sm font-medium text-gray-500">{{ studentState.totalCompleted }} out of {{ totalExercises }}</p>
                    </div>
                </div>
                <div class="px-6">
                    <div class=" rounded-full h-2.5 bg-gray-700 mt-4">
                        <div class=" h-2.5 rounded-full bg-pink-500" :style="{ 'width': percentComplete + '%' }"></div>
                    </div>
                </div>
            </div>


            <ul class="py-2 text-sm text-gray-200">
                <li>
                    <a href="/cloud" class="block text-left no-underline px-6 py-2 hover:bg-gray-600 hover:text-white">Workshop Dashboard</a>
                </li>
                <li v-if="enableShowTour">
                    <a href="#" @click="showTour" class="block text-left no-underline px-6 py-2 hover:bg-gray-600 hover:text-white">Show Tour Again</a>
                </li>
                <li>
                    <a href="#" @click="resetState" class="flex justify-between text-left no-underline px-6 py-2 hover:bg-gray-600 hover:text-white">
                        <span>Reset Progress</span>
                        <ArrowPathIcon v-cloak v-show="loadingStateReset" class="w-4 h-4 animate-spin text-pink-500"/>
                    </a>
                </li>
            </ul>
            <alert ref="resetProgressAlert"></alert>

            <div class="py-3">
                <a href="/cloud/logout" class="block text-left no-underline px-6 py-2 text-sm hover:bg-gray-600 text-gray-200 hover:text-white">Sign out</a>
            </div>
        </div>
    </div>
</template>