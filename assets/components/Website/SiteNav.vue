<script setup>

import {Menu, MenuButton, MenuItem, MenuItems} from "@headlessui/vue";
import Button from "./Button.vue";
import GitHubIcon from "../Icons/GitHubIcon.vue";
import {ChevronDownIcon} from "@heroicons/vue/20/solid";
import Logo from "./Logo.vue";
import {onMounted, onUnmounted, ref} from "vue";

const mobileMenuVisible = ref(false);
const showMobileMenuBlur = ref(false);

const hideMobileMenu = () => {
    if (window.innerWidth > 640) {
        mobileMenuVisible.value = false;
    }
};

onUnmounted(() => {
    window.removeEventListener('resize', hideMobileMenu);
});

const toggleMenu = () => {
    mobileMenuVisible.value = !mobileMenuVisible.value;
    showMobileMenuBlur.value = mobileMenuVisible.value;
};

onMounted(() => {
    window.addEventListener('resize', hideMobileMenu);
    hideMobileMenu();
});


onUnmounted(() => {
    window.removeEventListener('resize', hideMobileMenu);
});
</script>

<template>
    <nav class="bg-nav-pattern bg-center sm:bg-nav-pattern sm:bg-center sm:bg-[length:110%] p-4 w-full z-50">
        <div class=" mx-auto flex items-center justify-between ">

            <!-- Logo -->
            <figure class="text-white ">
                <a href="/">
                    <Logo class="h-10 w-10 md:h-20 md:w-20" />
                </a>
            </figure>


            <!-- Links (Hidden on Mobile) -->
            <ul class="hidden sm:flex space-x-2 md:space-x-4 justify-center items-center">
                <li>
                    <a href="/cloud" class="text-white text-xs uppercase hover:text-pink-500 cursor-pointer transition duration-150">Cloud</a>
                </li>
                <li><a href="#" class="text-white text-xs uppercase hover:text-pink-500 cursor-pointer transition duration-150">Local</a>
                </li>
                <li><a href="#" class="text-white text-xs uppercase hover:text-pink-500 cursor-pointer transition duration-150">Support</a></li>


                <!-- DROPDOWN START -->
                <Menu as="li" class="relative text-white text-xs uppercase mt-1" v-slot="{ open }">
                    <div class="group">
                        <MenuButton :class="open ? 'text-pink-500' : 'text-white'"
                                    class="inline-flex w-full justify-center items-center  uppercase text-xs group-hover:text-pink-500 cursor-pointer transition duration-200">
                            For Developers
                            <ChevronDownIcon :class="open ? 'rotate-180 text-pink-500' : ''"
                                             class="ml-2 h-5 w-5 text-gray-400 transition duration-200 group-hover:text-pink-500"
                                             aria-hidden="true" />
                        </MenuButton>
                    </div>

                    <transition enter-active-class="transition ease-out duration-100"
                                enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100"
                                leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100"
                                leave-to-class="transform opacity-0 scale-95">
                        <MenuItems
                                class="absolute text-left right-0 z-40 mt-4 w-56 origin-top-right rounded-md bg-gray-800 shadow-brand-shadow ring-1 ring-black ring-opacity-5 focus:outline-none">
                            <div class="py-1">
                                <MenuItem v-slot="{ active }">
                                    <a href="#"
                                       :class="[active ? 'bg-gray-700 text-pink-500 ' : 'text-white', 'block px-4 py-4 text-xs hover:no-underline']">Build
                                        a workshop</a>
                                </MenuItem>
                                <MenuItem v-slot="{ active }">
                                    <a href="#"
                                       :class="[active ? 'bg-gray-700 text-pink-500' : 'text-white', 'block px-4 py-4 text-xs hover:no-underline']">Submit
                                        your workshop</a>
                                </MenuItem>
                            </div>
                        </MenuItems>
                    </transition>
                </Menu>

                <li><a href="/events" class="text-white text-xs uppercase hover:text-pink-500 cursor-pointer transition duration-150">Events</a></li>
                <li><a href="/blog" class="text-white text-xs uppercase hover:text-pink-500 cursor-pointer transition duration-150">Blog</a></li>

            </ul>

            <!-- Mobile Menu Button -->
            <div class="block sm:hidden">
                <button @click="mobileMenuVisible = !mobileMenuVisible"
                        class="text-white hover:text-gray-300 focus:outline-none">
                    <img src="../../img/cloud/bars-solid-pink.svg" alt="" class="h-6 w-6">
                </button>
            </div>

            <!-- Sign In Button -->
            <div class="hidden sm:block">
                <Button href=" /cloud" class="flex items-center px-2 py-2">
                    <GitHubIcon class="h-5 w-5 mr-2" /><span class="text-xs font-normal ">Log In with github</span>
                </Button>
            </div>

        </div>
        <!-- Mobile Menu Links and Sign In Button (Hidden by Default) -->
        <div :class="mobileMenuVisible ? 'block ' : 'hidden sm:hidden '">
            <div class="flex flex-col text-center text-white uppercase divide-y divide-pink-500">
                <a href="#" class="py-6 px-4 text-white block hover:text-pink-500 no-underline font-semibold text-xl font-open-sans">cloud</a>
                <a href="#" class="py-6 px-4 text-white block hover:text-pink-500 no-underline font-semibold text-xl font-open-sans">local</a>
                <a href="#" class="py-6 px-4 text-white block hover:text-pink-500 no-underline font-semibold text-xl font-open-sans">support</a>
                <a href="#" class="py-6 px-4 text-white block hover:text-pink-500 no-underline font-semibold text-xl font-open-sans">build a workshop</a>
                <a href="#" class="py-6 px-4 text-white block hover:text-pink-500 no-underline font-semibold text-xl font-open-sans">submit a workshop</a>
                <a href="/events" class="py-6 px-4 text-white block hover:text-pink-500 no-underline font-semibold text-xl font-open-sans">events</a>
                <a href="/blog" class="py-6 px-4 text-white block hover:text-pink-500 no-underline font-semibold text-xl font-open-sans">blog</a>
                <div class="my-4">
                    <Button href=" /cloud" class=" mx-auto text-center px-4 py-2">
                        <GitHubIcon class="h-5 w-5 inline-block align-middle mr-2" /><span class="text-xs font-open-sans inline-block align-middle">Log In withgithub</span>
                    </Button>
                </div>
            </div>
        </div>
    </nav>
    <!-- Navigation End -->

    <!-- Blur Overlay -->
    <div :class="mobileMenuVisible ? 'block fixed inset-0 backdrop-blur bg-white bg-opacity-40 z-30' : 'hidden'"
         class="transition-opacity duration-300" @click="toggleMenu"></div>

</template>