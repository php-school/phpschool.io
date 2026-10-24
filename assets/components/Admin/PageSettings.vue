<script setup>
import Alert from "../Online/SiteAlert.vue";

import { clearCache } from "./api";
import { useNotice } from "./notice";

defineProps({
  search: {
    type: String,
    default: "",
  },
});

const { notice, success, error } = useNotice();

const clear = async () => {
  try {
    await clearCache();
    success("Cache was successfully cleared");
  } catch (e) {
    error(e);
  }
};
</script>

<template>
  <alert v-bind="notice" :timeout="4000" @close="notice.show = false" />

  <header class="flex items-center justify-between border-b border-pink-600/30 px-4 py-4 sm:px-6 sm:py-6 lg:px-8">
    <h1 class="text-base leading-7 font-semibold text-white">Settings</h1>
  </header>

  <div class="grid max-w-7xl grid-cols-1 gap-x-8 gap-y-10 border-b border-pink-600/30 px-4 py-16 sm:px-6 md:grid-cols-2 lg:px-8">
    <div>
      <h2 class="text-base leading-7 font-semibold text-white">Clear cache</h2>
      <p class="mt-1 text-sm leading-6 text-gray-400">Clear the full page and Redis cache. This includes Doctrine and HTML cache.</p>
    </div>

    <div class="flex items-start justify-end">
      <button @click="clear" type="submit" class="rounded-md bg-red-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-400">Clear cache</button>
    </div>
  </div>
</template>
