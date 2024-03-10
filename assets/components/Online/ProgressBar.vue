<script setup>
import { computed } from "vue";
import { useStudentStore } from "../../stores/student";
const studentStore = useStudentStore();

import { useWorkshopStore } from "../../stores/workshops";
const workshopStore = useWorkshopStore();

const percentComplete = computed(() => {
    let percent = (studentStore.totalCompleted() / workshopStore.totalExercises) * 100;

    //looks weird when the progressbar is smaller
    if (percent < 7) {
        percent = 7;
    }

    return percent;
});
</script>

<template>
  <div class="order-2 md:order-3 flex w-full md:flex-1 items-center lg:w-1/6 lg:flex-none">
    <div class="relative mt-0 flex h-2 flex-1 items-center justify-center rounded-full bg-gray-200 bg-gray-700 md:h-5 z-10">
      <div class="absolute left-0 h-2 rounded-full bg-pink-500 md:h-5 z-0" :style="{ width: percentComplete + '%' }"></div>
      <p class="absolute mx-auto ml-2 hidden items-center font-mono text-xs font-bold text-white md:inline-flex">{{ studentStore.totalCompleted() }}/{{ workshopStore.totalExercises }} Completed</p>
    </div>
    <p class="mx-auto ml-2 mr-2 items-center font-mono text-xs text-white md:hidden">{{ studentStore.totalCompleted() }}/{{ workshopStore.totalExercises }} Completed</p>
  </div>
</template>
