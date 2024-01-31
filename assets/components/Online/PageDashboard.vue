<script setup>
import StudentProgress from "./StudentProgress.vue";
import WorkshopExerciseSelectionList from "./WorkshopExerciseSelectionList.vue";
import { onMounted } from "vue";
import { useRoute } from "vue-router";
import { useStudentStore } from "../../stores/student";

defineProps({
  totalExercises: Number,
  workshops: Object,
  links: Object,
});

const studentStore = useStudentStore();

onMounted(async () => {
  const route = useRoute();

  if (route.query.code && route.query.state) {
    await studentStore.finishLogin(route.query.code, route.query.state);
  }
});
</script>

<template>
  <section class="flex-1 overflow-y-scroll pb-4 md:overflow-hidden">
    <div class="container mx-auto flex flex-col overflow-hidden md:h-full">
      <student-progress></student-progress>

      <workshop-exercise-selection-list></workshop-exercise-selection-list>
    </div>
  </section>
</template>
