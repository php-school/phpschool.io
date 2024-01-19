<script setup>
defineProps({
  id: {
    type: String,
    required: true,
  },
  level: {
    type: String,
    default: "h3",
  },
});
</script>
<template>
  <h3
    v-if="level === 'h3'"
    :id="id"
    class="relative !mb-4 flex items-center border-b border-gray-600 !pb-1 !pt-4 font-mono !text-lg"
  >
    <slot></slot>
    <a class="ml-[2px] inline-block fill-current !text-[#ccc]" :href="'#' + id">#</a>
    <a href="#app" class="absolute right-0 text-xs hover:underline">^ TOP</a>
  </h3>

  <h4
    v-if="level === 'h4'"
    :id="id"
    class="relative !mb-4 flex items-center border-b border-gray-600 !pb-1 !pt-4 font-mono !text-base font-semibold italic"
  >
    <slot></slot>
    <a class="ml-[2px] inline-block fill-current !text-[#ccc]" :href="'#' + id">#</a>
  </h4>

  <h4
    v-if="level === 'h4-code'"
    :id="id"
    class="relative !mb-2 flex items-center !pb-1 !pt-4 font-mono !text-base !not-italic"
  >
    <code class="text-pink-500"><slot></slot></code>
    <a class="ml-[3px] inline-block fill-current !text-[#ccc]" :href="'#' + id">#</a>
  </h4>

  <h5 v-if="level === 'h5'" :id="id" class="relative !mb-2 flex items-center !pb-1 !pt-4 font-mono !text-base italic">
    <slot></slot>
    <a class="ml-[2px] inline-block fill-current !text-[#ccc]" :href="'#' + id">#</a>
  </h5>
</template>
