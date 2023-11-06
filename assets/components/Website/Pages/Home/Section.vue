<template>
    <section class="bg-gray-900 ">
        <div class="flex justify-center items-center mb-5">
            <div class="horizontal-divider"></div>
        </div>
        <div class="max-w-[2048px] h-auto mx-auto px-9 mt-16">
            <div v-if="$slots.title">
                <div class="flex flex-row w-full mb-10">
                    <div class="w-[20px] border-l-4 border-pink-600 border-solid h-12"></div>
                    <div>
                        <h3 class=" text-5xl font-work-sans font-bold  capitalize text-pink-600 flex-inline p-0">
                            <slot name="title" />
                        </h3>
                    </div>
                </div>
            </div>
            <slot />
        </div>
    </section>
</template>
