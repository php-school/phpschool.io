<script setup>
import ConfirmDialog from "./ConfirmDialog.vue";
import Alert from "../Online/SiteAlert.vue";
import { onMounted, ref } from "vue";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import { EllipsisVerticalIcon, ArrowUpIcon, TrashIcon } from "@heroicons/vue/24/outline";

import { allWorkshops, regenerateWorkshopFeed, deleteWorkshop, promoteWorkshop } from "./api";
import { useSearch } from "./search";

const props = defineProps({
  search: {
    type: String,
    default: "",
  },
});

const workshops = ref([]);

const filteredWorkshops = useSearch(workshops, () => props.search, ["name", "code"]);

onMounted(async () => {
  const data = await allWorkshops();
  workshops.value = data.workshops;
});

const statuses = {
  "Not-approved": "text-gray-400 bg-gray-400/10 ring-gray-400/20",
  Approved: "text-pink-500 bg-pink-500/10 ring-pink-500/30",
};

const types = {
  Core: "text-green-400 bg-green-400/10 ring-green-400/30",
  Community: "text-sky-400 bg-sky-400/10 ring-sky-500/30",
};

const currentlyDeleting = ref(null);
const showDeleteSuccess = ref(false);
const deleteSuccess = ref("");

const showDeleteError = ref(false);
const deleteError = ref("");

const confirmDeleteWorkshop = (workshop) => {
  currentlyDeleting.value = workshop;
};

const doDeleteWorkshop = async () => {
  try {
    await deleteWorkshop(currentlyDeleting.value.id);

    const deletedId = currentlyDeleting.value.id;

    deleteSuccess.value = "Successfully removed: " + currentlyDeleting.value.name + "  and regenerated workshop feed";
    showDeleteSuccess.value = true;

    workshops.value = workshops.value.filter((workshop) => workshop.id !== deletedId);
  } catch (error) {
    currentlyDeleting.value = null;

    if (error.message) {
      deleteError.value = error.message;
    }
    showDeleteError.value = true;
  } finally {
    currentlyDeleting.value = null;
  }
};

const currentlyPromoting = ref(null);
const showPromoteSuccess = ref(false);
const promoteSuccess = ref("");

const showPromoteError = ref(false);
const promoteError = ref("");

const confirmPromoteWorkshop = (workshop) => {
  currentlyPromoting.value = workshop;
};

const doPromoteWorkshop = async () => {
  try {
    await promoteWorkshop(currentlyPromoting.value.id);

    promoteSuccess.value = "Successfully promoted: " + currentlyPromoting.value.name + "  and regenerated workshop feed";
    currentlyPromoting.value.type = "Core";
    showPromoteSuccess.value = true;
  } catch (error) {
    if (error.message) {
      promoteError.value = error.message;
    }
    showPromoteError.value = true;
  } finally {
    currentlyPromoting.value = null;
  }
};

const showRegenerateSuccess = ref(false);
const regenerateError = ref("");
const showRegenerateError = ref(false);

const regenerateFeed = async () => {
  try {
    await regenerateWorkshopFeed();
    showRegenerateSuccess.value = true;
  } catch (error) {
    if (error.message) {
      regenerateError.value = error.message;
    }
    showRegenerateError.value = true;
  }
};
</script>

