<script setup>
import GitHubIcon from "../Icons/GitHubIcon.vue";
import Logo from "./SiteLogo.vue";
import { ref } from "vue";
import JoinSlack from "./JoinSlack.vue";
import Contributors from "./ProjectContributors.vue";

const currentYear = new Date().getFullYear();

const slackModalOpen = ref(false);
const contributorModalOpen = ref(false);
</script>
<template>
  <footer class="bg-cyan-600 font-sans text-sm text-white">
    <div class="mx-auto flex h-auto max-w-[2048px] flex-wrap items-center justify-between px-4 py-16 sm:px-6 lg:px-8">
      <figure class="mb-4 text-white sm:mb-0">
        <a href="/"><Logo /></a>
      </figure>

      <div class="flex w-full flex-col items-start justify-between justify-items-start gap-5 sm:w-auto sm:flex-row md:gap-10">
        <div class="mb-4 flex-grow sm:mb-0">
          <h4 class="mb-2 font-work-sans text-lg font-bold capitalize not-italic text-white">The School</h4>
          <ul class="list-none">
            <li class="py-1"><router-link to="/online" class="decoration-pink-600 decoration-2 underline-offset-4 hover:underline">PHP School Online</router-link></li>
            <li class="py-1"><router-link to="/online" class="decoration-pink-600 decoration-2 underline-offset-4 hover:underline">PHP School School Offline</router-link></li>
            <li class="py-1"><router-link to="/docs" class="decoration-pink-600 decoration-2 underline-offset-4 hover:underline">The Docs</router-link></li>
          </ul>
        </div>

        <div class="mb-4 ml-0 flex-grow sm:mb-0 sm:ml-6">
          <h4 class="mb-2 font-work-sans text-lg font-bold capitalize not-italic text-white">The Community</h4>
          <ul class="list-none">
            <li class="py-1"><router-link to="/events" class="decoration-pink-600 decoration-2 underline-offset-4 hover:underline">Events</router-link></li>
            <li class="py-1"><router-link to="/blog" class="decoration-pink-600 decoration-2 underline-offset-4 hover:underline">Blog</router-link></li>
            <li class="py-1"><span @click="slackModalOpen = true" class="cursor-pointer decoration-pink-600 decoration-2 underline-offset-4 hover:underline">Slack</span></li>
          </ul>
        </div>

        <div class="mb-4 ml-0 flex-grow sm:mb-0 sm:ml-6">
          <h4 class="mb-2 font-work-sans text-lg font-bold capitalize not-italic text-white">The Creators</h4>
          <ul class="list-none">
            <li class="py-1"><span @click="contributorModalOpen = true" class="cursor-pointer decoration-pink-600 decoration-2 underline-offset-4 hover:underline">Contributors</span></li>
            <li class="py-1"><a href="mailto:phpschool.team@gmail.com" class="decoration-pink-600 decoration-2 underline-offset-4 hover:underline">Email Us</a></li>
            <li class="py-1"><a href="https://github.com/php-school" target="_blank" class="decoration-pink-600 decoration-2 underline-offset-4 hover:underline">GitHub</a></li>
          </ul>
        </div>
      </div>
    </div>

    <div class="mx-auto flex w-full max-w-[2048px] justify-center px-4 sm:px-6 lg:px-8">
      <hr class="w-full border-t-0 border-solid border-white" />
    </div>

    <div class="mx-auto flex h-auto max-w-[2048px] flex-wrap items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
      <p>&copy; Copyright {{ currentYear }}</p>

      <div class="mb-4 sm:mb-0">
        <p>
          This Site is
          <a href="https://github.com/php-school/phpschool.io" class="underline decoration-pink-600 decoration-2 underline-offset-4">Open Source on GitHub</a>
          & we welcome corrections/improvements
        </p>
      </div>

      <div class="">
        <a href="https://github.com/php-school/phpschool.io">
          <GitHubIcon class="h-10 w-10 hover:text-pink-600" />
        </a>
      </div>
    </div>
    <JoinSlack :open="slackModalOpen" @close="slackModalOpen = false" />
    <Contributors v-if="contributorModalOpen" :open="contributorModalOpen" @close="contributorModalOpen = false" />
  </footer>
</template>
