<script setup>
import FormField from "./FormField.vue";
import ConfirmDialog from "./ConfirmDialog.vue";
import { onMounted, reactive, ref } from "vue";
import { useAsyncState } from "@vueuse/core";
import { EllipsisVerticalIcon, TrashIcon, PencilIcon } from "@heroicons/vue/24/outline";
import { ArrowPathIcon } from "@heroicons/vue/24/solid";
import { Dialog, DialogPanel, Menu, MenuButton, MenuItem, MenuItems, TransitionChild, TransitionRoot } from "@headlessui/vue";

import Alert from "../Online/SiteAlert.vue";
import { allEvents, createEvent, deleteEvent, updateEvent } from "./api";
import { useSearch } from "./search";
import { useNotice } from "./notice";

const props = defineProps({
  search: {
    type: String,
    default: "",
  },
});

const events = ref([]);

onMounted(async () => {
  events.value = await allEvents();
});

const filteredEvents = useSearch(events, () => props.search, ["name", "description"]);

const { notice, success, error } = useNotice();

const currentlyDeleting = ref(null);
const { isLoading: deleteLoading, executeImmediate: doDeleteEvent } = useAsyncState(
  async () => {
    const event = currentlyDeleting.value;

    try {
      await deleteEvent(event.id);
      events.value = events.value.filter(({ id }) => id !== event.id);
      success("Successfully removed: " + event.name);
    } finally {
      currentlyDeleting.value = null;
    }
  },
  null,
  { immediate: false, onError: error },
);

const showForm = ref(false);
const form = reactive({});
const errors = ref({});
const file = ref(null);
const fileInput = ref(null);

// The current local time in the datetime-local format, e.g. 2026-10-23T09:30.
const now = () => {
  const date = new Date();
  date.setMinutes(date.getMinutes() - date.getTimezoneOffset());
  return date.toISOString().slice(0, 16);
};

const openForm = (event = {}) => {
  Object.assign(form, {
    id: event.id ?? null,
    name: event.name ?? null,
    description: event.description ?? null,
    link: event.link ?? "",
    date: event.date ?? now(),
    venue: event.venue ?? null,
  });
  file.value = null;
  errors.value = {};
  showForm.value = true;
};

const changeEventImage = () => {
  file.value = fileInput.value?.files[0];
};

