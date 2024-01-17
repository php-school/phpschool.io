<script setup>
import { computed } from 'vue'

const props = defineProps({
    id: {
        type: String,
        required: true
    },
    title: {
        type: String,
        required: true
    },
    file: {
        type: String,
        default: null
    }
})

const slug = computed(() => {
    return props.id.replace(/[^a-zA-Z0-9]/g, '-')
})
</script>

<template>
    <h2
        :id="id"
        class="flex relative items-center font-mono text-2xl pt-2 pb-1 mb-4 border-b border-gray-600"
    >
        <span class="text-[#e91e63]">{{ title }}</span>
        <a
            class="inline-block !text-gray-300 hover:!text-gray-200 ml-2 hover:underline"
            :href="'#' + slug"
            >#</a
        >
        <a
            v-if="file"
            title="Edit this page on GitHub!"
            target="_blank"
            class="!text-gray-300 hover:!text-gray-200 fill-current inline-block ml-2"
            :href="
                'https://www.github.com/php-school/phpschool.io/tree/master/assets/components/Website/Docs/Sections/' +
                file
            "
        >
            <svg
                class="h-4 w-4"
                version="1.1"
                id="Capa_1"
                xmlns="http://www.w3.org/2000/svg"
                xmlns:xlink="http://www.w3.org/1999/xlink"
                x="0px"
                y="0px"
                width="612px"
                height="612px"
                viewBox="0 0 612 612"
                style="enable-background: new 0 0 612 612"
                xml:space="preserve"
            >
                <g>
                    <g id="New_x5F_Post">
                        <g>
                            <path
                                d="M545.062,286.875c-15.854,0-28.688,12.852-28.688,28.688v239.062h-459v-459h239.062c15.854,0,28.688-12.852,28.688-28.688S312.292,38.25,296.438,38.25H38.25C17.136,38.25,0,55.367,0,76.5v497.25C0,594.883,17.136,612,38.25,612H535.5c21.114,0,38.25-17.117,38.25-38.25V315.562C573.75,299.727,560.917,286.875,545.062,286.875z M605.325,88.95L523.03,6.655C518.556,2.18,512.684,0,506.812,0s-11.743,2.18-16.218,6.675l-318.47,318.45v114.75h114.75l318.45-318.45c4.494-4.495,6.675-10.366,6.675-16.237C612,99.297,609.819,93.445,605.325,88.95z M267.75,382.5H229.5v-38.25L506.812,66.938l38.25,38.25L267.75,382.5z"
                            />
                        </g>
                    </g>
                </g>
            </svg>
        </a>
        <a href="#app" class="absolute right-0 text-xs hover:underline">^ TOP</a>
    </h2>
</template>
