<template>
    <!-- approve alerts -->
    <alert type="error" :message="approveError ?? 'An error occurred. Please try again later.'" :timeout="4000" v-if="showApproveError" @close="showApproveError = false"></alert>
    <alert type="success" :message="approveSuccess" :timeout="4000" v-if="showApproveSuccess" @close="showApproveSuccess = false"></alert>

    <header class="flex items-center justify-between border-b border-pink-600/30 px-4 py-4 sm:px-6 sm:py-6 lg:px-8">
        <h1 class="text-base font-semibold leading-7 text-white">New Workshop Requests</h1>
    </header>

    <div v-if="loading === false && workshops.length === 0" class="text-center mt-8">
        <CubeIcon class="mx-auto h-12 w-12 text-gray-400" aria-hidden="true" />
        <h3 class="mt-2 text-base text-gray-500">No new workshop requests</h3>
    </div>

    <ul role="list" class="divide-y divide-pink-600/30">
        <li v-for="workshop in filteredWorkshops" :key="workshop.id" class="relative flex items-center space-x-4 px-4 py-4 sm:px-6 lg:px-8">
            <div class="min-w-0 flex-auto">
                <div class="flex items-center gap-x-3">
                    <div :class="[statuses[workshop.status], 'flex-none rounded-full p-1']">
                        <div class="h-2 w-2 rounded-full bg-current" />
                    </div>
                    <h2 class="min-w-0 text-sm font-semibold leading-6 text-white">
                        <span class="flex gap-x-2">
                            <span class="truncate">{{ workshop.name }}</span>
                            <span class="text-gray-400">/</span>
                            <span class="whitespace-nowrap">{{ workshop.code }}</span>
                        </span>
                    </h2>
                </div>
                <div class="mt-3 flex items-center gap-x-2.5 text-xs leading-5 text-gray-400">
                    <a :href="workshop.repo_url" target="_blank" class=" hover:cursor-pointer hover:underline text-blue-600">{{ workshop.repo_url }}</a>
                    <svg viewBox="0 0 2 2" class="h-0.5 w-0.5 flex-none fill-gray-300">
                        <circle cx="1" cy="1" r="1" />
                    </svg>
                    <p class="whitespace-nowrap">{{ workshop.submitter_email }}</p>
                </div>
                <div class="mt-3 flex items-center gap-x-2.5 text-xs leading-5 text-gray-400">
                    <p class="whitespace-nowrap text-white">{{ workshop.description }}</p>
                </div>
            </div>
            <div :class="[statuses[workshop.status], 'rounded-full flex-none py-1 px-2 text-xs font-medium ring-1 ring-inset']">{{ workshop.status }}</div>

            <button @click="confirmApprove(workshop)" type="button" class="rounded-md bg-green-500 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-500">Approve</button>
        </li>
    </ul>

    <TransitionRoot as="template" :show="currentlyApproving !== null">
        <Dialog as="div" class="relative z-50" @close="currentlyApproving = null">
            <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
            </TransitionChild>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200" leave-from="opacity-100 translate-y-0 sm:scale-100" leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                        <DialogPanel class="relative transform overflow-hidden rounded-lg bg-gray-900 px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10">
                                    <ExclamationTriangleIcon class="h-6 w-6 text-green-600" aria-hidden="true" />
                                </div>
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                    <DialogTitle as="h3" class="text-base font-semibold leading-6 text-gray-300">Approve workshop</DialogTitle>
                                    <div class="mt-2">
                                        <p v-if="currentlyApproving" class="text-sm text-gray-400">Are you sure you want to approve "{{currentlyApproving.name}}"?</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                <button type="button" class="inline-flex w-full justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 sm:ml-3 sm:w-auto" @click="approveWorkshop" >Approve</button>
                                <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white/10 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-white/20 sm:mt-0 sm:w-auto" ref="cancelButtonRef" @click="currentlyApproving = null">Cancel</button>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script setup>

import {computed, onMounted, ref} from "vue";
import {
    Dialog,
    DialogPanel,
    DialogTitle,
    TransitionChild,
    TransitionRoot
} from "@headlessui/vue";
import {ExclamationTriangleIcon} from '@heroicons/vue/24/outline';
import {CubeIcon} from '@heroicons/vue/24/solid';
import Alert from "../Online/Alert.vue";

const props = defineProps({
    search: {
        type: String,
        default: '',
    }
});

const loading = ref(true);
const workshops = ref([]);

onMounted(() => {
    fetch('/admin/workshop/new')
        .then(response => response.json())
        .then(data => {
            loading.value = false;
            return workshops.value = data.workshops;
        });
});

const filteredWorkshops = computed(() => {
    if (props.search === '' || props.search === null) {
        return workshops.value;
    }

    return workshops.value.filter((workshop) => {
        return workshop.name.toLowerCase().includes(props.search.toLowerCase())
            || workshop.code.toLowerCase().includes(props.search.toLowerCase());
    });
});

const statuses = {
    'Not-approved': 'text-gray-400 bg-gray-400/10 ring-gray-400/20',
    Approved: 'text-pink-500 bg-pink-500/10 ring-pink-500/30',
}

const types = { Core: 'text-green-400 bg-green-400/10 ring-green-400/30', Community: 'text-sky-400 bg-sky-400/10 ring-sky-500/30' }

const currentlyApproving = ref(null);
const showApproveSuccess = ref(false);
const approveSuccess = ref('');

const showApproveError = ref(false);
const approveError = ref('');

const confirmApprove = (workshop) => {
    currentlyApproving.value = workshop;
}

const approveWorkshop = (workshop) => {
    fetch('/admin/workshop/approve/' + currentlyApproving.value.id, { method: 'POST' })
        .then((response) => {
            if (response.ok) {
                return response.json();
            }
            return Promise.reject(response);
        })
        .then(data => {
            const approveId = currentlyApproving.value.id;

            approveSuccess.value = 'Successfully approved: ' + currentlyApproving.value.name + '  and regenerated workshop feed';
            currentlyApproving.value = null;
            showApproveSuccess.value = true;

            workshops.value = workshops.value.filter((workshop) => workshop.id !== approveId);
        })
        .catch((response) => {
            currentlyApproving.value = null;

            response.json().then((json) => {

                if (json.error) {
                    approveError.value = json.error;
                }

                showApproveError.value = true;
            })
        });
}
</script>
