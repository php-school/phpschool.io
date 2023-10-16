<script setup>
import Footer from "../Site/SiteFooter.vue";
import Nav from "../Site/SiteNav.vue";
</script>

<template>
    <div class="flex flex-col w-full overflow-hidden">
        <Nav />
        <router-view></router-view>
        <Footer />
    </div>
</template>
