import { defineStore } from "pinia";

export const useStudentStore = defineStore("student", {
  state: () => ({
    // localStorage doesn't exist while the static pages are rendered
    student: import.meta.env.SSR ? null : JSON.parse(localStorage.getItem("student") ?? "null"),
    forceTour: false,
  }),
  actions: {
    async initialize() {
      const response = await fetch("/api/online/student", {
        headers: {
          "Content-Type": "application/json",
        },
      });

      if (response.status === 401) {
        this.student = null;
        localStorage.removeItem("student");
        return;
      }
      const data = await response.json();

      this.student = data.student;
      localStorage.setItem("student", JSON.stringify(this.student));
    },
    async startLogin() {
      const response = await fetch("/api/online/student/login-url");
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
      const response = await fetch("/api/online/student/login?" + new URLSearchParams({ code, state }));
      const data = await response.json();

      if (response.ok && data.success) {
        this.student = data.student;
        localStorage.setItem("student", JSON.stringify(this.student));
      }

      this.router.push("/online");
    },
    async logout() {
      await fetch("/api/online/student/logout", {
        method: "POST",
      });

      this.student = null;
      localStorage.removeItem("student");

      this.router.push("/online");
    },
    // The student's progress in a workshop, if they have started it.
    progress(workshopCode) {
      return this.student?.state.workshops[workshopCode];
    },
    isWorkshopComplete(workshop) {
      return this.progress(workshop.code)?.completedExercises.length === workshop.exercises.length;
    },
    isExerciseCompleted(workshopCode, exerciseName) {
      return this.progress(workshopCode)?.completedExercises.includes(exerciseName) ?? false;
    },
    async completeExercise() {
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
      const response = await fetch("/api/online/reset", {
        method: "POST",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
        },
      });

      if (!response.ok) {
        throw new Error("Could not reset");
      }

      this.student.state.total_completed = 0;
      this.student.state.workshops = {};

      localStorage.setItem("student", JSON.stringify(this.student));
    },
  },
});
