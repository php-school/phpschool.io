<script setup>
import Modal from "../ModalDialog.vue";
import { Cog6ToothIcon } from "@heroicons/vue/24/solid";
import { computed, ref } from "vue";

const openModal = ref(false);
const currentFailure = ref(null);
const props = defineProps({
  data: Object,
  renderers: Object,
});

const failures = computed(() => {
  return props.data.results.filter((r) => r.success === false);
});

const longestArg = computed(() => {
  return Math.max(...currentFailure.value.args.map((r) => r.length));
});

const openInfoModal = (failure) => {
  currentFailure.value = failure;
  openModal.value = true;
};
</script>

<template>
  <div class="mt-3 border-l-2 border-solid border-red-500 pl-2" v-for="(failure, i) in failures" :key="i">
    <div class="flex w-full justify-between">
      <span class="inline w-2/3 text-sm italic text-gray-300">Run #{{ i + 1 }}</span>
      <button class="p-x2 ml-2 text-left text-sm text-[#E91E63] underline" @click="openInfoModal(failure)">Show info</button>
    </div>
    <component v-if="renderers.hasOwnProperty(failure.type)" :is="renderers[failure.type]" :data="failure"></component>
  </div>

  <Transition
    enter-active-class="transition-opacity duration-100 ease-in"
    leave-active-class="transition-opacity duration-200 ease-in"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <Modal :scroll-content="true" size="2xl" max-height="max-h-[calc(5/6*100%)]" v-if="openModal" @close="openModal = false">
      <template #header>
        <div class="flex flex-col">
          <div class="flex items-center">
            <Cog6ToothIcon class="mr-2 h-6 w-6 text-pink-500" />

            <h2 class="mt-0 pt-0 font-mono text-base font-semibold text-white lg:text-xl">Run context</h2>
          </div>
        </div>
      </template>

      <template #body>
        <div id="cli-run-info">
          <h2 class="mb-2 pt-0 font-mono text-lg text-[#E91E63]">Command line arguments</h2>
          <div v-if="currentFailure.args.length">
            <div class="mb-8">
              <ul v-if="longestArg > 5" class="ml-2 mt-2 list-inside list-disc">
                <li v-for="arg in currentFailure.args" :key="arg" class="list-item p-1">
                  {{ arg }}
                </li>
              </ul>
              <div v-else>
                <code v-for="arg in currentFailure.args" :key="arg" class="mr-2 px-2">{{ arg }}</code>
              </div>
            </div>

            <p class="mb-4 text-sm text-white">Your program was executed like so:</p>
            <pre><code class="language-sh hljs bash text-sm">php solution.php {{currentFailure.args.join(' ')}}</code></pre>
          </div>

          <p v-else class="truncate font-medium text-white">
            <span class="text-sm">Your program was executed with no command line arguments.</span>
          </p>
        </div>
      </template>

      <template #footer>
        <div class="flex justify-end">
          <button
            @click="openModal = false"
            type="button"
            class="inline-flex w-full items-center justify-center rounded-full border border-transparent bg-pink-600 px-8 py-2 text-base font-medium text-white shadow-sm hover:bg-pink-700 focus:outline-none focus:ring focus:ring-pink-800 sm:ml-3 sm:w-auto sm:text-sm"
          >
            Close
          </button>
        </div>
      </template>
    </Modal>
  </Transition>
</template>

<style>
#cli-run-info pre {
  background-color: initial;
  @apply mb-4 rounded-none border-none p-0;
}
#cli-run-info pre code {
  background-color: #2a2c2d !important;
  @apply rounded-lg p-4;
}

#cli-run-info :not(pre) > code {
  font-size: 90%;
  background-color: #2a2c2d;
  @apply rounded px-2 py-1;
  color: #ff75b5;
}
</style>
