<script setup>
const exercises = [
  {
    title: "Hello World",
    description: "Simple Hello World exercise",
    type: "CLI",
  },
  {
    title: "Baby Steps",
    description: "Simple Addition",
    type: "CLI",
  },
  {
    title: "My First IO",
    description: "Read a file from the file system",
    type: "CLI",
  },
  {
    title: "Filtered LS",
    description: "Read files in a folder and filter by a given extension",
    type: "CLI",
  },
  {
    title: "Concerned about Separation?",
    description: "Separate code and utilise files and classes",
    type: "CLI",
  },
  {
    title: "Array We Go!",
    description: "Filter an array of file paths and map to SplFile objects",
    type: "CLI",
  },
  {
    title: "Exceptional Coding",
    description: "Introduction to Exceptions",
    type: "CLI",
  },
  {
    title: "Database Read",
    description: "Read an SQL databases contents",
    type: "CLI",
  },
  {
    title: "Time Server",
    description: "Build a Time Server!",
    type: "CLI",
  },
  {
    title: "HTTP JSON API",
    description: "HTTP JSON API - Servers JSON when it receives a GET request",
    type: "CGI",
  },
  {
    title: "Dependency Heaven",
    description: "An introduction to Composer dependency management",
    type: "CGI",
  },
];
</script>
<template>
  <div
    id="workshop-exercises"
    class="modal-border flex h-full flex-col items-center rounded-[24px] border-2 border-solid border-transparent bg-gray-800 text-left font-mono shadow-[-20px_20px_0_rgba(0,0,0,0.2)]"
  >
    <div class="w-full px-4 py-3 sm:px-6 md:py-5">
      <div class="flex w-full flex-col items-center">
        <h3 class="mt-4 text-lg font-medium leading-6 text-white">Learn You PHP! Exercises</h3>
        <p class="mb-2 mt-1 max-w-2xl text-sm text-gray-200 md:mb-4">Pick an exercise to start hacking!</p>
      </div>
    </div>
    <ul id="workshop-exercises-list" class="flex w-full flex-col">
      <li v-for="(exercise, i) in exercises" :key="exercise.title" class="group flex flex-row last:mb-4 last:md:mb-0">
        <div class="flex flex-1 select-none items-center px-3 py-2">
          <div class="mr-3 flex h-6 flex-col items-center justify-center md:mr-4 md:h-10">
            <a href="#" class="relative block">
              <img alt="workshop" src="../../../../../img/cloud/core-workshops.png" class="mx-auto h-5 w-5 object-cover md:h-8 md:w-8" />
            </a>
          </div>
          <div class="mr-8 flex-1 pl-1 text-white">
            <div class="text-[10px] font-medium">{{ exercise.title }}</div>
            <div class="hidden text-[10px] text-gray-300 md:flex">
              {{ exercise.description }}
            </div>
          </div>
          <div class="rounded-full bg-pink-600 px-2 py-1 text-[8px] text-gray-200">
            {{ exercise.type }}
          </div>
          <span class="ml-6 flex justify-end md:ml-0 md:w-24">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              aria-hidden="true"
              class="h-6 w-6 rounded-full border-2 border-solid border-pink-500 !fill-none text-pink-200"
              :class="{ 'animate-bounce': i === 0 }"
            >
              <path stroke-linecap="round" stroke-linejoin="round" d="M12.75 15l3-3m0 0l-3-3m3 3h-7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </span>
        </div>
      </li>
    </ul>
  </div>
</template>
