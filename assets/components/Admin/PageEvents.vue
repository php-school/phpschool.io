<script setup>
import { computed, onMounted, reactive, ref } from "vue";
import { EllipsisVerticalIcon, TrashIcon, PencilIcon, ExclamationTriangleIcon } from "@heroicons/vue/24/outline";
import { ArrowPathIcon } from "@heroicons/vue/24/solid";
import { Dialog, DialogPanel, DialogTitle, Menu, MenuButton, MenuItem, MenuItems, TransitionChild, TransitionRoot } from "@headlessui/vue";

import Alert from "../Online/SiteAlert.vue";
import { allEvents, createEvent, deleteEvent, updateEvent } from "./api";

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

const filteredEvents = computed(() => {
  if (props.search === "" || props.search === null) {
    return events.value;
  }

  return events.value.filter((event) => {
    return event.name.toLowerCase().includes(props.search.toLowerCase()) || event.description.toLowerCase().includes(props.search.toLowerCase());
  });
});

const currentlyDeleting = ref(null);
const showDeleteSuccess = ref(false);
const deleteSuccess = ref("");

const showDeleteError = ref(false);
const deleteError = ref("");

const deleteLoading = ref(false);

const confirmDeleteEvent = (event) => {
  currentlyDeleting.value = event;
};

const doDeleteEvent = async () => {
  deleteLoading.value = true;

  try {
    await deleteEvent(currentlyDeleting.value.id);

    const deletedId = currentlyDeleting.value.id;

    deleteSuccess.value = "Successfully removed: " + currentlyDeleting.value.name;
    showDeleteSuccess.value = true;

    events.value = events.value.filter((event) => event.id !== deletedId);
  } catch (error) {
    if (error.message) {
      deleteError.value = error.message;
    }

    showDeleteError.value = true;
  } finally {
    deleteLoading.value = false;
    currentlyDeleting.value = null;
  }
};

const showAdd = ref(false);
const form = reactive({
  id: null,
  name: null,
  description: null,
  link: null,
  date: null,
  venue: null,
});
const errors = ref({});

const getNow = () => {
  const now = new Date();
  now.setMinutes(now.getMinutes() - now.getTimezoneOffset());

  now.setMilliseconds(null);
  now.setSeconds(null);

  return now.toISOString().slice(0, -8);
};

const addEvent = () => {
  form.id = null;
  form.name = null;
  form.description = null;
  form.link = "";
  form.date = getNow();
  form.venue = null;

  showAdd.value = true;
};

const showEventSuccess = ref(false);
const eventSuccess = ref("");

const showEventError = ref(false);
const eventError = ref("");

const loading = ref(false);

const file = ref(null);
const fileInput = ref(null);

const changeEventImage = () => {
  file.value = fileInput.value?.files[0];
};

const saveEvent = async () => {
  errors.value = {}; //reset errors
  loading.value = true;

  const data = {
    name: form.name,
    description: form.description,
    link: form.link,
    date: form.date,
    venue: form.venue,
  };

  if (file.value) {
    data.poster = file.value;
  }

  const successMessage = form.id ? "Successfully updated event: " + form.name : "Successfully added event: " + form.name;

  try {
    let response;
    if (form.id) {
      response = await updateEvent(form.id, data);
    } else {
      response = await createEvent(data);
    }
    if (response.status === 404) {
      if (response.error) {
        eventError.value = response.error;
      }
      showEventError.value = true;
    } else if (response.status === 400) {
      if (response.form_errors) {
        errors.value = response.form_errors;
      }
    } else {
      //success
      events.value = await allEvents();

      eventSuccess.value = successMessage;
      showEventSuccess.value = true;

      showAdd.value = false;
    }
  } finally {
    loading.value = false;
  }
};

