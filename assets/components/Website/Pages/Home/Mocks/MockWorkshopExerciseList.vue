<script setup>
const exercises = [
    {
        title: 'Hello World',
        description: 'Simple Hello World exercise',
        type: 'CLI'
    },
    {
        title: 'Baby Steps',
        description: 'Simple Addition',
        type: 'CLI'
    },
    {
        title: 'My First IO',
        description: 'Read a file from the file system',
        type: 'CLI'
    },
    {
        title: 'Filtered LS',
        description: 'Read files in a folder and filter by a given extension',
        type: 'CLI'
    },
    {
        title: 'Concerned about Separation?',
        description: 'Separate code and utilise files and classes',
        type: 'CLI'
    },
    {
        title: 'Array We Go!',
        description: 'Filter an array of file paths and map to SplFile objects',
        type: 'CLI'
    },
    {
        title: 'Exceptional Coding',
        description: 'Introduction to Exceptions',
        type: 'CLI'
    },
    {
        title: 'Database Read',
        description: 'Read an SQL databases contents',
        type: 'CLI'
    },
    {
        title: 'Time Server',
        description: 'Build a Time Server!',
        type: 'CLI'
    },
    {
        title: 'HTTP JSON API',
        description: 'HTTP JSON API - Servers JSON when it receives a GET request',
        type: 'CGI'
    },
    {
        title: 'Dependency Heaven',
        description: 'An introduction to Composer dependency management',
        type: 'CGI'
    }
]
</script>
<template>
    <div
        id="workshop-exercises"
        class="font-mono text-left flex flex-col items-center bg-gray-800 rounded-[24px] h-full shadow-[-20px_20px_0_rgba(0,0,0,0.2)] modal-border border-2 border-solid border-transparent"
    >
        <div class="px-4 py-3 md:py-5 sm:px-6 w-full">
            <div class="w-full flex flex-col items-center">
                <h3 class="text-lg leading-6 font-medium text-white mt-4">
                    Learn You PHP! Exercises
                </h3>
                <p class="mt-1 mb-2 md:mb-4 max-w-2xl text-sm text-gray-200">
                    Pick an exercise to start hacking!
                </p>
            </div>
        </div>
        <ul id="workshop-exercises-list" class="flex flex-col w-full">
            <li
                v-for="(exercise, i) in exercises"
                :key="exercise.title"
                class="group flex flex-row last:mb-4 last:md:mb-0"
            >
                <div class="select-none flex flex-1 items-center px-3 py-2">
                    <div class="flex flex-col h-6 md:h-10 justify-center items-center mr-3 md:mr-4">
                        <a href="#" class="block relative">
                            <img
                                alt="workshop"
                                src="../../../../../img/cloud/core-workshops.png"
                                class="mx-auto object-cover h-5 w-5 md:h-8 md:w-8"
                            />
                        </a>
                    </div>
                    <div class="flex-1 pl-1 mr-8 text-white">
                        <div class="font-medium text-[10px]">{{ exercise.title }}</div>
                        <div class="text-gray-300 text-[10px] hidden md:flex">
                            {{ exercise.description }}
                        </div>
                    </div>
                    <div class="text-gray-200 text-[8px] bg-pink-600 py-1 px-2 rounded-full">
                        {{ exercise.type }}
                    </div>
                    <span class="md:w-24 ml-6 md:ml-0 flex justify-end">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            aria-hidden="true"
                            class="text-pink-200 h-6 w-6 rounded-full border-2 border-solid border-pink-500 !fill-none"
                            :class="{ 'animate-bounce': i === 0 }"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12.75 15l3-3m0 0l-3-3m3 3h-7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            ></path>
                        </svg>
                    </span>
                </div>
            </li>
        </ul>
    </div>
</template>
