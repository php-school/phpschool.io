<script setup>
import ConfirmDialog from "./ConfirmDialog.vue";
import { onMounted, ref } from "vue";
import { CubeIcon } from "@heroicons/vue/24/solid";
import Alert from "../Online/SiteAlert.vue";
import { approveWorkshop, newWorkshops } from "./api";
import { useSearch } from "./search";

const props = defineProps({
  search: {
    type: String,
    default: "",
  },
});

const loading = ref(true);
const workshops = ref([]);

onMounted(async () => {
  workshops.value = (await newWorkshops()).workshops;
  loading.value = false;
});

const filteredWorkshops = useSearch(workshops, () => props.search, ["name", "code"]);

const statuses = {
  "Not-approved": "text-gray-400 bg-gray-400/10 ring-gray-400/20",
  Approved: "text-pink-500 bg-pink-500/10 ring-pink-500/30",
};

const currentlyApproving = ref(null);
const showApproveSuccess = ref(false);
const approveSuccess = ref("");

const showApproveError = ref(false);
const approveError = ref("");

const confirmApprove = (workshop) => {
  currentlyApproving.value = workshop;
};

const doApproveWorkshop = async () => {
  try {
    await approveWorkshop(currentlyApproving.value.id);

    const approveId = currentlyApproving.value.id;

    approveSuccess.value = "Successfully approved: " + currentlyApproving.value.name + "  and regenerated workshop feed";
    showApproveSuccess.value = true;

    workshops.value = workshops.value.filter((workshop) => workshop.id !== approveId);
  } catch (error) {
    if (error.message) {
      approveError.value = error.message;
    }
    showApproveError.value = true;
  } finally {
    currentlyApproving.value = null;
  }
};
</script>

<template>
  <!-- approve alerts -->
  <alert type="error" :message="approveError || 'An error occurred. Please try again later.'" :timeout="4000" :show="showApproveError" @close="showApproveError = false"></alert>
  <alert type="success" :message="approveSuccess" :timeout="4000" :show="showApproveSuccess" @close="showApproveSuccess = false"></alert>

  <header class="flex items-center justify-between border-b border-pink-600/30 px-4 py-4 sm:px-6 sm:py-6 lg:px-8">
    <h1 class="text-base leading-7 font-semibold text-white">New Workshop Requests</h1>
  </header>

  <div v-if="loading === false && workshops.length === 0" class="mt-8 text-center">
    <CubeIcon class="mx-auto h-12 w-12 text-gray-400" aria-hidden="true" />
    <h3 class="mt-2 text-base text-gray-500">No new workshop requests</h3>
  </div>

  <ul role="list" class="divide-y divide-pink-600/30">
    <li v-for="workshop in filteredWorkshops" :key="workshop.id" class="relative flex items-center gap-x-4 px-4 py-4 sm:px-6 lg:px-8">
      <div class="min-w-0 flex-auto">
        <div class="flex items-center gap-x-3">
          <div :class="[statuses[workshop.status], 'flex-none rounded-full p-1']">
            <div class="h-2 w-2 rounded-full bg-current" />
          </div>
          <h2 class="min-w-0 text-sm leading-6 font-semibold text-white">
            <span class="flex gap-x-2">
              <span class="truncate">{{ workshop.name }}</span>
              <span class="text-gray-400">/</span>
              <span class="whitespace-nowrap">{{ workshop.code }}</span>
            </span>
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
          <p class="whitespace-nowrap text-white">{{ workshop.description }}</p>
        </div>
      </div>
      <div :class="[statuses[workshop.status], 'flex-none rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset']">
        {{ workshop.status }}
      </div>

      <button
        @click="confirmApprove(workshop)"
        type="button"
        class="rounded-md bg-green-500 px-3.5 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-green-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-500 focus-visible:outline-solid"
      >
        Approve
      </button>
    </li>
  </ul>

  <ConfirmDialog :show="currentlyApproving !== null" title="Approve workshop" confirm-label="Approve" @confirm="doApproveWorkshop" @close="currentlyApproving = null">
    Are you sure you want to approve "{{ currentlyApproving?.name }}"?
  </ConfirmDialog>
</template>
