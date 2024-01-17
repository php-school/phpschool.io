<script setup>
defineProps({
    title: String,
    columns: {
        type: Array,
    }
})
</script>

<template>
    <div class="">
        <div class="my-6 flow-root">
            <p v-if="title" class="pb-2 text-sm">{{ title }}</p>
            <div class="overflow-x-auto">
                <div class="inline-block min-w-full py-2 align-middle">
                    <table class="min-w-full divide-y divide-gray-500">
                        <thead>
                            <tr>
                                <th
                                    v-for="col in columns" :key="col"
                                    scope="col"
                                    class="text-left text-sm font-semibold text-white sm:pl-0"
                                >
                                    {{ col }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-600">
                            <slot></slot>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
