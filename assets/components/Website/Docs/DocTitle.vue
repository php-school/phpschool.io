<script setup>
import { computed } from "vue";
import { PencilSquareIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
  id: {
    type: String,
    required: true,
  },
  title: {
    type: String,
    required: true,
  },
  file: {
    type: String,
    default: null,
  },
});

const slug = computed(() => {
  return props.id.replace(/[^a-zA-Z0-9]/g, "-");
});
</script>

<template>
  <h2 :id="id" class="relative mb-4 flex items-center border-b border-gray-600 pb-1 pt-2 font-mono text-2xl">
    <span class="text-[#e91e63]">{{ title }}</span>
    <a class="ml-2 inline-block !text-gray-300 hover:!text-gray-200 hover:underline" :href="'#' + slug">#</a>
    <a
      v-if="file"
      title="Edit this page on GitHub!"
      target="_blank"
      class="ml-2 inline-block fill-current !text-gray-300 hover:!text-gray-200"
      :href="'https://www.github.com/php-school/phpschool.io/tree/master/assets/components/Website/Docs/Sections/' + file"
    >
      <PencilSquareIcon class="h-6 w-6"></PencilSquareIcon>
    </a>
    <a href="#app" class="absolute right-0 text-xs hover:underline">^ TOP</a>
  </h2>
</template>
