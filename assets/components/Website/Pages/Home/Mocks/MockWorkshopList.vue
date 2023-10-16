<template>
    <div id="workshop-exercises"
         class="text-left flex flex-col items-center bg-gray-800 rounded-[24px] shadow-[-10px_10px_0_rgba(0,0,0,0.2)] h-full max-w-lg">
        <div class="px-2 py-2 sm:px-6 border-b w-full">
            <div class="w-full flex flex-col">
                <h3 class="text-lg leading-6 font-medium text-white">PHP School Workshops</h3>
                <p class="mt-1 mb-4 max-w-2xl text-sm text-gray-200">Pick a workshop to try it out online!</p>
            </div>
        </div>
        <ul id="workshop-exercises-list" class="flex flex-col w-full divide divide-y">
            <li class="group flex flex-row">
                <div class="select-none flex flex-1 items-center px-3 py-2">
                    <div class="flex flex-col w-10 h-10 justify-center items-center mr-4">
                        <a href="#" class="block relative">
                            <img alt="workshop" src="../../../../../img/cloud/core-workshops.png" class="mx-auto object-cover h-8 w-8">
                        </a>
                    </div>
                    <div class="flex-1 pl-1 mr-8 text-white">
                        <div class="font-medium text-[10px]">Learn You PHP!</div>
                        <div class="text-gray-300 text-[10px]">An introduction to PHP's core features: i/o, http, arrays, exceptions and so on.</div>
                    </div>
                    <div class="text-gray-200 text-[8px] bg-pink-600 py-1 px-3 rounded-full">CLI</div>
                    <span class="w-24 flex justify-end">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" aria-hidden="true"
                             class="text-pink-200 h-6 w-6 rounded-full border-2 border-solid border-pink-500 !fill-none">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12.75 15l3-3m0 0l-3-3m3 3h-7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </span>
                </div>
            </li>
            <li class="group flex flex-row">
                <div class="select-none flex flex-1 items-center px-3 py-2">
                    <div class="flex flex-col w-10 h-10 justify-center items-center mr-4"><a href="#"
                                                                                             class="block relative"><img
                            alt="workshop" src="../../../../../img/cloud/core-workshops.png" class="mx-auto object-cover h-8 w-8"></a>
                    </div>
                    <div class="flex-1 pl-1 mr-8 text-white">
                        <div class="font-medium text-[10px]">PHP8 Appreciate</div>
                        <div class="text-gray-300 text-[10px]">2020 PHP: A showcase and classroom for the cutting edge features of PHP 8</div>
                    </div>
                    <div class="text-gray-200 text-[8px] bg-pink-600 py-1 px-3 rounded-full">CLI</div>
                    <span class="w-24 flex justify-end">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" aria-hidden="true"
                             class="text-pink-200 h-6 w-6 rounded-full border-2 border-solid border-pink-500 !fill-none ">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12.75 15l3-3m0 0l-3-3m3 3h-7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </span>
                </div>
            </li>
        </ul>
    </div>
</template>
<script setup>
</script>