<script setup>

import { TrophyIcon } from '@heroicons/vue/24/outline'
import { UserCircleIcon } from '@heroicons/vue/24/solid'
import GitHubIcon from "../Icons/GitHubIcon.vue";
import Button from "../Website/Button.vue";
import {computed} from "vue";

import {useStudentStore} from "../../stores/student";
const studentStore = useStudentStore();

import {useWorkshopStore} from "../../stores/workshops";
const workshopStore = useWorkshopStore();

const percentComplete = computed(() => {
  if (studentStore.student === null) {
    return 0;
  }

  return (studentStore.studentState.totalCompleted / workshopStore.totalExercises) * 100;
});

const totalCompleted = computed(() => {
  if (studentStore.student === null) {
    return 0;
  }

  return studentStore.studentState.totalCompleted;
});

const login = async () => {
  const response = await fetch('/api/student-login-url');
  const data = await response.json();

  if (data.student) {
    //already logged in
    return;
  }

  window.location.href = data.redirect;
}
</script>

<template>
  <div class="grow-0 w-1/3 mx-auto mt-10 bg-gray-800 p-8 rounded-xl">
    <div class="flex justify-between">

      <Button v-if="!studentStore.student" @click="login" class="flex items-center px-2 py-2">
        <GitHubIcon class="h-5 w-5 mr-2" /><span class="text-xs font-normal ">Log In with github</span>
      </Button>

      <div v-cloak v-if="studentStore.student" class="flex items-center space-x-4">
        <img v-if="studentStore.student.profile_picture" class="w-10 h-10 rounded-full" :src="studentStore.student.profile_picture" alt="{{ studentStore.student.name }}">
        <div v-if="!studentStore.student.profile_picture" class="w-10 h-10 rounded-full">
          <UserCircleIcon class="text-pink-600"/>
        </div>

        <div class="font-medium">
          <p class="text-white text-xs">{{ studentStore.student.name }}</p>
          <div class="text-sm text-gray-500 text-xs">Joined in {{ studentStore.student.join_date }}</div>
        </div>
      </div>
      <div class="flex items-center">
          <span class="flex rounded-lg p-2">
            <TrophyIcon class="h-6 w-6 text-yellow-400"/>
          </span>
        <p class="ml-2 text-sm font-medium text-white">{{ totalCompleted }} out of {{ workshopStore.totalExercises }}</p>
      </div>
    </div>

    <div class="w-full rounded-full h-2.5 bg-gray-700 mt-4">
      <div class="h-2.5 rounded-full bg-pink-500" :style="{ 'width': percentComplete + '%' }"></div>
    </div>
  </div>
</template>