const { isLoading: loading, executeImmediate: saveEvent } = useAsyncState(
  async () => {
    errors.value = {};

    const { id, ...data } = form;
    if (file.value) {
      data.poster = file.value;
    }

    const response = id ? await updateEvent(id, data) : await createEvent(data);

    // the server answers validation failures with a 200 and form_errors
    if (response.form_errors) {
      errors.value = response.form_errors;
    } else {
      events.value = await allEvents();
      success((id ? "Successfully updated event: " : "Successfully added event: ") + data.name);
      showForm.value = false;
    }
  },
  null,
  { immediate: false, onError: error },
);
</script>
<template>
  <alert v-bind="notice" :timeout="4000" @close="notice.show = false" />

  <header class="flex items-center justify-between border-b border-pink-600/30 px-4 py-4 sm:px-6 sm:py-6 lg:px-8">
    <h1 class="text-base leading-7 font-semibold text-white">Events</h1>
    <button
      @click="openForm()"
      type="button"
      class="flex-none rounded-md bg-pink-500/10 px-2 py-1.5 text-xs font-medium text-pink-500 ring-1 ring-pink-500/30 ring-inset hover:text-pink-600 hover:ring-pink-600/30"
    >
      Add
    </button>
  </header>

  <ul role="list" class="divide-y divide-pink-600/30">
    <li v-for="event in filteredEvents" :key="event.id" class="relative flex items-center gap-x-4 px-4 py-4 sm:pr-6 lg:pr-8">
      <div class="aspect-square h-20">
        <img v-if="event.poster" class="" :src="'/uploads/' + event.poster" />
      </div>

      <div class="min-w-0 flex-auto">
        <div class="flex items-center gap-x-3">
          <h2 class="min-w-0 text-sm leading-6 font-semibold text-white">
            <a v-if="event.link" :href="event.link">{{ event.name }}</a>
            <span v-else>{{ event.name }}</span>
          </h2>
        </div>
        <div class="mt-3 flex items-center gap-x-2.5 text-xs leading-5 text-gray-400">
          <p class="whitespace-nowrap">{{ event.date_formatted }}</p>
          <svg viewBox="0 0 2 2" class="h-0.5 w-0.5 flex-none fill-gray-300">
            <circle cx="1" cy="1" r="1" />
          </svg>
          <p class="">{{ event.venue }}</p>
        </div>
        <p class="mt-1 line-clamp-1 text-sm leading-6 text-gray-300">
          {{ event.description }}
        </p>
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
            <MenuItem v-slot="{ active }">
              <button @click="openForm(event)" :class="[active ? 'p-2 text-white' : 'text-slate-400', 'group flex w-full items-center px-4 py-2 text-sm']">
                <PencilIcon class="mr-3 h-5 w-5" aria-hidden="true" />
                Edit
                <span class="sr-only">, {{ event.name }}</span>
              </button>
            </MenuItem>
            <MenuItem v-slot="{ active }">
              <button @click="currentlyDeleting = event" :class="[active ? 'p-2 text-white' : 'text-slate-400', 'group flex w-full items-center px-4 py-2 text-sm']">
                <TrashIcon class="mr-3 h-5 w-5" aria-hidden="true" />
                Delete
                <span class="sr-only">, {{ event.name }}</span>
              </button>
            </MenuItem>
          </MenuItems>
        </transition>
      </Menu>
    </li>
  </ul>

  <ConfirmDialog :show="currentlyDeleting !== null" title="Delete event" confirm-label="Delete" danger :loading="deleteLoading" @confirm="doDeleteEvent" @close="currentlyDeleting = null">
    Are you sure you want to delete "{{ currentlyDeleting?.name }}". This action cannot be undone.
  </ConfirmDialog>

  <TransitionRoot as="template" :show="showForm">
    <Dialog as="div" class="relative z-50" @close="showForm = false">
      <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
        <div class="fixed inset-0 bg-gray-500/75 transition-opacity" />
      </TransitionChild>

      <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center">
          <TransitionChild
            as="template"
            enter="ease-out duration-300"
            enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            enter-to="opacity-100 translate-y-0 sm:scale-100"
            leave="ease-in duration-200"
            leave-from="opacity-100 translate-y-0 sm:scale-100"
            leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          >
            <DialogPanel class="relative w-full transform overflow-hidden rounded-lg bg-gray-900 px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:p-6 md:max-w-2xl">
              <div class="sm:flex sm:items-start">
                <form @submit.prevent="saveEvent" class="w-full" enctype="multipart/form-data">
                  <div class="space-y-12">
                    <div class="border-b border-white/10 pb-12">
                      <h2 v-if="form.id" class="text-base leading-7 font-semibold text-white">Updating event</h2>
                      <h2 v-else class="text-base leading-7 font-semibold text-white">Creating new event</h2>
                      <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                        <FormField id="name" label="Name" :errors="errors.name">
                          <div class="flex rounded-md bg-white/5 ring-1 ring-white/10 ring-inset focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-inset">
                            <input v-model="form.name" type="text" name="name" id="name" class="flex-1 border-0 bg-transparent py-1.5 pl-1 text-white focus:ring-0 sm:text-sm sm:leading-6" />
                          </div>
                        </FormField>
                        <FormField id="description" label="Description" hint="Write a description of the event." :errors="errors.description">
                          <textarea
                            v-model="form.description"
                            id="description"
                            name="description"
                            rows="3"
                            class="block w-full rounded-md border-0 bg-white/5 py-1.5 text-white shadow-xs ring-1 ring-white/10 ring-inset focus:ring-2 focus:ring-indigo-500 focus:ring-inset sm:text-sm sm:leading-6"
                          />
                        </FormField>
                        <FormField id="link" label="Link" :errors="errors.link">
                          <div class="flex rounded-md bg-white/5 ring-1 ring-white/10 ring-inset focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-inset">
                            <input v-model="form.link" type="text" name="link" id="link" class="flex-1 border-0 bg-transparent py-1.5 pl-1 text-white focus:ring-0 sm:text-sm sm:leading-6" />
                          </div>
                        </FormField>
                        <FormField id="date" label="Date" :errors="errors.date">
                          <div class="flex rounded-md bg-white/5 ring-1 ring-white/10 ring-inset focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-inset">
                            <input v-model="form.date" type="datetime-local" name="date" id="date" class="flex-1 border-0 bg-transparent py-1.5 pl-1 text-white focus:ring-0 sm:text-sm sm:leading-6" />
                          </div>
                        </FormField>
                        <FormField id="venue" label="Venue" :errors="errors.venue">
                          <textarea
                            v-model="form.venue"
                            id="venue"
                            name="venue"
                            rows="3"
                            class="block w-full rounded-md border-0 bg-white/5 py-1.5 text-white shadow-xs ring-1 ring-white/10 ring-inset focus:ring-2 focus:ring-indigo-500 focus:ring-inset sm:text-sm sm:leading-6"
                          />
                        </FormField>
                        <FormField id="poster" label="Poster" :errors="errors.poster">
                          <input
                            type="file"
                            ref="fileInput"
                            @change="changeEventImage"
                            accept="image/*"
                            name="poster"
                            id="poster"
                            class="block w-full rounded-lg border border-gray-200 bg-white/5 text-sm text-white shadow-xs file:me-4 file:border-0 file:bg-white/5 file:px-4 file:py-3 file:text-white focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:pointer-events-none disabled:opacity-50 dark:border-gray-700 dark:focus:ring-1 dark:focus:ring-gray-600 dark:focus:outline-hidden"
                          />
                        </FormField>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
              <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <button
                  type="button"
                  class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 sm:ml-3 sm:w-auto"
                  @click="saveEvent"
                >
                  <ArrowPathIcon v-show="loading" class="h-5 w-5 animate-spin" />
                  <span v-show="!loading">Save</span>
                </button>
                <button
                  type="button"
                  class="mt-3 inline-flex w-full justify-center rounded-md bg-white/10 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-white/20 sm:mt-0 sm:w-auto"
                  ref="cancelButtonRef"
                  @click="showForm = false"
                >
                  Cancel
                </button>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>
