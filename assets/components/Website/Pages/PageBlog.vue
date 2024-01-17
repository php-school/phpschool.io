<script setup>
import { useBlogStore } from '../../../stores/blog.js'
const blog = useBlogStore()

import Heading from '../PageHeading.vue'

defineProps({
    page: { type: Number, default: 1, required: false }
})
</script>
<template>
    <div>
        <section class="bg-gradient-to-b from-gray-900 to-cyan-500 bg-fixed items-stretch">
            <Heading>
                <template v-slot:title> What's happening in <br />the PHP School world </template>
            </Heading>
        </section>
        <div class="w-full bg-gray-900">
            <div class="font-open-sans flex flex-col mx-auto max-w-3xl text-left text-gray-300 p-8">
                <div v-if="blog.posts.length === 0">
                    <h1>No posts yet!</h1>
                </div>

                <div
                    v-for="post in blog.posts"
                    :key="post.slug"
                    class="p-8 border-b border-solid border-1 border-pink-600/40 text-lg last:border-none"
                >
                    <div class="text-center mb-3">
                        <h1
                            class="text-4xl font-bold leading-normal mt-[9px] mb-3 mx-0 font-mono text-white hover:text-[#e91e63]"
                        >
                            <router-link :to="'/blog/' + post.slug"> {{ post.title }}</router-link>
                        </h1>
                        <span class="text-xs font-bold hover:text-[#e91e63]"
                            ><a :href="post.authorLink">{{ post.author }}</a></span
                        >
                        <span class="text-xs text-gray-400"> - {{ post.date }}</span>
                    </div>

                    <div v-if="post.featuredImage" class="m-6 flex justify-center">
                        <router-link class="" :to="'/blog/' + post.slug">
                            <div v-html="post.featuredImage"></div>
                        </router-link>
                    </div>

                    <div class="mb-4 font-open-sans">{{ post.excerpt }}</div>

                    <p class="font-bold hover:text-[#e91e63] font-open-sans mb-2">
                        <router-link :to="'/blog/' + post.slug"
                            >Read more&nbsp;&nbsp;&rsaquo;</router-link
                        >
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
