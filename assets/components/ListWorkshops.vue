<script>
import StudentProgress from "./StudentProgress.vue";
import WorkshopExerciseSelectionList from "./WorkshopExerciseSelectionList.vue";
import HeaderNav from "./HeaderNav.vue";
import StudentDropdown from "./StudentDropdown.vue";

export default {
    components: {
        HeaderNav,
        WorkshopExerciseSelectionList,
        StudentProgress,
        StudentDropdown
    },
    props: {
        student: {
            type: Object,
            required: false
        },
        totalExercises: Number,
        workshops: Object,
        links: Object
    },
    data() {
        let studentState = null;

        if (this.student) {
            studentState =  {
                totalCompleted: this.student.state.total_completed,
                workshops: this.student.state.workshops,
                completedExercises: {}
            }
        }

        return {
            studentState: studentState,
        }
    },
    methods: {
        forceTour() {

        },
        resetState() {
            const studentState = this.studentState;

            return new Promise(async function (resolve, reject) {
                const opts = {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                };
                fetch('/cloud/reset', opts)
                    .then(response => {
                        if (response.ok) {

                            studentState.totalCompleted = 0;
                            studentState.completedExercises = [];
                            studentState.workshops = [];

                            resolve();
                        }

                        reject();
                    });
            });
        },
    }
}

</script>

<template>
    <header-nav :links="links">
        <template v-slot:nav-after>
            <ul v-if="student" class="order-3">
                <li>
                    <student-dropdown
                            @show-tour="$emit('show-tour')"
                            :student="student"
                            :student-state="studentState"
                            :total-exercises='totalExercises'
                            :reset-function="resetState"
                            :enable-show-tour="false"
                    />
                </li>
            </ul>
        </template>
    </header-nav>

    <section id="app" class="site-body h-full">
        <div class="container flex flex-col h-full">
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