<template>
  <!-- delete alerts -->
  <alert type="error" :message="deleteError || 'An error occurred. Please try again later.'" :timeout="4000" :show="showDeleteError" @close="showDeleteError = false"></alert>
  <alert type="success" :message="deleteSuccess" :timeout="4000" :show="showDeleteSuccess" @close="showDeleteSuccess = false"></alert>

  <!-- promote alerts -->
  <alert type="error" :message="promoteError || 'An error occurred. Please try again later.'" :timeout="4000" :show="showPromoteError" @close="showPromoteError = false"></alert>
  <alert type="success" :message="promoteSuccess" :timeout="4000" :show="showPromoteSuccess" @close="showPromoteSuccess = false"></alert>

  <!-- regenerate alerts -->
  <alert type="error" :message="regenerateError || 'An error occurred. Please try again later.'" :timeout="4000" :show="showRegenerateError" @close="showRegenerateError = false"></alert>
  <alert type="success" message="Successfully regenerated workshop feed" :timeout="4000" :show="showRegenerateSuccess" @close="showRegenerateSuccess = false"></alert>

  <header class="flex items-center justify-between border-b border-pink-600/30 px-4 py-4 sm:px-6 sm:py-6 lg:px-8">
    <h1 class="text-base leading-7 font-semibold text-white">All Workshops</h1>
    <button
      @click="regenerateFeed"
      type="button"
      class="flex-none rounded-md bg-pink-500/10 px-2 py-1.5 text-xs font-medium text-pink-500 ring-1 ring-pink-500/30 ring-inset hover:text-pink-600 hover:ring-pink-600/30"
    >
      Regenerate Feed
    </button>
  </header>

  <ul role="list" class="divide-y divide-pink-600/30">
    <li v-for="workshop in filteredWorkshops" :key="workshop.id" class="relative flex items-center gap-x-4 px-4 py-4 sm:px-6 lg:px-8">
      <div class="min-w-0 flex-auto">
        <div class="flex items-center gap-x-3">
          <div :class="[statuses[workshop.status], 'flex-none rounded-full p-1']">
            <div class="h-2 w-2 rounded-full bg-current" />
          </div>
          <h2 class="min-w-0 text-sm leading-6 font-semibold text-white">
            <router-link :to="'/admin/workshop/' + workshop.id" class="flex gap-x-2">
              <span class="truncate">{{ workshop.name }}</span>
              <span class="text-gray-400">/</span>
              <span class="whitespace-nowrap">{{ workshop.code }}</span>
            </router-link>
          </h2>
        </div>
        <div class="mt-3 flex items-center gap-x-2.5 text-xs leading-5 text-gray-400">
          <a :href="workshop.repo_url" target="_blank" class="text-blue-600 hover:cursor-pointer hover:underline">
            {{ workshop.repo_url }}
          </a>
          <svg viewBox="0 0 2 2" class="h-0.5 w-0.5 flex-none fill-gray-300">
            <circle cx="1" cy="1" r="1" />
          </svg>
          <p class="whitespace-nowrap">{{ workshop.submitter_email }}</p>
        </div>
        <div class="mt-3 flex items-center gap-x-2.5 text-xs leading-5 text-gray-400">
          <div :class="[types[workshop.type], 'flex-none rounded-full px-2 py-0.5 text-[10px] font-medium ring-1 ring-inset']">
            {{ workshop.type }}
          </div>
          <svg viewBox="0 0 2 2" class="h-0.5 w-0.5 flex-none fill-gray-300">
            <circle cx="1" cy="1" r="1" />
          </svg>
          <p class="whitespace-nowrap">{{ workshop.installs }} downloads</p>
        </div>
      </div>
      <div :class="[statuses[workshop.status], 'flex-none rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset']">
        {{ workshop.status }}
      </div>
      <Menu as="div" class="relative inline-block text-left">
        <MenuButton class="-m-2.5 block p-2.5 text-gray-500 hover:text-gray-200">
          <span class="sr-only">Open options</span>
          <EllipsisVerticalIcon class="h-5 w-5" aria-hidden="true" />
        </MenuButton>

        <transition
          enter-active-class="transition ease-out duration-100"
          enter-from-class="transform opacity-0 scale-95"
          enter-to-class="transform opacity-100 scale-100"
          leave-active-class="transition ease-in duration-75"
          leave-from-class="transform opacity-100 scale-100"
          leave-to-class="transform opacity-0 scale-95"
        >
          <MenuItems class="absolute right-0 z-10 mt-2 w-48 origin-top-right divide-y divide-gray-700 rounded-md bg-gray-800 p-2 shadow-lg ring-1 ring-pink-500/50 focus:outline-hidden">
            <MenuItem v-if="workshop.type === 'Community'" v-slot="{ active }">
              <button @click="confirmPromoteWorkshop(workshop)" :class="[active ? 'p-2 text-white' : 'text-slate-400', 'group flex w-full items-center px-4 py-2 text-sm']">
                <ArrowUpIcon class="mr-3 h-5 w-5" aria-hidden="true" />
                Promote to Core
                <span class="sr-only">, {{ workshop.code }}</span>
              </button>
            </MenuItem>
            <MenuItem v-slot="{ active }">
              <button @click="confirmDeleteWorkshop(workshop)" :class="[active ? 'p-2 text-white' : 'text-slate-400', 'group flex w-full items-center px-4 py-2 text-sm']">
                <TrashIcon class="mr-3 h-5 w-5" aria-hidden="true" />
                Delete
                <span class="sr-only">, {{ workshop.code }}</span>
              </button>
            </MenuItem>
          </MenuItems>
        </transition>
      </Menu>
    </li>
  </ul>

  <ConfirmDialog :show="currentlyDeleting !== null" title="Delete workshop" confirm-label="Delete" danger @confirm="doDeleteWorkshop" @close="currentlyDeleting = null">
    Are you sure you want to delete "{{ currentlyDeleting?.name }}". This action cannot be undone.
  </ConfirmDialog>

  <ConfirmDialog :show="currentlyPromoting !== null" title="Promote workshop" confirm-label="Promote" @confirm="doPromoteWorkshop" @close="currentlyPromoting = null">
    Are you sure you want to promote "{{ currentlyPromoting?.name }}" to Core?
  </ConfirmDialog>
</template>
