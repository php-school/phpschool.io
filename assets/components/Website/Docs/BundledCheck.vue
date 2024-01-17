<script setup>
import Note from './DocNote.vue'
import Code from './DocCode.vue'

defineProps({
    check: String,
    interfaceToImplement: String,
    type: {
        type: String,
        default: 'Simple'
    },
    compatible: {
        type: Array,
        validator(value) {
            return ['CLI, CGI'].includes(value)
        }
    },
    registered: {
        type: Boolean,
        default: true
    },
    link: String
})
</script>

<template>
    <h3 class="font-bold mb-4">{{ check }}</h3>
    <dl class="p-2 mb-4 w-full">
        <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
            <dt class="italic text-xs">Interface to implement:</dt>
            <dd class="sm:col-span-2 text-xs">
                <Code>{{ interfaceToImplement }}</Code>
            </dd>
        </div>
        <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
            <dt class="italic text-xs">Type:</dt>
            <dd class="sm:col-span-2 text-xs">
                <Code>{{ type }}</Code>
            </dd>
        </div>
        <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
            <dt class="italic text-xs">Compatible Exercise Types:</dt>
            <dd class="sm:col-span-2 text-xs">
                <Code>{{ compatible.join(', ') }}</Code>
            </dd>
        </div>
    </dl>

    <p class="mb-6"><slot></slot></p>

    <Note v-if="registered" type="success"
        >Note: You do not need to require this check yourself, it is done so automatically.</Note
    >
    <a v-if="link" :href="link">Learn how to use</a>
</template>
