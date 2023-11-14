<template>
  <div id="problem-modal"
    class="relative rounded-3xl bg-gray-800 flex flex-col justify-start w-full modal-border border-2 border-solid border-transparent max-w-[20rem] sm:max-w-2xl lg:max-w-lg xl:max-w-2xl max-h-[24rem] sm:max-h-[39rem] shadow-[-20px_20px_0_rgba(0,0,0,0.2)] p-2">
    <div class="p-2 rounded-t flex-none flex justify-between items-top border-b border-solid border-slate-600">
      <div class="flex flex-col">
        <div class="flex items-center"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
            aria-hidden="true" class="h-6 w-6 text-pink-500 mr-2">
            <path fill-rule="evenodd"
              d="M8.161 2.58a1.875 1.875 0 011.678 0l4.993 2.498c.106.052.23.052.336 0l3.869-1.935A1.875 1.875 0 0121.75 4.82v12.485c0 .71-.401 1.36-1.037 1.677l-4.875 2.437a1.875 1.875 0 01-1.676 0l-4.994-2.497a.375.375 0 00-.336 0l-3.868 1.935A1.875 1.875 0 012.25 19.18V6.695c0-.71.401-1.36 1.036-1.677l4.875-2.437zM9 6a.75.75 0 01.75.75V15a.75.75 0 01-1.5 0V6.75A.75.75 0 019 6zm6.75 3a.75.75 0 00-1.5 0v8.25a.75.75 0 001.5 0V9z"
              clip-rule="evenodd"></path>
          </svg>
          <h3 class="font-mono text-base font-semibold lg:text-lg text-white pt-0 mt-0"> The problem... </h3>
        </div>
        <h2 class="font-mono text-white mt-0 mb-2 text-lg text-left pt-[13.5px]">Hello World</h2>
      </div>
      <div>
          <button type="button" class="text-gray-400 bg-transparent rounded-lg text-sm p-1.5 ml-auto inline-flex items-center focus:outline-none focus:ring focus:ring-pink-800">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="w-5 h-5"><path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 011.06 0L12 10.94l5.47-5.47a.75.75 0 111.06 1.06L13.06 12l5.47 5.47a.75.75 0 11-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 01-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 010-1.06z" clip-rule="evenodd"></path></svg>
          </button>
      </div>
    </div>
    <div class="p-2 flex-1 items-start justify-start overflow-y-hidden  mr-[3px]">
      <div id="problem-file" class="text-white text-left">
        <p class="text-[10px]">Write a program that prints the text "Hello World" to the console (stdout).</p>
        <hr>
        <h3 class="text-[#e91e63] !text-base">HINTS</h3>
        <p class="text-[10px]">We've created you an empty file, look in the file tree for <code>solution.php</code>.
          That's your starting
          point.</p>
        <p class="text-[10px]">We'll execute your solution file for you when you press the <code>Run</code> or
          <code>Verify</code> buttons.
          The <code>Run</code> button simply runs your program and captures the output, displaying it for you to view. The
          <code>Verify</code> button runs your program but performs some extra tasks, such as comparing the output,
          checking for certain structures and function calls, etc. It then displays the result of those verifications.
        </p>
        <p class="text-[10px]">Both <code>Run</code> and <code>Verify</code> execute your program with random inputs which
          are determined by
          the current exercise. For example one exercise might generate a bunch of numbers to pass to your program, where
          another one might pass you a JSON encoded string.</p>
        <p class="text-[10px]">You can write to the console from a PHP program with the following code:</p>
        <pre><code class="language-php hljs php text-[10px]" data-lang="php">&lt;?php
echo "text";
</code></pre>
        <p class="text-[10px]">The first line tells PHP to interpret the code following it. It is required before any PHP
          code is written. The
          second line is the instruction to print out some text.</p>
        <p class="text-[10px]">Documentation on <code>PHP tags</code> can be found by pointing your browser here:<br><a
            rel="noopener noreferrer" target="_blank"
            href="#">https://php.net/manual/en/language.basic-syntax.phptags.php</a>
        </p>
        <p class="text-[10px]">Try pressing the <code>Run</code> button in the bottom right corner to execute your
          program.</p>
        <p class="text-[10px]">You should see the word "text" printed out on the console.</p>
        <p class="text-[10px]">Now you must adapt the code to pass the presented challenge. Remember, the challenge was:
          Write a program that
          prints the text "Hello World" to the console.</p>
        <p class="text-[10px]">When you have finished editing your program you must verify it. Click the
          <code>Verify</code> button in the
          bottom right corner.
        </p>
        <p class="text-[10px]">Your program will be tested, a report will be generated, and the lesson will be marked
          'completed' if you are
          successful.</p>
        <hr>
      </div>
    </div>
    <div class="pt-4 p-2 rounded-b border-t border-solid border-slate-600 flex-none">
      <div class="flex justify-end">
          <button type="button" class="inline-flex items-center w-full justify-center rounded-full border border-transparent bg-pink-600 px-4 py-1 text-[10px] text-white shadow-sm hover:bg-pink-700 focus:outline-none focus:ring focus:ring-pink-800 sm:ml-3 sm:w-auto">Let's go!</button>
      </div>
    </div>
  </div>
</template>