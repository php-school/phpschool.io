<script setup>
import GitHubIcon from "../Icons/GitHubIcon.vue";
import Logo from "./SiteLogo.vue";

const currentYear = new Date().getFullYear();
</script>
<template>
  <footer class="bg-cyan-600 font-sans text-sm text-white">
    <div class="mx-auto flex h-auto max-w-[2048px] flex-wrap items-center justify-between px-4 py-16 sm:px-6 lg:px-8">
      <figure class="mb-4 text-white sm:mb-0">
        <a href="/">
          <Logo />
        </a>
      </figure>

      <div class="flex w-full flex-col items-start justify-between justify-items-start gap-5 sm:w-auto sm:flex-row md:gap-10">
        <div class="mb-4 flex-grow sm:mb-0">
          <h4 class="mb-2 font-work-sans text-lg font-bold capitalize not-italic text-white">The School</h4>
          <ul class="list-none">
            <li><a href="#">PHP Cloud</a></li>
            <li><a href="#">PHP Terminal</a></li>
            <li><a href="#">The Docs</a></li>
          </ul>
        </div>

        <div class="mb-4 ml-0 flex-grow sm:mb-0 sm:ml-6">
          <h4 class="mb-2 font-work-sans text-lg font-bold capitalize not-italic text-white">The Community</h4>
          <ul class="list-none">
            <li><a href="#">Events</a></li>
            <li><a href="#">Blog</a></li>
            <li><a href="#">Need Help?</a></li>
          </ul>
        </div>

        <div class="mb-4 ml-0 flex-grow sm:mb-0 sm:ml-6">
          <h4 class="mb-2 font-work-sans text-lg font-bold capitalize not-italic text-white">The Creators</h4>
          <ul class="list-none">
            <li><a href="#">About us</a></li>
            <li><a href="#">Contact Us</a></li>
            <li><a href="#">History</a></li>
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
          <a href="https://github.com/php-school/phpschool.io" class="text-pink-400">Open Source on GitHub</a>
          & we welcome corrections/improvements
        </p>
      </div>

      <div class="">
        <a href="https://github.com/php-school/phpschool.io">
          <GitHubIcon class="h-10 w-10 hover:text-pink-400" />
        </a>
      </div>
    </div>
  </footer>
</template>
