<script setup>
</script>
<template>
    <component :is="$route.meta.layout">
        <router-view v-slot="{ Component }">
            <transition name="slide" mode="in-out">
                <component :is="Component" :key="$route.path"/>
            </transition>
        </router-view>
    </component>
</template>
<style>
.slide-enter-active,
.slide-leave-active {
    transition: opacity 0.3s;
}

.slide-enter-from,
.slide-leave-to {
    opacity: 0;
}
</style>