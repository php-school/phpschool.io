<script setup>
import { TrophyIcon } from "@heroicons/vue/24/outline";
import { ArrowPathIcon } from "@heroicons/vue/24/solid";
import Confirm from "./ConfirmDialog.vue";
import Alert from "./SiteAlert.vue";
import { computed, ref } from "vue";
import { useRoute } from "vue-router";

const route = useRoute();

import { useStudentStore } from "../../stores/student";
const studentStore = useStudentStore();

import { useWorkshopStore } from "../../stores/workshops";
const workshopStore = useWorkshopStore();

const isOpen = ref(false);
const loadingStateReset = ref(false);
const showResetProgressAlert = ref(false);

const resetProgressConfirm = ref(null);

const percentComplete = computed(() => {
  return (studentStore.totalCompleted() / workshopStore.totalExercises) * 100;
});

const clickAway = () => {
  isOpen.value = false;
};

const toggleDropdown = () => {
  isOpen.value = !isOpen.value;
};

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
  } finally {
    loadingStateReset.value = false;
  }
};

const showTour = () => {
  isOpen.value = false;
  studentStore.showTourAgain();
};

const logout = () => {
  studentStore.logout();
};
</script>

<template>
  <alert
    type="success"
    @close="showResetProgressAlert = false"
    :show="showResetProgressAlert"
    :timeout="4000"
    message="Progress successfully reset"
  ></alert>
  <div class="relative">
    <button
      @click.stop="toggleDropdown"
      class="hidden rounded-full bg-gray-800 text-sm focus:ring-4 focus:ring-gray-600 sm:flex"
      type="button"
    >
      <span class="sr-only">Open user menu</span>
      <img
        class="h-8 w-8 rounded-full"
        :src="studentStore.student.profile_picture"
        alt="{{ studentStore.student.name }}"
      />
    </button>

    <div
      :class="{ 'sm:hidden': !isOpen }"
      v-click-away="clickAway"
      class="block divide-y divide-solid divide-gray-600 rounded-lg bg-gray-800 shadow-xl sm:absolute sm:right-2.5 sm:top-10 sm:z-10 sm:w-[250px]"
    >
      <div class="flex flex-row items-center px-6 py-4 text-left text-sm text-white">
        <img
          class="mr-4 block h-8 w-8 rounded-full sm:hidden"
          :src="studentStore.student.profile_picture"
          alt="{{ studentStore.student.name }}"
        />
        <div>
          <div>{{ studentStore.student.name }}</div>
          <div class="truncate font-medium text-gray-400">
            {{ studentStore.student.email }}
          </div>
        </div>
      </div>
      <div class="py-5">
        <div class="flex justify-between px-6">
          <div class="flex items-center">
            <span class="flex rounded-lg p-2">
              <TrophyIcon class="h-6 w-6 text-yellow-400" />
            </span>
            <p class="ml-2 text-sm font-medium text-gray-500">
              {{ studentStore.totalCompleted() }} out of
              {{ workshopStore.totalExercises }}
            </p>
          </div>
        </div>
        <div class="px-6">
          <div class="mt-4 h-2.5 rounded-full bg-gray-700">
            <div class="h-2.5 rounded-full bg-pink-500" :style="{ width: percentComplete + '%' }"></div>
          </div>
        </div>
      </div>

      <ul class="py-2 text-sm text-gray-200">
        <li>
          <router-link to="/online" class="block px-6 py-2 text-left no-underline hover:bg-gray-600 hover:text-white">
            Workshop Dashboard
          </router-link>
        </li>
        <li v-if="route.name === 'editor'">
          <a
            href="#"
            @click="showTour"
            class="block px-6 py-2 text-left no-underline hover:bg-gray-600 hover:text-white"
          >
            Show Tour Again
          </a>
        </li>
        <li>
          <a
            href="#"
            @click="resetState"
            class="flex justify-between px-6 py-2 text-left no-underline hover:bg-gray-600 hover:text-white"
          >
            <span>Reset Progress</span>
            <ArrowPathIcon v-cloak v-show="loadingStateReset" class="h-4 w-4 animate-spin text-pink-500" />
          </a>
        </li>
      </ul>
      <confirm ref="resetProgressConfirm"></confirm>

      <div class="py-3">
        <a
          href="#"
          @click="logout"
          class="block px-6 py-2 text-left text-sm text-gray-200 no-underline hover:bg-gray-600 hover:text-white"
        >
          Sign out
        </a>
      </div>
    </div>
  </div>
</template>
