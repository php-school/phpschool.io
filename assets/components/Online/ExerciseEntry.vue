<script setup>
import { CheckCircleIcon } from "@heroicons/vue/24/solid";
import { ArrowRightCircleIcon } from "@heroicons/vue/24/outline";
import { computed } from "vue";

import { useRouter } from "vue-router";

import { useStudentStore } from "../../stores/student";
const studentStore = useStudentStore();

const props = defineProps({
  selectedWorkshop: Object,
  exercise: Object,
});

const router = useRouter();

const emit = defineEmits(["not-logged-in"]);

const selectExercise = () => {
  if (!studentStore.student) {
    emit("not-logged-in");
    return;
  }

  router.push("/online/editor/" + props.selectedWorkshop.code + "/" + props.exercise.slug);
};

const isCurrentExerciseComplete = computed(() => {
  return studentStore.isExerciseCompleted(props.selectedWorkshop.code, props.exercise.name);
});

const isNextWorkshop = computed(() => {
  const pos = props.selectedWorkshop.exercises.findIndex((e) => e.name === props.exercise.name);

  if (pos - 1 in props.selectedWorkshop.exercises) {
    const prevComplete = studentStore.isExerciseCompleted(props.selectedWorkshop.code, props.selectedWorkshop.exercises[pos - 1].name);

    const thisComplete = studentStore.isExerciseCompleted(props.selectedWorkshop.code, props.exercise.name);

    return prevComplete && !thisComplete;
  }

  return true;
});
</script>

<template>
  <li @click="selectExercise" class="group flex flex-row hover:bg-gray-600 last:hover:rounded-b-lg">
    <div class="flex flex-1 cursor-pointer select-none items-center p-4">
      <div class="mr-4 flex h-10 w-10 flex-col items-center justify-center">
        <a href="#" class="relative block">
          <img alt="workshop" src="../../img/cloud/core-workshops.png" class="mx-auto h-10 w-10 object-cover" />
        </a>
      </div>
      <div class="mr-16 flex-1 pl-1 text-white group-hover:text-pink-600">
        <div class="font-medium">{{ exercise.name }}</div>
        <div class="text-xs text-gray-300">{{ exercise.description }}</div>
      </div>
      <div class="rounded-full bg-pink-600 px-3 py-1 text-xs text-gray-200">
        {{ exercise.type }}
      </div>
      <a href="#" class="flex w-24 justify-end text-right">
        <CheckCircleIcon v-if="isCurrentExerciseComplete" class="h-8 w-8 rounded-full border-2 border-solid border-pink-300 text-pink-500" />
        <ArrowRightCircleIcon v-else class="h-8 w-8 rounded-full border-2 border-solid border-pink-500 !fill-none text-pink-200" :class="{ 'animate-bounce': isNextWorkshop }" />
      </a>
    </div>
  </li>
</template>
