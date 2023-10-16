<script setup>
    const props = defineProps({
        'lines': {
            type: Array,
        },
    })
</script>

<template>
    <div class="border-gray-600 rounded-lg border border-solid p-4 w-full lg:w-3/5 m-4 mt-8 lg:m-0 lg:mt-0">
        <div class="flex flex-col">
            <div class="flex pb-10">
                <div class="border border-gray-600 border-solid rounded-full w-6 h-6 mr-2"></div>
                <div class="border border-gray-600 border-solid rounded-full w-6 h-6 mr-2"></div>
                <div class="border border-gray-600 border-solid rounded-full w-6 h-6"></div>
            </div>
            <div class="bg-gray-900 border-none text-white p-0 lg:px-10 whitespace-pre-wrap font-mono">
                <div v-for="line in lines">
                    <span v-if="line.startsWith('//')" class="text-pink-600"><br>{{line}}</span>
                    <span v-else class="before:mr-2 before:text-pink-600 whitespace-pre before:content-['$']">{{line}}</span>
                </div>
            </div>
        </div>
    </div>
</template>