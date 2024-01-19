<script setup>
import SiteNav from "./SiteNav.vue";
</script>

<template>
  <div class="flex h-screen flex-col bg-gray-900">
    <site-nav></site-nav>
    <slot></slot>
  </div>
</template>
