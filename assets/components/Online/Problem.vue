<script setup>
import Modal from "./Modal.vue";
import {MapIcon} from "@heroicons/vue/24/solid";

const props = defineProps({
    exercise: Object,
    openProblemModal: Boolean,
});

const emit = defineEmits(['close']);
</script>

<template>
    <Transition enter-active-class="transition-opacity duration-100 ease-in"
                leave-active-class="transition-opacity duration-200 ease-in" enter-from-class="opacity-0"
                enter-to-class="opacity-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <Modal id="problem-modal" :scroll-content="true" size="4xl" max-height="max-h-[calc(5/6*100%)]"
               v-if="openProblemModal" @close="emit('close')">
            <template #header>
                <div class="flex flex-col">
                    <div class="flex items-center ">
                        <MapIcon class="h-6 w-6 text-pink-500 mr-2"/>
                        <h3 class="font-mono text-base font-semibold lg:text-xl text-white pt-0 mt-0 ">
                            The problem...
                        </h3>
                    </div>
                    <h2 class="font-mono mt-2 mb-2 text-2xl text-white pt-[13.5px]">{{ exercise.name }}</h2>
                </div>
            </template>

            <template #body class="">
                <div id="problem-file" class="text-white">
                    <slot></slot>
                </div>
            </template>

            <template #footer>
                <div class="flex justify-end">
                    <button id="lets-go" @click="emit('close')" type="button"
                            class="inline-flex items-center w-full justify-center rounded-full border border-transparent bg-pink-600 px-8 py-2 text-base font-medium text-white shadow-sm hover:bg-pink-700 focus:outline-none focus:ring focus:ring-pink-800 sm:ml-3 sm:w-auto sm:text-sm">
                        Let's go!
                    </button>
                </div>
            </template>
        </Modal>
    </Transition>
</template>


<style>
#problem-file p {
    @apply mt-2 mb-4 leading-loose tracking-wide text-sm;
}

#problem-file hr {
    @apply border-dotted border-gray-600;
}

#problem-file h2 {
    @apply mt-2 mb-4 text-2xl text-[#E91E63];
}

#problem-file h3 {
    @apply mt-2 mb-4 text-xl text-[#E91E63];
}

#problem-file h4 {
    @apply mt-2 mb-4 text-lg not-italic text-[#E91E63];
}

#problem-file a {
    @apply text-[#E91E63];
}

#problem-file pre {
    background-color: initial;
    @apply mt-2 mb-4 p-0 border-none rounded-none;
}

#problem-file pre code {
    @apply p-4 rounded-lg block text-sm border border-gray-600 !bg-[#2a2c2d];
}

#problem-file :not(pre) > code {
    font-size: 90%;
    @apply px-2 py-1 rounded bg-[#2a2c2d] text-[#ff75b5]
}

#problem-file ul {
    @apply mt-2 mb-4 list-disc list-inside;
}

#problem-file ul li {
    @apply list-item p-1;
}

</style>
