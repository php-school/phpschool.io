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
  <div class="flex order-2 w-full lg:w-1/6 items-center">
    <div class="relative mt-0 flex flex-1 h-2 md:h-5 items-center justify-center rounded-full bg-gray-200 bg-gray-700 ">
      <div class="absolute left-0 h-2 md:h-5 rounded-full bg-pink-500" :style="{ width: percentComplete + '%' }"></div>
      <p class="absolute mx-auto ml-2 hidden md:inline-flex items-center text-xs font-bold font-mono text-white">{{ studentStore.totalCompleted() }}/{{ workshopStore.totalExercises }} Completed</p>
    </div>
    <p class="mx-auto ml-2 md:hidden items-center text-xs text-white mr-2 font-mono">{{ studentStore.totalCompleted() }}/{{ workshopStore.totalExercises }} Completed</p>
  </div>
</template>
