<script setup>
import Code from '../../DocCode.vue'

defineProps({
    event: String,
    eventClass: String,
    args: Array
})
</script>

<template>
    <div class="">
        <h3 class="font-bold mb-4">{{ event }}</h3>
        <p class="mb-6 text-xs">
            Event Class: <Code>{{ eventClass }}</Code>
        </p>

        <p class="mb-8"><slot></slot></p>

        <template v-if="args.length > 0">
            <p class="text-xs font-semibold">Arguments:</p>
            <dl class="p-2 mb-4 w-full">
                <div
                    v-for="arg in args" :key="arg.name"
                    class="py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0 border-b last:border-none"
                >
                    <dt class="italic text-xs">{{ arg.name }}</dt>
                    <dd class="sm:col-span-2 text-xs">
                        <Code>{{ arg.type }}</Code>
                    </dd>
                </div>
            </dl>
        </template>
        <p v-else class="">Arguments - None</p>
    </div>
</template>
