<script>

import { ArrowRightIcon, CheckCircleIcon } from '@heroicons/vue/24/solid'
import { ArrowRightCircleIcon } from '@heroicons/vue/24/outline'

export default {
    components: {
        ArrowRightIcon,
        CheckCircleIcon,
        ArrowRightCircleIcon
    },
    props: {
        selectedWorkshop: Object,
        exercise: Object,
        student: Object,
        studentState: Object,
    },
    methods: {
        selectExercise() {
            window.location.href = '/cloud/workshop/' + this.selectedWorkshop.code + '/exercise/' + this.exercise.slug + '/editor';
        },
        isExerciseComplete(workshopCode, exerciseName) {
            if (this.student === undefined) {
                return false;
            }

            if (!this.studentState.workshops.hasOwnProperty(workshopCode)) {
                return false;
            }

            const workshop = this.studentState.workshops[workshopCode];
            return workshop.completedExercises.includes(exerciseName);
        }
    },
    computed: {
        isCurrentExerciseComplete() {
            return this.isExerciseComplete(this.selectedWorkshop.code, this.exercise.name);
        },
        isNextWorkshop() {
            const pos = this.selectedWorkshop.exercises.findIndex((e) => e.name === this.exercise.name);

            if (pos - 1 in this.selectedWorkshop.exercises) {
                const prevComplete = this.isExerciseComplete(
                    this.selectedWorkshop.code,
                    this.selectedWorkshop.exercises[pos - 1].name
                )

                const thisComplete = this.isExerciseComplete(
                    this.selectedWorkshop.code,
                    this.exercise.name
                )

                return prevComplete && !thisComplete;
            }

            return true
        }
    }
}
</script>

<template>
    <li  @click="selectExercise" class="group flex flex-row hover:bg-gray-600 last:hover:rounded-b-lg">
        <div class="select-none cursor-pointer flex flex-1 items-center p-4">

            <div class="flex flex-col w-10 h-10 justify-center items-center mr-4">
                <a href="#" class="block relative">
                    <img alt="workshop" src="../img/core-workshops.png" class="mx-auto object-cover h-10 w-10 "/>
                </a>
            </div>
            <div class="flex-1 pl-1 mr-16 text-white group-hover:text-pink-600">
                <div class="font-medium ">{{ exercise.name }}</div>
                <div class="text-gray-300 text-sm">{{ exercise.description }}</div>
            </div>
            <div class="text-gray-200 text-xs bg-pink-600 py-1 px-3 rounded-full">{{ exercise.type }}</div>
            <a href="#" class="w-24 text-right flex justify-end">
                <CheckCircleIcon v-if="isCurrentExerciseComplete" class="text-pink-500 h-8 w-8 rounded-full border-2 border-solid border-pink-300" />
                <ArrowRightCircleIcon v-else class="text-pink-200 h-8 w-8 rounded-full border-2 border-solid border-pink-500 !fill-none" :class="{ 'animate-bounce': isNextWorkshop}"/>
            </a>
        </div>
    </li>
</template>
