<script setup>
import { useBlogStore } from '../../../stores/blog.js'
const blog = useBlogStore()

import Heading from '../PageHeading.vue'
import { computed } from 'vue'

const props = defineProps({
    slug: String
})

const post = computed(() => {
    return blog.posts.find((post) => post.slug === props.slug)
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
                <div class="post" v-if="post">
                    <div class="text-center mb-3">
                        <h1
                            class="text-4xl font-bold leading-normal mt-[9px] mb-3 mx-0 font-mono text-[#e91e63]"
                        >
                            {{ post.title }}
                        </h1>
                        <span class="text-xs font-bold hover:text-[#e91e63] hover:underline"
                            ><a :href="post.authorLink">{{ post.author }}</a></span
                        >
                        <span class="text-xs text-gray-400"> - {{ post.date }}</span>
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
    @apply mt-[27px] mb-[13.5px] text-lg;
}

.post h2 {
    @apply font-bold text-2xl font-mono text-[#e91e63] pt-[13.5px] mb-[13.5px];
}

.post h3 {
    @apply font-bold text-lg font-mono text-gray-300 pt-[13.5px] mb-[13.5px];
}

.post ul {
    @apply mb-[13.5px] pl-[27px] list-disc;
}

.post ul > li {
    @apply leading-relaxed;
}

.post a {
    @apply text-[#e91e63] hover:underline;
}

.post pre > code {
    @apply block p-4 mb-4 text-xs !bg-gray-900 !border !border-gray-600 rounded-md overflow-y-scroll;
}

.post :not(pre) > code {
    @apply px-1 py-0.5 text-[12px] bg-[#2a2c2d] text-pink-500 rounded;
}
</style>
