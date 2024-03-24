<template>
  <div class="modal-border relative flex flex-col justify-start rounded-[20px] border-2 border-solid border-transparent bg-gray-800 shadow-[-20px_20px_0_rgba(0,0,0,0.2)]">
    <div class="items-top flex flex-none justify-between rounded-t border-b border-solid border-slate-600 p-2.5">
      <div class="flex flex-col">
        <div class="flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="mr-2 h-4 w-4 text-pink-500">
            <path
              fill-rule="evenodd"
              d="M3 6a3 3 0 013-3h12a3 3 0 013 3v12a3 3 0 01-3 3H6a3 3 0 01-3-3V6zm14.25 6a.75.75 0 01-.22.53l-2.25 2.25a.75.75 0 11-1.06-1.06L15.44 12l-1.72-1.72a.75.75 0 111.06-1.06l2.25 2.25c.141.14.22.331.22.53zm-10.28-.53a.75.75 0 000 1.06l2.25 2.25a.75.75 0 101.06-1.06L8.56 12l1.72-1.72a.75.75 0 10-1.06-1.06l-2.25 2.25z"
              clip-rule="evenodd"
            ></path>
          </svg>
          <h3 class="mt-0 pt-0 text-base text-sm font-semibold text-white">Official Solution</h3>
        </div>
      </div>
      <span type="button" class="ml-auto inline-flex items-center rounded-lg bg-transparent text-sm text-gray-400 focus:outline-none focus:ring focus:ring-pink-800">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="h-4 w-4">
          <path
            fill-rule="evenodd"
            d="M5.47 5.47a.75.75 0 011.06 0L12 10.94l5.47-5.47a.75.75 0 111.06 1.06L13.06 12l5.47 5.47a.75.75 0 11-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 01-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 010-1.06z"
            clip-rule="evenodd"
          ></path>
        </svg>
      </span>
    </div>
    <div class="flex-1 rounded-b-[20px] bg-gray-900 p-0">
      <div class="flex h-full">
        <div class="w-1/3">
          <div class="">
            <div class="flex justify-between border-b border-solid border-gray-600 px-3 py-3">
              <span class="font-mono text-xs text-white">Files</span>
            </div>
            <ul class="w-full font-mono text-[10px] text-gray-300">
              <li class="flex w-full flex-col bg-pink-500 py-1 pl-1">
                <div class="group flex w-full items-center justify-between">
                  <div class="flex min-w-0 items-center">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke-width="1.5"
                      stroke="currentColor"
                      aria-hidden="true"
                      class="mr-1 h-5 w-5"
                      style="fill: none !important"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"
                      ></path>
                    </svg>
                    <span class="flex-1 truncate">solution.php</span>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>
        <div class="flex h-full w-2/3 flex-1">
          <div class="flex h-full w-5 flex-col bg-[#0c1220] pt-1 text-[10px] text-[#8F908A]">
            <span class="leading-relaxed">1</span>
            <span class="leading-relaxed">2</span>
            <span class="leading-relaxed">3</span>
            <span class="leading-relaxed">4</span>
          </div>
          <div class="ml-1 flex flex-1 flex-col pt-1 text-left text-[10px]">
            <span class="text-[#f8f8f2]">&lt;?php</span>
            <br />
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
<script setup></script>
