<template>
  <div class="mx-auto h-auto max-w-[2048px] px-9 py-16">
    <div class="flex w-full flex-row">
      <div class="h-12 w-[20px] border-l-4 border-solid border-pink-600"></div>
      <div>
        <h3 class="flex-inline p-0 font-work-sans text-6xl font-bold capitalize text-pink-600">
          <slot name="title"></slot>
        </h3>
      </div>
    </div>
    <p class="mt-4 text-base text-white">
      <slot name="description"></slot>
    </p>
  </div>
</template>
