import { defineStore } from 'pinia'

const useWorkshopStore = defineStore('workshops', {
    state: () => ({
        workshops: [],
        totalExercises: 0
    }),
    actions: {
        async initialize() {
            if (this.workshops.length > 0) {
                return
            }

            try {
                const response = await fetch(import.meta.env.VITE_API_URL + '/api/online/workshops')
                const data = await response.json()

                this.workshops = data.workshops
                this.totalExercises = data.totalExercises
            } catch (error) {
                console.error('Error fetching workshops:', error)
                throw error
            }
        },
        getWorkshop(code) {
            return this.workshops.find((workshop) => workshop.code === code)
        },
        getExercise(workshopCode, exerciseSlug) {
            const workshop = this.getWorkshop(workshopCode)

            if (!workshop) {
                return null
            }

            return workshop.exercises.find((exercise) => exercise.slug === exerciseSlug)
        },
        findNextExercise(workshopCode, exerciseSlug) {
            const workshop = this.getWorkshop(workshopCode)

            if (!workshop) {
                return null
            }

            const exercise = workshop.exercises.find((exercise) => exercise.slug === exerciseSlug)

            if (!exercise) {
                return null
            }

            const exerciseIndex = workshop.exercises.indexOf(exercise)

            if (workshop.exercises[exerciseIndex + 1] !== undefined) {
                return workshop.exercises[exerciseIndex + 1]
            }

            return null
        }
    }
})

export { useWorkshopStore }
