<script setup>
import { computed } from 'vue'
import { useStudentStore } from '../../stores/student'
const studentStore = useStudentStore()

import { useWorkshopStore } from '../../stores/workshops'
const workshopStore = useWorkshopStore()

const percentComplete = computed(() => {
    return (studentStore.totalCompleted() / workshopStore.totalExercises) * 100
})
</script>

<template>
    <div
        class="w-1/6 bg-gray-200 rounded-full h-5 bg-gray-700 mt-0 relative flex justify-center items-center"
    >
        <div
            class="absolute left-0 h-5 rounded-full bg-pink-500"
            :style="{ width: percentComplete + '%' }"
        ></div>
        <p class="absolute inline-flex items-center text-xs font-bold text-white ml-2 mx-auto">
            {{ studentStore.totalCompleted() }} / {{ workshopStore.totalExercises }} completed
        </p>
    </div>
</template>
