<template>
  <div class="relative top-3 mx-auto flex w-full rounded-lg bg-gradient-to-r from-pink-500 to-purple-500 px-2 py-2">
    <div class="flex w-full items-center justify-center">
      <div class="flex items-center">
        <span class="flex rounded-lg p-2">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            aria-hidden="true"
            class="h-4 w-4 text-yellow-400"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0"
            ></path>
          </svg>
        </span>
        <p class="ml-1 mr-2 truncate text-xs font-medium text-white sm:mr-0 md:text-sm">
          <span class="md:hidden">Congratulations! your solution passed.</span>
          <span class="hidden md:inline">Congratulations! your solution passed.</span>
        </p>
      </div>
      <div class="order-3 mt-2 hidden w-full flex-shrink-0 sm:order-2 sm:mt-0 sm:w-auto md:flex">
        <button class="flex items-center justify-center px-2 py-2 text-xs font-bold text-white underline">
          See the official solution
        </button>
      </div>
      <div class="order-3 mt-2 hidden w-full flex-shrink-0 sm:order-2 sm:mt-0 sm:w-auto md:flex">
        <span class="flex items-center justify-center text-xs text-white">or</span>
        <span
          id="next-exercise-link"
          class="flex items-center justify-center px-2 py-2 text-xs font-bold text-white underline"
        >
          Continue to the next exercise →
        </span>
      </div>
      <div class="order-2 hidden flex-shrink-0 sm:order-3 sm:ml-1 sm:flex">
        <button type="button" class="mr-2 flex rounded-md focus:outline-none focus:ring-2 focus:ring-white">
          <span class="sr-only">Dismiss</span>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="currentColor"
            aria-hidden="true"
            class="h-4 w-4 text-white"
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
  </div>
</template>
