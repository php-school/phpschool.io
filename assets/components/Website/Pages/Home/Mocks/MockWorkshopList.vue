<script setup>
const workshops = [
  {
    title: "Learn You PHP!",
    description: "An introduction to PHP's core features: i/o, http, arrays, exceptions and so on.",
  },
  {
    title: "PHP8 Appreciate",
    description: "2020 PHP: A showcase and classroom for the cutting edge features of PHP 8",
  },
];
</script>
<template>
  <div
    id="workshop-exercises"
    class="modal-border flex h-full max-w-lg flex-col items-center rounded-[24px] border-2 border-solid border-transparent bg-gray-800 text-left font-mono shadow-[-20px_20px_0_rgba(0,0,0,0.2)]"
  >
    <div class="w-full px-4 py-3 sm:px-6 md:py-5">
      <div class="flex w-full flex-col items-center">
        <h3 class="mt-4 text-base font-medium leading-6 text-white md:text-lg">PHP School Workshops</h3>
        <p class="mb-2 mt-1 text-center text-xs text-gray-200 md:mb-4 md:text-sm">Pick a workshop to try it out online!</p>
      </div>
    </div>
    <ul id="workshop-exercises-list" class="flex w-full flex-col">
      <li v-for="workshop in workshops" :key="workshop.code" class="group flex flex-row last:mb-4">
        <div class="flex flex-1 select-none items-center justify-between px-3 py-2">
          <div class="mr-4 flex h-6 flex-col items-center justify-center md:h-10">
            <a href="#" class="relative block">
              <img alt="workshop" src="../../../../../img/cloud/core-workshops.png" class="mx-auto h-5 w-5 object-cover md:h-8 md:w-8" />
            </a>
          </div>
          <div class="mr-8 flex-1 pl-1 text-white">
            <div class="text-[10px] font-medium">{{ workshop.title }}</div>
            <div class="hidden text-[10px] text-gray-300 md:flex">
              {{ workshop.description }}
            </div>
          </div>
          <span class="flex justify-end">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              aria-hidden="true"
              class="h-6 w-6 rounded-full border-2 border-solid border-pink-500 !fill-none text-pink-200"
            >
              <path stroke-linecap="round" stroke-linejoin="round" d="M12.75 15l3-3m0 0l-3-3m3 3h-7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </span>
        </div>
      </li>
    </ul>
  </div>
</template>
