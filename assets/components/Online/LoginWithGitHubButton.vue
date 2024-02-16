<script setup>
import GitHubIcon from "../Icons/GitHubIcon.vue";
import PrimaryButton from "../Website/PrimaryButton.vue";

import { useStudentStore } from "../../stores/student";
const studentStore = useStudentStore();

const login = async () => {
  await studentStore.startLogin();
};
</script>
<template>
  <PrimaryButton @click="login" class="flex items-center px-2 py-2">
    <GitHubIcon class="mr-2 h-5 w-5" />
    <span class="flex text-xs font-normal">Log In &nbsp;with github</span>
  </PrimaryButton>
</template>
