<script setup>
import { computed } from "vue";
import { useStudentStore } from "../../stores/student";
const studentStore = useStudentStore();

import { useWorkshopStore } from "../../stores/workshops";
const workshopStore = useWorkshopStore();

const percentComplete = computed(() => {
  return (studentStore.totalCompleted() / workshopStore.totalExercises) * 100;
});
</script>

<template>
  <div class="relative mt-0 flex h-5 w-full lg:w-1/6 items-center justify-center rounded-full bg-gray-200 bg-gray-700">
    <div class="absolute left-0 h-5 rounded-full bg-pink-500" :style="{ width: percentComplete + '%' }"></div>
    <p class="absolute mx-auto ml-2 inline-flex items-center text-xs font-bold text-white">{{ studentStore.totalCompleted() }} / {{ workshopStore.totalExercises }} completed</p>
  </div>
</template>
