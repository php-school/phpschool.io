<template>
    <div id="workshop-exercises"
         class="font-mono text-left flex flex-col items-center bg-gray-800 rounded-[24px] h-full shadow-[-20px_20px_0_rgba(0,0,0,0.2)] modal-border border-2 border-solid border-transparent">
        <div class="px-4 py-5 sm:px-6 w-full">
            <div class="w-full flex flex-col items-center"><h3 class="text-lg leading-6 font-medium text-white">
                Learn You PHP! Exercises</h3>
                <p class="mt-1 mb-4 max-w-2xl text-sm text-gray-200">Pick an exercise to start hacking!</p>
            </div>
        </div>
        <ul id="workshop-exercises-list" class="flex flex-col w-full">
            <li class="group flex flex-row">
                <div class="select-none flex flex-1 items-center px-3 py-2">
                    <div class="flex flex-col w-10 h-10 justify-center items-center mr-4">
                        <a href="#" class="block relative">
                            <img alt="workshop" src="../../../../../img/cloud/core-workshops.png" class="mx-auto object-cover h-8 w-8">
                        </a>
                    </div>
                    <div class="flex-1 pl-1 mr-8 text-white">
                        <div class="font-medium text-[10px]">Hello World</div>
                        <div class="text-gray-300 text-[10px]">Simple Hello World exercise</div>
                    </div>
                    <div class="text-gray-200 text-[8px] bg-pink-600 py-1 px-3 rounded-full">CLI</div>
                    <span class="w-24 flex justify-end">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" aria-hidden="true"
                             class="text-pink-200 h-6 w-6 rounded-full border-2 border-solid border-pink-500 !fill-none animate-bounce">
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
                        <div class="font-medium text-[10px]">Baby Steps</div>
                        <div class="text-gray-300 text-[10px]">Simple Addition</div>
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
            <li class="group flex flex-row">
                <div class="select-none flex flex-1 items-center px-3 py-2">
                    <div class="flex flex-col w-10 h-10 justify-center items-center mr-4"><a href="#"
                                                                                             class="block relative"><img
                            alt="workshop" src="../../../../../img/cloud/core-workshops.png" class="mx-auto object-cover h-8 w-8"></a>
                    </div>
                    <div class="flex-1 pl-1 mr-8 text-white ">
                        <div class="font-medium text-[10px]">My First IO</div>
                        <div class="text-gray-300 text-[10px]">Read a file from the file system</div>
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
            <li class="group flex flex-row">
                <div class="select-none flex flex-1 items-center px-3 py-2">
                    <div class="flex flex-col w-10 h-10 justify-center items-center mr-4"><a href="#"
                                                                                             class="block relative"><img
                            alt="workshop" src="../../../../../img/cloud/core-workshops.png" class="mx-auto object-cover h-8 w-8"></a>
                    </div>
                    <div class="flex-1 pl-1 mr-8 text-white">
                        <div class="font-medium text-[10px] text-[10px]">Filtered LS</div>
                        <div class="text-gray-300 text-[10px]">Read files in a folder and filter by a given extension</div>
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
            <li class="group flex flex-row">
                <div class="select-none flex flex-1 items-center px-3 py-2">
                    <div class="flex flex-col w-10 h-10 justify-center items-center mr-4"><a href="#"
                                                                                             class="block relative"><img
                            alt="workshop" src="../../../../../img/cloud/core-workshops.png" class="mx-auto object-cover h-8 w-8"></a>
                    </div>
                    <div class="flex-1 pl-1 mr-8 text-white">
                        <div class="font-medium text-[10px]">Concerned about Separation?</div>
                        <div class="text-gray-300 text-[10px]">Separate code and utilise files and classes</div>
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
            <li class="group flex flex-row">
                <div class="select-none flex flex-1 items-center px-3 py-2">
                    <div class="flex flex-col w-10 h-10 justify-center items-center mr-4"><a href="#"
                                                                                             class="block relative"><img
                            alt="workshop" src="../../../../../img/cloud/core-workshops.png" class="mx-auto object-cover h-8 w-8"></a>
                    </div>
                    <div class="flex-1 pl-1 mr-8 text-white">
                        <div class="font-medium text-[10px]">Array We Go!</div>
                        <div class="text-gray-300 text-[10px]">Filter an array of file paths and map to SplFile objects
                        </div>
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
            <li class="group flex flex-row">
                <div class="select-none flex flex-1 items-center px-3 py-2">
                    <div class="flex flex-col w-10 h-10 justify-center items-center mr-4"><a href="#"
                                                                                             class="block relative"><img
                            alt="workshop" src="../../../../../img/cloud/core-workshops.png" class="mx-auto object-cover h-8 w-8"></a>
                    </div>
                    <div class="flex-1 pl-1 mr-8 text-white">
                        <div class="font-medium text-[10px]">Exceptional Coding</div>
                        <div class="text-gray-300 text-[10px]">Introduction to Exceptions</div>
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
            <li class="group flex flex-row">
                <div class="select-none flex flex-1 items-center px-3 py-2">
                    <div class="flex flex-col w-10 h-10 justify-center items-center mr-4"><a href="#"
                                                                                             class="block relative"><img
                            alt="workshop" src="../../../../../img/cloud/core-workshops.png" class="mx-auto object-cover h-8 w-8"></a>
                    </div>
                    <div class="flex-1 pl-1 mr-8 text-white">
                        <div class="font-medium text-[10px]">Database Read</div>
                        <div class="text-gray-300 text-[10px]">Read an SQL databases contents</div>
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
            <li class="group flex flex-row">
                <div class="select-none flex flex-1 items-center px-3 py-2">
                    <div class="flex flex-col w-10 h-10 justify-center items-center mr-4"><a href="#"
                                                                                             class="block relative"><img
                            alt="workshop" src="../../../../../img/cloud/core-workshops.png" class="mx-auto object-cover h-8 w-8"></a>
                    </div>
                    <div class="flex-1 pl-1 mr-8 text-white">
                        <div class="font-medium text-[10px]">Time Server</div>
                        <div class="text-gray-300 text-[10px]">Build a Time Server!</div>
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
            <li class="group flex flex-row">
                <div class="select-none flex flex-1 items-center px-3 py-2">
                    <div class="flex flex-col w-10 h-10 justify-center items-center mr-4"><a href="#"
                                                                                             class="block relative"><img
                            alt="workshop" src="../../../../../img/cloud/core-workshops.png" class="mx-auto object-cover h-8 w-8"></a>
                    </div>
                    <div class="flex-1 pl-1 mr-8 text-white">
                        <div class="font-medium text-[10px]">HTTP JSON API</div>
                        <div class="text-gray-300 text-[10px]">HTTP JSON API - Servers JSON when it receives a GET request
                        </div>
                    </div>
                    <div class="text-gray-200 text-[8px] bg-pink-600 py-1 px-3 rounded-full">CGI</div>
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
            <li class="group flex flex-row">
                <div class="select-none flex flex-1 items-center px-3 py-2">
                    <div class="flex flex-col w-10 h-10 justify-center items-center mr-4">
                        <a href="#" class="block relative">
                            <img alt="workshop" src="../../../../../img/cloud/core-workshops.png" class="mx-auto object-cover h-8 w-8">
                        </a>
                    </div>
                    <div class="flex-1 pl-1 mr-8 text-white">
                        <div class="font-medium text-[10px]">Dependency Heaven</div>
                        <div class="text-gray-300 text-[10px]">An introduction to Composer dependency management</div>
                    </div>
                    <div class="text-gray-200 text-[8px] bg-pink-600 py-1 px-3 rounded-full">CGI</div>
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