<script setup>

import { TrophyIcon } from '@heroicons/vue/24/outline'
import { UserCircleIcon } from '@heroicons/vue/24/solid'
import GitHubIcon from "../Icons/GitHubIcon.vue";
import Button from "../Website/Button.vue";
import {computed} from "vue";

const props = defineProps({
  student: {
    type: Object,
    required: false
  },
  totalExercises: Number,
  studentState: Object,
});

const percentComplete = computed(() => {
  if (props.student === undefined) {
    return 0;
  }

  return (props.studentState.totalCompleted / props.totalExercises) * 100;
});

const exerciseCompleted = computed(() => {
  if (props.student === undefined) {
    return 0;
  }

  return props.studentState.completedExercises.includes(props.exercise.name);
});

const totalCompleted = computed(() => {
  if (props.student === undefined) {
    return 0;
  }

  return props.studentState.totalCompleted;
});

const login = () => {
  window.location.href = '/student-login';
}
</script>

<template>
  <div class="grow-0 w-1/3 mx-auto mt-10 bg-gray-800 p-8 rounded-xl">
    <div class="flex justify-between">

      <Button v-if="!student" @click="login" class="flex items-center px-2 py-2">
        <GitHubIcon class="h-5 w-5 mr-2" /><span class="text-xs font-normal ">Log In with github</span>
      </Button>

      <div v-cloak v-if="student" class="flex items-center space-x-4">
        <img v-if="student.profile_picture" class="w-10 h-10 rounded-full" :src="student.profile_picture" alt="{{ student.name }}">
        <div v-if="!student.profile_picture" class="w-10 h-10 rounded-full">
          <UserCircleIcon class="text-pink-600"/>
        </div>

        <div class="font-medium">
          <p class="text-white text-xs">{{ student.name }}</p>
          <div class="text-sm text-gray-500 text-xs">Joined in {{ student.join_date }}</div>
        </div>
      </div>
      <div class="flex items-center">
          <span class="flex rounded-lg p-2">
            <TrophyIcon class="h-6 w-6 text-yellow-400"/>
          </span>
        <p class="ml-2 text-sm font-medium text-white">{{ totalCompleted }} out of {{ totalExercises }}</p>
      </div>
    </div>

    <div class="w-full rounded-full h-2.5 bg-gray-700 mt-4">
      <div class="h-2.5 rounded-full bg-pink-500" :style="{ 'width': percentComplete + '%' }"></div>
    </div>
  </div>
</template>