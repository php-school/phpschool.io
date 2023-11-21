<script setup>
import {onMounted, ref} from 'vue'
import {
    Dialog,
    DialogPanel, Menu, MenuButton, MenuItem, MenuItems,
    TransitionChild,
    TransitionRoot,
} from '@headlessui/vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'
import { Bars3Icon, ChevronRightIcon, ChevronDownIcon, MagnifyingGlassIcon, CalendarDaysIcon, PlusIcon, CubeIcon, HomeIcon, ChartBarIcon, CogIcon, UsersIcon } from '@heroicons/vue/20/solid'
import Logo from "../Website/Logo.vue";
import Button from "../Website/Button.vue";

import { useRoute } from 'vue-router'
const location = useRoute();

const props = defineProps({
    user: Object
})

const navigation = [
    { name: 'Home', to: '/', icon: HomeIcon },
    { name: 'Workshops', to: '/workshops', icon: CubeIcon },
    { name: 'Workshop Installs', to: '/workshop-installs', icon: ChartBarIcon },
    { name: 'New Workshops', to: '/new-workshops', icon: PlusIcon },
    { name: 'Students', to: '/students', icon: UsersIcon },
    { name: 'Events', to: '/events', icon: CalendarDaysIcon },
    { name: 'Settings', to: '/settings', icon: CogIcon },
]

const logout = () => {
    fetch('/logout', {method: 'POST'})
        .then((response) => {
            window.location.href = '/login';
        })
};


const workshops = ref([]);
const activityItems = ref([]);

const search = ref('');

onMounted(() => {
    fetch('/admin/workshop/new')
        .then(response => response.json())
        .then(data => {
            activityItems.value = data.workshops.map((workshop) => {
                return {
                    submitter: {
                        name: workshop.submitter_name,
                        imageUrl: workshop.submitter_avatar
                    },
                    dateTime: workshop.created_at,
                    name: workshop.name,
                    code: workshop.code,
                    repo_url: workshop.repo_url
                }
            })


            return workshops.value = data.workshops;
        });
});

const sidebarOpen = ref(false)
</script>

