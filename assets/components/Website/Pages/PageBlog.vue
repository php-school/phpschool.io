<script setup>
import { useBlogStore } from "../../../stores/blog.js";
const blog = useBlogStore();

import Heading from "../PageHeading.vue";

defineProps({
  page: { type: Number, default: 1, required: false },
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
        <div v-if="blog.posts.length === 0">
          <h1>No posts yet!</h1>
        </div>

        <div v-for="post in blog.posts" :key="post.slug" class="border-1 border-b border-solid border-pink-600/40 p-8 text-lg last:border-none">
          <div class="mb-3 text-center">
            <h1 class="mx-0 mb-3 mt-[9px] font-mono text-4xl font-bold leading-normal text-white hover:text-[#e91e63]">
              <router-link :to="'/blog/' + post.slug">{{ post.title }}</router-link>
            </h1>
            <span class="text-xs font-bold hover:text-[#e91e63]">
              <a :href="post.authorLink">{{ post.author }}</a>
            </span>
            <span class="text-xs text-gray-400">- {{ post.date }}</span>
          </div>

          <div v-if="post.featuredImage" class="m-6 flex justify-center">
            <router-link class="" :to="'/blog/' + post.slug">
              <div v-html="post.featuredImage"></div>
            </router-link>
          </div>

          <div class="mb-4 font-open-sans">{{ post.excerpt }}</div>

          <p class="mb-2 font-open-sans font-bold hover:text-[#e91e63]">
            <router-link :to="'/blog/' + post.slug">Read more&nbsp;&nbsp;&rsaquo;</router-link>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>
