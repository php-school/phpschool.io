<script setup>
import { TrophyIcon } from "@heroicons/vue/24/outline";
import { ChevronRightIcon, HomeIcon } from "@heroicons/vue/24/solid";
import { computed } from "vue";
import { useStudentStore } from "../../stores/student";

const props = defineProps({
  currentExercise: Object,
});

const studentStore = useStudentStore();

const exerciseCompleted = computed(() => {
  return studentStore.isExerciseCompleted(props.currentExercise.workshop.code, props.currentExercise.exercise.name);
});
</script>

<template>
  <nav class="flex w-full md:w-auto justify-center" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 font-mono md:space-x-3">
      <li class="inline-flex items-center">
        <router-link to="/online" class="inline-flex items-center text-sm font-medium text-white hover:text-pink-500">
          <HomeIcon class="mr-2 h-4 w-4"></HomeIcon>
          {{ currentExercise.workshop.name }}
        </router-link>
      </li>
      <li>
        <div class="flex items-center">
          <ChevronRightIcon class="h-4 w-4 text-pink-500"></ChevronRightIcon>
          <a href="#" class="ml-1 text-sm font-medium text-white hover:text-pink-500 md:ml-2">
            {{ currentExercise.exercise.name }}
          </a>
        </div>
      </li>
      <li>
        <div class="flex items-center">
          <span v-if="exerciseCompleted" title="You've already completed this exercise!">
            <TrophyIcon class="h-6 w-6 text-yellow-400" />
          </span>
        </div>
      </li>
    </ol>
  </nav>
</template>
