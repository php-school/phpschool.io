<script setup>

const props = defineProps({
    'post': String,
    'meta': Object
})

</script>

<template>
    <div class="flex flex-col mx-auto max-w-3xl text-left text-gray-800 p-8">
        <div class="text-center mb-3">
            <h1 class="text-4xl font-bold leading-normal mt-[9px] mb-3 mx-0 font-mono text-[#e91e63]">{{ meta.title }}</h1>
            <span class="text-xs font-bold hover:text-[#e91e63] hover:underline"><a :href="meta.authorLink">{{ meta.author }}</a></span>
            <span class="text-xs text-gray-400"> - {{ meta.date }}</span>
        </div>

         <div class="post">
             <slot />
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
    @apply font-bold text-2xl font-mono text-[#e91e63] pt-[13.5px] mb-[13.5px]
}

.post h3 {
    @apply font-bold text-lg font-mono text-black pt-[13.5px] mb-[13.5px]
}

.post ul {
    @apply  mb-[13.5px] pl-[27px] list-disc
}

.post ul > li {
    @apply  leading-relaxed
}

.post a {
    @apply text-[#e91e63] hover:underline

}

.post pre code.language-shell {
    @apply p-4 my-5 w-full block bg-gray-800 text-white text-xs leading-relaxed;
}

.post :not(pre)>code {
    @apply px-2 py-0.5 mx-1 text-xs bg-gray-200 text-pink-500;
}
</style>