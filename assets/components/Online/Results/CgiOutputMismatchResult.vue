<script setup>
import Modal from '../ModalDialog.vue'
import { ExclamationTriangleIcon } from '@heroicons/vue/24/solid'
import { ref } from 'vue'

const openModal = ref(false)
defineProps({
    data: Object
})

const headersAsString = (headers) => {
    let out = ''
    for (const [key, value] of Object.entries(headers)) {
        out += key + ': ' + value + '\n'
    }
    return out
}
</script>

<template>
    <div class="mt-1 w-full flex justify-between">
        <p class="text-sm text-red-500">Output was incorrect</p>
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
                            Output mismatch...
                        </h2>
                    </div>
                </div>
            </template>

            <template #body>
                <div id="diff">
                    <div v-if="data.body_different">
                        <div class="py-3">
                            <div class="flex flex-wrap items-center">
                                <div class="flex items-center">
                                    <p class="truncate font-medium text-white">
                                        <span class="text-sm"
                                            >Your programs output did not match the expected
                                            output:</span
                                        >
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="flex w-full">
                            <div class="w-1/2">
                                <h3
                                    class="font-mono text-base font-semibold lg:text-base text-white pb-3 pl-1 mt-0"
                                >
                                    Your Output
                                </h3>
                            </div>
                            <div class="w-1/2">
                                <h3
                                    class="font-mono text-base font-semibold lg:text-base text-white pb-3 pl-1 mt-0"
                                >
                                    Expected Output
                                </h3>
                            </div>
                        </div>
                        <Diff
                            mode="split"
                            theme="dark"
                            language="text"
                            :prev="data.actual_output"
                            :current="data.expected_output"
                        />
                    </div>
                    <div v-if="data.headers_different">
                        <div class="py-3">
                            <div class="flex flex-wrap items-center">
                                <div class="flex items-center">
                                    <p class="truncate font-medium text-white">
                                        <span class="text-sm"
                                            >Your programs headers did not match the expected
                                            headers:</span
                                        >
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="flex w-full">
                            <div class="w-1/2">
                                <h3
                                    class="font-mono text-base font-semibold lg:text-base text-white pb-3 pl-1 mt-0"
                                >
                                    Your Headers
                                </h3>
                            </div>
                            <div class="w-1/2">
                                <h3
                                    class="font-mono text-base font-semibold lg:text-base text-white pb-3 pl-1 mt-0"
                                >
                                    Expected Headers
                                </h3>
                            </div>
                        </div>
                        <Diff
                            mode="split"
                            theme="dark"
                            language="text"
                            :prev="headersAsString(data.actual_headers)"
                            :current="headersAsString(data.expected_headers)"
                        />
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
#diff pre {
    border: none;
}

#diff pre code {
    background-color: transparent !important;
}
</style>
