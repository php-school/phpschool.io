<script setup>
import { highlightCode } from "../../../../../helpers/highlightCode";
import { ref } from "vue";

const code = ref(highlightCode('<?php\necho "text";', "php"));
</script>

<template>
  <div
    id="problem-modal"
    class="modal-border relative flex max-h-[24rem] w-full max-w-[20rem] flex-col justify-start rounded-3xl border-2 border-solid border-transparent bg-gray-800 p-2 shadow-[-20px_20px_0_rgba(0,0,0,0.2)] sm:max-h-[39rem] sm:max-w-2xl lg:max-w-lg xl:max-w-2xl"
  >
    <div class="items-top flex flex-none justify-between rounded-t border-b border-solid border-slate-600 p-2">
      <div class="flex flex-col">
        <div class="flex items-center">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="currentColor"
            aria-hidden="true"
            class="mr-2 h-6 w-6 text-pink-500"
          >
            <path
              fill-rule="evenodd"
              d="M8.161 2.58a1.875 1.875 0 011.678 0l4.993 2.498c.106.052.23.052.336 0l3.869-1.935A1.875 1.875 0 0121.75 4.82v12.485c0 .71-.401 1.36-1.037 1.677l-4.875 2.437a1.875 1.875 0 01-1.676 0l-4.994-2.497a.375.375 0 00-.336 0l-3.868 1.935A1.875 1.875 0 012.25 19.18V6.695c0-.71.401-1.36 1.036-1.677l4.875-2.437zM9 6a.75.75 0 01.75.75V15a.75.75 0 01-1.5 0V6.75A.75.75 0 019 6zm6.75 3a.75.75 0 00-1.5 0v8.25a.75.75 0 001.5 0V9z"
              clip-rule="evenodd"
            ></path>
          </svg>
          <h3 class="mt-0 pt-0 font-mono text-base font-semibold text-white lg:text-lg">The problem...</h3>
        </div>
        <h2 class="mb-2 mt-0 pt-[13.5px] text-left font-mono text-lg text-[#E91E63]">Hello World</h2>
      </div>
      <div>
        <button
          type="button"
          class="ml-auto inline-flex items-center rounded-lg bg-transparent p-1.5 text-sm text-gray-400 focus:outline-none focus:ring focus:ring-pink-800"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="currentColor"
            aria-hidden="true"
            class="h-5 w-5"
          >
            <path
              fill-rule="evenodd"
              d="M5.47 5.47a.75.75 0 011.06 0L12 10.94l5.47-5.47a.75.75 0 111.06 1.06L13.06 12l5.47 5.47a.75.75 0 11-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 01-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 010-1.06z"
              clip-rule="evenodd"
            ></path>
          </svg>
        </button>
      </div>
    </div>
    <div class="mr-[3px] flex-1 items-start justify-start overflow-y-hidden p-2">
      <div id="problem-file" class="text-left text-white">
        <p class="mb-4 mt-2 text-[12px] leading-loose tracking-wide">
          Write a program that prints the text "Hello World" to the console (stdout).
        </p>
        <hr class="border-dotted border-gray-600" />
        <h3 class="mb-4 mt-2 text-base text-[#e91e63]">HINTS</h3>
        <p class="mb-4 mt-2 text-xs leading-loose tracking-wide">
          We've created you an empty file, look in the file tree for
          <code class="rounded bg-[#2a2c2d] px-2 py-1 text-[#ff75b5]">solution.php</code>
          . That's your starting point.
        </p>
        <p class="mb-4 mt-2 text-xs leading-loose tracking-wide">
          We'll execute your solution file for you when you press the
          <code class="rounded bg-[#2a2c2d] px-2 py-1 text-[#ff75b5]">Run</code>
          or
          <code class="rounded bg-[#2a2c2d] px-2 py-1 text-[#ff75b5]">Verify</code>
          buttons. The
          <code class="rounded bg-[#2a2c2d] px-2 py-1 text-[#ff75b5]">Run</code>
          button simply runs your program and captures the output, displaying it for you to view. The
          <code class="rounded bg-[#2a2c2d] px-2 py-1 text-[#ff75b5]">Verify</code>
          button runs your program but performs some extra tasks, such as comparing the output, checking for certain
          structures and function calls, etc. It then displays the result of those verifications.
        </p>
        <p class="mb-4 mt-2 text-xs leading-loose tracking-wide">
          Both
          <code class="rounded bg-[#2a2c2d] px-2 py-1 text-[#ff75b5]">Run</code>
          and
          <code class="rounded bg-[#2a2c2d] px-2 py-1 text-[#ff75b5]">Verify</code>
          execute your program with random inputs which are determined by the current exercise. For example one exercise
          might generate a bunch of numbers to pass to your program, where another one might pass you a JSON encoded
          string.
        </p>
        <p class="mb-4 mt-2 text-xs leading-loose tracking-wide">
          You can write to the console from a PHP program with the following code:
        </p>
        <pre
          class="mb-4 mt-2 rounded-none border-none p-0"
        ><code class="language-php hljs php text-xs p-4 rounded-lg block border border-gray-600 bg-[#2a2c2d]" data-lang="php" v-html="code"></code></pre>
        <p class="mb-4 mt-2 text-xs leading-loose tracking-wide">
          The first line tells PHP to interpret the code following it. It is required before any PHP code is written.
          The second line is the instruction to print out some text.
        </p>
        <p class="mb-4 mt-2 text-xs leading-loose tracking-wide">
          Documentation on
          <code class="rounded bg-[#2a2c2d] px-2 py-1 text-[#ff75b5]">PHP tags</code>
          can be found by pointing your browser here:
          <br />
          <a rel="noopener noreferrer" target="_blank" href="#">
            https://php.net/manual/en/language.basic-syntax.phptags.php
          </a>
        </p>
        <p class="mb-4 mt-2 text-xs leading-loose tracking-wide">
          Try pressing the
          <code class="rounded bg-[#2a2c2d] px-2 py-1 text-[#ff75b5]">Run</code>
          button in the bottom right corner to execute your program.
        </p>
        <p class="mb-4 mt-2 text-xs leading-loose tracking-wide">
          You should see the word "text" printed out on the console.
        </p>
        <p class="text-xs">
          Now you must adapt the code to pass the presented challenge. Remember, the challenge was: Write a program that
          prints the text "Hello World" to the console.
        </p>
        <p class="mb-4 mt-2 text-xs leading-loose tracking-wide">
          When you have finished editing your program you must verify it. Click the
          <code class="rounded bg-[#2a2c2d] px-2 py-1 text-[#ff75b5]">Verify</code>
          button in the bottom right corner.
        </p>
        <p class="mb-4 mt-2 text-xs leading-loose tracking-wide">
          Your program will be tested, a report will be generated, and the lesson will be marked 'completed' if you are
          successful.
        </p>
        <hr class="border-dotted border-gray-600" />
      </div>
    </div>
    <div class="flex-none rounded-b border-t border-solid border-slate-600 p-2 pt-4">
      <div class="flex justify-end">
        <button
          type="button"
          class="inline-flex w-full items-center justify-center rounded-full border border-transparent bg-pink-600 px-4 py-1 text-xs text-white shadow-sm hover:bg-pink-700 focus:outline-none focus:ring focus:ring-pink-800 sm:ml-3 sm:w-auto"
        >
          Let's go!
        </button>
      </div>
    </div>
  </div>
</template>
