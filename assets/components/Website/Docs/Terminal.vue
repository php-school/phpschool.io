<script setup>
import {ClipboardDocumentIcon} from "@heroicons/vue/24/solid";
import {computed, ref} from "vue";
import {TransitionRoot} from "@headlessui/vue";

const props = defineProps({
    'lines': {
        type: Array,
    },
})

const copied = ref(false);

const copy = () => {
    const lines = props.lines.join("\n");

    if (navigator.clipboard) {
        navigator.clipboard.writeText(lines);

        copied.value = true;

        setTimeout(() => copied.value = false, 2000);
    }
}

const clipboardAvailable = computed(() => {
    return navigator.clipboard;
});
</script>

<template>
    <div class="group font-mono bg-gray-900 border-gray-600 rounded-lg border border-solid p-4 w-full mb-8 overflow-y-scroll">
        <div class="flex flex-col">
            <div class="flex justify-between items-center pb-3">
                <div class="flex">
                    <div class="border border-gray-600 border-solid rounded-full w-4 h-4 mr-2"></div>
                    <div class="border border-gray-600 border-solid rounded-full w-4 h-4 mr-2"></div>
                    <div class="border border-gray-600 border-solid rounded-full w-4 h-4"></div>
                </div>

                <ClipboardDocumentIcon v-if="clipboardAvailable && !copied" @click="copy" class="opacity-0 group-hover:opacity-100 text-gray-300 w-4 h-4 hover:cursor-pointer hover:text-pink-600 transition-all duration-300"></ClipboardDocumentIcon>

                <TransitionRoot
                        :show="copied"
                        enter="transition-opacity duration-25"
                        enter-from="opacity-0"
                        enter-to="opacity-100"
                        leave="transition-opacity duration-150"
                        leave-from="opacity-100"
                        leave-to="opacity-0"
                        class="leading-none"
                >
                    <span class="text-[10px] text-pink-600 font-mono">Copied!</span>
                </TransitionRoot>
            </div>
            <div class="bg-gray-900 border-none text-white p-0  whitespace-pre-wrap font-mono">
                <div v-for="line in lines">
                    <span v-if="line.startsWith('//')" class="text-xs text-pink-600"><br>{{line}}</span>
                    <span v-else class="text-xs before:mr-2 before:text-pink-600 whitespace-pre before:content-['>']">{{line}}</span>
                </div>
            </div>
        </div>
    </div>
</template>