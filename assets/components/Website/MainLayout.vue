<script setup>
import SiteNav from "./SiteNav.vue";
import SiteFooter from "./SiteFooter.vue";
</script>

<template>
  <div class="flex min-h-screen flex-col">
    <site-nav></site-nav>
    <div class="flex-1 bg-gray-900">
      <slot></slot>
    </div>
    <site-footer></site-footer>
  </div>
</template>
