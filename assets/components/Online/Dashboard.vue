<script setup>
import StudentProgress from "./StudentProgress.vue";
import WorkshopExerciseSelectionList from "./WorkshopExerciseSelectionList.vue";
import {onMounted, ref} from "vue";
import {useRoute} from "vue-router";
import {useStudentStore} from "../../stores/student";

const props = defineProps({
    totalExercises: Number,
    workshops: Object,
    links: Object
});

const studentStore = useStudentStore();

onMounted(async () => {
    const route = useRoute();

    if (route.query.code && route.query.state) {
        await studentStore.finishLogin(
            route.query.code,
            route.query.state
        )
    }
})
</script>

<template>
    <section class="flex-1 pb-4 overflow-hidden">
        <div class="container mx-auto flex flex-col overflow-hidden h-full">
            <student-progress></student-progress>

            <workshop-exercise-selection-list></workshop-exercise-selection-list>
        </div>
    </section>
</template>