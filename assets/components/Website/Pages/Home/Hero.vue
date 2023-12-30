<script setup>
import Logo from "../../Logo.vue";
import CodeAnimation from "../../CodeAnimation.vue";
</script>

<template>
    <div class="bg-center bg-cover bg-[url('/img/cloud/phpCity.jpg')] -mt-28 z-10">
        <div class="max-w-[2048px] mx-auto w-full h-screen flex justify-between items-center text-center pt-28 ">
            <div class="drop-shadow-[0_0_5px_rgba(255,255,255,1)] text-white overflow-hidden px-4 code-fade">
                <CodeAnimation class="text-left text-sm" />
            </div>
            <div>
                <figure class="hover:drop-shadow-[0_0_10px_rgba(255,255,255,1)] transition-all ease-in-out duration-500">
                    <Logo class="text-white hover:text-white h-64 w-64 " />
                </figure>
                <div class="mt-20">
                    <a href=" /cloud"
                        class="text-white uppercase items-center px-10 py-2 rounded-full bg-none font-thin border-2 transition-all duration-300 ease-in-out hover:bg-white hover:text-gray-800 hover:no-underline">
                        get started
                    </a>
                </div>
            </div>
            <div class="drop-shadow-[0_0_5px_rgba(255,255,255,1)] text-white overflow-hidden px-4 code-fade">
                <CodeAnimation class="text-right text-sm" />
            </div>
        </div>
    </div>
</template>

<style>
.code-fade {
    -webkit-mask-image: linear-gradient(to bottom, black 50%, transparent 100%);
    mask-image: linear-gradient(to bottom, black 50%, transparent 100%);
    height: 320px;
    width: 600px;
    overflow-y: hidden;

}
</style>