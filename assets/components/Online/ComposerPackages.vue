<script setup>
import Modal from "./ModalDialog.vue";
import { ArrowPathIcon, CircleStackIcon, XMarkIcon } from "@heroicons/vue/24/solid";
import PackageSearch from "./PackageSearch.vue";
import { ref } from "vue";
import Alert from "./SiteAlert.vue";

const props = defineProps({
  open: Boolean,
  composerDeps: Array,
});

const emit = defineEmits(["close", "package-added", "package-removed"]);
const newDependency = ref("");
const loadingComposerAdd = ref(false);
const packageSearchRef = ref(null);

const packageSelected = (composerPackage) => {
  newDependency.value = composerPackage;
};

const addError = ref("");
const showAddError = ref(false);
const failed = (message) => {
  addError.value = message;
  showAddError.value = true;
};

const addDependency = async () => {
  const name = newDependency.value;

  if (!props.composerDeps.some((dep) => dep.name === name)) {
    loadingComposerAdd.value = true;

    try {
      const response = await fetch("/api/online/composer-package/add?package=" + encodeURIComponent(name), {
        headers: { Accept: "application/json" },
      });
      const json = await response.json();

      if (json.status === "error") {
        failed("Package could not be added because it has no tagged version.");
      } else {
        emit("package-added", { name, version: json.latest_version, versions: json.versions });
      }
    } catch {
      failed("Package could not be added. Please try again.");
    } finally {
      loadingComposerAdd.value = false;
    }
  }

  newDependency.value = "";
  packageSearchRef.value.reset();
};

const removeDependency = (packageName) => {
  const dep = props.composerDeps.find((dep) => dep.name === packageName);

  if (dep) {
    emit("package-removed", dep);
  }
};
</script>

<template>
  <alert type="error" @close="showAddError = false" :show="showAddError" :timeout="4000" :message="addError"></alert>

  <Transition
    enter-active-class="transition-opacity duration-100 ease-in"
    leave-active-class="transition-opacity duration-200 ease-in"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <Modal size="sm" max-height="max-h-1/2" v-if="open" @close="emit('close')" scrollContent>
      <template #header>
        <div class="flex items-center">
          <CircleStackIcon class="mr-2 h-6 w-6 text-pink-500" />
          <h3 class="mt-0 pt-0 font-mono text-base font-semibold text-white lg:text-xl">Composer Dependencies</h3>
        </div>
      </template>
      <template #body>
        <div class="flex items-center justify-between">
          <PackageSearch ref="packageSearchRef" @package-selected="packageSelected" v-model="newDependency" class="w-full"></PackageSearch>
          <button
            :disabled="newDependency === ''"
            @click.stop="addDependency"
            type="button"
            class="ml-3 inline-flex h-9 w-16 items-center justify-center rounded-full border border-transparent bg-pink-600 px-4 py-2 text-base text-sm font-medium text-white shadow-xs hover:bg-pink-700 focus:ring-3 focus:ring-pink-800 focus:outline-hidden disabled:opacity-70 disabled:hover:bg-pink-600"
          >
            <ArrowPathIcon v-cloak v-show="loadingComposerAdd" class="h-4 w-4 animate-spin" />
            <span v-if="!loadingComposerAdd">Add</span>
          </button>
        </div>
        <ul v-show="composerDeps.length > 0" class="mt-4 overflow-y-scroll">
          <li v-for="dep in composerDeps" :key="dep.name" class="mb-2 flex items-center pl-2 text-white">
            <p class="text-base">{{ dep.name }}</p>
            <p class="ml-2 rounded-sm bg-gray-900 px-2 py-1">{{ dep.version }}</p>
            <XMarkIcon @click.stop="removeDependency(dep.name)" class="ml-2 h-5 w-5 cursor-pointer text-zinc-400 hover:text-pink-600" />
          </li>
        </ul>
        <div v-show="composerDeps.length === 0" class="pt-6">
          <p class="text-sm text-white">You currently have no dependencies.</p>
        </div>
      </template>

      <template #footer>
        <div class="flex justify-end">
          <button
            @click="emit('close')"
            type="button"
            class="inline-flex w-full items-center justify-center rounded-full border border-transparent bg-pink-600 px-8 py-2 text-base font-medium text-white shadow-xs hover:bg-pink-700 focus:ring-3 focus:ring-pink-800 focus:outline-hidden sm:ml-3 sm:w-auto sm:text-sm"
          >
            Close
          </button>
        </div>
      </template>
    </Modal>
  </Transition>
</template>
