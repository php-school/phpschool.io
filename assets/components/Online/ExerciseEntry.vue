<script setup>
import { CheckCircleIcon } from '@heroicons/vue/24/solid'
import { ArrowRightCircleIcon } from '@heroicons/vue/24/outline'
import { computed } from 'vue'

import { useRouter } from 'vue-router'

import { useStudentStore } from '../../stores/student'
const studentStore = useStudentStore()

const props = defineProps({
    selectedWorkshop: Object,
    exercise: Object
})

const router = useRouter()

const emit = defineEmits(['not-logged-in'])

const selectExercise = () => {
    if (!studentStore.student) {
        emit('not-logged-in')
        return
    }

    router.push('/online/editor/' + props.selectedWorkshop.code + '/' + props.exercise.slug)
}

const isCurrentExerciseComplete = computed(() => {
    return studentStore.isExerciseCompleted(props.selectedWorkshop.code, props.exercise.name)
})

const isNextWorkshop = computed(() => {
    const pos = props.selectedWorkshop.exercises.findIndex((e) => e.name === props.exercise.name)

    if (pos - 1 in props.selectedWorkshop.exercises) {
        const prevComplete = studentStore.isExerciseCompleted(
            props.selectedWorkshop.code,
            props.selectedWorkshop.exercises[pos - 1].name
        )

        const thisComplete = studentStore.isExerciseCompleted(
            props.selectedWorkshop.code,
            props.exercise.name
        )

        return prevComplete && !thisComplete
    }

    return true
})
</script>

<template>
    <li
        @click="selectExercise"
        class="group flex flex-row hover:bg-gray-600 last:hover:rounded-b-lg"
    >
        <div class="select-none cursor-pointer flex flex-1 items-center p-4">
            <div class="flex flex-col w-10 h-10 justify-center items-center mr-4">
                <a href="#" class="block relative">
                    <img
                        alt="workshop"
                        src="../../img/cloud/core-workshops.png"
                        class="mx-auto object-cover h-10 w-10"
                    />
                </a>
            </div>
            <div class="flex-1 pl-1 mr-16 text-white group-hover:text-pink-600">
                <div class="font-medium">{{ exercise.name }}</div>
                <div class="text-gray-300 text-xs">{{ exercise.description }}</div>
            </div>
            <div class="text-gray-200 text-xs bg-pink-600 py-1 px-3 rounded-full">
                {{ exercise.type }}
            </div>
            <a href="#" class="w-24 text-right flex justify-end">
                <CheckCircleIcon
                    v-if="isCurrentExerciseComplete"
                    class="text-pink-500 h-8 w-8 rounded-full border-2 border-solid border-pink-300"
                />
                <ArrowRightCircleIcon
                    v-else
                    class="text-pink-200 h-8 w-8 rounded-full border-2 border-solid border-pink-500 !fill-none"
                    :class="{ 'animate-bounce': isNextWorkshop }"
                />
            </a>
        </div>
    </li>
</template>
