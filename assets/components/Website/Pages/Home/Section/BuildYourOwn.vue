<script setup>
import PageSection from "../../PageSection.vue";
</script>

<template>
  <PageSection>
    <template #title>
      Build your own workshop
      <br />
      + Host it on PHP School
    </template>
    <div class="flex flex-wrap bg-gray-900 p-4 text-white">
      <div class="mb-10 flex flex-wrap">
        <div class="w-full p-4 sm:w-1/2">
          <h3 class="mb-5 font-open-sans text-lg font-bold text-white">Documentation</h3>
          <p class="font-open-sans text-base text-white">
            Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni
            dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est.
          </p>
        </div>

        <div class="w-full p-4 sm:w-1/2">
          <h3 class="mb-5 font-open-sans text-lg font-bold text-white">Build</h3>
          <p class="font-open-sans text-base text-white">
            Amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut
            enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi.
          </p>
        </div>
      </div>

      <div class="mb-10 flex flex-wrap">
        <div class="w-full p-4 sm:w-1/2">
          <h3 class="mb-5 font-open-sans text-lg font-bold text-white">Submit</h3>
          <p class="font-open-sans text-base text-white">
            Consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem
            ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam.
          </p>
        </div>

        <div class="w-full p-4 sm:w-1/2">
          <h3 class="mb-5 font-open-sans text-lg font-bold text-white">Open to the world</h3>
          <p class="font-open-sans text-base text-white">
            Eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet,
            consectetur, adipisci velit, sed quia non numquam eius modi tempora.
          </p>
        </div>
      </div>
    </div>
  </PageSection>
</template>
