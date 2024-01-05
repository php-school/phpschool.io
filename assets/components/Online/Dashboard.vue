<script setup>
import StudentProgress from "./StudentProgress.vue";
import WorkshopExerciseSelectionList from "./WorkshopExerciseSelectionList.vue";
import StudentDropdown from "./StudentDropdown.vue";
import SiteNav from "../Website/SiteNav.vue";
import {ref} from "vue";

const props = defineProps({
    student: {
        type: Object,
        required: false
    },
    totalExercises: Number,
    workshops: Object,
    links: Object
});

const studentState = ref(null);

if (props.student) {
    studentState.value =  {
        totalCompleted: props.student.state.total_completed,
        workshops: props.student.state.workshops,
        completedExercises: {}
    }
}

const resetState = () => {
    return new Promise(async function (resolve, reject) {
        const opts = {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        };
        fetch('/online/reset', opts)
            .then(response => {
                if (response.ok) {

                    studentState.value.totalCompleted = 0;
                    studentState.value.completedExercises = [];
                    studentState.value.workshops = [];

                    resolve();
                }

                reject();
            });
    });
};
</script>

<template>
<!--    <site-nav compact :links="links" :show-login-button="student === undefined">-->
<!--        <template v-slot:nav-after>-->
<!--            <ul v-if="student" class="order-3">-->
<!--                <li>-->
<!--                    <student-dropdown-->
<!--                            :student="student"-->
<!--                            :student-state="studentState"-->
<!--                            :total-exercises='totalExercises'-->
<!--                            :reset-function="resetState"-->
<!--                            :enable-show-tour="false"-->
<!--                    />-->
<!--                </li>-->
<!--            </ul>-->
<!--        </template>-->
<!--    </site-nav>-->
    <section class="flex-1 pb-4 overflow-hidden">
        <div class="container mx-auto flex flex-col overflow-hidden h-full">
            <student-progress
                    :student="student"
                    :student-state="studentState"
                    :total-exercises='totalExercises'>
            </student-progress>

            <workshop-exercise-selection-list
                    :student="student"
                    :student-state="studentState"
                    :workshops="workshops" />
        </div>
    </section>
</template>