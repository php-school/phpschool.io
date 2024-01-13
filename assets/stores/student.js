import { defineStore } from 'pinia';

export const useStudentStore = defineStore({
    id: 'student',
    state: () => {
        let student = localStorage.getItem('student');

        if (student === null) {
            return {
                student: null,
                forceTour: false
            }
        }

        student = JSON.parse(student);

        return {
            student,
            forceTour: false
        }
    },
    actions: {
        async initialize() {
            const response = await fetch('/api/online/student', {
                headers: {
                    'Content-Type': 'application/json'
                },
            });

            if (response.status === 401) {
                this.student = null;
                this.studentState = null;
                localStorage.removeItem('student');
                return;
            }
            const data = await response.json();

            this.student = data.student;
            localStorage.setItem('student', JSON.stringify(this.student));
        },
        async startLogin() {
            const response = await fetch('/api/online/student/login-url');
            const data = await response.json();

            if (data.student) {
                //already logged in
                return;
            }

            window.location.href = data.redirect;
        },
        async finishLogin(code, state) {
            //redirect after auth from GH
            //finish the flow on the server
            const response = await fetch('/api/online/student/login?' + new URLSearchParams({ code, state }));
            const data = await response.json();

            if (response.ok && data.success) {
                this.student = data.student;

                this.studentState =  {
                    totalCompleted: data.student.state.total_completed,
                    workshops: data.student.state.workshops,
                }

                localStorage.setItem('student', JSON.stringify(this.student));
            }

            this.router.push('/online');
        },
        async logout() {
            const response = await fetch('/api/online/student/logout', { method: 'POST'});

            this.student = null;
            this.studentState = null;
            localStorage.removeItem('student');

            this.router.push('/online');
        },
        isWorkshopComplete(workshop) {
            if (this.student === null) {
                return false;
            }

            if (!this.student.state.workshops.hasOwnProperty(workshop.code)) {
                return false;
            }

            const completedExercises = this.student.state.workshops[workshop.code].completedExercises;
            return workshop.exercises.length === completedExercises.length;
        },
        isExerciseCompleted(workshopCode, exerciseName) {
            if (this.student === null) {
                return false;
            }

            if (this.student.state.workshops[workshopCode] === undefined) {
                return false;
            }

            return this.student.state.workshops[workshopCode].completedExercises.includes(exerciseName);
        },
        async completeExercise(workshopCode, exerciseName) {
            await this.initialize();
        },
        totalCompleted() {
            return this.student.state.total_completed;
        },
        tourComplete() {
            return this.student.tour_complete;
        },
        showTourAgain() {
            this.forceTour = true;
        },
        async resetState() {
            const response = await fetch('/api/online/reset', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Could not reset');
            }

            this.student.state.total_completed = 0;
            this.student.state.workshops = [];

            localStorage.setItem('student', JSON.stringify(this.student));
        }
    }
});