<template>
  <section class="bg-gray-900">
    <div class="mb-5 flex items-center justify-center">
      <div class="horizontal-divider"></div>
    </div>
    <div class="mx-auto mt-16 h-auto max-w-[2048px] px-9">
      <div v-if="$slots.title">
        <div class="mb-10 flex w-full flex-row">
          <div class="h-12 w-[20px] border-l-4 border-solid border-pink-600"></div>
          <div>
            <h3 class="flex-inline p-0 font-work-sans text-5xl font-bold capitalize text-pink-600">
              <slot name="title" />
            </h3>
          </div>
        </div>
      </div>
      <slot />
    </div>
  </section>
</template>
