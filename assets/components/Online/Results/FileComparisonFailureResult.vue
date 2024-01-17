<script setup>
import Modal from '../ModalDialog.vue'
import { ExclamationTriangleIcon } from '@heroicons/vue/24/solid'
import { ref } from 'vue'

const openModal = ref(false)
defineProps({
    data: Object
})
</script>

<template>
    <div class="mt-1 w-full flex justify-between items-start">
        <p class="text-sm text-red-500 w-2/3">
            File content was incorrect for
            <code class="result-list-file">{{ data.file_name }}</code>
        </p>
        <button class="text-sm underline text-[#E91E63] text-left p-x2" @click="openModal = true">
            Show diff
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
                            File content mismatch...
                        </h2>
                    </div>
                </div>
            </template>

            <template #body>
                <div class="py-3" id="file-comparison-failure">
                    <div class="flex flex-wrap items-center">
                        <div class="flex items-center">
                            <p class="truncate font-medium text-white">
                                <span class="text-sm"
                                    >The content of the file <code>{{ data.file_name }}</code> your
                                    program was required to create, did not match the expected
                                    content.</span
                                >
                            </p>
                        </div>
                    </div>
                </div>
                <div id="diff">
                    <div class="flex w-full">
                        <div class="w-1/2">
                            <h3
                                class="font-mono pt-[13.5px] text-base font-semibold lg:text-base text-white pb-3 pl-1 mt-0"
                            >
                                Your Content
                            </h3>
                        </div>
                        <div class="w-1/2">
                            <h3
                                class="font-mono pt-[13.5px] text-base font-semibold lg:text-base text-white pb-3 pl-1 mt-0"
                            >
                                Expected Content
                            </h3>
                        </div>
                    </div>
                    <Diff
                        mode="split"
                        theme="dark"
                        language="text"
                        :prev="data.actual_value"
                        :current="data.expected_value"
                    />
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
#file-comparison-failure :not(pre) > code {
    font-size: 90%;
    background-color: #2a2c2d;
    @apply px-2 py-1 rounded mx-1;
    color: #ff75b5;
}

#diff pre {
    border: none;
}

#diff pre code {
    background-color: transparent !important;
}

.result-list-file {
    font-size: 90%;
    @apply p-0 bg-inherit text-[#ff75b5];
}
</style>
