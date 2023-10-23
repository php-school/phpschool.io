<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import Button from "../../Button.vue";
import Logo from "../../Logo.vue";
import codeAnimation from "../../codeAnimation.vue";


</script>

<template>
    <div class="bg-center bg-cover bg-[url('/img/cloud/phpCity.jpg')] -mt-28 z-10">
        <div class="max-w-[2048px] mx-auto w-full h-screen flex justify-evenly items-center text-center pt-28 ">
            <div class="glow-effect text-white w-[400px] max-h-80 overflow-hidden px-4">
                <codeAnimation class="text-left " />
            </div>
            <div>
                <figure class="glow-effect">
                    <Logo class="text-white h-64 w-64 " />
                </figure>
                <div class="mt-20">
                    <a href=" /cloud"
                        class="text-white uppercase items-center px-10 py-2 rounded-full bg-none font-thin border-2 transition-all duration-300 ease-in-out hover:bg-white hover:text-gray-800 hover:no-underline">
                        get started
                    </a>
                </div>
            </div>
            <div class="glow-effect text-white w-[400px] max-h-80 overflow-hidden px-4">
                <codeAnimation class="text-left" />
            </div>
        </div>
    </div>
</template>

<style>
.glow-effect {
    filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.8));
    transition: filter 0.5s ease-in-out;
}

.glow-effect:hover {
    filter: drop-shadow(0 0 20px rgba(255, 255, 255, 1));
}
</style>