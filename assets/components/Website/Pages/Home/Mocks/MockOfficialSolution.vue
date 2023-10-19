<template>
    <div class="relative bg-gray-800 flex flex-col justify-start modal-border border-2 border-solid border-transparent rounded-[20px] shadow-[-20px_20px_0_rgba(0,0,0,0.2)]">
        <div class="p-2.5 rounded-t flex-none flex justify-between items-top border-b border-solid border-slate-600">
            <div class="flex flex-col">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="h-4 w-4 text-pink-500 mr-2"><path fill-rule="evenodd" d="M3 6a3 3 0 013-3h12a3 3 0 013 3v12a3 3 0 01-3 3H6a3 3 0 01-3-3V6zm14.25 6a.75.75 0 01-.22.53l-2.25 2.25a.75.75 0 11-1.06-1.06L15.44 12l-1.72-1.72a.75.75 0 111.06-1.06l2.25 2.25c.141.14.22.331.22.53zm-10.28-.53a.75.75 0 000 1.06l2.25 2.25a.75.75 0 101.06-1.06L8.56 12l1.72-1.72a.75.75 0 10-1.06-1.06l-2.25 2.25z" clip-rule="evenodd"></path></svg>
                    <h3 class="text-base font-semibold text-sm text-white pt-0 mt-0">Official Solution</h3>
                </div>
            </div>
            <span type="button" class="text-gray-400 bg-transparent rounded-lg text-sm  ml-auto inline-flex items-center focus:outline-none focus:ring focus:ring-pink-800">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="w-4 h-4"><path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 011.06 0L12 10.94l5.47-5.47a.75.75 0 111.06 1.06L13.06 12l5.47 5.47a.75.75 0 11-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 01-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 010-1.06z" clip-rule="evenodd"></path></svg>
            </span>
        </div>
        <div class="flex-1 p-0 rounded-b-[20px] bg-gray-900">
            <div class="flex h-full">
                <div class="w-1/3">
                    <div class="">
                        <div class="border-b border-solid border-gray-600 py-3 px-3 flex justify-between">
                            <span class="text-white text-xs font-mono">Files</span>
                        </div>
                        <ul class="w-full text-gray-300 font-mono text-[10px]">
                            <li class="bg-pink-500 flex flex-col pl-1 py-1 w-full">
                                <div class="group flex w-full items-center justify-between">
                                    <div class="flex items-center min-w-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" class="mr-1 h-5 w-5" style="fill: none !important;"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path></svg>
                                        <span class="flex-1 truncate">solution.php</span>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="w-2/3 h-full flex flex-1">
                    <div class="h-full w-5 flex flex-col text-[10px] text-[#8F908A] bg-[#0c1220] pt-1">
                        <span class="leading-relaxed">1</span>
                        <span class="leading-relaxed">2</span>
                        <span class="leading-relaxed">3</span>
                        <span class="leading-relaxed">4</span>
                    </div>
                    <div class="flex flex-1 flex-col text-[10px] text-left ml-1 pt-1">
                       <span class="text-[#f8f8f2]">&lt;?php</span>
                       <br>
                       <span>
                           <span class="text-[#66D9EF]">echo</span>
                           &nbsp;
                           <span class="text-[#E6DB74]">"Hello World"</span>
                           <span class="text-[#f8f8f2]">;</span>
                       </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
</script>