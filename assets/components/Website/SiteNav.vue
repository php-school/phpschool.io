<script setup>
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import Button from "./PrimaryButton.vue";
import GitHubIcon from "../Icons/GitHubIcon.vue";
import { ChevronDownIcon } from "@heroicons/vue/20/solid";
import Logo from "./SiteLogo.vue";
import StudentDropdown from "../Online/StudentDropdown.vue";
import { onMounted, onUnmounted, ref } from "vue";

import { useStudentStore } from "../../stores/student";
const studentStore = useStudentStore();

defineProps({
  compact: {
    type: Boolean,
    default: false,
  },
});

const links = {
  github: "https://github.com/php-school/learn-you-php",
  twitter: "https://twitter.com/PHPSchoolTeam",
  slack: "https://phpschool.herokuapp.com",
  discussions: "https://github.com/php-school/discussions",
  workshop: "https://github.com/php-school/php-workshop",
  "github-website": "https://github.com/php-school/phpschool.io",
};

const mobileMenuVisible = ref(false);
const showMobileMenuBlur = ref(false);

const hideMobileMenu = () => {
  if (window.innerWidth > 640) {
    mobileMenuVisible.value = false;
  }
};

onUnmounted(() => {
  window.removeEventListener("resize", hideMobileMenu);
});

const toggleMenu = () => {
  mobileMenuVisible.value = !mobileMenuVisible.value;
  showMobileMenuBlur.value = mobileMenuVisible.value;
};

onMounted(() => {
  window.addEventListener("resize", hideMobileMenu);
  hideMobileMenu();
});

onUnmounted(() => {
  window.removeEventListener("resize", hideMobileMenu);
});
</script>

