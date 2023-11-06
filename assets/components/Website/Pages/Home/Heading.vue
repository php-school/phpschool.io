<script setup>
</script>
<template>
    <div class="max-w-[2048px] h-auto mx-auto px-9 py-16">
        <div class="flex flex-row w-full ">
            <div class="w-[20px] border-l-4 border-pink-600 border-solid h-12"></div>
            <div>
                <h3 class=" text-6xl font-work-sans font-bold  capitalize text-pink-600 flex-inline p-0">
                    <slot name="title"></slot>
                </h3>
            </div>

        </div>
        <p class="text-base text-white mt-4">
            <slot name="description"></slot>
        </p>
    </div>
</template>
