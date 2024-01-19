<script setup>
import { useBlogStore } from "../../../stores/blog.js";
const blog = useBlogStore();

import Heading from "../PageHeading.vue";
import { computed } from "vue";

const props = defineProps({
  slug: String,
});

const post = computed(() => {
  return blog.posts.find((post) => post.slug === props.slug);
});
</script>

<template>
  <div>
    <section class="items-stretch bg-gradient-to-b from-gray-900 to-cyan-500 bg-fixed">
      <Heading>
        <template v-slot:title>
          What's happening in
          <br />
          the PHP School world
        </template>
      </Heading>
    </section>
    <div class="w-full bg-gray-900">
      <div class="mx-auto flex max-w-3xl flex-col p-8 text-left font-open-sans text-gray-300">
        <div class="post" v-if="post">
          <div class="mb-3 text-center">
            <h1 class="mx-0 mb-3 mt-[9px] font-mono text-4xl font-bold leading-normal text-[#e91e63]">
              {{ post.title }}
            </h1>
            <span class="text-xs font-bold hover:text-[#e91e63] hover:underline">
              <a :href="post.authorLink">{{ post.author }}</a>
            </span>
            <span class="text-xs text-gray-400">- {{ post.date }}</span>
          </div>
          <div v-html="post.content"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<style>
.post img {
  @apply mx-auto;
}

.post p {
  @apply mb-[13.5px] mt-[27px] text-lg;
}

.post h2 {
  @apply mb-[13.5px] pt-[13.5px] font-mono text-2xl font-bold text-[#e91e63];
}

.post h3 {
  @apply mb-[13.5px] pt-[13.5px] font-mono text-lg font-bold text-gray-300;
}

.post ul {
  @apply mb-[13.5px] list-disc pl-[27px];
}

.post ul > li {
  @apply leading-relaxed;
}

.post a {
  @apply text-[#e91e63] hover:underline;
}

.post pre > code {
  @apply mb-4 block overflow-y-scroll rounded-md !border !border-gray-600 !bg-gray-900 p-4 text-xs;
}

.post :not(pre) > code {
  @apply rounded bg-[#2a2c2d] px-1 py-0.5 text-[12px] text-pink-500;
}
</style>
