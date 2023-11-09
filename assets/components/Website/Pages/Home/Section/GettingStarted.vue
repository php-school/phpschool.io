<script setup>
import HomeSection from "../Section.vue";
import {CogIcon} from "@heroicons/vue/24/outline";
import {CheckIcon} from "@heroicons/vue/24/solid";
</script>

<template>
    <HomeSection>
        <template #title>Getting Started</template>
        <div class="bg-gray-900 text-white p-4 flex flex-col">
            <p class="mb-4">PHP School workshops are a set of exercises focused on a specific topic or overarching theme, such as new language versions or tools and techniques.
            See the <a class="text-[#e91e63] hover:underline" href="#the-workshops">below section</a> for the list of available workshops.
            </p>

            <p class="mb-4">Workshops can be run in your browser or you can download them and run them on your own computer. Running them on your own computer is a little bit more involved, however, it is also more flexible and self contained.</p>

            <h4 class="text-lg font-work-sans font-bold capitalize text-white mt-0 p-0 not-italic mb-4">Browser Based</h4>
            <p class="mb-4">Simply login or register with your GitHub account, head to the dashboard, select an exercise and go.</p>
            <h4 class="text-lg font-work-sans font-bold capitalize text-white mt-0 p-0 not-italic mb-4">Offline</h4>
            <p class="mb-4">You need to configure your system, install the Workshop Manager, and then install the workshops. Head on over to the <a class="text-[#e91e63] hover:underline" href="/offline">Running PHP School locally page to get the low down.</a></p>
        </div>
    </HomeSection>
</template>