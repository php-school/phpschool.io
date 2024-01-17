<script setup>

import {TrophyIcon} from '@heroicons/vue/24/outline'
import {ArrowPathIcon, UserCircleIcon} from '@heroicons/vue/24/solid'
import Confirm from "./Confirm.vue";
import Alert from "./Alert.vue";
import {computed, ref} from "vue";
import {useRoute} from "vue-router";

const route = useRoute();

import {useStudentStore} from "../../stores/student";
const studentStore = useStudentStore();

import {useWorkshopStore} from "../../stores/workshops";
const workshopStore = useWorkshopStore();

const emit = defineEmits(['show-tour']);

const isOpen = ref(false);
const loadingStateReset = ref(false);
const showResetProgressAlert = ref(false);

const resetProgressConfirm = ref(null);

const percentComplete = computed(() => {
    return (studentStore.totalCompleted() / workshopStore.totalExercises) * 100;
});

const clickAway = () => {
    isOpen.value = false;
}

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
}

const resetState = async () => {
    if (loadingStateReset.value) {
        return;
    }

    const ok = await resetProgressConfirm.value.show({
        title: "Resetting progress...",
        message: "All workshop progress will be reset. Are you sure you want to continue?",
        okMessage: "Confirm",
    });

    if (!ok) {
        return;
    }

    loadingStateReset.value = true;

    try {
        await studentStore.resetState();
        showResetProgressAlert.value = true;

        setTimeout(() => {
            showResetProgressAlert.value = false;
        }, 3000)
    } finally {
        loadingStateReset.value = false;
    }
}

const showTour = () => {
    isOpen.value = false;
    studentStore.showTourAgain();
}

const logout = () => {
    studentStore.logout();
}
</script>

<template>
    <alert type="success" @close="showResetProgressAlert = false" v-show="showResetProgressAlert" message="Progress successfully reset"></alert>
    <div class="relative">
        <button @click.stop="toggleDropdown"
                class="hidden sm:flex text-sm bg-gray-800 rounded-full focus:ring-4  focus:ring-gray-600"
                type="button">
            <span class="sr-only">Open user menu</span>
            <img class="w-8 h-8 rounded-full" :src="studentStore.student.profile_picture" alt="{{ studentStore.student.name }}">
        </button>

        <div :class="{'sm:hidden': !isOpen }" v-click-away="clickAway"
             class="block sm:absolute sm:top-10 sm:right-2.5 sm:z-10 sm:w-[250px] divide-solid divide-y divide-gray-600 rounded-lg shadow-xl bg-gray-800">
            <div class="px-6 py-4 text-sm text-white text-left flex flex-row items-center">
                <img class="block sm:hidden w-8 h-8 rounded-full mr-4" :src="studentStore.student.profile_picture" alt="{{ studentStore.student.name }}">
                <div>
                    <div>{{ studentStore.student.name }}</div>
                    <div class="font-medium truncate text-gray-400">{{ studentStore.student.email }}</div>
                </div>
            </div>
            <div class="py-5">
                <div class="px-6 flex justify-between">
                    <div class="flex items-center">
                        <span class="flex rounded-lg p-2">
                          <TrophyIcon class="h-6 w-6 text-yellow-400"/>
                        </span>
                        <p class="ml-2 text-sm font-medium text-gray-500">{{ studentStore.totalCompleted() }} out of {{ workshopStore.totalExercises }}</p>
                    </div>
                </div>
                <div class="px-6">
                    <div class=" rounded-full h-2.5 bg-gray-700 mt-4">
                        <div class=" h-2.5 rounded-full bg-pink-500" :style="{ 'width': percentComplete + '%' }"></div>
                    </div>
                </div>
            </div>

            <ul class="py-2 text-sm text-gray-200">
                <li>
                    <router-link to="/online" class="block text-left no-underline px-6 py-2 hover:bg-gray-600 hover:text-white">Workshop Dashboard</router-link>
                </li>
                <li v-if="route.name === 'editor'">
                    <a href="#" @click="showTour" class="block text-left no-underline px-6 py-2 hover:bg-gray-600 hover:text-white">Show Tour Again</a>
                </li>
                <li>
                    <a href="#" @click="resetState" class="flex justify-between text-left no-underline px-6 py-2 hover:bg-gray-600 hover:text-white">
                        <span>Reset Progress</span>
                        <ArrowPathIcon v-cloak v-show="loadingStateReset" class="w-4 h-4 animate-spin text-pink-500"/>
                    </a>
                </li>
            </ul>
            <confirm ref="resetProgressConfirm"></confirm>

            <div class="py-3">
                <a href="#" @click="logout" class="block text-left no-underline px-6 py-2 text-sm hover:bg-gray-600 text-gray-200 hover:text-white">Sign out</a>
            </div>
        </div>
    </div>
</template>