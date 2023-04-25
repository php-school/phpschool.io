<script>
import {ExclamationTriangleIcon, XMarkIcon, CheckIcon} from "@heroicons/vue/24/solid";

export default {
    components: {
        ExclamationTriangleIcon,
        XMarkIcon,
        CheckIcon
    },
    props: {
        message: String,
        type: {
            type: String,
            default: 'success',
            validator(value) {
                return ['success', 'error'].includes(value)
            }
        },
    },
}
</script>

<template>
    <Transition enter-active-class="transition-opacity duration-100 ease-in" leave-active-class="transition-opacity duration-300 ease-in" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-cloak class="absolute top-4 left-0 z-[51] shadow-lg w-full flex justify-center">
            <div class="mx-auto py-3 px-3 sm:px-6 lg:px-8  bg-gradient-to-r rounded-lg" :class="{'from-red-600 to-pink-700': type === 'error', 'from-pink-500 to-purple-500': type === 'success' }">
                <div class="flex flex-wrap items-center justify-center">
                    <div class="flex items-center">
                        <ExclamationTriangleIcon v-if="type === 'error'" class="h-6 w-6 text-white"/>
                        <CheckIcon v-if="type === 'success'" class="h-6 w-6 text-white"/>
                        <p class="ml-3 truncate font-medium text-white">
                            <span class="">{{ message }}</span>
                        </p>
                    </div>
                    <div class="order-2 flex-shrink-0 sm:order-3 sm:ml-3">
                        <button @click="$emit('close')" type="button" class="-mr-1 flex rounded-md p-2 hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-white sm:-mr-2">
                            <span class="sr-only">Dismiss</span>
                            <XMarkIcon class="h-6 w-6 text-white"/>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>
