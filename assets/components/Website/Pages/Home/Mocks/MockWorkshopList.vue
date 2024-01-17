<script setup>
const workshops = [
    {
        title: 'Learn You PHP!',
        description:
            "An introduction to PHP's core features: i/o, http, arrays, exceptions and so on."
    },
    {
        title: 'PHP8 Appreciate',
        description: '2020 PHP: A showcase and classroom for the cutting edge features of PHP 8'
    }
]
</script>
<template>
    <div
        id="workshop-exercises"
        class="font-mono text-left flex flex-col items-center bg-gray-800 rounded-[24px] shadow-[-20px_20px_0_rgba(0,0,0,0.2)] h-full max-w-lg modal-border border-2 border-solid border-transparent"
    >
        <div class="px-4 py-3 md:py-5 sm:px-6 w-full">
            <div class="w-full flex flex-col items-center">
                <h3 class="text-base md:text-lg leading-6 font-medium text-white mt-4">
                    PHP School Workshops
                </h3>
                <p class="mt-1 mb-2 md:mb-4 text-xs md:text-sm text-gray-200 text-center">
                    Pick a workshop to try it out online!
                </p>
            </div>
        </div>
        <ul id="workshop-exercises-list" class="flex flex-col w-full">
            <li
                v-for="workshop in workshops"
                :key="workshop.code"
                class="group flex flex-row last:mb-4"
            >
                <div class="select-none flex flex-1 items-center px-3 py-2 justify-between">
                    <div class="flex flex-col h-6 md:h-10 justify-center items-center mr-4">
                        <a href="#" class="block relative">
                            <img
                                alt="workshop"
                                src="../../../../../img/cloud/core-workshops.png"
                                class="mx-auto object-cover h-5 w-5 md:h-8 md:w-8"
                            />
                        </a>
                    </div>
                    <div class="flex-1 pl-1 mr-8 text-white">
                        <div class="font-medium text-[10px]">{{ workshop.title }}</div>
                        <div class="text-gray-300 text-[10px] hidden md:flex">
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
                            class="text-pink-200 h-6 w-6 rounded-full border-2 border-solid border-pink-500 !fill-none"
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
