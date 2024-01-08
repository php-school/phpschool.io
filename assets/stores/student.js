import { defineStore } from 'pinia';
import { useRouter } from 'vue-router'

export const useStudentStore = defineStore({
    id: 'student',
    state: () => {
        let student = localStorage.getItem('student');

        if (student === null) {
            return {
                student: null,
                studentState: {
                    totalCompleted: 0,
                    workshops: [],
                    completedExercises: []
                }
            }
        }

        student = JSON.parse(student);

        return {
            student: student,
            studentState: {
                totalCompleted: student.state.total_completed,
                workshops: student.state.workshops,
                completedExercises: []
            }
        }
    },
    actions: {
        async initialize() {
            const response = await fetch('/api/student', {
                headers: {
                    'Content-Type': 'application/json'
                },
            });

            const data = await response.json();

            if (response.status === 401) {
                this.student = null;
                localStorage.removeItem('student');
                return;
            }

            this.student = data.student;
            localStorage.setItem('student', JSON.stringify(this.student));
        },
        async finishLogin(code, state) {
            //redirect after auth from GH
            //finish the flow on the server
            const response = await fetch('/api/student-login?' + new URLSearchParams({ code, state }));
            const data = await response.json();
            if (response.ok && data.success) {
                this.student = data.student;

                this.studentState =  {
                    totalCompleted: this.student.state.total_completed,
                    workshops: this.student.state.workshops,
                    completedExercises: {}
                }
            }

            this.router.push('/online');
        },
        async logout() {

        },
        totalCompleted() {
            return this.student.state.total_completed;
        },
        tourComplete() {
            return this.student.tour_complete;
        },
        resetState() {
            return new Promise(async function (resolve, reject) {
                const opts = {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                };


                const response = await fetch('/online/reset', opts);

                if (response.ok) {
                    this.studentState.value.totalCompleted = 0;
                    this.studentState.value.completedExercises = [];
                    this.studentState.value.workshops = [];

                    return resolve();
                }

                return reject();
            });
        }
    }
});