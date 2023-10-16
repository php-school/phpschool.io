<script setup>

import Terminal from "./Terminal.vue";

const props = defineProps({
    'step': {
        type: Number,
    },
    'title': {
        type: String,
    },
    'description': {
        type: String,
    },
    'lines': {
        type: Array,
    },
})
</script>
<template>
    <li class="w-full p-4 flex justify-between mb-12">
        <div class="flex flex-col">
            <div class="bg-slate-800 rounded font-bold w-8 h-8 flex items-center justify-center mb-4">{{step}}</div>
            <div class="border-l border-solid border-slate-800 flex-grow ml-4"></div>
        </div>

        <div class="w-full flex flex-col lg:flex-row">
            <div class="flex-1 px-4 mr-8">
                <h4 class="text-lg font-work-sans font-bold capitalize text-pink-600 mt-0 p-0  not-italic">{{title}}</h4>
                <p class="text-sm text-gray-300 mt-4">{{description}}</p>
            </div>

            <Terminal :lines="lines" />
        </div>
    </li>
</template>