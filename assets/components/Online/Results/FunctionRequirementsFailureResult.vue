<script setup>
import Modal from '../ModalDialog.vue'
import { ExclamationTriangleIcon } from '@heroicons/vue/24/solid'
import { ref } from 'vue'

const openModal = ref(false)
defineProps({
    data: Object,
    bannedFunctions: Array,
    missingFunctions: Array
})
</script>

<template>
    <div class="mt-1 w-full flex justify-between items-start">
        <p class="text-sm text-red-500 w-2/3">Functions requirements were not fulfilled</p>
        <button class="text-sm underline text-[#E91E63] text-left" @click="openModal = true">
            More info...
        </button>
    </div>

    <Transition
        enter-active-class="transition-opacity duration-100 ease-in"
        leave-active-class="transition-opacity duration-200 ease-in"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <Modal
            :scroll-content="true"
            size="4xl"
            max-height="max-h-[calc(5/6*100%)]"
            v-if="openModal"
            @close="openModal = false"
        >
            <template #header>
                <div class="flex flex-col">
                    <div class="flex items-center">
                        <ExclamationTriangleIcon class="h-6 w-6 text-rose-600 mr-2" />

                        <h2
                            class="font-mono text-base font-semibold lg:text-xl text-white pt-0 mt-0"
                        >
                            Function requirements failure...
                        </h2>
                    </div>
                </div>
            </template>

            <template #body>
                <div class="py-3 mb-4">
                    <div class="flex flex-wrap items-center">
                        <div class="flex items-center">
                            <p class="truncate font-medium text-white">
                                <span class="text-sm"
                                    >Your program was required to use certain functions and was
                                    prohibited from using others.</span
                                >
                            </p>
                        </div>
                    </div>
                </div>

                <div id="func-requirements" class="flex space-x-4">
                    <div v-if="data.banned_functions.length" class="w-1/2">
                        <h2 class="font-mono text-[#E91E63] mb-2 text-lg pt-0">Banned functions</h2>
                        <p class="text-sm mb-4">
                            Some functions were used which should not be used in this exercise:
                        </p>
                        <code
                            class="text-xs block mb-2 last:mb-0"
                            v-for="(call, i) in data.banned_functions" :key="i"
                            >{{ call.function }} on line {{ call.line }}</code>
                    </div>
                    <div v-if="data.missing_functions.length" class="w-1/2">
                        <h2 class="font-mono text-[#E91E63] mb-2 text-lg pt-0">
                            Missing functions
                        </h2>
                        <p class="text-sm mb-4">
                            Some functions were missing. You should use the following functions:
                        </p>
                        <code
                            class="text-xs block mb-2 last:mb-0"
                            v-for="func in data.missing_functions" :key="func"
                            >{{ func }}</code
                        >
                    </div>
                </div>
            </template>

            <template #footer>
                <div class="flex justify-end">
                    <button
                        @click="openModal = false"
                        type="button"
                        class="inline-flex items-center w-full justify-center rounded-full border border-transparent bg-pink-600 px-8 py-2 text-base font-medium text-white shadow-sm hover:bg-pink-700 focus:outline-none focus:ring focus:ring-pink-800 sm:ml-3 sm:w-auto sm:text-sm"
                    >
                        Close
                    </button>
                </div>
            </template>
        </Modal>
    </Transition>
</template>

<style>
#func-requirements pre {
    background-color: initial;
    @apply mb-4 p-0 border-none rounded-none;
}
#func-requirements pre code {
    background-color: #2a2c2d !important;
    @apply p-4 rounded-lg;
}

#func-requirements :not(pre) > code {
    background-color: #2a2c2d;
    @apply px-2 py-1 rounded;
    color: #ff75b5;
}
</style>
