<script setup>
import Button from "../../Button.vue";
</script>

<template>
    <div class="max-w-[2048px] mx-auto  my-8  sm:my-12 md:my-14 lg:my-20">
        <div class="w-full flex items-center justify-center flex-col  text-center pt-20 pb-10 gap-4 ">
            <p class="text-white text text-2xl font-sans font-normal">A revolutionary new way to learn PHP</p>
            <h1 class="text-white text-5xl md:text-6xl font-mono uppercase font-bold tracking-wider">Open source learning
                for php
            </h1>
            <p class="text-white text text-2xl font-sans font-light">Bring your imagination to life in an open learning
                eco-system</p>
            <Button href="/cloud">GET STARTED</Button>
        </div>
        <figure class="w-full flex items-center justify-center flex-col  text-center">
            <img src="../../../../img/cloud/editor-placeholder.png" alt=" placeholder">
        </figure>
    </div>
</template>