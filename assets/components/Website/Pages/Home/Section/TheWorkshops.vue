<script setup>
import PageSection from "../../PageSection.vue";

import { useWorkshopStore } from "../../../../../stores/workshops";
import GitHubIcon from "../../../../Icons/GitHubIcon.vue";
const workshopStore = useWorkshopStore();

const { loggedIn } = defineProps({
  loggedIn: Boolean,
});
</script>

<template>
  <PageSection>
    <template #title>The Workshops</template>

    <div id="the-workshops" class="flex grid grid-cols-2 flex-wrap gap-x-16 py-4">
      <div class="mb-4 w-full px-4">
        <img src="../../../../../img/cloud/core-workshops.png" alt="" class="mb-4 h-48 w-auto" />
        <h4 class="mb-4 mt-0 p-0 font-work-sans text-2xl font-bold capitalize not-italic text-white">core</h4>
        <p class="mb-4 text-base text-white">
          Workshops meticulously crafted by the PHP School team with the utmost quality to help
          <i>you</i>
          learn the core concepts of PHP and web development.
        </p>
      </div>

      <div class="mb-4 w-full px-4">
        <img src="../../../../../img/cloud/community-workshops.png" alt="" class="mb-4 h-48 w-auto" />
        <h4 class="mb-4 mt-0 p-0 font-work-sans text-2xl font-bold capitalize not-italic text-white">Community</h4>
        <p class="text-balance mb-4 text-base text-white">
          Community workshops are those created by, well, you! They are not officially maintained by the PHP School team and not all of them are compatible with the online system. Compatible ones are
          labeled, otherwise, you can always run them
          <router-link class="text-[#e91e63] hover:underline" to="/offline">offline</router-link>
          .
        </p>
      </div>
      <ul class="flex flex-col overflow-hidden">
        <li v-for="workshop in workshopStore.getAllCoreWorkshops()" :key="workshop.code" class="group flex flex-row last:rounded-b-lg">
          <div class="hidden flex-1 select-none items-center py-4 md:flex">
            <div class="mr-16 flex-1 pl-1 text-white">
              <div class="flex items-center font-mono text-xl font-medium text-pink-600">
                <svg class="ml-1 mr-2 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>

                <router-link v-if="loggedIn" :to="'/online/' + workshop.code">{{ workshop.name }}</router-link>
                <span v-else>{{ workshop.name }}</span>

                <div class="ml-3 flex-none rounded-full bg-pink-500/10 px-2 py-0.5 text-xs font-medium text-pink-500 ring-1 ring-inset ring-pink-500/30">Online</div>

                <a target="_blank" :href="workshop.repo_url">
                  <GitHubIcon class="ml-2 h-5 w-5 text-white hover:text-pink-600"></GitHubIcon>
                </a>
              </div>
              <div class="ml-9 mt-4 text-base">
                {{ workshop.description }}
              </div>
            </div>
          </div>
        </li>
      </ul>
      <div>
        <ul class="flex flex-col overflow-hidden">
          <li v-for="workshop in workshopStore.getAllCommunityWorkshops()" :key="workshop.code" class="group flex flex-row last:rounded-b-lg">
            <div class="hidden flex-1 select-none items-center py-4 md:flex">
              <div class="mr-16 flex-1 pl-1 text-white">
                <div class="flex items-center font-mono text-xl font-medium text-pink-600">
                  <svg class="ml-1 mr-2 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                  </svg>
                  <span>{{ workshop.name }}</span>
                  <p class="ml-2 mt-1 text-sm text-gray-300">
                    by
                    <a target="_blank" class="underline decoration-pink-600 decoration-2 underline-offset-4" :href="workshop.submitter_contact">{{ workshop.submitter_name }}</a>
                  </p>
                  <a target="_blank" :href="workshop.repo_url">
                    <GitHubIcon class="ml-2 mt-1 h-5 w-5 text-white hover:text-pink-600"></GitHubIcon>
                  </a>
                </div>
                <div class="ml-9 mt-4 text-base">
                  {{ workshop.description }}
                </div>
              </div>
            </div>
          </li>
        </ul>
        <div class="px-4 text-sm">
          <p class="mb-4 mt-8 text-gray-300">
            Visit the
            <router-link to="/docs" class="text-[#e91e63] hover:underline">documentation</router-link>
            to learn how to build your own Workshop.
          </p>
          <p class="mb-4 text-gray-300">
            <router-link to="/submit" class="text-[#e91e63] hover:underline">Submit your workshop here</router-link>
            so it appears here and can be installable with the workshop manager!?
          </p>
        </div>
      </div>
    </div>
  </PageSection>
</template>
