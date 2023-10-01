<template>
    <div class="h-full w-full relative bg-gray-900 border p-1.5 border-gray-600 rounded-[20px] shadow-[-20px_20px_0_rgba(0,0,0,0.2)]">
        <div class="h-full flex flex-col">
            <div class="flex flex-1 h-full relative">
                <div class="w-3/12">
                    <div class="">
                        <div class="border-b border-solid border-gray-600 py-3 px-3 flex justify-between">
                            <span class="text-white text-xs font-mono">Files</span>
                            <div class="flex text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" class="mr-2 h-3 w-3" style="fill: none !important;"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" class="mr-2 h-3 w-3" style="fill: none !important;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10.5v6m3-3H9m4.06-7.19l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z"></path></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" class="h-3 w-3" style="fill: none !important;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
                            </div>
                        </div>
                        <ul class="w-full text-gray-300 font-mono p-1 text-[10px]">
                            <li class="flex flex-col pl-1 py-1 w-full">
                                <div class="group flex w-full items-center justify-between">
                                    <div class="flex items-center min-w-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" class="mr-1 h-3 w-3" style="fill: none !important;"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path></svg>
                                        <span class="flex-1 truncate">solution.php</span>
                                    </div>
                                </div>
                            </li>

                            <li class="flex flex-col pl-1 py-1 w-full">
                                <div class="group flex w-full items-center justify-between cursor-pointer">
                                    <div class="flex items-center min-w-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" class="mr-1 h-3 w-3" style="fill: none !important;"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776"></path></svg>
                                        <span class="hover:text-white flex-1 truncate" style="">cloud</span>
                                        <span class="ml-2 mr-2">[-]</span>
                                    </div>
                                </div>
                                <div class="ml-3" style="">
                                    <ul class="mt-1 w-full text-gray-300">
                                        <li class="flex flex-col pl-1 py-1 w-full">
                                            <div class="group flex w-full items-center justify-between cursor-pointer">
                                                <div class="flex items-center min-w-0"><!--v-if--><!--v-if-->
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" class="mr-1 h-3 w-3" style="fill: none !important;"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path></svg>
                                                    <span class="hover:text-white flex-1 truncate" style="">workshop.php</span>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="flex flex-col pl-1 py-1 w-full">
                                <div class="group flex w-full items-center justify-between cursor-pointer">
                                    <div class="flex items-center min-w-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" class="mr-1 h-3 w-3" style="fill: none !important;"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776"></path></svg>
                                        <span class="hover:text-white flex-1 truncate" style="">styles</span>
                                        <span class="ml-2 mr-2">[-]</span>
                                    </div>
                                </div>
                                <div class="ml-3" style="">
                                    <ul class="mt-1 w-full text-gray-300">
                                        <li class="flex flex-col pl-1 py-1 w-full">
                                            <div class="group flex w-full items-center justify-between cursor-pointer">
                                                <div class="flex items-center min-w-0">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" class="mr-1 h-3 w-3" style="fill: none !important;"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path></svg>
                                                    <span class="hover:text-white flex-1 truncate" style="">cloud.css</span>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="flex border-l border-solid border-gray-600 h-full w-9/12">
                    <div class="tabs w-full flex flex-col">
                        <ul class="tabs list-reset flex justify-start flex-none border-solid border-gray-600">
                            <li class="flex justify-between border-solid border-r border-r-gray-600 pl-2 pr-1 inline-block text-white hover:text-pink-500 text-xs no-underline items-center border-t -mt-[1px] border-t-pink-600">
                                <a href="#" class="text-xs text-gray-300 hover:no-underline py-3 pl-1 text-pink-600">solution.php</a>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                     aria-hidden="true"
                                     class="cursor-pointer ml-2 mr-1 w-3 h-3 text-zinc-400 hover:text-white">
                                    <path fill-rule="evenodd"
                                          d="M5.47 5.47a.75.75 0 011.06 0L12 10.94l5.47-5.47a.75.75 0 111.06 1.06L13.06 12l5.47 5.47a.75.75 0 11-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 01-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 010-1.06z"
                                          clip-rule="evenodd"></path>
                                </svg>
                            </li>
                            <li class="flex-1 border-b border-solid border-gray-600"></li>
                        </ul>
                        <div class="h-full flex flex-1 bg-gray-900">
                            <div class="h-full w-5 flex flex-col text-[10px] text-[#8F908A] bg-[#0c1220]">
                                <span class="leading-relaxed">1</span>
                                <span class="leading-relaxed">2</span>
                                <span class="leading-relaxed">3</span>
                                <span class="leading-relaxed">4</span>
                            </div>
                        </div>
                    </div>
                </div><!--v-if--></div><!-- start footer -->
            <div class="border-t border-solid border-gray-600 flex-none flex items-center justify-between px-2 pt-2 pb-2">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="text-[10px] inline-flex items-center space-x-1 md:space-x-2">
                        <li class="inline-flex items-center">
                            <span  class="inline-flex items-center font-medium text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="w-2.5 h-2.5 mr-2"><path d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z"></path><path d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.43z"></path></svg>
                                Learn You PHP!
                            </span>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="w-2.5 h-2.5 text-pink-500"><path fill-rule="evenodd" d="M16.28 11.47a.75.75 0 010 1.06l-7.5 7.5a.75.75 0 01-1.06-1.06L14.69 12 7.72 5.03a.75.75 0 011.06-1.06l7.5 7.5z" clip-rule="evenodd"></path></svg>
                                <span class="ml-1 font-medium text-white md:ml-2">Hello World</span>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                            </div>
                        </li>
                    </ol>
                </nav>
                <!-- Progress Tracker Bar -->
                <div class="w-1/6 bg-gray-200 rounded-full h-4 bg-gray-700 mt-0 relative flex justify-center items-center">
                    <div class="absolute left-0 h-5 rounded-full bg-pink-500" style="width: 0%;"></div>
                    <p class="absolute inline-flex items-center text-[8px] font-bold text-white ml-2 mx-auto">0 / 24 completed</p>
                </div>
                <div class="flex">
                    <button class="border-[#E91E63] text-[10px] border-solid border-2 text-white flex items-center justify-center mt-0 mr-2 rounded py-1 px-2 w-30">
                        <span>Composer deps</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="ml-2 w-3 h-3"><path d="M21 6.375c0 2.692-4.03 4.875-9 4.875S3 9.067 3 6.375 7.03 1.5 12 1.5s9 2.183 9 4.875z"></path><path d="M12 12.75c2.685 0 5.19-.586 7.078-1.609a8.283 8.283 0 001.897-1.384c.016.121.025.244.025.368C21 12.817 16.97 15 12 15s-9-2.183-9-4.875c0-.124.009-.247.025-.368a8.285 8.285 0 001.897 1.384C6.809 12.164 9.315 12.75 12 12.75z"></path><path d="M12 16.5c2.685 0 5.19-.586 7.078-1.609a8.282 8.282 0 001.897-1.384c.016.121.025.244.025.368 0 2.692-4.03 4.875-9 4.875s-9-2.183-9-4.875c0-.124.009-.247.025-.368a8.284 8.284 0 001.897 1.384C6.809 15.914 9.315 16.5 12 16.5z"></path><path d="M12 20.25c2.685 0 5.19-.586 7.078-1.609a8.282 8.282 0 001.897-1.384c.016.121.025.244.025.368 0 2.692-4.03 4.875-9 4.875s-9-2.183-9-4.875c0-.124.009-.247.025-.368a8.284 8.284 0 001.897 1.384C6.809 19.664 9.315 20.25 12 20.25z"></path></svg>
                    </button>
                    <button id="show-problem" class="border-[#E91E63] text-[10px] border-solid border-2 text-white flex items-center justify-center mt-0 mr-2 rounded py-1 px-2 w-30">
                        <span>Show problem</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="ml-2 w-3 h-3"><path fill-rule="evenodd" d="M8.161 2.58a1.875 1.875 0 011.678 0l4.993 2.498c.106.052.23.052.336 0l3.869-1.935A1.875 1.875 0 0121.75 4.82v12.485c0 .71-.401 1.36-1.037 1.677l-4.875 2.437a1.875 1.875 0 01-1.676 0l-4.994-2.497a.375.375 0 00-.336 0l-3.868 1.935A1.875 1.875 0 012.25 19.18V6.695c0-.71.401-1.36 1.036-1.677l4.875-2.437zM9 6a.75.75 0 01.75.75V15a.75.75 0 01-1.5 0V6.75A.75.75 0 019 6zm6.75 3a.75.75 0 00-1.5 0v8.25a.75.75 0 001.5 0V9z" clip-rule="evenodd"></path></svg>
                    </button>
                    <div class="flex items-center">
                        <button id="run" class="text-[10px] border-[#E91E63] border-solid border-2 text-white flex items-center justify-center mt-0 mr-2 py-1 px-2 w-18 rounded">
                            <span>Run</span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="ml-2 w-3 h-3">
                                <path fill-rule="evenodd" d="M2.25 6a3 3 0 013-3h13.5a3 3 0 013 3v12a3 3 0 01-3 3H5.25a3 3 0 01-3-3V6zm3.97.97a.75.75 0 011.06 0l2.25 2.25a.75.75 0 010 1.06l-2.25 2.25a.75.75 0 01-1.06-1.06l1.72-1.72-1.72-1.72a.75.75 0 010-1.06zm4.28 4.28a.75.75 0 000 1.5h3a.75.75 0 000-1.5h-3z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                        <button id="verify" class="text-[10px] text-white flex items-center justify-center mt-0 py-1.5 px-2 w-18 rounded bg-gradient-to-r from-pink-600 to-purple-500">
                            <span>Verify</span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="ml-2 w-3 h-3"><path fill-rule="evenodd" d="M9 4.5a.75.75 0 01.721.544l.813 2.846a3.75 3.75 0 002.576 2.576l2.846.813a.75.75 0 010 1.442l-2.846.813a3.75 3.75 0 00-2.576 2.576l-.813 2.846a.75.75 0 01-1.442 0l-.813-2.846a3.75 3.75 0 00-2.576-2.576l-2.846-.813a.75.75 0 010-1.442l2.846-.813A3.75 3.75 0 007.466 7.89l.813-2.846A.75.75 0 019 4.5zM18 1.5a.75.75 0 01.728.568l.258 1.036c.236.94.97 1.674 1.91 1.91l1.036.258a.75.75 0 010 1.456l-1.036.258c-.94.236-1.674.97-1.91 1.91l-.258 1.036a.75.75 0 01-1.456 0l-.258-1.036a2.625 2.625 0 00-1.91-1.91l-1.036-.258a.75.75 0 010-1.456l1.036-.258a2.625 2.625 0 001.91-1.91l.258-1.036A.75.75 0 0118 1.5zM16.5 15a.75.75 0 01.712.513l.394 1.183c.15.447.5.799.948.948l1.183.395a.75.75 0 010 1.422l-1.183.395c-.447.15-.799.5-.948.948l-.395 1.183a.75.75 0 01-1.422 0l-.395-1.183a1.5 1.5 0 00-.948-.948l-1.183-.395a.75.75 0 010-1.422l1.183-.395c.447-.15.799-.5.948-.948l.395-1.183A.75.75 0 0116.5 15z" clip-rule="evenodd"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>