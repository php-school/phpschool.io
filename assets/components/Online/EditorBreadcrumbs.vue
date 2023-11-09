<script setup>
import {TrophyIcon} from "@heroicons/vue/24/outline";
import {ChevronRightIcon, HomeIcon} from "@heroicons/vue/24/solid";
import {computed} from "vue";

const props = defineProps({
    currentExercise: Object,
    studentState: Object,
});

const exerciseCompleted = computed(() => {
    return props.studentState.completedExercises.includes(props.currentExercise.exercise.name);
});
</script>

<template>
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3 font-mono">
            <li class="inline-flex items-center">
                <a href="/cloud"
                   class="inline-flex items-center text-sm font-medium text-white hover:text-pink-500">
                    <HomeIcon class="w-4 h-4 mr-2"></HomeIcon>
                    {{ currentExercise.workshop.name }}
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <ChevronRightIcon class="w-4 h-4 text-pink-500"></ChevronRightIcon>
                    <a href="#" class="ml-1 text-sm font-medium text-white hover:text-pink-500 md:ml-2">
                        {{ currentExercise.exercise.name }}</a>
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
</template>