<script setup>
import SiteNav from "./SiteNav.vue";
import SiteFooter from "./SiteFooter.vue";
</script>

<template>
  <div class="flex min-h-screen flex-col">
    <SiteNav></SiteNav>
    <div class="flex-1 bg-gray-900">
      <slot></slot>
    </div>
    <SiteFooter></SiteFooter>
  </div>
</template>
