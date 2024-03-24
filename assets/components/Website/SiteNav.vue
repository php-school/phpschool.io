<script setup>
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import GitHubIcon from "../Icons/GitHubIcon.vue";
import { ChevronDownIcon } from "@heroicons/vue/20/solid";
import SiteLogo from "./SiteLogo.vue";
import StudentDropdown from "../Online/StudentDropdown.vue";
import { onMounted, onUnmounted, ref } from "vue";

import { useStudentStore } from "../../stores/student";
import TwitterIcon from "../Icons/TwitterIcon.vue";
import SlackIcon from "../Icons/SlackIcon.vue";
import LoginWithGitHubButton from "../Online/LoginWithGitHubButton.vue";
import JoinSlack from "./JoinSlack.vue";
const studentStore = useStudentStore();

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

const slackModalOpen = ref(false);
</script>

<template>
  <nav class="relative z-50 w-full bg-nav-pattern bg-center px-4 py-3 sm:bg-nav-pattern sm:bg-[length:110%] sm:bg-center">
    <div class="mx-auto flex items-center justify-between">
      <!-- Logo -->
      <figure class="text-white">
        <a href="/">
          <SiteLogo class="h-8 w-8 md:h-10 md:w-10" />
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

          <li><router-link to="/events" class="cursor-pointer text-xs uppercase text-white transition duration-150 hover:text-pink-500">Events</router-link></li>
          <li><router-link to="/blog" class="cursor-pointer text-xs uppercase text-white transition duration-150 hover:text-pink-500">Blog</router-link></li>
        </ul>

        <ul v-if="links" class="m-0 ml-4 hidden h-full list-none items-center space-x-4 border-l border-pink-600 py-1 pl-4 lg:flex">
          <li class="m-0">
            <a :href="links.github" target="_blank" class="block" title="PHPSchool on Github">
              <GitHubIcon class="h-6 w-6 fill-current align-middle text-white hover:text-[#e91e63]"></GitHubIcon>
            </a>
          </li>
          <li class="social-nav__item">
            <a :href="links.twitter" target="_blank" class="block" title="PHPSchool on Twitter">
              <TwitterIcon class="h-5 w-5 fill-current align-middle text-white hover:text-[#e91e63]"></TwitterIcon>
            </a>
          </li>
          <li class="social-nav__item">
            <button @click="slackModalOpen = true" class="block" title="PHPSchool on Slack">
              <SlackIcon class="h-5 w-5 fill-current align-middle text-white hover:text-[#e91e63]"></SlackIcon>
            </button>
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
        <LoginWithGitHubButton />
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
          <LoginWithGitHubButton />
        </div>
      </div>
    </div>
  </nav>
  <!-- Navigation End -->

  <!-- Blur Overlay -->
  <div :class="mobileMenuVisible ? 'fixed inset-0 z-30 block bg-white bg-opacity-40 backdrop-blur' : 'hidden'" class="transition-opacity duration-300" @click="toggleMenu"></div>

  <JoinSlack :open="slackModalOpen" @close="slackModalOpen = false" />
</template>