<template>
  <nav class="relative z-50 w-full bg-nav-pattern bg-center px-4 py-2 sm:bg-nav-pattern sm:bg-[length:110%] sm:bg-center">
    <div class="mx-auto flex items-center justify-between">
      <!-- Logo -->
      <figure class="text-white">
        <a href="/">
          <Logo :class="[compact ? 'h-7 w-7' : 'h-10 w-10 md:h-12 md:w-12']" />
        </a>
      </figure>

      <!-- Links (Hidden on Mobile) -->
      <div class="hidden items-center justify-center sm:flex">
        <ul class="flex space-x-2 md:space-x-4">
          <li>
            <router-link to="/" class="cursor-pointer text-xs uppercase text-white transition duration-150 hover:text-pink-500">Home</router-link>
          </li>
          <li>
            <router-link to="/online" class="cursor-pointer text-xs uppercase text-white transition duration-150 hover:text-pink-500">Workshops</router-link>
          </li>
          <li>
            <router-link to="/offline" class="cursor-pointer text-xs uppercase text-white transition duration-150 hover:text-pink-500">Offline</router-link>
          </li>

          <!-- DROPDOWN START -->
          <Menu as="li" class="relative mt-1 text-xs uppercase text-white" v-slot="{ open }">
            <div class="group">
              <MenuButton
                :class="open ? 'text-pink-500' : 'text-white'"
                class="inline-flex w-full cursor-pointer items-center justify-center text-xs uppercase transition duration-200 group-hover:text-pink-500 focus:outline-none"
              >
                For Developers
                <ChevronDownIcon
                  :class="open ? 'rotate-180 text-pink-500' : ''"
                  class="ml-2 h-5 w-5 text-gray-400 transition duration-200 group-hover:text-pink-500 focus:outline-none"
                  aria-hidden="true"
                />
              </MenuButton>
            </div>

            <transition
              enter-active-class="transition ease-out duration-100"
              enter-from-class="transform opacity-0 scale-95"
              enter-to-class="transform opacity-100 scale-100"
              leave-active-class="transition ease-in duration-75"
              leave-from-class="transform opacity-100 scale-100"
              leave-to-class="transform opacity-0 scale-95"
            >
              <MenuItems class="absolute right-0 z-40 mt-4 w-56 origin-top-right rounded-md bg-gray-800 text-left shadow-brand-shadow focus:outline-none">
                <div class="py-1">
                  <RouterLink v-slot="{ href, navigate }" to="/docs" custom>
                    <MenuItem v-slot="{ active, close }">
                      <a :href="href" @click.prevent="navigate() && close()" :class="[active ? 'bg-gray-700 text-pink-500' : 'text-white', 'block px-4 py-4 text-xs hover:no-underline']">
                        Workshop documentation
                      </a>
                    </MenuItem>
                  </RouterLink>

                  <RouterLink v-slot="{ href, navigate }" to="/submit" custom>
                    <MenuItem v-slot="{ active, close }">
                      <a :href="href" @click.prevent="navigate() && close()" :class="[active ? 'bg-gray-700 text-pink-500' : 'text-white', 'block px-4 py-4 text-xs hover:no-underline']">
                        Submit your workshop
                      </a>
                    </MenuItem>
                  </RouterLink>
                </div>
              </MenuItems>
            </transition>
          </Menu>

          <li>
            <router-link to="/events" class="cursor-pointer text-xs uppercase text-white transition duration-150 hover:text-pink-500">Events</router-link>
          </li>
          <li>
            <router-link to="/blog" class="cursor-pointer text-xs uppercase text-white transition duration-150 hover:text-pink-500">Blog</router-link>
          </li>
        </ul>

        <ul v-if="links" class="m-0 ml-4 hidden h-full list-none items-center space-x-4 border-l border-pink-600 py-1 pl-4 lg:flex">
          <li class="m-0">
            <a :href="links.github" target="_blank" class="block" title="PHPSchool on Github">
              <svg
                class="h-5 w-5 fill-current align-middle text-white hover:text-[#e91e63]"
                version="1.1"
                xmlns="http://www.w3.org/2000/svg"
                xmlns:xlink="http://www.w3.org/1999/xlink"
                xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/"
                x="0px"
                y="0px"
                width="32px"
                height="31.2px"
                viewBox="0 0 32 31.2"
                enable-background="new 0 0 32 31.2"
                xml:space="preserve"
              >
                <defs></defs>
                <path
                  d="M16,0C7.2,0,0,7.2,0,16c0,7.1,4.6,13.1,10.9,15.2c0.8,0.1,1.1-0.3,1.1-0.8c0-0.4,0-1.4,0-2.8c-4.4,1-5.4-2.1-5.4-2.1c-0.7-1.8-1.8-2.3-1.8-2.3c-1.5-1,0.1-1,0.1-1c1.6,0.1,2.5,1.6,2.5,1.6c1.4,2.4,3.7,1.7,4.7,1.3c0.1-1,0.6-1.7,1-2.1c-3.6-0.4-7.3-1.8-7.3-7.9c0-1.7,0.6-3.2,1.6-4.3c-0.2-0.4-0.7-2,0.2-4.2c0,0,1.3-0.4,4.4,1.6c1.3-0.4,2.6-0.5,4-0.5c1.4,0,2.7,0.2,4,0.5c3.1-2.1,4.4-1.6,4.4-1.6c0.9,2.2,0.3,3.8,0.2,4.2c1,1.1,1.6,2.5,1.6,4.3c0,6.1-3.7,7.5-7.3,7.9c0.6,0.5,1.1,1.5,1.1,2.9c0,2,0,3.9,0,4.4c0,0.4,0.3,0.9,1.1,0.7C27.4,29.1,32,23.1,32,16C32,7.2,24.8,0,16,0z"
                />
              </svg>
            </a>
          </li>
          <li class="social-nav__item">
            <a :href="links.twitter" target="_blank" class="block" title="PHPSchool on Twitter">
              <svg
                class="h-5 w-5 fill-current align-middle text-white hover:text-[#e91e63]"
                version="1.1"
                xmlns="http://www.w3.org/2000/svg"
                xmlns:xlink="http://www.w3.org/1999/xlink"
                xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/"
                x="0px"
                y="0px"
                width="273.5px"
                height="222.3px"
                viewBox="0 0 273.5 222.3"
                enable-background="new 0 0 273.5 222.3"
                xml:space="preserve"
              >
                <defs></defs>
                <path
                  d="M273.5,26.3c-10.1,4.5-20.9,7.5-32.2,8.8c11.6-6.9,20.5-17.9,24.7-31c-10.9,6.4-22.9,11.1-35.7,13.6C220.1,6.8,205.5,0,189.4,0c-31,0-56.1,25.1-56.1,56.1c0,4.4,0.5,8.7,1.5,12.8c-46.7-2.4-88-24.7-115.7-58.6c-4.8,8.3-7.6,17.9-7.6,28.2c0,19.5,9.9,36.6,25,46.7c-9.2-0.3-17.8-2.8-25.4-7c0,0.2,0,0.5,0,0.7c0,27.2,19.3,49.8,45,55c-4.7,1.3-9.7,2-14.8,2c-3.6,0-7.1-0.4-10.6-1c7.1,22.3,27.9,38.5,52.4,39c-19.2,15-43.4,24-69.7,24c-4.5,0-9-0.3-13.4-0.8c24.8,15.9,54.3,25.2,86,25.2c103.2,0,159.6-85.5,159.6-159.6c0-2.4-0.1-4.9-0.2-7.3C256.5,47.4,266,37.5,273.5,26.3z"
                />
              </svg>
            </a>
          </li>
          <li class="social-nav__item">
            <a :href="links.slack" target="_blank" class="block" title="PHPSchool on Slack">
              <svg
                class="h-5 w-5 fill-current align-middle text-white hover:text-[#e91e63]"
                version="1.1"
                xmlns="http://www.w3.org/2000/svg"
                xmlns:xlink="http://www.w3.org/1999/xlink"
                xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/"
                x="0px"
                y="0px"
                width="125.1px"
                height="125.1px"
                viewBox="0 0 125.1 125.1"
                enable-background="new 0 0 125.1 125.1"
                xml:space="preserve"
              >
                <defs></defs>
                <g>
                  <rect x="54.1" y="53.8" transform="matrix(0.3176 0.9482 -0.9482 0.3176 102.0437 -16.5935)" width="17" height="17.6" />
                  <g>
                    <rect x="54.1" y="53.8" transform="matrix(0.3176 0.9482 -0.9482 0.3176 102.0437 -16.5935)" width="17" height="17.6" />
                    <path
                      d="M119.8,45.3c-12.9-43-31.5-53-74.5-40.1s-53,31.5-40.1,74.5s31.5,53,74.5,40.1S132.7,88.3,119.8,45.3z M98.1,73.2L90,75.9l2.8,8.4c1.1,3.4-0.7,7.1-4.1,8.2c-0.7,0.2-1.5,0.4-2.2,0.3c-2.6-0.1-5.1-1.8-6-4.4L77.7,80L61,85.7l2.8,8.4c1.1,3.4-0.7,7.1-4.1,8.2c-0.7,0.2-1.5,0.4-2.2,0.3c-2.6-0.1-5.1-1.8-6-4.4l-2.8-8.4l-8.1,2.7c-0.7,0.2-1.5,0.4-2.2,0.3c-2.6-0.1-5.1-1.8-6-4.4c-1.1-3.4,0.7-7.1,4.1-8.2l8.1-2.7l-5.4-16.1l-8.1,2.7c-0.7,0.2-1.5,0.4-2.2,0.3c-2.6-0.1-5.1-1.8-6-4.4c-1.1-3.4,0.7-7.1,4.1-8.2l8.1-2.7l-2.8-8.4c-1.1-3.4,0.7-7.1,4.1-8.2c3.4-1.1,7.1,0.7,8.2,4.1l2.8,8.4l16.7-5.6L61.3,31c-1.1-3.4,0.7-7.1,4.1-8.2s7.1,0.7,8.2,4.1l2.8,8.4l8.1-2.7c3.4-1.1,7.1,0.7,8.2,4.1c1.1,3.4-0.7,7.1-4.1,8.2l-8.1,2.7l5.4,16.1L94,61c3.4-1.1,7.1,0.7,8.2,4.1C103.3,68.4,101.5,72.1,98.1,73.2z"
                    />
                  </g>
                </g>
              </svg>
            </a>
          </li>
        </ul>
      </div>

      <!-- Mobile Menu Button -->
      <div class="block sm:hidden">
        <button @click="mobileMenuVisible = !mobileMenuVisible" class="text-white hover:text-gray-300 focus:outline-none">
          <img src="../../img/cloud/bars-solid-pink.svg" alt="" class="h-6 w-6" />
        </button>
      </div>

      <!-- Sign In Button -->
      <div v-if="!studentStore.student" class="hidden sm:block">
        <Button to="/online" class="flex items-center px-2 py-2" :class="{ '!my-0': compact }">
          <GitHubIcon class="mr-2 h-5 w-5" />
          <span class="flex text-xs font-normal">
            Log In
            <span class="hidden md:flex">&nbsp;with github</span>
          </span>
        </Button>
      </div>

      <ul v-if="studentStore.student" class="order-3 hidden sm:flex">
        <li>
          <student-dropdown />
        </li>
      </ul>
    </div>
    <!-- Mobile Menu Links and Sign In Button (Hidden by Default) -->
    <div :class="mobileMenuVisible ? 'block' : 'hidden sm:hidden '">
      <div class="flex flex-col divide-y divide-pink-500 text-center text-white">
        <router-link to="/online" class="block px-4 py-6 font-open-sans text-xl font-semibold uppercase text-white no-underline hover:text-pink-500">Workshops</router-link>
        <router-link to="/offline" class="block px-4 py-6 font-open-sans text-xl font-semibold uppercase text-white no-underline hover:text-pink-500">Offline</router-link>
        <router-link to="/docs" class="block px-4 py-6 font-open-sans text-xl font-semibold uppercase text-white no-underline hover:text-pink-500">Workshop Documentation</router-link>
        <router-link to="/submit" class="block px-4 py-6 font-open-sans text-xl font-semibold uppercase text-white no-underline hover:text-pink-500">Submit Your Workshop</router-link>
        <router-link to="/events" class="block px-4 py-6 font-open-sans text-xl font-semibold uppercase text-white no-underline hover:text-pink-500">Events</router-link>
        <router-link to="/blog" class="block px-4 py-6 font-open-sans text-xl font-semibold uppercase text-white no-underline hover:text-pink-500">Blog</router-link>
        <div v-if="studentStore.student" class="py-6">
          <student-dropdown />
        </div>
        <div v-else class="flex justify-center">
          <Button to="/online" class="flex items-center px-2 py-2">
            <GitHubIcon class="mr-2 h-5 w-5" />
            <span class="flex text-xs font-normal">Log In &nbsp;with github</span>
          </Button>
        </div>
      </div>
    </div>
  </nav>
  <!-- Navigation End -->

  <!-- Blur Overlay -->
  <div :class="mobileMenuVisible ? 'fixed inset-0 z-30 block bg-white bg-opacity-40 backdrop-blur' : 'hidden'" class="transition-opacity duration-300" @click="toggleMenu"></div>
</template>
