<script setup>

import {computed, onMounted, ref} from "vue";
import {allStudents} from "./api";

const props = defineProps({
    search: {
        type: String,
        default: '',
    }
});

const students = ref([]);

onMounted(async () => {
    students.value = await allStudents()
});

const filteredStudents = computed(() => {
    if (props.search === '' || props.search === null) {
        return students.value;
    }

    return students.value.filter((student) => {
        return student.name.toLowerCase().includes(props.search.toLowerCase())
            || student.email.toLowerCase().includes(props.search.toLowerCase());
    });
});
</script>

<template>
    <header class="flex items-center justify-between border-b border-pink-600/30 px-4 py-4 sm:px-6 sm:py-6 lg:px-8">
        <h1 class="text-base font-semibold leading-7 text-white">All Students</h1>
    </header>


    <ul role="list" class="divide-y divide-pink-600/30">
        <li v-for="student in filteredStudents" :key="student.email" class="flex justify-between items-center space-x-4 px-4 py-4 sm:px-6 lg:px-8">
            <div class="flex min-w-0 gap-x-4">
                <img class="h-12 w-12 flex-none rounded-full bg-gray-800" :src="student.profile_picture" alt="" />
                <div class="min-w-0 flex-auto">
                    <p class="text-sm font-semibold leading-6 text-white">{{ student.name }}</p>
                    <p class="mt-1 truncate text-xs leading-5 text-gray-400">{{ student.email }}</p>
                </div>
            </div>
            <div class="hidden shrink-0 sm:flex sm:flex-col sm:items-end">
                <p class="text-sm leading-6 text-white">{{ student.location }}</p>
                <p v-if="student.join_date" class="mt-1 text-xs leading-5 text-gray-400">
                    Last seen <time :datetime="student.join_date">{{ student.join_date }}</time>
                </p>
                <div v-else class="mt-1 flex items-center gap-x-1.5">
                    <div class="flex-none rounded-full bg-emerald-500/20 p-1">
                        <div class="h-1.5 w-1.5 rounded-full bg-emerald-500" />
                    </div>
                    <p class="text-xs leading-5 text-gray-400">Online</p>
                </div>
            </div>
        </li>
    </ul>
</template>