<template>
    <div>
        <TransitionRoot as="template" :show="sidebarOpen">
            <Dialog as="div" class="relative z-50 xl:hidden" @close="sidebarOpen = false">
                <TransitionChild as="template" enter="transition-opacity ease-linear duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="transition-opacity ease-linear duration-300" leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed inset-0 bg-gray-900/80" />
                </TransitionChild>

                <div class="fixed inset-0 flex">
                    <TransitionChild as="template" enter="transition ease-in-out duration-300 transform" enter-from="-translate-x-full" enter-to="translate-x-0" leave="transition ease-in-out duration-300 transform" leave-from="translate-x-0" leave-to="-translate-x-full">
                        <DialogPanel class="relative mr-16 flex w-full max-w-xs flex-1">
                            <TransitionChild as="template" enter="ease-in-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in-out duration-300" leave-from="opacity-100" leave-to="opacity-0">
                                <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
                                    <button type="button" class="-m-2.5 p-2.5" @click="sidebarOpen = false">
                                        <span class="sr-only">Close sidebar</span>
                                        <XMarkIcon class="h-6 w-6 text-white" aria-hidden="true" />
                                    </button>
                                </div>
                            </TransitionChild>
                            <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-gray-900 px-6 ring-1 ring-pink-600/30">
                                <div class="flex h-16 shrink-0 items-center">
                                    <img class="h-8 w-auto" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=500" alt="Your Company" />
                                </div>
                                <nav class="flex flex-1 flex-col">
                                    <ul role="list" class="flex flex-1 flex-col gap-y-7">
                                        <li>
                                            <ul role="list" class="-mx-2 space-y-1">
                                                <li v-for="item in navigation" :key="item.name">
                                                    <router-link :to="item.to" :class="[item.to === location.path ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800', 'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold']">
                                                        {{ item.name }}
                                                    </router-link>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </Dialog>
        </TransitionRoot>

        <!-- Static sidebar for desktop -->
        <div class="hidden xl:fixed xl:inset-y-0 xl:z-50 xl:flex xl:w-72 xl:flex-col">
            <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-black/10 px-6 ring-1 ring-pink-600/30">
                <div class="flex h-16 shrink-0 items-center">
                    <Logo class="h-8 w-auto text-white" alt="PHP School"></Logo>
                </div>
                <nav class="flex flex-1 flex-col">
                    <ul role="list" class="flex flex-1 flex-col gap-y-7">
                        <li>
                            <ul role="list" class="-mx-2 space-y-1">
                                <li v-for="item in navigation" :key="item.name">
                                    <router-link :to="item.to" :class="[item.to === location.path  ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800', 'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold']">
                                        <component :is="item.icon" class="h-6 w-6 shrink-0" aria-hidden="true" />
                                        {{ item.name }}
                                    </router-link>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <div class="xl:pl-72">
            <!-- Sticky search header -->
            <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-6 border-b border-pink-600/30 bg-gray-900 pl-4 shadow-sm sm:pl-6 lg:pl-8 pr-2">
                <button type="button" class="-m-2.5 p-2.5 text-white xl:hidden" @click="sidebarOpen = true">
                    <span class="sr-only">Open sidebar</span>
                    <Bars3Icon class="h-5 w-5" aria-hidden="true" />
                </button>

                <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                    <form class="flex flex-1" action="#" method="GET">
                        <label for="search-field" class="sr-only">Search</label>
                        <div class="relative w-full">
                            <MagnifyingGlassIcon class="pointer-events-none absolute inset-y-0 left-0 h-full w-5 text-gray-500" aria-hidden="true" />
                            <input v-model="search" id="search-field" class="block h-full w-full border-0 bg-transparent py-0 pl-8 pr-0 text-white focus:ring-0 sm:text-sm" placeholder="Search..." type="search" name="search" />
                        </div>
                    </form>
                </div>

                <div class="ml-4 flex items-center md:ml-6">
                    <Menu as="div" class="relative ml-3">
                        <div>
                            <MenuButton class="relative flex max-w-xs items-center h-full text-sm focus:outline-none rounded-md lg:p-2 hover:bg-gray-800">
                                <span class="absolute -inset-1.5 lg:hidden" />
                                <img class="h-8 w-8 rounded-full" :src="user.avatar" alt="" />
                                <span class="ml-3 hidden text-sm font-medium text-white lg:block"><span class="sr-only">Open user menu for </span> {{user.name}}</span>
                                <ChevronDownIcon class="ml-1 hidden h-5 w-5 flex-shrink-0 text-white lg:block" aria-hidden="true" />
                            </MenuButton>
                        </div>
                        <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                            <MenuItems class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-gray-800 p-2 shadow-lg ring-1 ring-pink-500/50 focus:outline-none">
                                <MenuItem v-slot="{ active }">
                                    <button @click="logout" type="button" :class="[active ? 'bg-slate-900/40 text-white' : '', 'rounded-md block px-4 py-2 text-sm text-slate-400']">Logout</button>
                                </MenuItem>
                            </MenuItems>
                        </transition>
                    </Menu>
                </div>
            </div>

            <main class="lg:pr-96">
                <RouterView v-slot="{ Component }">
                    <component
                            :is="Component"
                            :search="search"
                    />
                </RouterView>
            </main>

            <!-- Activity feed -->
            <aside class="bg-black/10 lg:fixed lg:bottom-0 lg:right-0 lg:top-16 lg:w-96 lg:overflow-y-auto lg:border-l lg:border-pink-600/30">
                <header class="flex items-center justify-between border-b border-pink-600/30 px-4 py-4 sm:px-6 sm:py-6 lg:px-8">
                    <h2 class="text-base font-semibold leading-7 text-white">Activity feed</h2>
                </header>
                <ul role="list" class="divide-y divide-white/5">
                    <li v-for="item in activityItems" class="px-4 py-4 sm:px-6 lg:px-8">
                        <div class="flex items-center gap-x-3">
                            <img :src="item.submitter.imageUrl" alt="" class="h-6 w-6 flex-none rounded-full bg-gray-800" />
                            <h3 class="flex-auto truncate text-sm font-semibold leading-6 text-white">{{ item.submitter.name }}</h3>
                            <time :datetime="item.dateTime" class="flex-none text-xs text-gray-600">{{ item.dateTime }}</time>
                        </div>
                        <p class="mt-3 text-sm text-gray-500">
                            Submitted <span class="text-gray-400">{{ item.name }}</span> (<span class="font-mono text-gray-400">{{ item.code }}</span>) from <a :href="item.repo_url" target="_blank" class=" hover:cursor-pointer hover:underline text-blue-600 text-xs">{{ item.repo_url }}</a>.
                        </p>
                    </li>
                </ul>
            </aside>
        </div>
    </div>
</template>