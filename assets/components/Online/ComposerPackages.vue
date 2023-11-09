<script setup>
import Modal from "./Modal.vue";
import {ArrowPathIcon, CircleStackIcon, XMarkIcon} from "@heroicons/vue/24/solid";
import PackageSearch from "./PackageSearch.vue";
import {ref} from "vue";
import Alert from "./Alert.vue";

const props = defineProps({
    open: Boolean,
    composerDeps: Array,
});

const emit = defineEmits(['close', 'package-added', 'package-removed']);
const newDependency = ref('');
const loadingComposerAdd = ref(false);
const showPackageAddError = ref(false);
const packageSearch = ref(null);
const showPackageErrorTimerId = ref(null);

const packageSelected = (composerPackage)  => {
    newDependency.value = composerPackage;
};

const addDependency = () => {
    loadingComposerAdd.value = true;
    const index = props.composerDeps.find(p => p.name === newDependency.value);

    if (index) {
        newDependency.value = '';
        return;
    }

    const opts = {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    };

    fetch('/online/composer-package/add?package=' + encodeURIComponent(newDependency.value), opts)
        .then(response => response.json())
        .then(json => {
            if (json.status === 'error') {
                loadingComposerAdd.value = false;
                packageSearch.value.reset();

                showPackageAddError.valye = true;

                if (showPackageErrorTimerId.value) {
                    clearInterval(showPackageErrorTimerId.value);
                }

                showPackageErrorTimerId.value = setTimeout(() => showPackageAddError.value = false, 3000);
                return;
            }

            emit('package-added', {
                name: newDependency.value,
                version: json.latest_version,
                versions: json.versions
            });

            newDependency.value = '';
            loadingComposerAdd.value = false;
            packageSearch.value.reset();
        })
        .catch((error) => {
            loadingComposerAdd.value = false;
            packageSearch.value.reset();
        })
}

const removeDependency = (packageName) => {
    const index = props.composerDeps.find(p => p.name === packageName);

    if (index) {
        emit('package-removed', index);
    }
};
</script>

<template>
    <alert type="error" @close="showPackageAddError = false" v-show="showPackageAddError" message="Package could not be added because it has no tagged version."></alert>

    <Transition enter-active-class="transition-opacity duration-100 ease-in"
                leave-active-class="transition-opacity duration-200 ease-in" enter-from-class="opacity-0"
                enter-to-class="opacity-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <Modal size="sm" max-height="max-h-[calc(1/2*100%)]" v-if="open"
               @close="emit('close')" scrollContent>
            <template #header>
                <div class="flex items-center ">
                    <CircleStackIcon class="h-6 w-6 text-pink-500 mr-2"/>
                    <h3 class="font-mono text-base font-semibold lg:text-xl text-white pt-0 mt-0 ">
                        Composer Dependencies
                    </h3>
                </div>

            </template>
            <template #body>
                <div class="flex justify-between items-center">
                    <package-search ref="packageSearch" @package-selected="packageSelected"
                                    v-model="newDependency" class="w-full"></package-search>
                    <button :disabled="newDependency === ''" @click.stop="addDependency" type="button"
                            class="inline-flex items-center h-9 justify-center rounded-full border border-transparent w-16 bg-pink-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-pink-700 focus:outline-none focus:ring focus:ring-pink-800 sm:ml-3 sm:text-sm disabled:opacity-70 disabled:hover:bg-pink-600">
                        <ArrowPathIcon v-cloak v-show="loadingComposerAdd" class="w-4 h-4 animate-spin"/>
                        <span v-if="!loadingComposerAdd">Add</span>
                    </button>
                </div>
                <ul v-show="composerDeps.length > 0" class="mt-4 overflow-y-scroll ">
                    <li v-for="dep in composerDeps" class="text-white pl-2 mb-2 flex items-center">
                        <p class="text-base">{{ dep.name }}</p>
                        <p class="bg-gray-900 ml-2 px-2 py-1 rounded">{{ dep.version }}</p>
                        <XMarkIcon @click.stop="removeDependency(dep.name)"
                                   class="cursor-pointer ml-2 w-5 h-5 text-zinc-400 hover:text-pink-600"/>
                    </li>
                </ul>
                <div v-show="composerDeps.length === 0" class="pt-6">
                    <p class="text-white text-sm">You currently have no dependencies.</p>
                </div>
            </template>

            <template #footer>
                <div class="flex justify-end">
                    <button @click="emit('close')" type="button"
                            class="inline-flex items-center w-full justify-center rounded-full border border-transparent bg-pink-600 px-8 py-2 text-base font-medium text-white shadow-sm hover:bg-pink-700 focus:outline-none focus:ring focus:ring-pink-800 sm:ml-3 sm:w-auto sm:text-sm">
                        Close
                    </button>
                </div>
            </template>
        </Modal>
    </Transition>
</template>