const editEvent = (event) => {
  form.id = event.id;
  form.name = event.name;
  form.description = event.description;
  form.date = event.date;
  form.venue = event.venue;

  showAdd.value = true;
};
</script>
<template>
  <!-- delete alerts -->
  <alert type="error" :message="deleteError !== '' ? deleteError : 'An error occurred. Please try again later.'" :timeout="4000" :show="showDeleteError" @close="showDeleteError = false"></alert>
  <alert type="success" :message="deleteSuccess" :timeout="4000" :show="showDeleteSuccess" @close="showDeleteSuccess = false"></alert>

  <!-- event create/update alerts -->
  <alert type="error" :message="eventError !== '' ? eventError : 'An error occurred. Please try again later.'" :timeout="4000" :show="showEventError" @close="showEventError = false"></alert>
  <alert type="success" :message="eventSuccess" :timeout="4000" :show="showEventSuccess" @close="showEventSuccess = false"></alert>

  <header class="flex items-center justify-between border-b border-pink-600/30 px-4 py-4 sm:px-6 sm:py-6 lg:px-8">
    <h1 class="text-base font-semibold leading-7 text-white">Events</h1>
    <button
      @click="addEvent"
      type="button"
      class="flex-none rounded-md bg-pink-500/10 px-2 py-1.5 text-xs font-medium text-pink-500 ring-1 ring-inset ring-pink-500/30 hover:text-pink-600 hover:ring-pink-600/30"
    >
      Add
    </button>
  </header>

  <ul role="list" class="divide-y divide-pink-600/30">
    <li v-for="event in filteredEvents" :key="event.id" class="relative flex items-center space-x-4 px-4 py-4 sm:pr-6 lg:pr-8">
      <div class="aspect-square h-20">
        <img v-if="event.poster" class="" :src="'/uploads/' + event.poster" />
      </div>

      <div class="min-w-0 flex-auto">
        <div class="flex items-center gap-x-3">
          <h2 class="min-w-0 text-sm font-semibold leading-6 text-white">
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
          <MenuItems class="absolute right-0 z-10 mt-2 w-48 origin-top-right divide-y divide-gray-700 rounded-md bg-gray-800 p-2 shadow-lg ring-1 ring-pink-500/50 focus:outline-none">
            <MenuItem v-slot="{ active }">
              <button @click="editEvent(event)" :class="[active ? 'p-2 text-white' : 'text-slate-400', 'group flex w-full items-center px-4 py-2 text-sm']">
                <PencilIcon class="mr-3 h-5 w-5" aria-hidden="true" />
                Edit
                <span class="sr-only">, {{ event.name }}</span>
              </button>
            </MenuItem>
            <MenuItem v-slot="{ active }">
              <button @click="confirmDeleteEvent(event)" :class="[active ? 'p-2 text-white' : 'text-slate-400', 'group flex w-full items-center px-4 py-2 text-sm']">
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

  <TransitionRoot as="template" :show="currentlyDeleting !== null">
    <Dialog as="div" class="relative z-50" @close="currentlyDeleting = null">
      <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
      </TransitionChild>

      <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
          <TransitionChild
            as="template"
            enter="ease-out duration-300"
            enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            enter-to="opacity-100 translate-y-0 sm:scale-100"
            leave="ease-in duration-200"
            leave-from="opacity-100 translate-y-0 sm:scale-100"
            leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          >
            <DialogPanel class="relative transform overflow-hidden rounded-lg bg-gray-900 px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
              <div class="sm:flex sm:items-start">
                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10">
                  <ExclamationTriangleIcon class="h-6 w-6 text-red-600" aria-hidden="true" />
                </div>
                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                  <DialogTitle as="h3" class="text-base font-semibold leading-6 text-gray-300">Delete event</DialogTitle>
                  <div class="mt-2">
                    <p v-if="currentlyDeleting" class="text-sm text-gray-400">Are you sure you want to delete "{{ currentlyDeleting.name }}". This action cannot be undone.</p>
                  </div>
                </div>
              </div>
              <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <button
                  type="button"
                  class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto"
                  @click="doDeleteEvent"
                >
                  <ArrowPathIcon v-show="deleteLoading" class="h-5 w-5 animate-spin" />
                  <span v-show="!deleteLoading">Delete</span>
                </button>
                <button
                  type="button"
                  class="mt-3 inline-flex w-full justify-center rounded-md bg-white/10 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-white/20 sm:mt-0 sm:w-auto"
                  ref="cancelButtonRef"
                  @click="currentlyDeleting = null"
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

  <TransitionRoot as="template" :show="showAdd">
    <Dialog as="div" class="relative z-50" @close="showAdd = false">
      <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
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
            <DialogPanel class="relative w-full transform overflow-hidden rounded-lg bg-gray-900 px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:p-6 md:max-w-2xl">
              <div class="sm:flex sm:items-start">
                <form @submit.prevent="saveEvent" class="w-full" enctype="multipart/form-data">
                  <div class="space-y-12">
                    <div class="border-b border-white/10 pb-12">
                      <h2 v-if="form.id" class="text-base font-semibold leading-7 text-white">Updating event</h2>
                      <h2 v-else class="text-base font-semibold leading-7 text-white">Creating new event</h2>
                      <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                        <div class="col-span-full">
                          <label for="title" class="block text-sm font-medium leading-6 text-white">Name</label>
                          <div class="mt-2">
                            <div class="flex rounded-md bg-white/5 ring-1 ring-inset ring-white/10 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500">
                              <input
                                v-model="form.name"
                                type="text"
                                name="name"
                                id="name"
                                autocomplete="title"
                                class="flex-1 border-0 bg-transparent py-1.5 pl-1 text-white focus:ring-0 sm:text-sm sm:leading-6"
                              />
                            </div>
                          </div>
                          <ul v-if="errors.name" class="">
                            <li v-for="err in errors.name" :key="err" class="mt-2 text-xs text-red-600">
                              {{ err }}
                            </li>
                          </ul>
                        </div>

                        <div class="col-span-full">
                          <label for="description" class="block text-sm font-medium leading-6 text-white">Description</label>
                          <div class="mt-2">
                            <textarea
                              v-model="form.description"
                              id="description"
                              name="description"
                              rows="3"
                              class="block w-full rounded-md border-0 bg-white/5 py-1.5 text-white shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6"
                            />
                          </div>
                          <p class="mt-3 text-sm leading-6 text-gray-400">Write a description of the event.</p>
                          <ul v-if="errors.description" class="">
                            <li v-for="err in errors.description" :key="err" class="mt-2 text-xs text-red-600">
                              {{ err }}
                            </li>
                          </ul>
                        </div>

                        <div class="col-span-full">
                          <label for="link" class="block text-sm font-medium leading-6 text-white">Link</label>
                          <div class="mt-2">
                            <div class="flex rounded-md bg-white/5 ring-1 ring-inset ring-white/10 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500">
                              <input
                                v-model="form.link"
                                type="text"
                                name="link"
                                id="link"
                                autocomplete="link"
                                class="flex-1 border-0 bg-transparent py-1.5 pl-1 text-white focus:ring-0 sm:text-sm sm:leading-6"
                              />
                            </div>
                          </div>
                          <ul v-if="errors.link" class="">
                            <li v-for="err in errors.link" :key="err" class="mt-2 text-xs text-red-600">
                              {{ err }}
                            </li>
                          </ul>
                        </div>

                        <div class="col-span-full">
                          <label for="date" class="block text-sm font-medium leading-6 text-white">Date</label>
                          <div class="mt-2">
                            <div class="flex rounded-md bg-white/5 ring-1 ring-inset ring-white/10 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500">
                              <input
                                v-model="form.date"
                                type="datetime-local"
                                name="date"
                                id="date"
                                autocomplete="link"
                                class="flex-1 border-0 bg-transparent py-1.5 pl-1 text-white focus:ring-0 sm:text-sm sm:leading-6"
                              />
                            </div>
                          </div>
                          <ul v-if="errors.date" class="">
                            <li v-for="err in errors.date" :key="err" class="mt-2 text-xs text-red-600">
                              {{ err }}
                            </li>
                          </ul>
                        </div>

                        <div class="col-span-full">
                          <label for="venue" class="block text-sm font-medium leading-6 text-white">Venue</label>
                          <div class="mt-2">
                            <textarea
                              v-model="form.venue"
                              id="venue"
                              name="venue"
                              rows="3"
                              class="block w-full rounded-md border-0 bg-white/5 py-1.5 text-white shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6"
                            />
                          </div>
                          <ul v-if="errors.venue" class="">
                            <li v-for="err in errors.venue" :key="err" class="mt-2 text-xs text-red-600">
                              {{ err }}
                            </li>
                          </ul>
                        </div>

                        <div class="col-span-full">
                          <label for="poster" class="block text-sm font-medium leading-6 text-white">Poster</label>

                          <div class="mt-2">
                            <label for="file-input" class="sr-only">Choose file</label>
                            <input
                              type="file"
                              ref="fileInput"
                              @change="changeEventImage"
                              accept="image/*"
                              name="poster"
                              id="poster"
                              class="block w-full rounded-lg border border-gray-200 bg-white/5 text-sm text-white shadow-sm file:me-4 file:border-0 file:bg-white/5 file:px-4 file:py-3 file:text-white focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:pointer-events-none disabled:opacity-50 dark:border-gray-700 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
                            />
                          </div>
                          <ul v-if="errors.poster" class="">
                            <li v-for="err in errors.poster" :key="err" class="mt-2 text-xs text-red-600">
                              {{ err }}
                            </li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
              <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <button
                  type="button"
                  class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto"
                  @click="saveEvent"
                >
                  <ArrowPathIcon v-show="loading" class="h-5 w-5 animate-spin" />
                  <span v-show="!loading">Save</span>
                </button>
                <button
                  type="button"
                  class="mt-3 inline-flex w-full justify-center rounded-md bg-white/10 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-white/20 sm:mt-0 sm:w-auto"
                  ref="cancelButtonRef"
                  @click="showAdd = false"
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
