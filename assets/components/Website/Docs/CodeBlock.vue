<script setup>
import { computed, useSlots } from 'vue'

const slots = useSlots()

import { ref, onMounted, watch } from 'vue'

import { highlightCode } from '../../../helpers/highlightCode'

import { TransitionRoot } from '@headlessui/vue'
import { ClipboardDocumentIcon } from '@heroicons/vue/24/solid'

const language = ref('php')
const formattedCode = ref('')
const code = ref(slots.default()[0].children)

const highlight = () => {
    formattedCode.value = highlightCode(code.value, language.value)
}

onMounted(() => {
    highlight()
})

watch(
    () => slots.default(),
    () => {
        highlight()
    }
)

const copied = ref(false)

const copy = () => {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(code.value)

        copied.value = true

        setTimeout(() => (copied.value = false), 2000)
    }
}

const clipboardAvailable = computed(() => {
    return typeof window !== 'undefined' && navigator.clipboard
})
</script>
<template>
    <div class="group relative">
        <ClipboardDocumentIcon
            v-if="clipboardAvailable && !copied"
            @click="copy"
            class="absolute bg-gray-900 top-4 right-4 opacity-0 group-hover:opacity-100 text-gray-300 w-4 h-4 hover:cursor-pointer hover:text-pink-600 transition-all duration-300"
        ></ClipboardDocumentIcon>

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
            <span class="absolute top-4 right-4 text-[10px] text-pink-600 font-mono">Copied!</span>
        </TransitionRoot>
        <div class="p-4 mb-4 bg-gray-900 border border-gray-600 rounded-md overflow-y-scroll">
            <pre><code class="block  text-xs " :class="language" v-html="formattedCode"></code></pre>
        </div>
    </div>
</template>
