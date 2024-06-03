<script setup>
const { list } = defineProps({
  list: Array,
});
</script>

<template>
  <ul class="mt-4">
    <li v-for="line in list" :key="line" class="group">
      <div class="py-3">
        <div class="mr-16 pl-1 text-white">
          <div class="flex items-start text-base text-pink-600">
            <svg class="ml-1 mr-2 mt-0.5 h-5 w-5 flex-none" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
            <span class="">{{ line }}</span>
          </div>
        </div>
      </div>
    </li>
  </ul>
</template>
